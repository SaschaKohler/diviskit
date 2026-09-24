#!/usr/bin/env bash
#
# update_skills.sh — Fetch Divi schema dump and regenerate DiviSkit skill references.
#
# Usage:
#   ./update_skills.sh [project_dir]
#
# Without an argument the script searches upward from the current directory
# for .devin/mcp_config.local.json — run it from anywhere inside the project.
# The config may name the server "diviskit-mcp" or "diviops-mcp" (both are
# tried); as a last resort any server entry with a WP_URL env is used.
#
# What it does:
#   1. Reads WP_URL, WP_USER, WP_APP_PASSWORD from .devin/mcp_config.local.json
#   2. Fetches schema dump via REST API (/wp-json/diviskit/v1/schema/module/dump-all)
#   3. Runs gen_all.py to regenerate:
#      - references/module-formats.md (combined Tier 3 reference)
#      - references/modules/divi_*.md (individual module files)
#      - references/modules/index.md (module index)
#      - references/modules/schema-raw.json (raw schema)
#   4. Updates divi-version in SKILL.md frontmatter and body text
#   5. Reports what changed
#
set -euo pipefail

# ── Resolve paths ───────────────────────────────────────────────────

if [[ $# -ge 1 ]]; then
    PROJECT_DIR="$1"
else
    PROJECT_DIR="$(pwd)"
    while [[ ! -f "$PROJECT_DIR/.devin/mcp_config.local.json" && "$PROJECT_DIR" != "/" ]]; do
        PROJECT_DIR="$(dirname "$PROJECT_DIR")"
    done
fi
MCP_CONFIG="$PROJECT_DIR/.devin/mcp_config.local.json"

# Script directory (where gen_all.py lives)
SCRIPT_DIR="$(cd "$(dirname "$0")" && pwd)"
SKILL_DIR="$(cd "$SCRIPT_DIR/../.." && pwd)"

# ── Check prerequisites ─────────────────────────────────────────────

if [[ ! -f "$MCP_CONFIG" ]]; then
    echo "ERROR: no .devin/mcp_config.local.json found (searched upward from $(pwd))."
    echo "Pass the project directory as argument: ./update_skills.sh /path/to/ddev-project"
    exit 1
fi

if ! command -v python3 &>/dev/null; then
    echo "ERROR: python3 is required."
    exit 1
fi

if ! command -v curl &>/dev/null; then
    echo "ERROR: curl is required."
    exit 1
fi

# ── Read credentials from MCP config ────────────────────────────────

# Emits shell-quoted assignments; tries diviskit-mcp, then diviops-mcp,
# then any server entry carrying a WP_URL env.
eval "$(python3 - "$MCP_CONFIG" <<'PY'
import json, shlex, sys

with open(sys.argv[1]) as f:
    cfg = json.load(f)
servers = cfg.get('mcpServers', {})

env = {}
for key in ('diviskit-mcp', 'diviops-mcp'):
    cand = servers.get(key, {}).get('env', {})
    if cand.get('WP_URL'):
        env = cand
        break
else:
    for srv in servers.values():
        cand = srv.get('env', {})
        if cand.get('WP_URL'):
            env = cand
            break

print('WP_URL=' + shlex.quote(env.get('WP_URL', '')))
print('WP_USER=' + shlex.quote(env.get('WP_USER', '')))
print('WP_APP_PASSWORD=' + shlex.quote(env.get('WP_APP_PASSWORD', '')))
PY
)"

if [[ -z "$WP_URL" || -z "$WP_USER" || -z "$WP_APP_PASSWORD" ]]; then
    echo "ERROR: Could not read WP_URL, WP_USER, or WP_APP_PASSWORD from $MCP_CONFIG"
    exit 1
fi

echo "Site: $WP_URL"
echo "User: $WP_USER"
echo ""

# ── Fetch schema dump ───────────────────────────────────────────────

echo "Fetching schema dump..."
DUMP_FILE=$(mktemp -t divi-schema-dump).json

HTTP_CODE=$(curl -s -k -w "%{http_code}" -o "$DUMP_FILE" \
    "${WP_URL}/wp-json/diviskit/v1/schema/module/dump-all" \
    -u "${WP_USER}:${WP_APP_PASSWORD}")

if [[ "$HTTP_CODE" != "200" ]]; then
    echo "ERROR: HTTP $HTTP_CODE from REST API"
    echo "Response: $(cat "$DUMP_FILE" | head -200)"
    rm -f "$DUMP_FILE"
    exit 1
fi

# Verify the dump is valid
OK=$(python3 -c "
import json
with open('$DUMP_FILE') as f:
    d = json.load(f)
print(d.get('ok', False))
")

if [[ "$OK" != "True" ]]; then
    echo "ERROR: Schema dump response not ok"
    cat "$DUMP_FILE" | head -200
    rm -f "$DUMP_FILE"
    exit 1
fi

# Extract version info
NEW_DIVI_VERSION=$(python3 -c "
import json
with open('$DUMP_FILE') as f:
    d = json.load(f)
print(d['data']['divi_version'])
")

NEW_SCHEMA_VERSION=$(python3 -c "
import json
with open('$DUMP_FILE') as f:
    d = json.load(f)
print(d['data']['schema_version'])
")

MODULE_COUNT=$(python3 -c "
import json
with open('$DUMP_FILE') as f:
    d = json.load(f)
print(len(d['data']['modules']))
")

echo "  Divi version: $NEW_DIVI_VERSION"
echo "  Schema version: $NEW_SCHEMA_VERSION"
echo "  Modules: $MODULE_COUNT"
echo ""

# ── Run generator ───────────────────────────────────────────────────

echo "Running gen_all.py..."
python3 "$SCRIPT_DIR/gen_all.py" "$DUMP_FILE" "$SCRIPT_DIR"
echo ""

# ── Update version references in SKILL.md ───────────────────────────

SKILL_MD="$SKILL_DIR/SKILL.md"

if [[ -f "$SKILL_MD" ]]; then
    echo "Updating diviskit-builder SKILL.md..."
    RESULT=$(python3 "$SCRIPT_DIR/update_version.py" "$SKILL_MD" "$NEW_DIVI_VERSION" 2>&1)
    echo "  $RESULT"
fi

# ── Cleanup ─────────────────────────────────────────────────────────

rm -f "$DUMP_FILE"

echo ""
echo "Done. DiviSkit skill references updated to Divi $NEW_DIVI_VERSION."
echo "Tip: re-run after every Divi theme update — the schema fingerprint in"
echo "wp-admin → Diviskit Agent → Schema dump changes when the schema does."
