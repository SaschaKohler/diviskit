#!/usr/bin/env bash
# release.sh — Diviskit plugin release pipeline.
#
#   bin/release.sh <slug> <version> [--staging] [--push] [--tested <wp>] [--changelog "<html>"]
#
# Source of truth: the suite repos (Active_Projects/diviskit = free,
# Active_Projects/diviskit-pro = payware). This script works from any of
# the three locations (site repo bin/ or either suite repo bin/).
#
# Steps: bump Version header + version constant IN THE REPO → rsync to
# wp-content/plugins (dev site) → php -l → zip → attach to dev store
# (ddev) + license meta → optionally staging (kubectl pod) → optionally
# commit+push the source repo (and site repo mirror).
set -euo pipefail
cd "$(dirname "$0")/.."
ROOT=$PWD
die() { echo "error: $*" >&2; exit 1; }

# ---- locate site + suite repos -----------------------------------------
# Works from diviskit-shop/bin or <suite-repo>/bin.
if [ -d "$ROOT/wp-content" ]; then
  SITE=$ROOT
else
  SITE="$ROOT/../diviskit-shop"
fi
SITE=$(cd "$SITE" && pwd)
AP=$(cd "$SITE/.." && pwd)
FREE_REPO="$AP/diviskit"
PRO_REPO="$AP/diviskit-pro"
[ -d "$FREE_REPO" ] && [ -d "$PRO_REPO" ] || die "suite repos not found next to $SITE"

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
# BUNDLE=1: free plugins that also ship inside the pro suite repo.
case "$SLUG" in
  diviskit-agent)           MAIN=diviskit-agent.php;            PID=344; VCONST="const VERSION";            PUBLIC=1; BUNDLE=1 ;;
  diviskit-design-library)  MAIN=diviskit-design-library.php;   PID=319; VCONST="const VERSION";            PUBLIC=1; BUNDLE=1 ;;
  sk-mailerlite-doi)        MAIN=sk-mailerlite-doi.php;         PID="";  VCONST="SKML_VERSION";             PUBLIC=1; BUNDLE=0 ;;
  diviskit-pro)             MAIN=diviskit-pro.php;              PID=318; VCONST="DIVISKIT_PRO_VERSION";     PUBLIC=0; BUNDLE=0 ;;
  sk-consent)               MAIN=sk-consent.php;                PID=317; VCONST="SK_CONSENT_VERSION";       PUBLIC=0; BUNDLE=0 ;;
  vendokit)                 MAIN=vendokit.php;                  PID=320; VCONST="VK_VERSION";               PUBLIC=0; BUNDLE=0 ;;
  vendokit-divi)            MAIN=vendokit-divi.php;             PID=320; VCONST="VKD_VERSION";              PUBLIC=0; BUNDLE=0 ;;
  vendokit-support)         MAIN=vendokit-support.php;          PID=320; VCONST="VKS_VERSION";              PUBLIC=0; BUNDLE=0 ;;
  *) die "unknown slug $SLUG" ;;
esac
if [ "$PUBLIC" = "1" ]; then SRC_REPO=$FREE_REPO; else SRC_REPO=$PRO_REPO; fi
SRC="$SRC_REPO/plugins/$SLUG"
PDIR="wp-content/plugins/$SLUG"
[ -f "$SRC/$MAIN" ] || die "missing $SRC/$MAIN"

# ---- 1. bump in repo (source of truth) ----------------------------------
echo "==> bump $SLUG → $VERSION ($SRC_REPO)"
sed -i '' -E "s/^([[:space:]]*\*[[:space:]]*Version: )[0-9]+\.[0-9]+\.[0-9]+/\1$VERSION/" "$SRC/$MAIN"
sed -i '' -E "/$VCONST/s/'[0-9]+\.[0-9]+\.[0-9]+'/'$VERSION'/" "$SRC/$MAIN"
grep -nE "Version: |$VCONST" "$SRC/$MAIN" | grep "$VERSION" || die "version bump failed — check $SRC/$MAIN"

# ---- 2. sync repo → site (+ pro-repo bundle mirror for free plugins) ----
echo "==> sync → $PDIR"
rsync -a --delete --exclude '.DS_Store' --exclude '__MACOSX' "$SRC/" "$SITE/$PDIR/"
if [ "$BUNDLE" = "1" ]; then
  rsync -a --delete --exclude '.DS_Store' --exclude '__MACOSX' "$SRC/" "$PRO_REPO/plugins/$SLUG/"
fi

# ---- 3. lint -----------------------------------------------------------
echo "==> php -l"
(cd "$SITE" && ddev exec bash -c "find /var/www/html/$PDIR -name '*.php' -print0 | xargs -0 -n1 php -l 2>&1 | grep -v 'No syntax errors' || true") | tee /tmp/release-lint.txt
grep -q "Parse error\|Fatal" /tmp/release-lint.txt && die "php lint failed"

# ---- 4. zip ------------------------------------------------------------
echo "==> zip"
mkdir -p "$SITE/.devin/tmp/release-zips"
ZIP="$SITE/.devin/tmp/release-zips/$SLUG.zip"
ZIP_IN_CONTAINER="/var/www/html/.devin/tmp/release-zips/$SLUG.zip"
(cd "$SITE/wp-content/plugins" && zip -qr "$ZIP" "$SLUG" -x "*.DS_Store" "$SLUG/vendor/*" "$SLUG/tests/*" "$SLUG/composer.json" "$SLUG/composer.lock" "$SLUG/phpunit.xml*" "$SLUG/.gitignore" "$SLUG/.phpunit*")
ls -la "$ZIP" | awk '{print $5, $9}'
# keep the top-level zip in the suite repo(s) fresh
cp "$ZIP" "$SRC_REPO/$SLUG.zip"
if [ "$BUNDLE" = "1" ]; then cp "$ZIP" "$PRO_REPO/$SLUG.zip"; fi

# ---- 5. attach dev store ----------------------------------------------
if [ -n "$PID" ]; then
  echo "==> attach dev store (product $PID)"
  META_EXTRA=""
  [ -n "$TESTED" ]    && META_EXTRA="$META_EXTRA update_post_meta(\$pid, \"_vk_license_tested\", \"$TESTED\");"
  [ -n "$CHANGELOG" ] && META_EXTRA="$META_EXTRA update_post_meta(\$pid, \"_vk_license_changelog\", \"$CHANGELOG\");"
  if [ "$SLUG" = "vendokit-divi" ] || [ "$SLUG" = "vendokit-support" ]; then
    # satellite: own version meta (_vk_license_version_<slug>), NOT the
    # product-level _vk_license_version (that's vendokit's version).
    (cd "$SITE" && ddev wp eval "
\$pid=$PID; \$row = Vendokit_Downloads::attach(\$pid, \"$ZIP_IN_CONTAINER\", [\"title\"=>\"$SLUG\",\"file_name\"=>\"$SLUG.zip\"]);
if (is_wp_error(\$row)) { fwrite(STDERR, \$row->get_error_message()); exit(1); }
update_post_meta(\$pid, \"_vk_license_version_$SLUG\", \"$VERSION\");$META_EXTRA
echo \"dev: satellite dl {\$row[\"id\"]} ver $VERSION\n\";")
  else
    (cd "$SITE" && ddev wp eval "
\$pid=$PID; \$row = Vendokit_Downloads::attach(\$pid, \"$ZIP_IN_CONTAINER\", [\"title\"=>\"$SLUG\",\"file_name\"=>\"$SLUG.zip\"]);
if (is_wp_error(\$row)) { fwrite(STDERR, \$row->get_error_message()); exit(1); }
update_post_meta(\$pid, \"_vk_license_update_download_id\", (int)\$row[\"id\"]);
update_post_meta(\$pid, \"_vk_license_version\", \"$VERSION\");
update_post_meta(\$pid, \"_vk_license_enabled\", \"yes\");$META_EXTRA
echo \"dev: dl {\$row[\"id\"]} ver $VERSION\n\";")
  fi
else
  echo "==> no store product for $SLUG — skipping dev-store attach"
fi

# ---- 6. attach staging -------------------------------------------------
if [ "$STAGING" = "1" ] && [ -n "$PID" ]; then
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

# ---- 7. commit+push ----------------------------------------------------
if [ "$PUSH" = "1" ]; then
  echo "==> commit + push"
  REPOS=("$SRC_REPO")
  if [ "$BUNDLE" = "1" ]; then REPOS+=("$PRO_REPO"); fi
  for repo in "${REPOS[@]}"; do
    (cd "$repo" && git add "plugins/$SLUG" "$SLUG.zip" && { git commit -m "$SLUG $VERSION" || true; } && git push)
  done
  (cd "$SITE" && git add "$PDIR" && { git commit -m "$SLUG $VERSION release" || echo "  (site repo: nothing to commit)"; } && git push)
fi

cat <<EOF

done. verify:
  curl -sk -X POST 'https://diviskit-shop.ddev.site/vendokit-license/get_license_version' \
    -d 'item=$SLUG&current_version=0.0.1' | python3 -m json.tool | grep -E 'new_version|package'
  # client site: clear dklc_update_$SLUG + update_plugins transients → Dashboard → Updates
EOF
