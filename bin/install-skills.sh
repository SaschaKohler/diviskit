#!/usr/bin/env bash
# install-skills.sh — sync Diviskit authoring skills from the site's REST
# endpoint into an AI client's skills directory.
#
# The skills ship inside the WordPress plugins (canonical source) and are
# served at /wp-json/diviskit/v1/skills — the synced copy is always
# version-locked to the installed plugin surface. Re-run after every
# plugin update.
#
# Claude Code users may instead install via the marketplace bundle:
#   claude plugin marketplace add /path/to/diviskit-pro-suite
#   claude plugin install diviskit-agent@diviskit
#   claude plugin install diviskit-pro@diviskit
set -euo pipefail

CLIENT=""
SCOPE="user"
CONFIG=""

usage() {
  cat <<'EOF'
Usage: install-skills.sh --client <devin|codex> [--scope user|project] [--config <path>]

  --client   devin | codex          (claude: use the marketplace, see header)
  --scope    user    → ~/.config/devin/skills/ or ~/.codex/skills/   (default)
             project → ./.devin/skills/ or ./.codex/skills/ (run from project root)
  --config   path to a Devin mcp_config.local.json (default: search
             upward from $PWD for .devin/mcp_config.local.json)

Credentials are read from the MCP config's "diviskit-mcp" server entry
(WP_URL / WP_USER / WP_APP_PASSWORD), or from the same-named environment
variables if set. The site decides which bundles are served — sites with
diviskit-pro installed also return the Pro skills; no --pro flag needed.

Examples:
  ./bin/install-skills.sh --client devin --scope user
  ./bin/install-skills.sh --client devin --scope project
  ./bin/install-skills.sh --client codex --scope user
EOF
}

while [ $# -gt 0 ]; do
  case "$1" in
    --client) CLIENT="${2:-}"; shift 2 ;;
    --scope)  SCOPE="${2:-}";  shift 2 ;;
    --config) CONFIG="${2:-}"; shift 2 ;;
    --pro)    echo "Note: --pro is deprecated — the site's /skills endpoint already"; \
              echo "      gates Pro bundles on the installed plugin surface."; shift ;;
    -h|--help) usage; exit 0 ;;
    *) echo "Unknown argument: $1" >&2; usage; exit 2 ;;
  esac
done

if [ "$CLIENT" = "claude" ]; then
  echo "Claude Code installs skills via the marketplace — no copying needed:"
  echo "  claude plugin marketplace add <suite-dir>"
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

if ! command -v python3 &>/dev/null; then
  echo "ERROR: python3 is required." >&2
  exit 1
fi

# ── Resolve credentials ─────────────────────────────────────────────
# Explicit env vars win; otherwise read the diviskit-mcp server entry
# (or any server carrying a WP_URL) from .devin/mcp_config.local.json,
# searched upward from $PWD unless --config points at a file.

if [ -z "${WP_URL:-}" ] || [ -z "${WP_USER:-}" ] || [ -z "${WP_APP_PASSWORD:-}" ]; then
  if [ -z "$CONFIG" ]; then
    dir="$PWD"
    while [ ! -f "$dir/.devin/mcp_config.local.json" ] && [ "$dir" != "/" ]; do
      dir="$(dirname "$dir")"
    done
    [ -f "$dir/.devin/mcp_config.local.json" ] && CONFIG="$dir/.devin/mcp_config.local.json"
  fi

  if [ -z "$CONFIG" ] || [ ! -f "$CONFIG" ]; then
    echo "ERROR: no credentials." >&2
    echo "Set WP_URL / WP_USER / WP_APP_PASSWORD, or run from a project" >&2
    echo "containing .devin/mcp_config.local.json (or pass --config)." >&2
    exit 1
  fi

  CREDS="$(python3 - "$CONFIG" <<'PY'
import json, shlex, sys
with open(sys.argv[1]) as f:
    cfg = json.load(f)
servers = cfg.get('mcpServers', {})
env = servers.get('diviskit-mcp', {}).get('env', {})
if not env.get('WP_URL'):
    for srv in servers.values():
        cand = srv.get('env', {})
        if cand.get('WP_URL'):
            env = cand
            break
for k in ('WP_URL', 'WP_USER', 'WP_APP_PASSWORD'):
    print(f"{k}={shlex.quote(str(env.get(k, '')))}")
PY
)"
  eval "$CREDS"
fi

if [ -z "${WP_URL:-}" ] || [ -z "${WP_USER:-}" ] || [ -z "${WP_APP_PASSWORD:-}" ]; then
  echo "ERROR: WP_URL / WP_USER / WP_APP_PASSWORD incomplete." >&2
  exit 1
fi

# ── Pull manifest + bundles ─────────────────────────────────────────

mkdir -p "$TARGET"

WP_URL="$WP_URL" WP_USER="$WP_USER" WP_APP_PASSWORD="$WP_APP_PASSWORD" \
TARGET="$TARGET" python3 <<'PY'
import base64, hashlib, json, os, ssl, sys, urllib.request

wp_url = os.environ['WP_URL'].rstrip('/')
target = os.environ['TARGET']

ctx = ssl.create_default_context()
ctx.check_hostname = False
ctx.verify_mode = ssl.CERT_NONE

auth = base64.b64encode(
    f"{os.environ['WP_USER']}:{os.environ['WP_APP_PASSWORD']}".encode()
).decode()

def get(path):
    req = urllib.request.Request(
        f"{wp_url}/wp-json/diviskit/v1{path}",
        headers={'Authorization': f'Basic {auth}'},
    )
    with urllib.request.urlopen(req, context=ctx) as r:
        body = json.loads(r.read())
    if not body.get('ok'):
        err = body.get('error', {})
        sys.exit(f"ERROR {err.get('code','?')}: {err.get('message','request failed')}")
    return body['data']

manifest = get('/skills')
names = [s['name'] for s in manifest.get('skills', [])]
if not names:
    sys.exit("No skills served by this site (plugin bundles missing?).")

print(f"plugin_version: {manifest.get('plugin_version','?')}")
for name in names:
    bundle = get(f'/skills/{name}')
    dest = os.path.join(target, name)
    seen = set()
    for f in bundle['files']:
        rel = f['path']
        if rel.startswith('/') or '..' in rel.split('/'):
            sys.exit(f"ERROR: unsafe path in bundle: {rel}")
        data = base64.b64decode(f['content'])
        if hashlib.sha256(data).hexdigest() != f['sha256']:
            sys.exit(f"ERROR: checksum mismatch for {name}/{rel}")
        path = os.path.join(dest, rel)
        os.makedirs(os.path.dirname(path), exist_ok=True)
        with open(path, 'wb') as fh:
            fh.write(data)
        os.chmod(path, f.get('mode', 0o644))
        seen.add(rel)
    # remove stale files no longer in the bundle
    for root, dirs, files in os.walk(dest):
        for fn in files:
            p = os.path.join(root, fn)
            if os.path.relpath(p, dest) not in seen:
                os.remove(p)
    print(f"installed {name}  ({bundle.get('bundle','?')} → {dest})")

print()
print("Done. Restart the client/session so it picks up the skills.")
PY
