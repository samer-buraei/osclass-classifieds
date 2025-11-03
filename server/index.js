import express from 'express';
import cors from 'cors';
import http from 'http';
import { WebSocketServer } from 'ws';

const app = express();
app.use(cors());
app.use(express.json({ limit: '2mb' }));

const PORT = process.env.PORT || 8000;
const API_PREFIX = '/api/v1';

// Health
app.get(`${API_PREFIX}`, (req, res) => res.json({ status_code: 200, message: 'OK' }));

// In-memory store
const db = {
  users: [
    { id: 1, email: 'alice@example.com', first_name: 'Alice', last_name: 'A', birthday: '01-01-1998', passion: 'Music', phone_number: '111', profile_picture: 'url(https://picsum.photos/400?1)' },
    { id: 2, email: 'bob@example.com', first_name: 'Bob', last_name: 'B', birthday: '01-01-1997', passion: 'Art', phone_number: '222', profile_picture: 'url(https://picsum.photos/400?2)' },
    { id: 3, email: 'cara@example.com', first_name: 'Cara', last_name: 'C', birthday: '01-01-1996', passion: 'Tech', phone_number: '333', profile_picture: 'url(https://picsum.photos/400?3)' }
  ],
  tokens: new Map(), // token -> userEmail
  matches: new Map(), // userEmail -> Set<userEmail>
  conversations: new Map() // key `${a}|${b}` -> [{user:{id,email}, content, type}]
};

function ok(message = 'OK') { return { status_code: 200, message }; }
function created(message = 'Created') { return { status_code: 201, message }; }
function tokenFor(email) { const t = Math.random().toString(36).slice(2); db.tokens.set(t, email); return t; }
function auth(req, res, next) {
  const authz = req.headers.authorization || '';
  const token = authz.startsWith('Bearer ') ? authz.slice(7) : null;
  if (!token || !db.tokens.has(token)) return res.status(401).json({ status_code: 401, message: 'unauthorized' });
  req.userEmail = db.tokens.get(token);
  next();
}

// Auth
app.post(`${API_PREFIX}/auth/register`, (req, res) => {
  const body = req.body || {};
  const { email, password } = body;
  if (!email) return res.json({ status_code: 400, message: 'email required' });
  if (!db.users.find(u => u.email === email)) {
    const id = db.users.length + 1;
    db.users.push({ id, email, first_name: body.first_name || 'User', last_name: body.last_name || '', birthday: body.birthday || '01-01-1999', passion: body.passion || '', phone_number: body.phone_number || '', profile_picture: body.profile_picture || '' });
  }
  const token = tokenFor(email);
  res.json(created('User registered', { token: { access_token: token } }));
});

app.post(`${API_PREFIX}/auth/login`, (req, res) => {
  // Body is a JSON string of URL-encoded fields in client; accept either
  const raw = req.body;
  const emailMatch = typeof raw === 'string' && raw.match(/username=([^&]+)/);
  const email = emailMatch ? decodeURIComponent(emailMatch[1]) : (raw?.email || 'alice@example.com');
  const token = tokenFor(email);
  res.json({ access_token: token });
});

// User
app.get(`${API_PREFIX}/user/profile`, auth, (req, res) => {
  const user = db.users.find(u => u.email === req.userEmail);
  res.json({ status_code: 200, user });
});

app.get(`${API_PREFIX}/user/logout`, auth, (req, res) => {
  // Best-effort logout
  res.json(ok('Logged out'));
});

app.put(`${API_PREFIX}/user/reset-password`, auth, (req, res) => {
  res.json(ok('Password updated'));
});

app.put(`${API_PREFIX}/user/profile`, auth, (req, res) => {
  const user = db.users.find(u => u.email === req.userEmail);
  if (!user) return res.json({ status_code: 404, message: 'not found' });
  const { first_name, last_name, passion, phone_number } = req.body || {};
  if (first_name) user.first_name = first_name;
  if (last_name) user.last_name = last_name;
  if (passion) user.passion = passion;
  if (phone_number) user.phone_number = phone_number;
  res.json(ok('Profile updated'));
});

app.put(`${API_PREFIX}/user/profile-image`, auth, (req, res) => {
  res.json(ok('Image updated'));
});

app.get(`${API_PREFIX}/user/all`, auth, (req, res) => {
  // Filter out current user
  const me = req.userEmail;
  const result = db.users.filter(u => u.email !== me);
  res.json({ status_code: 200, result });
});

// Matches
app.get(`${API_PREFIX}/matches`, auth, (req, res) => {
  const me = req.userEmail;
  const set = db.matches.get(me) || new Set();
  const result = db.users.filter(u => set.has(u.email));
  res.json({ status_code: 200, result });
});

app.post(`${API_PREFIX}/matches`, auth, (req, res) => {
  const me = req.userEmail;
  const email = req.body?.match;
  if (!email) return res.json({ status_code: 400, message: 'match required' });
  if (!db.matches.has(me)) db.matches.set(me, new Set());
  db.matches.get(me).add(email);
  res.json(created('Matched'));
});

// Messages
app.get(`${API_PREFIX}/messages`, auth, (req, res) => {
  // Return people I have matched with as chat users
  const me = req.userEmail;
  const set = db.matches.get(me) || new Set();
  const result = db.users.filter(u => set.has(u.email)).map(u => ({ id: u.id, email: u.email, first_name: u.first_name }));
  res.json({ status_code: 200, result });
});

app.get(`${API_PREFIX}/message/users`, auth, (req, res) => {
  // Return recent chats (simple mirror of messages list)
  const me = req.userEmail;
  const set = db.matches.get(me) || new Set();
  const result = db.users.filter(u => set.has(u.email)).map(u => ({ id: u.id, email: u.email, first_name: u.first_name }));
  res.json({ status_code: 200, result });
});

app.get(`${API_PREFIX}/message`, auth, (req, res) => {
  const me = req.userEmail;
  const receiverEmail = req.query.receiver;
  const key = [me, receiverEmail].sort().join('|');
  const convo = db.conversations.get(key) || [];
  res.json({ status_code: 200, result: convo });
});

const server = http.createServer(app);

// Helpful info route for WS base
app.get(`${API_PREFIX}/ws`, (req, res) => {
  res.json({
    status_code: 200,
    message: 'WebSocket base. Connect with ws://<host>:<port>/api/v1/ws/chat/:senderId/:receiverId',
    example: '/api/v1/ws/chat/1/2'
  });
});

// WebSocket under /api/v1/ws/chat/:sender/:receiver
const wss = new WebSocketServer({ noServer: true });

server.on('upgrade', (request, socket, head) => {
  const url = new URL(request.url, `http://${request.headers.host}`);
  if (!url.pathname.startsWith(`${API_PREFIX}/ws/chat/`)) {
    socket.destroy();
    return;
  }
  wss.handleUpgrade(request, socket, head, (ws) => {
    wss.emit('connection', ws, request);
  });
});

const clients = new Map(); // key `${a}|${b}` -> Set<ws>

wss.on('connection', (ws, request) => {
  const parts = request.url.split('/');
  const senderId = parts[parts.length - 2];
  const receiverId = parts[parts.length - 1];
  const key = [senderId, receiverId].sort().join('|');
  if (!clients.has(key)) clients.set(key, new Set());
  clients.get(key).add(ws);

  ws.on('message', (data) => {
    let msg;
    try { msg = JSON.parse(data); } catch { return; }
    const payload = { user: { id: Number(senderId) }, content: msg.content, type: msg.type || 'text', media: msg.preview ? { preview: msg.preview } : undefined };
    // store
    const emails = db.users.filter(u => u.id === Number(senderId) || u.id === Number(receiverId)).map(u => u.email);
    const convoKey = emails.sort().join('|');
    const arr = db.conversations.get(convoKey) || [];
    arr.push({ user: { id: Number(senderId) }, content: msg.content, type: msg.type || 'text' });
    db.conversations.set(convoKey, arr);
    // broadcast to room
    for (const client of clients.get(key) || []) {
      if (client.readyState === 1) client.send(JSON.stringify(payload));
    }
  });

  ws.on('close', () => {
    const set = clients.get(key);
    if (set) set.delete(ws);
  });
});

server.listen(PORT, () => {
  console.log(`API listening on http://localhost:${PORT}${API_PREFIX}`);
});


