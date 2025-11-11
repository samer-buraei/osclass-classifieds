import argparse
import json
import os
import sys
import time
from typing import Dict, List, Optional, Tuple
from urllib.parse import urlparse, urljoin

import requests
from bs4 import BeautifulSoup

OUTPUT_DIR = os.path.join("scripts", "crawlers", "halooglasi", "output")
DEFAULT_INPUT = os.path.join(OUTPUT_DIR, "urls.txt")
DEFAULT_RULES = os.path.join("scripts", "crawlers", "halooglasi", "ads_extract_rules.json")
DEFAULT_OUT = os.path.join(OUTPUT_DIR, "ads.jsonl")
DEFAULT_CHECKPOINT = os.path.join(OUTPUT_DIR, "ads_checkpoint.json")


def load_rules(path: str) -> Dict:
    with open(path, "r", encoding="utf-8") as f:
        return json.load(f)


def is_same_domain(url: str, domain: str) -> bool:
    return urlparse(url).netloc.endswith(domain)


def fetch(url: str, timeout: int = 20) -> Optional[str]:
    try:
        resp = requests.get(url, timeout=timeout, headers={
            "User-Agent": (
                "Mozilla/5.0 (Windows NT 10.0; Win64; x64) "
                "AppleWebKit/537.36 (KHTML, like Gecko) "
                "Chrome/122.0.0.0 Safari/537.36"
            )
        })
        if resp.status_code == 200 and "text/html" in resp.headers.get("Content-Type", "").lower():
            return resp.text
    except Exception:
        return None
    return None


def extract_fields(url: str, html: str, rules: Dict, fields: Optional[List[str]] = None) -> Dict:
    soup = BeautifulSoup(html, "html.parser")
    selectors: Dict[str, str] = rules.get("selectors", {})
    attributes: Dict[str, str] = rules.get("attributes", {})
    trim_keys: List[str] = rules.get("trim", [])
    max_images: int = int(rules.get("max_images", 10))

    data: Dict[str, object] = {"url": url}

    keys = fields if fields else list(selectors.keys())
    for key in keys:
        sel = selectors.get(key)
        if not sel:
            continue
        if key == "image_urls":
            attr = attributes.get(key, "src")
            imgs = []
            for img in soup.select(sel):
                val = img.get(attr) or img.get("data-src") or ""
                val = val.strip()
                if val:
                    full = urljoin(url, val)
                    imgs.append(full)
                if len(imgs) >= max_images:
                    break
            data[key] = imgs
        else:
            el = soup.select_one(sel)
            if el:
                text = el.get_text(" ", strip=True)
                data[key] = text

    for k in trim_keys:
        if k in data and isinstance(data[k], str):
            data[k] = data[k].strip()

    return data


def load_category_map(path: str) -> Dict[str, str]:
    """Load urls_by_category.jsonl into url->category map if present."""
    mapping: Dict[str, str] = {}
    if not os.path.exists(path):
        return mapping
    with open(path, "r", encoding="utf-8") as f:
        for line in f:
            try:
                obj = json.loads(line)
                mapping[obj["url"]] = obj.get("category", "")
            except Exception:
                continue
    return mapping


def save_checkpoint(path: str, state: Dict) -> None:
    try:
        with open(path, "w", encoding="utf-8") as f:
            json.dump(state, f, ensure_ascii=False)
    except Exception:
        pass


def load_checkpoint(path: str) -> Optional[Dict]:
    try:
        if not os.path.exists(path):
            return None
        with open(path, "r", encoding="utf-8") as f:
            return json.load(f)
    except Exception:
        return None


def main():
    ap = argparse.ArgumentParser(description="Configurable ads harvester")
    ap.add_argument("--input", default=DEFAULT_INPUT, help="Input file with one URL per line")
    ap.add_argument("--rules", default=DEFAULT_RULES, help="Extraction rules JSON")
    ap.add_argument("--output", default=DEFAULT_OUT, help="Output JSONL for extracted ads")
    ap.add_argument("--max-ads", type=int, default=200, help="Max ads to process")
    ap.add_argument("--delay-seconds", type=float, default=1.0, help="Delay between requests")
    ap.add_argument("--progress-every", type=int, default=25, help="Progress interval")
    ap.add_argument("--fields", nargs='*', default=None, help="Only extract these fields (keys in rules.selectors)")
    ap.add_argument("--include-images", action="store_true", help="Include image URLs in output (no download by default)")
    ap.add_argument("--download-images", action="store_true", help="Download images to disk (implies --include-images)")
    ap.add_argument("--image-dir", default=os.path.join(OUTPUT_DIR, "images"), help="Directory for downloaded images")
    ap.add_argument("--domain", default="halooglasi.com", help="Restrict to this domain")
    ap.add_argument("--category-file", default=os.path.join(OUTPUT_DIR, "urls_by_category.jsonl"), help="Optional category mapping file")
    ap.add_argument("--category", nargs='*', default=None, help="Only process URLs in these categories (exact match)")
    ap.add_argument("--resume", action="store_true", help="Resume from checkpoint if available")
    ap.add_argument("--checkpoint", default=DEFAULT_CHECKPOINT, help="Checkpoint file path")
    ap.add_argument("--dry-run", action="store_true", help="Parse but do not write output")
    args = ap.parse_args()

    rules = load_rules(args.rules)

    # Build URL list
    with open(args.input, "r", encoding="utf-8") as f:
        urls = [line.strip() for line in f if line.strip()]

    # Filter by domain
    urls = [u for u in urls if is_same_domain(u, args.domain)]

    # Optional category filter
    if args.category:
        cmap = load_category_map(args.category_file)
        urls = [u for u in urls if cmap.get(u, None) in set(args.category)]

    # Resume support
    start_index = 0
    processed: Dict[str, bool] = {}
    if args.resume:
        state = load_checkpoint(args.checkpoint)
        if state:
            processed = {u: True for u in state.get("processed", [])}
            start_index = int(state.get("index", 0))
            print(f"[resume] index={start_index} processed={len(processed)}")

    # Prepare outputs
    os.makedirs(OUTPUT_DIR, exist_ok=True)
    out_f = None
    if not args.dry_run:
        out_f = open(args.output, "a", encoding="utf-8")

    # Ensure image dir if download
    if args.download_images:
        args.include_images = True
        os.makedirs(args.image_dir, exist_ok=True)

    total = 0
    for i, url in enumerate(urls[start_index:], start=start_index):
        if total >= args.max_ads:
            break
        if processed.get(url):
            continue
        html = fetch(url)
        if not html:
            continue
        item = extract_fields(url, html, rules, fields=args.fields)

        # Control images
        if not args.include_images and "image_urls" in item:
            del item["image_urls"]
        elif args.download_images and "image_urls" in item:
            saved: List[str] = []
            for idx, img_url in enumerate(item.get("image_urls", []) or []):
                try:
                    r = requests.get(img_url, timeout=20)
                    if r.status_code == 200:
                        name = f"ad_{i}_{idx}.jpg"
                        with open(os.path.join(args.image_dir, name), "wb") as imgf:
                            imgf.write(r.content)
                        saved.append(name)
                except Exception:
                    continue
            item["image_files"] = saved
        if not args.dry_run and out_f:
            out_f.write(json.dumps(item, ensure_ascii=False) + "\n")
        total += 1
        if total % max(1, args.progress_every) == 0:
            print(f"[progress] ads={total} idx={i}")
        time.sleep(max(0.0, args.delay_seconds))
        # Save checkpoint periodically
        if total % 50 == 0:
            save_checkpoint(args.checkpoint, {"index": i + 1, "processed": list(processed.keys())})

    # Final checkpoint
    save_checkpoint(args.checkpoint, {"index": start_index + total, "processed": list(processed.keys())})

    if out_f:
        out_f.close()

    print(f"[ok] Extracted {total} ads -> {args.output}")


if __name__ == "__main__":
    raise SystemExit(main())
