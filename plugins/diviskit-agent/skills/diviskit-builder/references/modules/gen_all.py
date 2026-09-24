#!/usr/bin/env python3
"""
gen_all.py — Comprehensive DiviSkit module reference generator.

Reads a schema dump (raw REST API response or MCP overflow file) and generates:
  1. module-formats.md   — combined Tier 3 reference (elements, innerContent, surprises)
  2. divi_*.md           — individual module files (element map, decoration paths, CSS selectors)
  3. index.md            — module index with links
  4. schema-raw.json      — raw schema for future re-generation

Usage:
  python3 gen_all.py <schema_dump_file> [output_dir]

The schema dump file can be:
  - Raw REST API response from /wp-json/diviskit/v1/schema/module/dump-all
  - MCP overflow file (list of {text: ...} objects)
  - Already-parsed {ok: true, data: {...}} envelope
  - Bare {schema_version, divi_version, modules} data object
"""
import json
import sys
import os
from collections import defaultdict

# ── Parse input ─────────────────────────────────────────────────────

input_file = sys.argv[1] if len(sys.argv) > 1 else None
output_dir = sys.argv[2] if len(sys.argv) > 2 else os.path.dirname(os.path.abspath(__file__))

if not input_file:
    print("Usage: python3 gen_all.py <schema_dump_file> [output_dir]", file=sys.stderr)
    sys.exit(1)

with open(input_file, "r") as f:
    raw = f.read()

# Try multiple parse strategies (REST API, MCP overflow, bare data)
try:
    parsed = json.loads(raw)
    if isinstance(parsed, list) and len(parsed) > 0 and isinstance(parsed[0], dict) and "text" in parsed[0]:
        data = json.loads(parsed[0]["text"])
    elif isinstance(parsed, dict) and "ok" in parsed:
        data = parsed
    elif isinstance(parsed, dict) and "modules" in parsed:
        data = {"ok": True, "data": parsed}
    else:
        data = parsed
except json.JSONDecodeError:
    idx = raw.find('{"ok"')
    if idx >= 0:
        data = json.loads(raw[idx:])
    else:
        raise

if not data.get("ok"):
    print("ERROR: response not ok", file=sys.stderr)
    sys.exit(1)

schema_version = data["data"]["schema_version"]
divi_version = data["data"]["divi_version"]
modules = data["data"]["modules"]

# Save raw JSON
raw_json_path = os.path.join(output_dir, "schema-raw.json")
with open(raw_json_path, "w") as f:
    json.dump(data["data"], f, indent=2, ensure_ascii=False)

# ── Constants ───────────────────────────────────────────────────────

STANDARD_DECORATION = {
    "animation", "attributes", "background", "border", "boxShadow",
    "conditions", "disabledOn", "filters", "interactions", "layout",
    "overflow", "order", "position", "scroll", "sizing", "spacing",
    "sticky", "transform", "transition", "zIndex"
}

STANDARD_TOPLEVEL = {"lock", "metadata", "className", "style", "builderVersion", "modulePreset", "adminLabel"}

FONT_FAMILY_A = {"bodyFont"}

INTERNAL_KEYS = {"settings", "component", "props", "fields", "items", "styleProps",
                 "propertySelectors", "desktop", "value", "item", "groupType",
                 "groupSlug", "attrName", "subName", "groupLabel", "fieldLabel",
                 "dynamicSubgroupHost", "dynamicSubgroupHostLayoutStyle", "grouped",
                 "render", "priority", "name", "type", "selector", "description",
                 "features", "category", "groups", "defaultGroupAttr", "label"}


# ── Helpers ──────────────────────────────────────────────────────────

def clean_path(path):
    """Strip internal schema path components."""
    parts = path.split(".")
    cleaned = []
    for p in parts:
        if not cleaned or cleaned[-1] != p:
            cleaned.append(p)
    skip = INTERNAL_KEYS
    result = [p for p in cleaned if p not in skip]
    return ".".join(result)


def get_all_elements(schema):
    """Get ALL elements (including standard toplevel) for individual module files."""
    attrs = schema.get("attributes", {})
    elements = []
    for key, val in attrs.items():
        if not isinstance(val, dict):
            continue
        if val.get("type") or val.get("selector") or val.get("settings"):
            elements.append(key)
    return sorted(elements)


def get_elements(schema):
    """Get non-standard elements for combined module-formats.md."""
    attrs = schema.get("attributes", {})
    elements = []
    for key, val in attrs.items():
        if key in STANDARD_TOPLEVEL:
            continue
        if not isinstance(val, dict):
            continue
        if val.get("type") or val.get("selector") or val.get("settings"):
            elements.append(key)
    return sorted(elements)


def get_inner_content(schema):
    """Find all innerContent entries. Returns list of {element, path, label, preset}.
    Only one entry per element (uses first group for into-multiple-groups format)."""
    attrs = schema.get("attributes", {})
    results = []
    seen_elements = set()

    def search(obj, path="", element=""):
        if not isinstance(obj, dict):
            return
        if "innerContent" in obj and isinstance(obj["innerContent"], dict) and element not in seen_elements:
            ic = obj["innerContent"]
            seen_elements.add(element)
            # Handle group-item format
            if isinstance(ic.get("item"), dict):
                item = ic["item"]
                results.append({
                    "element": element,
                    "path": f"{element}.{element}.settings",
                    "label": item.get("label", f"{element}.{element}.settings"),
                    "preset": item.get("features", {}).get("preset", "") or "",
                })
            # Handle into-multiple-groups format (use first group)
            elif isinstance(ic.get("groups"), dict):
                for gk, gv in ic["groups"].items():
                    if isinstance(gv, dict) and isinstance(gv.get("item"), dict):
                        item = gv["item"]
                        results.append({
                            "element": element,
                            "path": f"{element}.{element}.settings",
                            "label": item.get("label", f"{element}.{element}.settings"),
                            "preset": item.get("features", {}).get("preset", "") or "",
                        })
                        break  # Only first group
            else:
                results.append({
                    "element": element,
                    "path": f"{element}.{element}.settings",
                    "label": f"{element}.{element}.settings",
                    "preset": "",
                })
        for key, val in obj.items():
            if isinstance(val, dict):
                search(val, f"{path}.{key}" if path else key, element or key)
            elif isinstance(val, list):
                for item in val:
                    if isinstance(item, dict):
                        search(item, f"{path}.{key}" if path else key, element)

    for attr_key, attr_val in attrs.items():
        if attr_key in STANDARD_TOPLEVEL:
            continue
        search({attr_key: attr_val}, attr_key, attr_key)
    return results


def get_decoration_paths(element_name, element_schema):
    """Extract all decoration paths for an element.

    Pattern: key → add path. If key has component with props.fields, add sub-paths.
    If key has item with component with props.fields, add item sub-paths + field sub-paths.
    """
    paths = []
    settings = element_schema.get("settings", {})
    if not isinstance(settings, dict):
        return paths
    decoration = settings.get("decoration", {})
    if not isinstance(decoration, dict):
        return paths

    def extract_fields(obj, base_path):
        """Extract field names from component.props.fields if present."""
        if not isinstance(obj, dict):
            return
        # Direct component with fields
        if "component" in obj and isinstance(obj["component"], dict):
            props = obj["component"].get("props", {})
            fields = props.get("fields", {})
            if isinstance(fields, dict):
                for field_key in sorted(fields.keys()):
                    paths.append(f"{base_path}.{field_key}")
        # Via item → component with fields
        if "item" in obj and isinstance(obj["item"], dict):
            item = obj["item"]
            if "component" in item and isinstance(item["component"], dict):
                props = item["component"].get("props", {})
                fields = props.get("fields", {})
                if isinstance(fields, dict) and fields:
                    paths.append(f"{base_path}.item")
                    for field_key in sorted(fields.keys()):
                        paths.append(f"{base_path}.item.{field_key}")

    for key, val in sorted(decoration.items()):
        path = f"{element_name}.settings.decoration.{key}"
        paths.append(path)
        if isinstance(val, dict):
            extract_fields(val, path)
        # Also check if val has nested component via item
        elif isinstance(val, list):
            pass  # Simple decoration, no sub-paths

    return sorted(paths)


def get_element_type(element_schema):
    return element_schema.get("type", "unknown")


def get_css_selectors_all(schema):
    """Get CSS selectors for ALL elements (for individual module files)."""
    attrs = schema.get("attributes", {})
    selectors = []
    for elem_name, elem_val in attrs.items():
        if not isinstance(elem_val, dict):
            continue
        sel = elem_val.get("selector", "")
        if sel:
            selectors.append((elem_name, sel))
    return selectors


def get_css_selectors(schema):
    """Get non-standard CSS selectors (for combined module-formats.md)."""
    attrs = schema.get("attributes", {})
    selectors = []
    for elem_name, elem_val in attrs.items():
        if not isinstance(elem_val, dict):
            continue
        if elem_name in STANDARD_TOPLEVEL:
            continue
        sel = elem_val.get("selector", "")
        if sel and sel != "{{selector}}":
            selectors.append((elem_name, sel))
    return selectors


def detect_surprises(name, schema):
    attrs = schema.get("attributes", {})
    surprises = []

    module_attrs = attrs.get("module", {})
    module_settings = module_attrs.get("settings", {}) if isinstance(module_attrs, dict) else {}
    module_advanced = module_settings.get("advanced", {}) if isinstance(module_settings, dict) else {}
    module_decoration = module_settings.get("decoration", {}) if isinstance(module_settings, dict) else {}

    if isinstance(module_advanced, dict):
        if "sizing" in module_advanced and "sizing" not in module_decoration:
            surprises.append(f"sizing/spacing on `module.advanced.{{sizing, spacing}}` — NOT `module.decoration`")
        if "align" in module_advanced:
            surprises.append(f"Alignment: `module.advanced.align.desktop.value`")
        if "lightbox" in module_advanced:
            surprises.append(f"Lightbox: `module.advanced.lightbox.desktop.value: \"on\"`")

    for elem_name, elem_val in attrs.items():
        if elem_name in STANDARD_TOPLEVEL or elem_name == "module":
            continue
        if not isinstance(elem_val, dict):
            continue
        elem_settings = elem_val.get("settings", {})
        if not isinstance(elem_settings, dict):
            continue

        elem_advanced = elem_settings.get("advanced", {})
        if isinstance(elem_advanced, dict):
            for adv_key in elem_advanced:
                if adv_key not in ("link",):
                    surprises.append(f"`{elem_name}.advanced.{adv_key}` — element-specific advanced field")

        elem_decoration = elem_settings.get("decoration", {})
        if isinstance(elem_decoration, dict):
            for dec_key in elem_decoration:
                if dec_key in FONT_FAMILY_A:
                    surprises.append(f"Font Family A (bodyFont) on `{elem_name}`")

            available = set(elem_decoration.keys())
            standard_available = available & STANDARD_DECORATION
            if standard_available and len(standard_available) < 4:
                dec_list = ", ".join(sorted(standard_available))
                surprises.append(f"`{elem_name}.decoration` limited to: {dec_list}")

    # VB-hidden fields
    def find_hidden(obj, path="", element=""):
        if not isinstance(obj, dict):
            return
        if obj.get("render") is False and "component" not in obj and "props" not in obj and "fields" not in obj:
            cleaned = clean_path(path)
            if cleaned and "." in cleaned and not cleaned.startswith("module."):
                surprises.append(f"VB-hidden: `{cleaned}` (functional via block JSON)")
        for key, val in obj.items():
            if isinstance(val, dict):
                find_hidden(val, f"{path}.{key}" if path else key, element)

    for attr_key, attr_val in attrs.items():
        if attr_key == "module":
            continue
        find_hidden({attr_key: attr_val}, attr_key, attr_key)

    # CSS !important
    important_paths = set()

    def find_important(obj, path="", element=""):
        if not isinstance(obj, dict):
            return
        if "important" in obj:
            imp = obj["important"]
            if isinstance(imp, bool) and imp:
                cleaned = clean_path(path)
                if cleaned:
                    important_paths.add(cleaned)
            elif isinstance(imp, dict):
                def check_imp(imp_obj, imp_path=""):
                    if isinstance(imp_obj, bool) and imp_obj:
                        cleaned = clean_path(f"{path}.{imp_path}")
                        if cleaned:
                            important_paths.add(cleaned)
                    elif isinstance(imp_obj, dict):
                        for k, v in imp_obj.items():
                            check_imp(v, f"{imp_path}.{k}" if imp_path else k)
                check_imp(imp)
        for key, val in obj.items():
            if isinstance(val, dict):
                find_important(val, f"{path}.{key}" if path else key, element)

    for attr_key, attr_val in attrs.items():
        find_important({attr_key: attr_val}, attr_key, attr_key)

    by_element = defaultdict(set)
    for p in important_paths:
        elem = p.split(".")[0]
        rest = ".".join(p.split(".")[1:]) if "." in p else p
        by_element[elem].add(rest)

    for elem, paths in sorted(by_element.items()):
        if len(paths) <= 2:
            for p in sorted(paths):
                surprises.append(f"CSS `!important` on `{elem}.{p}`")
        else:
            surprises.append(f"CSS `!important` on `{elem}`: {', '.join(sorted(paths))}")

    # Deduplicate
    seen = set()
    unique = []
    for text in surprises:
        if text not in seen:
            seen.add(text)
            unique.append(text)
    return unique


# ── Generate combined module-formats.md ────────────────────────────

def gen_combined_md():
    categories = defaultdict(list)
    for name, schema in modules.items():
        cat = schema.get("category", "unknown")
        categories[cat].append((name, schema))

    category_order = ["structure", "module", "fullwidth-module", "child-module", "unsupported", ""]
    category_labels = {
        "structure": "Structure Modules",
        "module": "Content Modules",
        "fullwidth-module": "Fullwidth Modules",
        "child-module": "Child Modules",
        "unsupported": "Unsupported",
        "": "Other",
    }

    lines = []
    lines.append("# DiviSkit Module Reference (Tier 3)")
    lines.append("")
    lines.append(f"Auto-generated from Divi {divi_version} schema dump.")
    lines.append(f"Schema version: `{schema_version}`")
    lines.append(f"Modules: {len(modules)}")
    lines.append("")
    lines.append("Each entry lists **elements**, **innerContent shapes**, and **surprises** only.")
    lines.append("Standard decoration (`{element}.decoration.*`) is assumed — NOT repeated here.")
    lines.append("Combine with Tier 1 (universal decoration) and Tier 2 (font/icon patterns) for full blocks.")
    lines.append("")
    lines.append("Generated via `diviskit_schema_get_module` with `mode: 'dump_all'`.")
    lines.append("Re-generate with: `python3 gen_all.py`")
    lines.append("")
    lines.append("---")
    lines.append("")

    for cat in category_order:
        if cat not in categories:
            continue
        cat_label = category_labels.get(cat, cat)
        cat_modules = sorted(categories[cat], key=lambda x: x[0])
        lines.append(f"## {cat_label} ({len(cat_modules)})")
        lines.append("")

        for name, schema in cat_modules:
            title = schema.get("title", name)
            elements = get_elements(schema)
            inner_contents = get_inner_content(schema)
            surprises = detect_surprises(name, schema)
            selectors = get_css_selectors(schema)

            lines.append(f"### {title}")
            lines.append(f"`{name}`")
            lines.append("")

            if elements:
                lines.append(f"**Elements**: {', '.join(f'`{e}`' for e in elements)}")
                lines.append("")

            if inner_contents:
                lines.append("| Element | innerContent | Preset |")
                lines.append("|---------|-------------|--------|")
                for ic in inner_contents:
                    elem = ic["element"]
                    label = ic.get("label", "") or elem
                    preset = ic.get("preset", "") or "—"
                    lines.append(f"| `{elem}` | {label} | {preset} |")
                lines.append("")

            if surprises:
                for text in surprises:
                    lines.append(f"- {text}")
                lines.append("")

            if selectors:
                lines.append("**CSS selectors**:")
                for elem, sel in selectors:
                    if len(sel) > 120:
                        sel = sel[:120] + "..."
                    lines.append(f"- `{elem}`: `{sel}`")
                lines.append("")

            lines.append("---")
            lines.append("")

    return "\n".join(lines)


# ── Generate individual module files ────────────────────────────────

def gen_module_file(name, schema):
    title = schema.get("title", name)
    category = schema.get("category", "unknown")
    attrs = schema.get("attributes", {})
    elements = get_all_elements(schema)
    inner_contents = get_inner_content(schema)
    selectors = get_css_selectors_all(schema)

    lines = []
    lines.append(f"# {title}")
    lines.append(f"`{name}`")
    lines.append("")
    lines.append(f"- Category: `{category}`")
    lines.append(f"- Divi version: {divi_version}")
    lines.append(f"- Schema version: `{schema_version}`")
    lines.append("")

    # innerContent
    if inner_contents:
        lines.append("## innerContent")
        lines.append("")
        lines.append("| Path | Label | Preset |")
        lines.append("|------|-------|--------|")
        for ic in inner_contents:
            path = ic["path"]
            label = ic.get("label", "") or path
            preset = ic.get("preset", "") or "—"
            lines.append(f"| `{path}` | {label} | {preset} |")
        lines.append("")

    # Element Map
    if elements:
        lines.append("## Element Map")
        lines.append("")
        lines.append("| Element | Type | Decoration Paths |")
        lines.append("|---------|------|-----------------|")

        # Collect decoration paths per element
        elem_dec_paths = {}
        for elem in elements:
            elem_schema = attrs.get(elem, {})
            dec_paths = get_decoration_paths(elem, elem_schema)
            elem_dec_paths[elem] = dec_paths

        for elem in sorted(elements):
            elem_schema = attrs.get(elem, {})
            elem_type = get_element_type(elem_schema)
            dec_paths = elem_dec_paths[elem]
            if dec_paths:
                if len(dec_paths) > 8:
                    shown = ", ".join(f"`{p}`" for p in dec_paths[:8])
                    remaining = len(dec_paths) - 8
                    dec_str = f"{shown} *(+{remaining} more)*"
                else:
                    dec_str = ", ".join(f"`{p}`" for p in dec_paths)
            else:
                dec_str = "—"
            lines.append(f"| `{elem}` | `{elem_type}` | {dec_str} |")
        lines.append("")

        # Per-element decoration path details
        for elem in sorted(elements):
            dec_paths = elem_dec_paths[elem]
            if dec_paths:
                lines.append(f"### `{elem}` decoration paths")
                lines.append("")
                for p in dec_paths:
                    lines.append(f"- `{p}`")
                lines.append("")

    # CSS Selectors
    if selectors:
        lines.append("## CSS Selectors")
        lines.append("")
        lines.append("| Element | Selector |")
        lines.append("|---------|----------|")
        for elem, sel in selectors:
            if len(sel) > 120:
                sel = sel[:120] + "..."
            lines.append(f"| `{elem}` | `{sel}` |")
        lines.append("")

    return "\n".join(lines)


# ── Generate index.md ───────────────────────────────────────────────

def gen_index_md():
    categories = defaultdict(list)
    for name, schema in modules.items():
        cat = schema.get("category", "unknown")
        categories[cat].append((name, schema))

    category_order = ["structure", "module", "fullwidth-module", "child-module", "unsupported", ""]
    category_labels = {
        "structure": "structure",
        "module": "module",
        "fullwidth-module": "fullwidth-module",
        "child-module": "child-module",
        "unsupported": "unsupported",
        "": "other",
    }

    lines = []
    lines.append("# DiviSkit Module Reference")
    lines.append("")
    lines.append(f"Auto-generated from Divi {divi_version} schema dump.")
    lines.append(f"Schema version: `{schema_version}`")
    lines.append(f"Generated: {len(modules)} modules")
    lines.append("")
    lines.append("This reference replaces the Diviskit Pro Tier 2+3 module maps.")
    lines.append("Generated via `diviskit_schema_get_module` with `mode: 'dump_all'`.")
    lines.append("")

    for cat in category_order:
        if cat not in categories:
            continue
        cat_label = category_labels.get(cat, cat)
        cat_modules = sorted(categories[cat], key=lambda x: x[0])
        lines.append(f"## {cat_label} ({len(cat_modules)})")
        lines.append("")
        lines.append("| Module | Title | Elements | innerContent |")
        lines.append("|--------|-------|----------|-------------|")
        for name, schema in cat_modules:
            title = schema.get("title", name)
            elements = get_all_elements(schema)
            ic = get_inner_content(schema)
            filename = name.replace("/", "_") + ".md"
            lines.append(f"| [`{name}`]({filename}) | {title} | {len(elements)} | {len(ic)} |")
        lines.append("")

    return "\n".join(lines)


# ── Main ────────────────────────────────────────────────────────────

# Generate combined module-formats.md (goes in parent of modules/ dir)
combined_path = os.path.join(os.path.dirname(os.path.normpath(output_dir)), "module-formats.md")
with open(combined_path, "w") as f:
    f.write(gen_combined_md())
print(f"Generated: {combined_path}")

# Generate individual module files
for name, schema in modules.items():
    filename = name.replace("/", "_") + ".md"
    filepath = os.path.join(output_dir, filename)
    with open(filepath, "w") as f:
        f.write(gen_module_file(name, schema))

# Generate index.md
index_path = os.path.join(output_dir, "index.md")
with open(index_path, "w") as f:
    f.write(gen_index_md())
print(f"Generated: {index_path}")

print(f"Raw JSON: {raw_json_path}")
print(f"Modules: {len(modules)}")
print(f"Divi version: {divi_version}")
print(f"Schema version: {schema_version}")
