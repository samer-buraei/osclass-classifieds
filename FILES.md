### Repository Map (one-pager)

Use this flat file to share what each file is for and how to use it.

## Getting Started (10 seconds)

```bash
curl -sL [gist-url] | sh  # Creates all files, ready to `/s tinder`
```

## Files

- **`orchestrator.md`**: Orchestrator commands and flow
  - Return format: minimal `{"done":"swipe","files":["..."],"tests":"✅","next":"hint"}`
  - One-line status: `/status → Auth✅ UI✅ API🔨 DB🔜 Deploy⏳`
  - Shortcuts: `/s` (= /start), `/t` (= /test), `/n` (suggest next), `/shipit` (test + build + deploy checklist)
  - AUTO_MODE: on "continue"/"next" → read `state.json`, pick highest-priority unblocked task, spawn worker, respond `✅ <feature> done. Continue?`
  - Retry/escalation: up to 3 tries, then search codebase and apply minimal fix

- **`testing.md`**: Testing loop and auto-fix
  - Browser loop with explicit `console.error` capture
  - MAGIC_FIX_CHAIN: auto-add import → infer type → add null guard → mirror working example
  - Mirrors minimal return block for consistency

- **`templates/worker.min.yaml`**: Minimal worker template (drop-in)
  - Keys: `task`, `files`, `test`, `max_tries`, `return`
  - Example:
    - `task`: name of subtask/feature
    - `files`: only the files the worker should touch
    - `test`: command string (e.g., `vitest <name> && playwright --grep <name>`)
    - `max_tries`: attempt budget (default 3)
    - `return`: `{ done, files, tests, next }`

- **`examples/example.yaml`**: Minimal example showing the corrected keys
  - `example: { user: "add swipe", flow: [spawn, test, return] }`

- **`state.json`**: Minimal state machine

```json
{"project":"tinder","current":"swipe","done":["auth","profile"],"next":["match","chat"]}
```

## Expected (optional) files/directories

- **`logs/`**: Run/test logs (optional)
- **`workers/`**: Additional worker templates/configs (optional)

## Quickstart

1. Duplicate `templates/worker.min.yaml` for a new worker and set `task`, `files`, `test`.
2. Use `/s` to start, `/t` to test, `/n` for next, `/shipit` to ship.
3. Keep results minimal using the shared return contract.

## Publish to GitHub (Windows)

- One-liner (with GitHub CLI installed):
  - Run: `scripts\windows\publish-github.bat --RepoName MyRepo --Visibility public`
  - The script: initializes git (if needed), commits, creates repo via `gh`, pushes, and verifies.

- Without GitHub CLI:
  - Run: `scripts\windows\publish-github.bat`
  - Paste your remote HTTPS URL when prompted (e.g. `https://github.com/<you>/MyRepo.git`).
  - Script pushes and verifies remote HEAD matches local.




## Classifieds (Osclass) Quickstart

- **Why Osclass**: Simple PHP app, large plugin ecosystem, multi-language, responsive themes, SEO-friendly, payment gateways.
- **Repo**: `https://github.com/osclass/Osclass`

### Setup (Local, Windows-friendly)

1. Clone Osclass into this workspace root:

```bash
git clone https://github.com/osclass/Osclass osclass
```

2. Create a MySQL database/user (e.g., via XAMPP/WAMP or your MySQL install):

```sql
CREATE DATABASE osclass CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'osclass'@'localhost' IDENTIFIED BY 'strong_password_here';
GRANT ALL PRIVILEGES ON osclass.* TO 'osclass'@'localhost';
FLUSH PRIVILEGES;
```

3. Serve the `osclass/` folder via Apache or another web server with URL rewriting enabled.
   - Easiest on Windows: XAMPP/WAMP → set DocumentRoot to the `osclass` folder or create a VirtualHost pointing to it.
   - Ensure `mod_rewrite` is on and `.htaccess` files are allowed for clean URLs.

4. Run the installer in your browser and complete setup using your DB credentials.
   - Navigate to your local host pointing at `osclass/` (the installer will appear on first run).
   - After install, access the admin at `oc-admin/`.

5. Post-install essentials (optional but recommended):
   - Enable multilingual support and add locales as needed.
   - Install theme(s) for responsive UI.
   - Add plugins for domain features: Car Attributes, Real Estate, Stripe/PayPal gateways.

### Notes

- This repo does not modify Osclass; it hosts it alongside orchestrator files. Keeping it in `osclass/` at the root avoids mixing code.
- Prefer Apache/Nginx over the PHP built-in server due to required URL rewriting.
- For Docker users: any LAMP stack image works—mount `./osclass` to the web root, provision MySQL with the DB above, and run the web installer.
