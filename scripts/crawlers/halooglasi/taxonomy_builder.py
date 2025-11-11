import argparse
import csv
import json
import os
import re
from collections import defaultdict
from typing import Dict, List, Tuple

OUTPUT_DIR = os.path.join("scripts", "crawlers", "halooglasi", "output")
DEFAULT_URLS_CSV = os.path.join(OUTPUT_DIR, "urls.csv")
DEFAULT_RULES = os.path.join("scripts", "crawlers", "halooglasi", "taxonomy_rules.json")


def load_rules(path: str) -> Tuple[int, List[Tuple[re.Pattern, str]]]:
    with open(path, "r", encoding="utf-8") as f:
        data = json.load(f)
    max_depth = int(data.get("max_depth", 3))
    patterns = []
    for item in data.get("mappings", []):
        pat = re.compile(item["pattern"], re.IGNORECASE)
        patterns.append((pat, item["category"]))
    return max_depth, patterns


def match_category(path: str, patterns: List[Tuple[re.Pattern, str]]) -> str:
    for pat, cat in patterns:
        if pat.search(path):
            return cat
    # fallback: derive from first path segment(s)
    parts = [p for p in path.split("/") if p]
    return parts[0] if parts else "/"


def build_tree(assignments: List[Tuple[str, str]], max_depth: int) -> Dict:
    tree = {"/": {}}
    counts = {"/": 0}
    for url, cat in assignments:
        counts["/"] = counts.get("/", 0) + 1
        node = tree["/"]
        segments = [s for s in cat.split("/") if s]
        if max_depth > 0:
            segments = segments[:max_depth]
        path_so_far = []
        for seg in segments:
            path_so_far.append(seg)
            if seg not in node:
                node[seg] = {"_count": 0}
            node[seg]["_count"] = node[seg].get("_count", 0) + 1
            node = node[seg]
    return {"/": tree["/"]}


def main():
    ap = argparse.ArgumentParser(description="Build taxonomy from urls.csv using mapping rules")
    ap.add_argument("--urls", default=DEFAULT_URLS_CSV, help="Path to urls.csv")
    ap.add_argument("--rules", default=DEFAULT_RULES, help="Path to taxonomy_rules.json")
    args = ap.parse_args()

    max_depth, patterns = load_rules(args.rules)

    assignments: List[Tuple[str, str]] = []
    with open(args.urls, "r", encoding="utf-8") as f:
        reader = csv.DictReader(f)
        for row in reader:
            url = row.get("url", "").strip()
            path = row.get("path", "").strip() or "/"
            cat = match_category(path, patterns)
            assignments.append((url, cat))

    # Write URLs by category
    os.makedirs(OUTPUT_DIR, exist_ok=True)
    by_cat_path = os.path.join(OUTPUT_DIR, "urls_by_category.jsonl")
    with open(by_cat_path, "w", encoding="utf-8") as f:
        for url, cat in assignments:
            f.write(json.dumps({"url": url, "category": cat}, ensure_ascii=False) + "\n")

    # Build and write taxonomy tree
    tree = build_tree(assignments, max_depth=max_depth)
    tax_path = os.path.join(OUTPUT_DIR, "taxonomy.json")
    with open(tax_path, "w", encoding="utf-8") as f:
        json.dump(tree, f, ensure_ascii=False, indent=2)

    print(f"[ok] Wrote {by_cat_path} and {tax_path}")


if __name__ == "__main__":
    main()
