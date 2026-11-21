import http from 'node:http';
import fs from 'node:fs/promises';
import path from 'node:path';
import { testDbConnection } from './db.js';
import { convertAllPdfs } from './pdf-to-md.js';
import { askAboutDocument, listMarkdownFiles } from './docs-chat.js';
import { askDatabase } from './sql-chat.js';

const PORT = process.env.PORT || 3000;
const HTML_PATH = path.resolve('public/index.html');

// Lee el cuerpo JSON enviado desde el navegador
async function readJsonBody(req) {
  let body = '';
  for await (const chunk of req) {
    body += chunk;
  }
  return body ? JSON.parse(body) : {};
}

// Envía una respuesta JSON ordenada
function sendJson(res, statusCode, data) {
  res.writeHead(statusCode, { 'Content-Type': 'application/json; charset=utf-8' });
  res.end(JSON.stringify(data));
}

const server = http.createServer(async (req, res) => {
  try {
    // 1. Servir la página web principal (un solo archivo HTML)
    if (req.method === 'GET' && req.url === '/') {
      const html = await fs.readFile(HTML_PATH, 'utf-8');
      res.writeHead(200, { 'Content-Type': 'text/html; charset=utf-8' });
      res.end(html);
      return;
    }

    // 2. Probar conexión con PostgreSQL
    if (req.method === 'GET' && req.url === '/api/test-db') {
      const info = await testDbConnection();
      sendJson(res, 200, { ok: true, serverTime: info.hora_servidor });
      return;
    }

    // 3. Convertir PDFs de data/pdfs a data/md
    if (req.method === 'POST' && req.url === '/api/convert') {
      const results = await convertAllPdfs();
      sendJson(res, 200, { ok: true, results });
      return;
    }

    // 4. Listar archivos .md disponibles
    if (req.method === 'GET' && req.url === '/api/docs') {
      const files = await listMarkdownFiles();
      sendJson(res, 200, { ok: true, files });
      return;
    }

    // 5. Preguntar a Gemini sobre un archivo .md
    if (req.method === 'POST' && req.url === '/api/ask-doc') {
      const { filename, question } = await readJsonBody(req);
      if (!filename || !question?.trim()) {
        sendJson(res, 400, { ok: false, error: 'Debes seleccionar un documento y escribir una pregunta.' });
        return;
      }
      const answer = await askAboutDocument(filename, question.trim());
      sendJson(res, 200, { ok: true, answer });
      return;
    }

    // 6. Preguntar a Gemini sobre la base de datos PostgreSQL
    if (req.method === 'POST' && req.url === '/api/ask-sql') {
      const { question } = await readJsonBody(req);
      if (!question?.trim()) {
        sendJson(res, 400, { ok: false, error: 'Debes escribir una pregunta sobre la base de datos.' });
        return;
      }
      const result = await askDatabase(question.trim());
      sendJson(res, 200, { ok: true, ...result });
      return;
    }

    // Ruta no encontrada
    sendJson(res, 404, { ok: false, error: 'Ruta no encontrada' });
  } catch (error) {
    sendJson(res, 500, { ok: false, error: error.message });
  }
});

server.listen(PORT, () => {
  console.log(`\n🚀 DevAssist Enterprise listo en: http://localhost:${PORT}\n`);
});