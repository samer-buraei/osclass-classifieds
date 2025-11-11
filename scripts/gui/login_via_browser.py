import argparse
import json
import os
import sys


def ensure_playwright():
    try:
        import playwright  # noqa: F401
        return True
    except Exception:
        return False


def save_cookies(context, path: str):
    cookies = context.cookies()
    with open(path, 'w', encoding='utf-8') as f:
        json.dump(cookies, f, ensure_ascii=False, indent=2)


def main():
    ap = argparse.ArgumentParser(description='Login via browser and save cookies')
    ap.add_argument('--seed', required=True, help='Start URL (login page or site root)')
    ap.add_argument('--output-dir', required=True, help='Where to write cookies.json')
    args = ap.parse_args()

    if not ensure_playwright():
        sys.stderr.write('[fail] Playwright not installed. In your venv run:\n')
        sys.stderr.write('  pip install playwright\n  playwright install\n')
        return 1

    from playwright.sync_api import sync_playwright
    os.makedirs(args.output_dir, exist_ok=True)
    cookies_path = os.path.join(args.output_dir, 'cookies.json')

    with sync_playwright() as p:
        # Persistent context lets you keep sessions; store profile in output dir
        user_data_dir = os.path.join(args.output_dir, '.browser_profile')
        os.makedirs(user_data_dir, exist_ok=True)
        browser = p.chromium.launch_persistent_context(user_data_dir=user_data_dir, headless=False)
        page = browser.new_page()
        page.goto(args.seed, wait_until='load')
        print('[info] A browser window opened. Log in manually, then return here and press Enter to save cookies.')
        try:
            input()
        except EOFError:
            pass
        save_cookies(browser, cookies_path)
        print(f'[ok] Saved cookies to {cookies_path}')
        browser.close()
    return 0


if __name__ == '__main__':
    raise SystemExit(main())
