import fs from 'node:fs/promises';
import path from 'node:path';
import { pool } from './db.js';
import { askGemini } from './gemini.js';

const MD_DIR = path.resolve('data/md');

// Lista los archivos .md disponibles en data/md
export async function listMarkdownFiles() {
  await fs.mkdir(MD_DIR, { recursive: true });
  const files = await fs.readdir(MD_DIR);
  return files.filter((file) => file.toLowerCase().endsWith('.md'));
}

// Obtiene el contenido de un .md desde PostgreSQL (y si no está ahí, lo lee desde data/md)
async function getDocumentContent(filename) {
  const dbResult = await pool.query(
    'SELECT content FROM documents WHERE filename = $1 LIMIT 1',
    [filename]
  );

  if (dbResult.rows.length > 0 && dbResult.rows[0].content) {
    return dbResult.rows[0].content;
  }

  const filePath = path.join(MD_DIR, filename);
  return fs.readFile(filePath, 'utf-8');
}

// Busca un fragmento útil del documento según las palabras clave de la pregunta
function extractRelevantExcerpt(text, question, maxChars = 12000) {
  if (text.length <= maxChars) {
    return text;
  }

  const words = question
    .toLowerCase()
    .split(/[^a-záéíóúñ0-9_]+/i)
    .filter((w) => w.length >= 4);

  const paragraphs = text.split(/\n\s*\n/);
  const scored = paragraphs.map((paragraph, index) => {
    const lower = paragraph.toLowerCase();
    let score = 0;
    for (const word of words) {
      if (lower.includes(word)) {
        score += 1;
      }
    }
    return { paragraph, index, score };
  });

  // Ordenamos los párrafos con más coincidencias y conservamos un orden legible
  const bestParagraphs = scored
    .filter((item) => item.score > 0)
    .sort((a, b) => b.score - a.score || a.index - b.index);

  let selected = '';
  for (const item of bestParagraphs) {
    if ((selected + '\n\n' + item.paragraph).length > maxChars) break;
    selected += (selected ? '\n\n' : '') + item.paragraph;
  }

  // Si no hubo coincidencias claras, devolvemos el inicio del documento
  return selected || text.slice(0, maxChars);
}

/**
 * Responde una pregunta usando un archivo .md como fuente de información.
 * @param {string} filename - Nombre del archivo .md (ej. "Aprendizaje-PHP.md").
 * @param {string} question - Pregunta del estudiante o desarrollador.
 */
export async function askAboutDocument(filename, question) {
  const fullContent = await getDocumentContent(filename);
  const excerpt = extractRelevantExcerpt(fullContent, question);

  const prompt = `
Documento: ${filename}

Fragmento de referencia:
"""
${excerpt}
"""

Pregunta del usuario:
${question}
`.trim();

  const answer = await askGemini(prompt, {
    systemInstruction: `
Eres un asistente técnico para estudiantes de programación.
Responde la pregunta usando principalmente el "Fragmento de referencia" entregado.
Si la respuesta no aparece claramente en el fragmento, indícalo con honestidad antes de dar una explicación general.
Explica de forma clara, ordenada y con ejemplos sencillos cuando ayude.
`.trim()
  });

  return answer;
}
