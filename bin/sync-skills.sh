#!/usr/bin/env bash
# sync-skills.sh — copy plugin-bundled skills into this suite's claude/
# marketplace bundles. The plugins' skills/ dirs are the canonical source;
# claude/ is a build artifact for the Claude Code marketplace.
#
# Usage: bin/sync-skills.sh <wp-content/plugins dir>
set -euo pipefail

SUITE_ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
PLUGINS_DIR="${1:?usage: sync-skills.sh <wp-content/plugins dir>}"

sync_bundle() {
  local plugin="$1" bundle="$2"
  local src="$PLUGINS_DIR/$plugin/skills"
  local dest="$SUITE_ROOT/claude/$bundle/skills"
  if [ ! -d "$src" ]; then
    echo "skip $plugin (no skills dir at $src)"
    return
  fi
  if [ ! -d "$SUITE_ROOT/claude/$bundle" ]; then
    echo "skip $bundle (not part of this suite)"
    return
  fi
  mkdir -p "$dest"
  if command -v rsync &>/dev/null; then
    rsync -a --delete "$src/" "$dest/"
  else
    rm -rf "$dest" && mkdir -p "$dest" && cp -R "$src/." "$dest/"
  fi
  echo "synced $plugin/skills → claude/$bundle/skills"
}

sync_bundle diviskit-agent diviskit-agent
sync_bundle diviskit-pro diviskit-pro
