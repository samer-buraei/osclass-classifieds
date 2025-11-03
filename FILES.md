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



