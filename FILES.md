# 📁 Osclass Classifieds Platform - File Structure

**Quick reference map of all files and their purposes**

---

## 📚 Documentation Files (Start Here!)

### Essential Documentation
- **`INDEX.md`** - Master documentation hub (READ THIS FIRST!)
- **`START_HERE.md`** - 5-minute orientation for new team members ⭐⭐⭐
- **`HANDOVER.md`** - Complete onboarding guide (1 hour) ⭐⭐⭐
- **`QUICK_REFERENCE.md`** - Quick reference card (print this!) ⭐⭐⭐
- **`ARCHITECTURE.md`** - Complete technical guide (your daily reference) ⭐⭐⭐
- **`PROJECT_OVERVIEW.txt`** - Visual overview diagram

### Additional Documentation
- **`README.md`** - Project introduction and overview
- **`FILES.md`** - This file! File structure reference
- **`DOCUMENTATION_INDEX.md`** - Topic-based navigation
- **`QUICKSTART.md`** - 5-minute setup guide
- **`DEPLOYMENT.md`** - Production deployment guide
- **`CONTRIBUTING.md`** - Contribution guidelines
- **`LICENSE`** - MIT License

---

## 🚀 Getting Started (2 minutes)

1. Start XAMPP (Apache + MySQL)
2. Visit: `http://localhost/osclass/public/test-homepage.php`
3. Read: **START_HERE.md** (5 minutes)
4. Read: **HANDOVER.md** (1 hour)

---

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


