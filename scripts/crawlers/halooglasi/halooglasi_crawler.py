import argparse
import csv
import gzip
import json
import os
import sys
import time
from collections import deque
from dataclasses import dataclass
from typing import Iterable, List, Optional, Set, Tuple, Dict, Any
from urllib.parse import urljoin, urlparse, urlunparse
import xml.etree.ElementTree as ET

import requests
from bs4 import BeautifulSoup
from urllib import robotparser


DEFAULT_USER_AGENT = (
    "Mozilla/5.0 (Windows NT 10.0; Win64; x64) "
    "AppleWebKit/537.36 (KHTML, like Gecko) "
    "Chrome/122.0.0.0 Safari/537.36"
)

ASSET_EXTENSIONS = {
    ".css", ".js", ".json", ".xml", ".png", ".jpg", ".jpeg", ".gif", ".svg",
    ".webp", ".ico", ".pdf", ".mp4", ".woff", ".woff2", ".ttf", ".eot",
}

DEFAULT_OUTPUT_DIR = os.path.join("scripts", "crawlers", "halooglasi", "output")


@dataclass
class CrawlConfig:
    seed: str
    domain: str
    output_dir: str
    max_pages: int = 500
    delay_seconds: float = 1.5
    max_depth: int = 6
    user_agent: str = DEFAULT_USER_AGENT
    timeout_seconds: int = 20
    tree_max_children: int = 30
    anonymize_numbers: bool = True
    progress_every: int = 25
    verbose: bool = False
    resume: bool = False
    checkpoint_path: str = ""
    checkpoint_every: int = 50
    log_discoveries: bool = False
    discoveries_per_page: int = 10
    export_pages: bool = True
    export_tree_json: bool = True
    export_sitemap: bool = True
    export_edges: bool = False
    cookies_file: str = ""


class RobotsAgent:
    def __init__(self, seed_url: str, user_agent: str):
        parsed = urlparse(seed_url)
        robots_url = urlunparse((parsed.scheme, parsed.netloc, "/robots.txt", "", "", ""))
        self.user_agent = user_agent
        self.rp = robotparser.RobotFileParser()
        try:
            self.rp.set_url(robots_url)
            self.rp.read()
        except Exception:
            self.rp = None

    def allowed(self, url: str) -> bool:
        if self.rp is None:
            return True
        try:
            return self.rp.can_fetch(self.user_agent, url)
        except Exception:
            return True


class SitemapAgent:
    COMMON_SITEMAP_PATHS = [
        "/sitemap.xml",
        "/sitemap_index.xml",
        "/sitemap-index.xml",
        "/sitemap.xml.gz",
        "/sitemap_index.xml.gz",
        "/sitemap-index.xml.gz",
        "/sitemapindex.xml",
        "/sitemapindex.xml.gz",
    ]

    def __init__(self, seed_url: str, user_agent: str, timeout_seconds: int):
        parsed = urlparse(seed_url)
        self.base = f"{parsed.scheme}://{parsed.netloc}"
        self.session = requests.Session()
        self.session.headers.update({"User-Agent": user_agent})
        self.timeout = timeout_seconds

    def _fetch_text(self, url: str) -> Optional[bytes]:
        try:
            resp = self.session.get(url, timeout=self.timeout)
            if resp.status_code == 200:
                return resp.content
        except Exception:
            pass
        return None

    def _parse_xml_urls(self, xml_bytes: bytes) -> Tuple[List[str], List[str]]:
        try:
            root = ET.fromstring(xml_bytes)
        except ET.ParseError:
            return ([], [])
        ns = "{http://www.sitemaps.org/schemas/sitemap/0.9}"
        sitemaps, urls = [], []
        if root.tag.endswith("sitemapindex"):
            for sm in root.findall(f"{ns}sitemap") if root.tag.startswith(ns) else root.findall("sitemap"):
                loc = sm.find(f"{ns}loc") if root.tag.startswith(ns) else sm.find("loc")
                if loc is not None and loc.text:
                    sitemaps.append(loc.text.strip())
        if root.tag.endswith("urlset"):
            for u in root.findall(f"{ns}url") if root.tag.startswith(ns) else root.findall("url"):
                loc = u.find(f"{ns}loc") if root.tag.startswith(ns) else u.find("loc")
                if loc is not None and loc.text:
                    urls.append(loc.text.strip())
        return (sitemaps, urls)

    def discover(self) -> List[str]:
        discovered: Set[str] = set()
        to_visit = deque([self.base + p for p in self.COMMON_SITEMAP_PATHS])
        seen = set()
        while to_visit:
            sm_url = to_visit.popleft()
            if sm_url in seen:
                continue
            seen.add(sm_url)
            raw = self._fetch_text(sm_url)
            if raw is None:
                continue
            if sm_url.endswith(".gz"):
                try:
                    raw = gzip.decompress(raw)
                except Exception:
                    continue
            sitemaps, urls = self._parse_xml_urls(raw)
            for u in urls:
                discovered.add(u)
            for sm in sitemaps:
                if sm not in seen:
                    to_visit.append(sm)
        return list(discovered)


class ReporterAgent:
    def __init__(self, output_dir: str, seed_url: str):
        self.output_dir = output_dir
        os.makedirs(self.output_dir, exist_ok=True)
        parsed = urlparse(seed_url)
        self.base = f"{parsed.scheme}://{parsed.netloc}"

    def _path_of(self, url: str) -> str:
        parsed = urlparse(url)
        return parsed.path or "/"

    def export(self, urls: Iterable[str], stats: dict) -> None:
        url_list = sorted(set(urls))
        txt_path = os.path.join(self.output_dir, "urls.txt")
        with open(txt_path, "w", encoding="utf-8") as f:
            for u in url_list:
                f.write(u + "\n")
        csv_path = os.path.join(self.output_dir, "urls.csv")
        with open(csv_path, "w", newline="", encoding="utf-8") as f:
            writer = csv.writer(f)
            writer.writerow(["url", "path"])
            for u in url_list:
                writer.writerow([u, self._path_of(u)])
        rep_path = os.path.join(self.output_dir, "report.json")
        with open(rep_path, "w", encoding="utf-8") as f:
            json.dump({"stats": stats, "count": len(url_list)}, f, indent=2)

    def export_pages(self, titles: Dict[str, str]) -> None:
        items = sorted(((u, titles.get(u, "")) for u in titles.keys()), key=lambda x: x[0])
        csv_path = os.path.join(self.output_dir, "pages.csv")
        with open(csv_path, "w", newline="", encoding="utf-8") as f:
            writer = csv.writer(f)
            writer.writerow(["url", "path", "title"])
            for u, t in items:
                writer.writerow([u, self._path_of(u), t])
        jsonl_path = os.path.join(self.output_dir, "pages.jsonl")
        with open(jsonl_path, "w", encoding="utf-8") as f:
            for u, t in items:
                f.write(json.dumps({"url": u, "path": self._path_of(u), "title": t}, ensure_ascii=False) + "\n")

    def _anonymize_segment(self, segment: str, anonymize_numbers: bool) -> str:
        if anonymize_numbers and segment.isdigit():
            return ":num"
        return segment

    def _build_trie(self, paths: Iterable[str], anonymize_numbers: bool) -> dict:
        root: dict = {"/": {}}
        for p in paths:
            segments = [s for s in p.split("/") if s]
            node = root["/"]
            for seg in segments:
                norm = self._anonymize_segment(seg, anonymize_numbers)
                if norm not in node:
                    node[norm] = {}
                node = node[norm]
        return root

    def _write_tree(self, fh, node: dict, prefix: str, max_children: int):
        keys = sorted(node.keys())
        if max_children and len(keys) > max_children:
            visible = keys[:max_children]
            remainder = len(keys) - max_children
        else:
            visible = keys
            remainder = 0
        for i, key in enumerate(visible):
            is_last = (i == len(visible) - 1 and remainder == 0)
            connector = "└─ " if is_last else "├─ "
            fh.write(prefix + connector + key + "\n")
            next_prefix = prefix + ("   " if is_last else "│  ")
            self._write_tree(fh, node[key], next_prefix, max_children)
        if remainder > 0:
            fh.write(prefix + "└─ … (" + str(remainder) + " more)\n")

    def export_tree(self, urls: Iterable[str], max_children: int, anonymize_numbers: bool) -> None:
        paths = [self._path_of(u) for u in urls]
        trie = self._build_trie(paths, anonymize_numbers=anonymize_numbers)
        tree_path = os.path.join(self.output_dir, "tree.txt")
        with open(tree_path, "w", encoding="utf-8") as f:
            f.write("/\n")
            self._write_tree(f, trie["/"], prefix="", max_children=max_children)

    def export_tree_json(self, urls: Iterable[str], anonymize_numbers: bool) -> None:
        paths = [self._path_of(u) for u in urls]
        trie = self._build_trie(paths, anonymize_numbers=anonymize_numbers)
        json_path = os.path.join(self.output_dir, "tree.json")
        with open(json_path, "w", encoding="utf-8") as f:
            json.dump(trie, f, ensure_ascii=False, indent=2)

    def export_sitemap(self, urls: Iterable[str]) -> None:
        url_list = sorted(set(urls))
        urlset = ET.Element("urlset", xmlns="http://www.sitemaps.org/schemas/sitemap/0.9")
        for u in url_list:
            if u.startswith(self.base):
                url_el = ET.SubElement(urlset, "url")
                loc = ET.SubElement(url_el, "loc")
                loc.text = u
        xml_path = os.path.join(self.output_dir, "sitemap.generated.xml")
        tree = ET.ElementTree(urlset)
        tree.write(xml_path, encoding="utf-8", xml_declaration=True)

    def export_edges(self, edges: List[Tuple[str, str]]) -> None:
        if not edges:
            return
        edges_path = os.path.join(self.output_dir, "edges.jsonl")
        with open(edges_path, "w", encoding="utf-8") as f:
            for src, dst in edges:
                f.write(json.dumps({"from": src, "to": dst}, ensure_ascii=False) + "\n")


class CrawlerAgent:
    def __init__(self, config: CrawlConfig, robots: RobotsAgent):
        self.config = config
        self.robots = robots
        self.session = requests.Session()
        self.session.headers.update({"User-Agent": self.config.user_agent})
        # Load cookies if provided (expects Playwright-style list [{name,value,domain,path}]) or Netscape cookiefile
        if self.config.cookies_file:
            try:
                if self.config.cookies_file.lower().endswith(('.json', '.jsonl')):
                    with open(self.config.cookies_file, 'r', encoding='utf-8') as f:
                        data = json.load(f)
                    if isinstance(data, list):
                        for c in data:
                            try:
                                name = c.get('name'); value = c.get('value')
                                domain = c.get('domain', self.config.domain)
                                path = c.get('path', '/')
                                if name is None or value is None:
                                    continue
                                cookie = requests.cookies.create_cookie(name=name, value=value, domain=domain, path=path)
                                self.session.cookies.set_cookie(cookie)
                            except Exception:
                                continue
                else:
                    # Try reading Netscape cookie file format
                    from http.cookiejar import MozillaCookieJar
                    cj = MozillaCookieJar()
                    cj.load(self.config.cookies_file, ignore_discard=True, ignore_expires=True)
                    for c in cj:
                        self.session.cookies.set_cookie(c)
                print(f"[init] Loaded cookies from {self.config.cookies_file}", flush=True)
            except Exception as e:
                print(f"[warn] Failed to load cookies: {e}", flush=True)
        self.seen: Set[str] = set()
        self.collected: Set[str] = set()
        self.titles: Dict[str, str] = {}
        self.edges: List[Tuple[str, str]] = []

    def _normalize(self, base_url: str, href: str) -> Optional[str]:
        if not href:
            return None
        try:
            abs_url = urljoin(base_url, href.strip())
            parsed = urlparse(abs_url)
            if parsed.scheme not in ("http", "https"):
                return None
            parsed = parsed._replace(fragment="")
            path_lower = parsed.path.lower()
            for ext in ASSET_EXTENSIONS:
                if path_lower.endswith(ext):
                    return None
            if not parsed.netloc.endswith(self.config.domain):
                return None
            normalized = urlunparse(parsed)
            return normalized
        except Exception:
            return None

    def _extract_links(self, url: str, html: str) -> Tuple[str, List[str]]:
        soup = BeautifulSoup(html, "html.parser")
        page_title = soup.title.get_text(strip=True) if soup.title else ""
        links: List[str] = []
        for a in soup.select("a[href]"):
            href = a.get("href", "")
            norm = self._normalize(url, href)
            if norm is not None:
                links.append(norm)
        return page_title, links

    def _fetch(self, url: str) -> Optional[str]:
        try:
            resp = self.session.get(url, timeout=self.config.timeout_seconds)
            ctype = resp.headers.get("Content-Type", "").lower()
            if resp.status_code == 200 and ("text/html" in ctype or "text/" in ctype or ctype == ""):
                return resp.text
        except Exception:
            pass
        return None

    def _print_progress(self, pages_fetched: int, queue_len: int, depth: int) -> None:
        if self.config.progress_every <= 0:
            return
        if pages_fetched % self.config.progress_every == 0:
            print(f"[progress] pages={pages_fetched} | queue={queue_len} | seen={len(self.seen)} | depth={depth}", flush=True)

    def _save_checkpoint(self, queue: deque, pages_fetched: int) -> None:
        try:
            data = {
                "seen": list(self.seen),
                "collected": list(self.collected),
                "titles": self.titles,
                "queue": [[u, d] for (u, d) in list(queue)],
                "pages_fetched": pages_fetched,
                "timestamp": int(time.time()),
                "domain": self.config.domain,
                "seed": self.config.seed,
                "output_dir": self.config.output_dir,
                "max_depth": self.config.max_depth,
            }
            os.makedirs(os.path.dirname(self.config.checkpoint_path), exist_ok=True)
            with open(self.config.checkpoint_path, "w", encoding="utf-8") as f:
                json.dump(data, f, ensure_ascii=False)
        except Exception:
            pass

    def crawl(self, seeds: List[str], resume_state: Optional[Dict[str, Any]] = None) -> Set[str]:
        queue: deque[Tuple[str, int]] = deque()
        enqueued: Set[str] = set()
        pages_fetched = 0

        if resume_state:
            for u in resume_state.get("seen", []):
                self.seen.add(u)
            for u in resume_state.get("collected", []):
                self.collected.add(u)
            for u, t in resume_state.get("titles", {}).items():
                self.titles[u] = t
            for item in resume_state.get("queue", []):
                if isinstance(item, list) and len(item) == 2:
                    url, depth = item
                else:
                    url, depth = item, 0
                queue.append((url, int(depth)))
                enqueued.add(url)
            pages_fetched = int(resume_state.get("pages_fetched", 0))
            print(f"[resume] loaded checkpoint: seen={len(self.seen)} collected={len(self.collected)} queue={len(queue)}", flush=True)
        else:
            for s in seeds:
                if s not in enqueued:
                    queue.append((s, 0))
                    enqueued.add(s)
            print(f"[crawl] initial queue={len(queue)}", flush=True)

        while queue and pages_fetched < self.config.max_pages:
            url, depth = queue.popleft()
            if url in self.seen:
                continue
            if depth > self.config.max_depth:
                continue
            if not self.robots.allowed(url):
                continue
            if url in self.seen:
                continue
            if self.config.verbose:
                print(f"[visit] depth={depth} {url}", flush=True)
            if pages_fetched > 0:
                time.sleep(self.config.delay_seconds)
            html = self._fetch(url)
            self.seen.add(url)
            if html is None:
                continue
            pages_fetched += 1
            self.collected.add(url)
            title, links = self._extract_links(url, html)
            if title:
                self.titles[url] = title
            self._print_progress(pages_fetched, len(queue), depth)
            printed_for_page = 0
            for link in links:
                if link not in enqueued and link not in self.seen:
                    queue.append((link, depth + 1))
                    enqueued.add(link)
                    if self.config.export_edges:
                        self.edges.append((url, link))
                    if self.config.log_discoveries:
                        if self.config.discoveries_per_page <= 0 or printed_for_page < self.config.discoveries_per_page:
                            print(f"[discover] depth={depth+1} {link}", flush=True)
                            printed_for_page += 1
            if self.config.checkpoint_every > 0 and pages_fetched % self.config.checkpoint_every == 0:
                self._save_checkpoint(queue, pages_fetched)
        self._save_checkpoint(queue, pages_fetched)
        return self.collected


def infer_domain(seed: str) -> str:
    netloc = urlparse(seed).netloc
    if netloc.count(".") >= 2:
        parts = netloc.split(".")
        return ".".join(parts[-2:])
    return netloc


def parse_args(argv: List[str]) -> argparse.Namespace:
    ap = argparse.ArgumentParser(description="Polite crawler for halooglasi.com")
    ap.add_argument("--seed", default="https://www.halooglasi.com/", help="Seed URL to start crawling")
    ap.add_argument("--output-dir", default=DEFAULT_OUTPUT_DIR, help="Directory to write outputs and checkpoints")
    ap.add_argument("--max-pages", type=int, default=500, help="Max HTML pages to fetch")
    ap.add_argument("--delay-seconds", type=float, default=1.5, help="Delay between requests")
    ap.add_argument("--max-depth", type=int, default=6, help="Max crawl depth from the seed")
    ap.add_argument("--tree-max-children", type=int, default=30, help="Max children per node in tree output")
    ap.add_argument("--keep-numbers", action="store_true", help="Keep numeric segments as-is in tree (disable anonymization)")
    ap.add_argument("--progress-every", type=int, default=25, help="Print progress every N fetched pages (0=disable)")
    ap.add_argument("--verbose", action="store_true", help="Print each visited URL and depth")
    ap.add_argument("--resume", action="store_true", help="Resume from checkpoint if present")
    ap.add_argument("--checkpoint-path", default="", help="Path to checkpoint file (defaults to output-dir/checkpoint.json)")
    ap.add_argument("--checkpoint-every", type=int, default=50, help="Checkpoint every N fetched pages (0=disable)")
    ap.add_argument("--log-discoveries", action="store_true", help="Print each newly discovered internal link as it is enqueued")
    ap.add_argument("--discoveries-per-page", type=int, default=10, help="Max discovery lines to print per fetched page (0=unlimited)")
    ap.add_argument("--no-export-pages", dest="export_pages", action="store_false", help="Disable exporting pages.csv/jsonl with titles")
    ap.add_argument("--no-export-tree-json", dest="export_tree_json", action="store_false", help="Disable exporting tree.json")
    ap.add_argument("--no-export-sitemap", dest="export_sitemap", action="store_false", help="Disable generating sitemap.generated.xml")
    ap.add_argument("--export-edges", action="store_true", help="Export link graph edges to edges.jsonl")
    ap.add_argument("--cookies-file", default="", help="Path to cookies file (Playwright JSON or Netscape cookies.txt)")
    ap.set_defaults(export_pages=True, export_tree_json=True, export_sitemap=True)
    return ap.parse_args(argv)


def _load_checkpoint(path: str) -> Optional[Dict[str, Any]]:
    try:
        if not os.path.exists(path):
            return None
        with open(path, "r", encoding="utf-8") as f:
            return json.load(f)
    except Exception:
        return None


def main(argv: List[str]) -> int:
    args = parse_args(argv)
    domain = infer_domain(args.seed)
    if not domain.endswith("halooglasi.com"):
        print(f"[guard] This tool is configured for halooglasi.com; got domain '{domain}'. Proceeding anyway but filtering to '{domain}'.", flush=True)

    output_dir = args.output_dir
    os.makedirs(output_dir, exist_ok=True)
    checkpoint_path = args.checkpoint_path or os.path.join(output_dir, "checkpoint.json")

    config = CrawlConfig(
        seed=args.seed,
        domain=domain,
        output_dir=output_dir,
        max_pages=args.max_pages,
        delay_seconds=args.delay_seconds,
        max_depth=args.max_depth,
        tree_max_children=args.tree_max_children,
        anonymize_numbers=(not args.keep_numbers),
        progress_every=args.progress_every,
        verbose=args.verbose,
        resume=args.resume,
        checkpoint_path=checkpoint_path,
        checkpoint_every=args.checkpoint_every,
        log_discoveries=args.log_discoveries,
        discoveries_per_page=args.discoveries_per_page,
        export_pages=args.export_pages,
        export_tree_json=args.export_tree_json,
        export_sitemap=args.export_sitemap,
        export_edges=args.export_edges,
        cookies_file=args.cookies_file,
    )

    print("[init] Loading robots.txt …", flush=True)
    robots = RobotsAgent(config.seed, config.user_agent)

    resume_state = None
    if config.resume:
        resume_state = _load_checkpoint(config.checkpoint_path)
        if resume_state:
            print(f"[init] Resuming from {config.checkpoint_path}", flush=True)

    seeds: List[str]
    if resume_state is None:
        print("[seed] Discovering sitemap URLs (if available) …", flush=True)
        sitemap = SitemapAgent(config.seed, config.user_agent, config.timeout_seconds)
        sitemap_urls = sitemap.discover()
        if sitemap_urls:
            print(f"[seed] Found {len(sitemap_urls)} URLs via sitemap.", flush=True)
            seeds = sitemap_urls
        else:
            print("[seed] No sitemap URLs found; falling back to the seed only.", flush=True)
            seeds = [config.seed]
    else:
        seeds = []
        sitemap_urls = []

    print("[crawl] Starting BFS crawl (polite, robots-aware) …", flush=True)
    crawler = CrawlerAgent(config, robots)
    collected = crawler.crawl(seeds, resume_state=resume_state)

    stats = {
        "seed": config.seed,
        "domain": config.domain,
        "output_dir": config.output_dir,
        "max_pages": config.max_pages,
        "delay_seconds": config.delay_seconds,
        "max_depth": config.max_depth,
        "visited_count": len(crawler.seen),
        "collected_html_pages": len(collected),
        "seeded_from_sitemap": bool(resume_state is None and seeds and len(seeds) > 0),
        "resumed": bool(resume_state is not None),
        "checkpoint_path": config.checkpoint_path,
    }

    print(f"[done] Collected {len(collected)} internal HTML pages. Exporting results …", flush=True)
    reporter = ReporterAgent(config.output_dir, seed_url=config.seed)
    reporter.export(collected, stats)
    if config.export_pages:
        reporter.export_pages(crawler.titles)
    reporter.export_tree(
        collected,
        max_children=config.tree_max_children,
        anonymize_numbers=config.anonymize_numbers,
    )
    if config.export_tree_json:
        reporter.export_tree_json(collected, anonymize_numbers=config.anonymize_numbers)
    if config.export_sitemap:
        reporter.export_sitemap(collected)
    if config.export_edges:
        reporter.export_edges(crawler.edges)
    print(f"[ok] Wrote outputs in: {config.output_dir}", flush=True)
    return 0


if __name__ == "__main__":
    raise SystemExit(main(sys.argv[1:]))
