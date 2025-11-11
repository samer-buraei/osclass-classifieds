# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

This is an **agent-based workflow automation system** (referred to as "open-agent-builder") that orchestrates development tasks through YAML-defined workers. The repository contains:

1. **FirstDate** - A React dating app (Tinder clone) used as a reference implementation
2. **Server** - Express.js WebSocket backend for FirstDate
3. **Worker System** - YAML-based task orchestration framework
4. **State Management** - JSON-based project state tracking

The system is designed to automate feature implementation by spawning specialized workers that operate on specific files with defined test criteria.

## Architecture

### Core Components

**Orchestrator System** (`orchestrator.md`)
- Manages workflow execution and worker spawning
- Tracks project state in `state.json`
- Provides shortcuts: `/s` (start), `/t` (test), `/n` (next), `/shipit` (deploy)
- AUTO_MODE: automatically picks next unblocked task when user says "continue" or "next"
- Return format: `{"done":"feature","files":["..."],"tests":"✅","next":"hint"}`

**Worker Templates** (`workers/` directory)
- YAML-based task definitions with structure:
  - `task`: feature name
  - `context`: description and constraints
  - `files`: specific files to modify
  - `test`: validation command
  - `max_tries`: retry budget (default 3)
  - `return`: expected output format
- Examples: `feature.boost.yaml`, `feature.passport.yaml`, etc.

**State Machine** (`state.json`)
- Tracks project progress: `project`, `current`, `done`, `next`, `features`, `stats`
- Current state: "osclass-app" classified ads platform (completed)
- Workers reference this for context and next actions

**FirstDate Application** (`FirstDate/` directory)
- React 18 + Redux frontend (Create React App)
- Material-UI components
- Real-time chat with WebSocket
- Redux store: `authReducer`, `chatReducer`, `matchReducer`, `commonReducer`
- Main pages: Landing, OnBoarding, Recognition (main app)
- API layer: `src/api/` (MatchAPI, ChatAPI, UsersAPI)

**Backend Server** (`server/` directory)
- Express.js with WebSocket support (`ws` package)
- In-memory data store (no database)
- REST API at `/api/v1`
- WebSocket chat at `/api/v1/ws/chat/:senderId/:receiverId`
- Key endpoints: auth, user profiles, matches, messages

### Directory Structure

```
open-agent-builder/
├── FirstDate/              # React dating app (reference implementation)
│   ├── src/
│   │   ├── components/     # Reusable UI components (CardsContainer, ChatFooter, etc.)
│   │   ├── pages/          # Main pages (Landing, OnBoarding, Recognition)
│   │   ├── redux/          # Redux state management
│   │   ├── api/            # API client layer
│   │   └── App.js          # Main routing and auth logic
│   └── package.json        # CRA scripts: start, build, test
├── server/                 # Express backend
│   ├── index.js            # Main server file (Express + WebSocket)
│   └── package.json        # Server dependencies
├── workers/                # YAML task definitions
│   ├── feature.*.yaml      # Feature implementation workers
│   └── backend.*.yaml      # Backend-specific workers
├── state.json              # Project state machine
├── orchestrator.md         # Orchestrator command reference
└── testing.md              # Auto-fix testing loop
```

## Common Development Commands

### FirstDate (React Client)

```bash
# Navigate to FirstDate directory first
cd FirstDate

# Install dependencies
npm install

# Start development server (requires env vars)
npm start
# → Opens http://localhost:3000

# Build for production
npm build

# Run tests
npm test

# Required environment variables:
# export REACT_APP_SERVER_URL=http://localhost:8000/api/v1
# export REACT_APP_SOCKET_URL=ws://localhost:8000/api/v1/ws
```

### Backend Server

```bash
# Navigate to server directory
cd server

# Install dependencies
npm install

# Start server
npm start
# → Listens on http://localhost:8000/api/v1
```

### Worker System Commands

The orchestrator uses special commands (defined in `orchestrator.md`):
- `/s` or `/start` - Begin a new task
- `/t` or `/test` - Run tests for current task
- `/n` or `/next` - Suggest next task from state.json
- `/status` - Show one-line status: `Auth✅ UI✅ API🔨 DB🔜 Deploy⏳`
- `/shipit` - Run full checklist: test + build + deploy
- `continue` or `next` - AUTO_MODE triggers automatic task selection

### Publishing to GitHub (Windows)

```bash
# With GitHub CLI installed:
scripts\windows\publish-github.bat --RepoName MyRepo --Visibility public

# Without GitHub CLI (prompts for remote URL):
scripts\windows\publish-github.bat
```

## Key Technical Details

### FirstDate Application Flow

1. **Authentication**: User lands on `/` → sees Landing page → can register/login
2. **Onboarding**: After signup, `/app/onboarding` → profile setup
3. **Main App**: `/app/recs` (Recognition page) → swipe cards, view matches, chat
4. **Protected Routes**: Routes check `currentAuthUser` and redirect accordingly
5. **State Management**: Redux store persists to localStorage ("user" key)
6. **Real-time Chat**: WebSocket connection per conversation (sender/receiver pair)

### Worker Execution Pattern

When implementing a feature:
1. Read `state.json` to understand project context
2. Select/create appropriate worker YAML in `workers/`
3. Worker modifies only files listed in `files` array
4. Run test command specified in `test` field
5. Return result: `{ done, files, tests, next }`
6. Retry up to `max_tries` on failure
7. On repeated failure: search codebase for working example and apply minimal fix

### API Architecture (server/index.js)

- **Auth**: `/auth/register`, `/auth/login` → returns access token
- **User**: `/user/profile` (GET/PUT), `/user/all` → requires Bearer token
- **Matches**: `/matches` (GET/POST) → manages user matches
- **Messages**: `/messages`, `/message` → retrieves chat data
- **WebSocket**: Real-time messaging with room-based broadcasting

### Redux Store Structure

```javascript
// Store shape
{
  auth: { user, isAuthenticated },
  match: { users, matches },
  chat: { messages, conversations },
  common: { loading, errors }
}
```

## Development Guidelines

1. **Worker Context**: Always include minimal summary from state.json when spawning workers
2. **File Scope**: Workers should only modify files explicitly listed in their YAML
3. **Testing**: Each worker must define a test command; use `echo FEATURE_OK` for placeholder
4. **Return Format**: Maintain consistent JSON return structure for orchestrator
5. **State Updates**: Update `state.json` after completing features (add to "done" array)
6. **Auto-Mode**: System can build entire app by repeatedly typing "next" when state.json is properly configured
7. **Retry Logic**: Workers attempt up to 3 times before escalating to codebase search
8. **Critical Paths**: Workers receive "Don't break: [critical paths]" warnings for auth/routing

## Important Notes

- The README.md describes a "classified ads platform" (Osclass) but this is **misleading** - the actual project is an agent orchestration system with a dating app reference implementation
- The FirstDate app is a complete, working Tinder clone that serves as a test case for the worker system
- The PHP/MySQL stack described in README.md **does not exist** in this repository
- state.json shows "osclass-app" as completed, but this refers to documentation artifacts, not actual implementation
- The real value is the **worker orchestration framework** that can be applied to any project

## Testing Patterns

From `testing.md`, the system uses:
- **MAGIC_FIX_CHAIN**: Auto-add imports → infer types → add null guards → mirror working examples
- **Browser loop**: Capture `console.error` explicitly for debugging
- **Minimal return blocks**: Consistent return format across all workers

## Security Considerations

- Server uses in-memory storage (no data persistence)
- Token-based auth with Map storage (tokens lost on restart)
- No password validation or hashing in current implementation
- CORS enabled for all origins
- WebSocket connections not authenticated (sender ID from URL)
