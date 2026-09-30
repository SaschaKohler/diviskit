#!/usr/bin/env bash
# release.sh — Diviskit plugin release pipeline.
#
#   bin/release.sh <slug> <version> [--staging] [--push] [--tested <wp>] [--changelog "<html>"]
#
# Steps: bump Version header + version constant → php -l → zip → attach to
# dev store (ddev) + license meta → optionally staging (kubectl pod) →
# dist tree/zip sync → optionally commit+push site + dist repos.
set -euo pipefail
cd "$(dirname "$0")/.."
ROOT=$PWD
die() { echo "error: $*" >&2; exit 1; }

# ---- args -------------------------------------------------------------
SLUG=${1:-}; VERSION=${2:-}; shift 2 || true
STAGING=0; PUSH=0; TESTED=""; CHANGELOG=""
while [ $# -gt 0 ]; do
  case "$1" in
    --staging) STAGING=1 ;;
    --push) PUSH=1 ;;
    --tested) TESTED=$2; shift ;;
    --changelog) CHANGELOG=$2; shift ;;
    *) die "unknown arg $1" ;;
  esac; shift
done
[ -n "$SLUG" ] && [ -n "$VERSION" ] || die "usage: release.sh <slug> <version> [--staging] [--push] [--tested <wp>] [--changelog <html>]"

# ---- per-plugin config -------------------------------------------------
case "$SLUG" in
  diviskit-agent)           MAIN=diviskit-agent.php;            PID=344; VCONST="const VERSION";            PUBLIC=1 ;;
  diviskit-pro)             MAIN=diviskit-pro.php;              PID=318; VCONST="DIVISKIT_PRO_VERSION";     PUBLIC=0 ;;
  diviskit-design-library)  MAIN=diviskit-design-library.php;   PID=319; VCONST="const VERSION";            PUBLIC=1 ;;
  sk-consent)               MAIN=sk-consent.php;                PID=317; VCONST="SK_CONSENT_VERSION";       PUBLIC=0 ;;
  vendokit)                 MAIN=vendokit.php;                  PID=320; VCONST="VK_VERSION";               PUBLIC=0 ;;
  vendokit-divi)            MAIN=vendokit-divi.php;             PID=320; VCONST="VKD_VERSION";              PUBLIC=0 ;;
  vendokit-support)         MAIN=vendokit-support.php;          PID=320; VCONST="VKS_VERSION";              PUBLIC=0 ;;
  *) die "unknown slug $SLUG" ;;
esac
PDIR="wp-content/plugins/$SLUG"
[ -f "$PDIR/$MAIN" ] || die "missing $PDIR/$MAIN"

# ---- 1. bump -----------------------------------------------------------
echo "==> bump $SLUG → $VERSION"
sed -i '' -E "s/^([[:space:]]*\*[[:space:]]*Version: )[0-9]+\.[0-9]+\.[0-9]+/\1$VERSION/" "$PDIR/$MAIN"
sed -i '' -E "/$VCONST/s/'[0-9]+\.[0-9]+\.[0-9]+'/'$VERSION'/" "$PDIR/$MAIN"
grep -nE "Version: |$VCONST" "$PDIR/$MAIN" | grep "$VERSION" || die "version bump failed — check $PDIR/$MAIN"

# ---- 2. lint -----------------------------------------------------------
echo "==> php -l"
ddev exec bash -c "find /var/www/html/$PDIR -name '*.php' -print0 | xargs -0 -n1 php -l 2>&1 | grep -v 'No syntax errors' || true" | tee /tmp/release-lint.txt
grep -q "Parse error\|Fatal" /tmp/release-lint.txt && die "php lint failed"

# ---- 3. zip ------------------------------------------------------------
echo "==> zip"
mkdir -p .devin/tmp/release-zips
ZIP="$ROOT/.devin/tmp/release-zips/$SLUG.zip"
ZIP_IN_CONTAINER="/var/www/html/.devin/tmp/release-zips/$SLUG.zip"
(cd wp-content/plugins && zip -qr "$ZIP" "$SLUG" -x "*.DS_Store" "$SLUG/vendor/*" "$SLUG/tests/*" "$SLUG/composer.json" "$SLUG/composer.lock" "$SLUG/phpunit.xml*" "$SLUG/.gitignore" "$SLUG/.phpunit*")
ls -la "$ZIP" | awk '{print $5, $9}'

# ---- 4. attach dev store ----------------------------------------------
echo "==> attach dev store (product $PID)"
META_EXTRA=""
[ -n "$TESTED" ]    && META_EXTRA="$META_EXTRA update_post_meta(\$pid, \"_vk_license_tested\", \"$TESTED\");"
[ -n "$CHANGELOG" ] && META_EXTRA="$META_EXTRA update_post_meta(\$pid, \"_vk_license_changelog\", \"$CHANGELOG\");"
if [ "$SLUG" = "vendokit-divi" ] || [ "$SLUG" = "vendokit-support" ]; then
  # satellite: own version meta (_vk_license_version_<slug>), NOT the
  # product-level _vk_license_version (that's vendokit's version).
  ddev wp eval "
\$pid=$PID; \$row = Vendokit_Downloads::attach(\$pid, \"$ZIP_IN_CONTAINER\", [\"title\"=>\"$SLUG\",\"file_name\"=>\"$SLUG.zip\"]);
if (is_wp_error(\$row)) { fwrite(STDERR, \$row->get_error_message()); exit(1); }
update_post_meta(\$pid, \"_vk_license_version_$SLUG\", \"$VERSION\");$META_EXTRA
echo \"dev: satellite dl {\$row[\"id\"]} ver $VERSION\n\";"
else
  ddev wp eval "
\$pid=$PID; \$row = Vendokit_Downloads::attach(\$pid, \"$ZIP_IN_CONTAINER\", [\"title\"=>\"$SLUG\",\"file_name\"=>\"$SLUG.zip\"]);
if (is_wp_error(\$row)) { fwrite(STDERR, \$row->get_error_message()); exit(1); }
update_post_meta(\$pid, \"_vk_license_update_download_id\", (int)\$row[\"id\"]);
update_post_meta(\$pid, \"_vk_license_version\", \"$VERSION\");
update_post_meta(\$pid, \"_vk_license_enabled\", \"yes\");$META_EXTRA
echo \"dev: dl {\$row[\"id\"]} ver $VERSION\n\";"
fi

# ---- 5. attach staging -------------------------------------------------
if [ "$STAGING" = "1" ]; then
  echo "==> attach staging (product $PID)"
  NS=wordpress-diviskit-staging
  POD=$(kubectl -n "$NS" get pod -l app=diviskit --field-selector=status.phase=Running -o jsonpath='{.items[0].metadata.name}')
  kubectl -n "$NS" cp "$ZIP" "$POD:/tmp/$SLUG.zip"
  VERMETA="_vk_license_version"; UPD_DL="update_post_meta(\$pid, \"_vk_license_update_download_id\", (int)\$row[\"id\"]);"
  if [ "$SLUG" = "vendokit-divi" ] || [ "$SLUG" = "vendokit-support" ]; then VERMETA="_vk_license_version_$SLUG"; UPD_DL=""; fi
  kubectl -n "$NS" exec "$POD" -- su -s /bin/sh www-data -c "php -r '
define(\"WP_USE_THEMES\",false); require \"/var/www/html/wp-load.php\";
\$pid=$PID; \$row = Vendokit_Downloads::attach(\$pid, \"/tmp/$SLUG.zip\", [\"title\"=>\"$SLUG\",\"file_name\"=>\"$SLUG.zip\"]);
if (is_wp_error(\$row)) { exit(1); }
$UPD_DL
update_post_meta(\$pid, \"$VERMETA\", \"$VERSION\");
update_post_meta(\$pid, \"_vk_license_enabled\", \"yes\");$META_EXTRA
echo \"staging: dl {\$row[\"id\"]}\n\";'"
fi

# ---- 6. dist sync ------------------------------------------------------
echo "==> dist sync"
DIST_PRO=$ROOT/plugins/diviskit-pro-suite-v1.0.0
sync_dist() {
  local dist=$1
  rsync -a --delete --exclude '.DS_Store' --exclude 'vendor/' --exclude 'tests/' \
    --exclude 'composer.json' --exclude 'composer.lock' --exclude 'phpunit.xml*' \
    --exclude '.gitignore' --exclude '.phpunit*' \
    "wp-content/plugins/$SLUG/" "$dist/plugins/$SLUG/"
  (cd wp-content/plugins && zip -qr "$dist/$SLUG.zip" "$SLUG" -x "*.DS_Store" "$SLUG/vendor/*" "$SLUG/tests/*" "$SLUG/composer.json" "$SLUG/composer.lock" "$SLUG/phpunit.xml*" "$SLUG/.gitignore" "$SLUG/.phpunit*")
  echo "  synced $dist"
}
[ "$PUBLIC" = "1" ] && sync_dist "$ROOT/plugins/diviskit-dist"
sync_dist "$DIST_PRO"

# ---- 7. commit+push ----------------------------------------------------
if [ "$PUSH" = "1" ]; then
  echo "==> commit + push"
  git add "wp-content/plugins/$SLUG"
  git commit -m "$SLUG $VERSION release" || echo "  (site repo: nothing to commit)"
  git push
  for d in "$ROOT/plugins/diviskit-dist" "$DIST_PRO"; do
    [ "$PUBLIC" = "1" ] || [ "$d" = "$DIST_PRO" ] || continue
    (cd "$d" && git add -A && { git commit -m "$SLUG $VERSION" || true; } && git push)
  done
fi

cat <<EOF

done. verify:
  curl -sk -X POST 'https://divi-ops-ext.ddev.site/vendokit-license/get_license_version' \
    -d 'item=$SLUG&current_version=0.0.1' | python3 -m json.tool | grep -E 'new_version|package'
  # client site: clear dklc_update_$SLUG + update_plugins transients → Dashboard → Updates
EOF
