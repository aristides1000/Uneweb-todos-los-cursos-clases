import 'dotenv/config';
import path from 'node:path';

function requiredEnv(name, fallback = undefined) {
  const value = process.env[name] ?? fallback;
  if (value === undefined || String(value).trim() === '') {
    throw new Error(`Falta la variable de entorno obligatoria: ${name}`);
  }
  return value;
}

export const env = {
  nodeEnv: process.env.NODE_ENV || 'development',
  port: Number(process.env.PORT || 3000),
  databaseUrl: requiredEnv('DATABASE_URL'),
  readonlyDatabaseUrl: process.env.READONLY_DATABASE_URL || requiredEnv('DATABASE_URL'),
  paths: {
    rawPdfsDir: path.resolve(process.env.RAW_PDFS_DIR || './data/raw-pdfs'),
    processedMdDir: path.resolve(process.env.PROCESSED_MD_DIR || './data/processed-md'),
    extraDocsDir: path.resolve(process.env.EXTRA_DOCS_DIR || './data/extra-docs')
  },
  gemini: {
    apiKey: process.env.GEMINI_API_KEY || '',
    model: process.env.GEMINI_MODEL || 'gemini-2.5-flash',
    embeddingModel: process.env.GEMINI_EMBEDDING_MODEL || 'text-embedding-004'
  }
};