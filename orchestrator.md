### Orchestrator Commands and Flow

**Return format**

```json
{"done":"swipe","files":["..."],"tests":"✅","next":"hint"}
```

**One-line status**

```
/status → Auth✅ UI✅ API🔨 DB🔜 Deploy⏳
```

**Vibecoding shortcuts**

- **/s**: alias for `/start`
- **/t**: alias for `/test`
- **/n**: suggest next step based on `state.json`
- **/shipit**: run test + build + deploy checklist

**AUTO_MODE**

If the user sends only "continue" or "next":

1. Read `state.json`
2. Pick the highest-priority unblocked task
3. Spawn the appropriate worker automatically
4. Return only: `✅ <feature> done. Continue?`

This enables building the entire app by repeatedly typing "next".

**Retry and escalation**

- Up to 3 tries per task before escalation
- On repeated failure: search codebase for a working example and apply the minimal fix


### CLONE_HANDLER

When user says "clone <url>":

1. git clone → scan with @codebase
2. Generate `/docs/context.md` with stack/architecture
3. Create `state.json` from found features
4. Pass focused context to each worker

### WORKER_CONTEXT_INJECTION

Before spawning any worker, include:

- Mini summary from `/docs/context.md`
- Only files relevant to their task
- "Don't break: [critical paths]"

Example: "Next.js app with Supabase. Your task: remove AI. Key files: [ai-engine.ts]. Keep auth working."

### CONTEXT_FILES (auto-created on clone)

- `/docs/context.md` - 200-token tech summary
- `/docs/codebase-map.json` - file dependency tree


