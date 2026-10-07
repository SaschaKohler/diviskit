#!/usr/bin/env bash
# Regenerate POT files and compile .mo files for the suite plugins.
#
# Runs wp-cli inside the DDEV project whose docroot is SITE (default:
# ../diviskit-com). Syncs each plugin into wp-content/plugins first so the
# pot reflects the exact shipped code, then copies the generated
# languages/ artifacts back into this repo.
#
# Usage: bin/make-translations.sh [site-dir]
#
# Domains:
#   <slug>        — plugin's own domain, loaded JIT via Domain Path header
#   diviskit      — shared client-SDK domain (license/support client);
#                   each plugin registers its languages/ dir for it via
#                   load_plugin_textdomain()
set -euo pipefail

REPO="$(cd "$(dirname "$0")/.." && pwd)"
SITE="$(cd "${1:-$REPO/../diviskit-com}" && pwd)"

# slug => source dir in this repo (free suite). The Pro suite repo mirrors
# these via release.sh; run the same script there for diviskit-pro.
PLUGINS="diviskit-agent"
EXTRA_DOMAINS="diviskit"

for SLUG in $PLUGINS; do
  SRC="$REPO/plugins/$SLUG"
  DST="$SITE/wp-content/plugins/$SLUG"
  echo "==> sync $SLUG -> $SITE"
  rsync -a --delete --exclude '.DS_Store' "$SRC/" "$DST/"
  mkdir -p "$DST/languages"

  echo "==> pot $SLUG"
  (cd "$SITE" && ddev wp i18n make-pot "wp-content/plugins/$SLUG" \
    "wp-content/plugins/$SLUG/languages/$SLUG.pot" --domain="$SLUG")

  for D in $EXTRA_DOMAINS; do
    echo "==> pot $D ($SLUG)"
    (cd "$SITE" && ddev wp i18n make-pot "wp-content/plugins/$SLUG" \
      "wp-content/plugins/$SLUG/languages/$D.pot" --domain="$D")
  done

  echo "==> mo $SLUG"
  (cd "$SITE" && ddev wp i18n make-mo "wp-content/plugins/$SLUG/languages/")

  echo "==> artifacts -> repo"
  cp "$DST/languages/"*.pot "$SRC/languages/" 2>/dev/null || true
  cp "$DST/languages/"*.mo  "$SRC/languages/" 2>/dev/null || true
done

echo "Done. Review languages/ in $REPO/plugins/*/ before committing."
