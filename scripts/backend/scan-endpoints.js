const fs = require('fs');
const path = require('path');

const projectRoot = path.resolve(__dirname, '..', '..', 'FirstDate');
const apiDir = path.join(projectRoot, 'src', 'api');
const outputFile = path.join(projectRoot, 'docs', 'api-endpoints.json');

function readRecursive(dir) {
  const entries = fs.readdirSync(dir, { withFileTypes: true });
  return entries.flatMap((entry) => {
    const full = path.join(dir, entry.name);
    if (entry.isDirectory()) return readRecursive(full);
    if (entry.isFile() && entry.name.endsWith('.js')) return [full];
    return [];
  });
}

function extractEndpoints(content) {
  const httpMatches = [];
  const wsMatches = [];
  const httpRegex = /\$\{Server\.endpoint\}([^"'`\)\s]*)/g;
  const wsRegex = /\$\{SOCKET_URL\}([^"'`\)\s]*)/g;
  let m;
  while ((m = httpRegex.exec(content))) {
    httpMatches.push(m[1]);
  }
  while ((m = wsRegex.exec(content))) {
    wsMatches.push(m[1]);
  }
  return { http: httpMatches, ws: wsMatches };
}

function main() {
  const files = readRecursive(apiDir);
  const result = { http: new Set(), ws: new Set(), byFile: {} };
  for (const file of files) {
    const content = fs.readFileSync(file, 'utf8');
    const { http, ws } = extractEndpoints(content);
    result.byFile[path.relative(projectRoot, file)] = { http, ws };
    http.forEach((p) => result.http.add(p));
    ws.forEach((p) => result.ws.add(p));
  }
  const output = {
    discoveredAt: new Date().toISOString(),
    baseHttp: '${Server.endpoint}',
    baseWs: '${Server.socketEndpoint}',
    httpPaths: Array.from(result.http).sort(),
    wsPaths: Array.from(result.ws).sort(),
    byFile: result.byFile,
  };
  fs.mkdirSync(path.dirname(outputFile), { recursive: true });
  fs.writeFileSync(outputFile, JSON.stringify(output, null, 2));
  console.log('Wrote', path.relative(projectRoot, outputFile));
}

main();


