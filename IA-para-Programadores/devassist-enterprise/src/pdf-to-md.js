import fs from 'node:fs/promises';
import path from 'node:path';
import pdfParse from 'pdf-parse';
import { pool } from './db.js';

const PDF_DIR = path.resolve('data/pdfs');
const MD_DIR = path.resolve('data/md');

// Limpieza sencilla para ordenar líneas vacías excesivas y espacios raros
function cleanExtractedText(rawText) {
  return rawText
    .replace(/\u0000/g, '') // Elimina caracteres nulos que PostgreSQL rechaza en UTF-8
    .replace(/[\x00-\x08\x0B\x0C\x0E-\x1F]/g, '') // Quita otros caracteres de control invisibles
    .replace(/\r\n/g, '\n')
    .replace(/[ \t]+\n/g, '\n')
    .replace(/\n{3,}/g, '\n\n')
    .trim();
}

// Convierte un archivo PDF a texto Markdown limpio
export async function convertSinglePdfToMd(pdfFileName) {
  const pdfPath = path.join(PDF_DIR, pdfFileName);
  const baseName = path.basename(pdfFileName, path.extname(pdfFileName));
  const mdFileName = `${baseName}.md`;
  const mdPath = path.join(MD_DIR, mdFileName);

  // 1. Leer el PDF y extraer su texto
  const buffer = await fs.readFile(pdfPath);
  const parsed = await pdfParse(buffer);
  const cleanedText = cleanExtractedText(parsed.text || '');

  if (!cleanedText) {
    throw new Error(`El PDF "${pdfFileName}" no contiene texto extraíble.`);
  }

  // 2. Armar un contenido Markdown sencillo con título y texto extraído
  const mdContent = `# ${baseName}\n\n${cleanedText}\n`;

  // 3. Asegurar que exista data/md y guardar el archivo .md
  await fs.mkdir(MD_DIR, { recursive: true });
  await fs.writeFile(mdPath, mdContent, 'utf-8');

  // 4. Guardar o actualizar el documento en PostgreSQL
  await pool.query(
    `INSERT INTO documents (filename, content)
     VALUES ($1, $2)
     ON CONFLICT (filename)
     DO UPDATE SET content = EXCLUDED.content`,
    [mdFileName, mdContent]
  );

  return {
    pdfFileName,
    mdFileName,
    mdPath,
    pages: parsed.numpages
  };
}

// Convierte todos los PDFs que estén dentro de data/pdfs
export async function convertAllPdfs() {
  await fs.mkdir(PDF_DIR, { recursive: true });
  const files = await fs.readdir(PDF_DIR);
  const pdfFiles = files.filter((file) => file.toLowerCase().endsWith('.pdf'));

  if (pdfFiles.length === 0) {
    return [];
  }

  const results = [];
  for (const file of pdfFiles) {
    const info = await convertSinglePdfToMd(file);
    results.push(info);
  }

  return results;
}

// Permite ejecutar este archivo directamente con: npm run convert
const isDirectRun =
  process.argv[1] && path.resolve(process.argv[1]) === path.resolve(import.meta.url.replace('file://', ''));

if (isDirectRun) {
  convertAllPdfs()
    .then((results) => {
      if (results.length === 0) {
        console.log('No se encontraron archivos .pdf en data/pdfs/');
      } else {
        console.log('PDFs convertidos con éxito:');
        for (const r of results) {
          console.log(`- ${r.pdfFileName} (${r.pages} págs.) -> data/md/${r.mdFileName}`);
        }
      }
      process.exit(0);
    })
    .catch((err) => {
      console.error('Error al convertir PDFs:', err.message);
      process.exit(1);
    });
}