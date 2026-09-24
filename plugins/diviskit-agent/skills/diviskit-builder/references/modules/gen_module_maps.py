#!/usr/bin/env python3
"""
Generate DiviSkit module-formats.md — Tier 3 module reference (v3).
Clean path formatting, reduced noise, authoring-relevant output.
"""
import json
import sys
import os
import re
from collections import defaultdict

overflow_path = sys.argv[1] if len(sys.argv) > 1 else "/var/folders/7f/cz70pt696q38cqpzdx7_0tdc0000gn/T/devin-overflows-501/d7b478f9/content.txt"
output_file = sys.argv[2] if len(sys.argv) > 2 else "/tmp/diviskit-module-maps/module-formats.md"
raw_json_output = sys.argv[3] if len(sys.argv) > 3 else "/tmp/diviskit-module-maps/schema-raw.json"

with open(overflow_path, "r") as f:
    raw = f.read()

try:
    parsed = json.loads(raw)
    if isinstance(parsed, list) and len(parsed) > 0 and isinstance(parsed[0], dict) and "text" in parsed[0]:
        data = json.loads(parsed[0]["text"])
    elif isinstance(parsed, dict) and "ok" in parsed:
        data = parsed
    else:
        data = parsed
except json.JSONDecodeError:
    idx = raw.find('{"ok"')
    if idx >= 0:
        data = json.loads(raw[idx:])
    else:
        raise

if not data.get("ok"):
    print("ERROR: MCP response not ok", file=sys.stderr)
    sys.exit(1)

schema_version = data["data"]["schema_version"]
divi_version = data["data"]["divi_version"]
modules = data["data"]["modules"]

# Save raw JSON for future re-generation
os.makedirs(os.path.dirname(raw_json_output), exist_ok=True)
with open(raw_json_output, "w") as f:
    json.dump(data["data"], f, indent=2, ensure_ascii=False)

# ── Helpers ────────────────────────────────────────────────────────

STANDARD_DECORATION = {
    "animation", "attributes", "background", "border", "boxShadow",
    "conditions", "disabledOn", "filters", "interactions", "layout",
    "overflow", "order", "position", "scroll", "sizing", "spacing",
    "sticky", "transform", "transition", "zIndex"
}

STANDARD_TOPLEVEL = {"lock", "metadata", "className", "style", "builderVersion", "modulePreset", "adminLabel"}

FONT_FAMILY_A = {"bodyFont"}


def clean_path(path):
    """Strip internal schema path components, keep only authoring-relevant parts."""
    # Remove duplicate element prefix (e.g., "button.button.settings..." → "button...")
    parts = path.split(".")
    # Remove consecutive duplicates
    cleaned = []
    for p in parts:
        if not cleaned or cleaned[-1] != p:
            cleaned.append(p)
    # Remove internal schema keys
    skip = {"settings", "component", "props", "fields", "items", "styleProps",
            "propertySelectors", "desktop", "value", "item", "groupType",
            "groupSlug", "attrName", "subName", "groupLabel", "fieldLabel",
            "dynamicSubgroupHost", "dynamicSubgroupHostLayoutStyle", "grouped"}
    result = [p for p in cleaned if p not in skip]
    return ".".join(result)


def get_elements(schema):
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
    attrs = schema.get("attributes", {})
    results = []

    def search(obj, path="", element=""):
        if not isinstance(obj, dict):
            return
        if "innerContent" in obj and isinstance(obj["innerContent"], dict):
            ic = obj["innerContent"]
            results.append({
                "element": element or path.split(".")[0],
                "label": ic.get("label", ""),
                "preset": ic.get("features", {}).get("preset", ""),
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


def detect_surprises(name, schema):
    attrs = schema.get("attributes", {})
    surprises = []

    module_attrs = attrs.get("module", {})
    module_settings = module_attrs.get("settings", {}) if isinstance(module_attrs, dict) else {}
    module_advanced = module_settings.get("advanced", {}) if isinstance(module_settings, dict) else {}
    module_decoration = module_settings.get("decoration", {}) if isinstance(module_settings, dict) else {}

    # Image exception
    if isinstance(module_advanced, dict):
        if "sizing" in module_advanced and "sizing" not in module_decoration:
            surprises.append(("exception", f"sizing/spacing on `module.advanced.{{sizing, spacing}}` — NOT `module.decoration`"))
        if "align" in module_advanced:
            surprises.append(("info", f"Alignment: `module.advanced.align.desktop.value`"))
        if "lightbox" in module_advanced:
            surprises.append(("info", f"Lightbox: `module.advanced.lightbox.desktop.value: \"on\"`"))

    # Element-level surprises
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
                    surprises.append(("info", f"`{elem_name}.advanced.{adv_key}` — element-specific advanced field"))

        elem_decoration = elem_decoration = elem_settings.get("decoration", {})
        if isinstance(elem_decoration, dict):
            for dec_key in elem_decoration:
                if dec_key in FONT_FAMILY_A:
                    surprises.append(("font", f"Font Family A (bodyFont) on `{elem_name}`"))

            # Limited decoration
            available = set(elem_decoration.keys())
            standard_available = available & STANDARD_DECORATION
            if standard_available and len(standard_available) < 4:
                dec_list = ", ".join(sorted(standard_available))
                surprises.append(("limit", f"`{elem_name}.decoration` limited to: {dec_list}"))

    # VB-hidden fields — clean paths
    def find_hidden(obj, path="", element=""):
        if not isinstance(obj, dict):
            return
        if obj.get("render") is False and "component" not in obj and "props" not in obj and "fields" not in obj:
            cleaned = clean_path(path)
            if cleaned and "." in cleaned and not cleaned.startswith("module."):
                surprises.append(("hidden", f"VB-hidden: `{cleaned}` (functional via block JSON)"))
        for key, val in obj.items():
            if isinstance(val, dict):
                find_hidden(val, f"{path}.{key}" if path else key, element)

    for attr_key, attr_val in attrs.items():
        if attr_key == "module":
            continue
        find_hidden({attr_key: attr_val}, attr_key, attr_key)

    # CSS !important — clean and deduplicate
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

    # Group important paths by element
    by_element = defaultdict(set)
    for p in important_paths:
        elem = p.split(".")[0]
        rest = ".".join(p.split(".")[1:]) if "." in p else p
        by_element[elem].add(rest)

    for elem, paths in sorted(by_element.items()):
        if len(paths) <= 2:
            for p in sorted(paths):
                surprises.append(("important", f"CSS `!important` on `{elem}.{p}`"))
        else:
            surprises.append(("important", f"CSS `!important` on `{elem}`: {', '.join(sorted(paths))}"))

    # Deduplicate by text
    seen = set()
    unique = []
    for kind, text in surprises:
        if text not in seen:
            seen.add(text)
            unique.append((kind, text))
    return unique


def get_css_selectors(schema):
    attrs = schema.get("attributes", {})
    selectors = []
    for elem_name, elem_val in attrs.items():
        if not isinstance(elem_val, dict):
            continue
        sel = elem_val.get("selector", "")
        if sel and elem_name not in STANDARD_TOPLEVEL and sel != "{{selector}}":
            selectors.append((elem_name, sel))
    return selectors


# ── Generate markdown ──────────────────────────────────────────────

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
lines.append("Re-generate with: `python3 gen_module_maps_v3.py`")
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
            # Group by kind
            for kind, text in surprises:
                lines.append(f"- {text}")
            lines.append("")

        if selectors:
            lines.append("**CSS selectors**:")
            for elem, sel in selectors:
                # Truncate very long selectors
                if len(sel) > 120:
                    sel = sel[:120] + "..."
                lines.append(f"- `{elem}`: `{sel}`")
            lines.append("")

        lines.append("---")
        lines.append("")

with open(output_file, "w") as f:
    f.write("\n".join(lines))

print(f"Generated {output_file}")
print(f"Raw JSON: {raw_json_output}")
print(f"Modules: {len(modules)}")
