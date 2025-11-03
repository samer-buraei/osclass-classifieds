### Testing Loop and Auto-Fix

**Browser loop**

- Capture `console.error` explicitly and fix surfaced issues first.

**MAGIC_FIX_CHAIN**

```javascript
// If a test fails, attempt focused auto-fixes before escalating
if (test_fails && attempt < 3) {
  // 1) Import errors → auto-add missing import
  // 2) Type errors → infer and add the minimal type
  // 3) null/undefined → add a targeted guard clause
  // 4) otherwise → search the codebase for a working example and mirror it
}
// This eliminates most manual fixes
```

**Minimal return payload**

```json
{"done":"swipe","files":["..."],"tests":"✅","next":"hint"}
```


