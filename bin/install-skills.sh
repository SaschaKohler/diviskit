#!/usr/bin/env bash
# install-skills.sh — copy Diviskit skill bundles into an AI client's
# skills directory. For clients without a plugin marketplace (Codex, Devin,
# generic MCP clients). Claude Code users should use the marketplace instead:
#   claude plugin marketplace add /path/to/diviskit-pro-suite
#   claude plugin install diviskit-agent@diviskit
#   claude plugin install diviskit-pro@diviskit
set -euo pipefail

SUITE_ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
CLIENT=""
SCOPE="user"
PRO=0

usage() {
  cat <<'EOF'
Usage: install-skills.sh --client <devin|codex> [--scope user|project] [--pro]

  --client   devin | codex          (claude: use the marketplace, see header)
  --scope    user    → ~/.config/devin/skills/ or ~/.codex/skills/   (default)
             project → ./.devin/skills/ or ./.codex/skills/ (run from project root)
  --pro      also install the diviskit-pro bundle (diviskit-vendokit,
             diviskit-mega-menu). Requires the diviskit-pro WP plugin.

Examples:
  ./bin/install-skills.sh --client devin --scope user
  ./bin/install-skills.sh --client devin --scope project --pro
  ./bin/install-skills.sh --client codex --scope user --pro
EOF
}

while [ $# -gt 0 ]; do
  case "$1" in
    --client) CLIENT="${2:-}"; shift 2 ;;
    --scope)  SCOPE="${2:-}";  shift 2 ;;
    --pro)    PRO=1; shift ;;
    -h|--help) usage; exit 0 ;;
    *) echo "Unknown argument: $1" >&2; usage; exit 2 ;;
  esac
done

if [ "$CLIENT" = "claude" ]; then
  echo "Claude Code installs skills via the marketplace — no copying needed:"
  echo "  claude plugin marketplace add $SUITE_ROOT"
  echo "  claude plugin install diviskit-agent@diviskit"
  echo "  claude plugin install diviskit-pro@diviskit"
  exit 0
fi

case "$CLIENT" in
  devin) ;;
  codex) ;;
  *) echo "Missing or unsupported --client (devin|codex)." >&2; usage; exit 2 ;;
esac

case "$SCOPE" in
  user)
    if [ "$CLIENT" = "devin" ]; then
      TARGET="$HOME/.config/devin/skills"
    else
      TARGET="$HOME/.codex/skills"
    fi
    ;;
  project)
    if [ "$CLIENT" = "devin" ]; then
      TARGET="$PWD/.devin/skills"
    else
      TARGET="$PWD/.codex/skills"
    fi
    ;;
  *) echo "Unknown --scope (user|project)." >&2; usage; exit 2 ;;
esac

BUNDLES="diviskit-agent"
[ "$PRO" -eq 1 ] && BUNDLES="$BUNDLES diviskit-pro"

mkdir -p "$TARGET"
for bundle in $BUNDLES; do
  src="$SUITE_ROOT/claude/$bundle/skills"
  if [ ! -d "$src" ]; then
    echo "Bundle not found: $src" >&2
    exit 1
  fi
  for skill in "$src"/*/; do
    name="$(basename "$skill")"
    rm -rf "$TARGET/$name"
    cp -R "$skill" "$TARGET/$name"
    echo "installed $name  ($bundle → $TARGET)"
  done
done

echo
echo "Done. Restart the client/session so it picks up the skills."
[ "$PRO" -eq 0 ] && echo "Tip: rerun with --pro to add the diviskit-pro skills."
