# HaloOglasi Polite Crawler (Easy Script)

This is a simple, non-aggressive crawler to enumerate subdirectories (internal URLs) of `halooglasi.com`, prioritizing sitemap discovery and obeying `robots.txt`.

Subagents (modules inside the script):
- SitemapAgent: fetches and parses `sitemap.xml` and common variants
- RobotsAgent: loads and checks `robots.txt` permissions for each URL
- CrawlerAgent: breadth-first crawling within the domain, with delay and retries
- ReporterAgent: dedupes, filters to subdirectories, and exports CSV/TXT/JSON summary + compact tree

Defaults aim to be polite (single-threaded, small delay, robots-aware). You can tune limits via CLI flags.

## Quick start

1) Create a virtual environment (recommended) and install dependencies:

```bash
python -m venv .venv
. .venv/Scripts/activate  # PowerShell: .\.venv\Scripts\Activate.ps1
pip install -r scripts/crawlers/halooglasi/requirements.txt
```

2) Run the crawler with safe defaults (about 1.5s between requests, 500 page cap):

```bash
python scripts/crawlers/halooglasi/halooglasi_crawler.py \
  --seed https://www.halooglasi.com/ \
  --max-pages 500 \
  --delay-seconds 1.5
```

3) Outputs will be written to `scripts/crawlers/halooglasi/output/`:
- `urls.txt` — one URL per line
- `urls.csv` — `url,path`
- `report.json` — crawl stats
- `tree.txt` — compact ASCII path tree for easy reading and minimal tokens

## Minimizing token waste
- The tree output anonymizes purely numeric path segments by default (e.g. `/item/1234` → `/item/:num`) to reduce repeated unique IDs.
- Limit children per node in the tree with `--tree-max-children` (default: 30). Extra items collapse into a single summary line.
- Keep numeric segments as-is by adding `--keep-numbers`.

Examples:

```bash
# Smaller tree (top 20 children per node), anonymize numeric segments
python scripts/crawlers/halooglasi/halooglasi_crawler.py \
  --seed https://www.halooglasi.com/ \
  --tree-max-children 20

# Keep numeric segments (no anonymization), 50 children per node
python scripts/crawlers/halooglasi/halooglasi_crawler.py \
  --seed https://www.halooglasi.com/ \
  --tree-max-children 50 \
  --keep-numbers
```

## Notes
- The crawler only visits URLs whose host ends with `halooglasi.com` and allowed by `robots.txt`.
- It respects canonical robots directives using Python's standard `urllib.robotparser`.
- If a sitemap is present, it seeds from there first; otherwise it falls back to BFS from the seed.
- Keep it polite; consider increasing `--delay-seconds` or reducing `--max-pages`.

Reference used for approach and best practices: [ScrapingBee guide on finding all URLs](https://www.scrapingbee.com/blog/how-to-find-all-urls-on-a-domains-website-multiple-methods/).
