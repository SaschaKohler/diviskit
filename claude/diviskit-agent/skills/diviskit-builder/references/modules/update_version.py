#!/usr/bin/env python3
"""
update_version.py — Update divi-version references in SKILL.md files.

Usage:
  python3 update_version.py <skill_md_path> <new_divi_version>
"""
import re
import sys

skill_md_path = sys.argv[1]
new_version = sys.argv[2]

with open(skill_md_path) as f:
    content = f.read()

# Extract old version
m = re.search(r'divi-version:\s*["\']?([^"\'\n]+)', content)
old_version = m.group(1).strip() if m else ""

if old_version == new_version:
    print(f"already at {new_version}")
    sys.exit(0)

if not old_version:
    print(f"WARNING: no divi-version found in {skill_md_path}")
    sys.exit(0)

# Update frontmatter divi-version
content = re.sub(
    r'(divi-version:\s*["\']?)([^"\'\n]+)',
    lambda m: m.group(1) + new_version,
    content,
)

# Update body text "Divi X.Y.Z schema"
content = re.sub(
    r'Divi [0-9]+\.[0-9]+\.[0-9]+ schema',
    f'Divi {new_version} schema',
    content,
)

with open(skill_md_path, 'w') as f:
    f.write(content)

print(f"{old_version} -> {new_version}")
