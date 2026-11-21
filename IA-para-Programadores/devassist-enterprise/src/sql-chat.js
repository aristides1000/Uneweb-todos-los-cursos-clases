import { pool } from './db.js';
import { askGemini } from './gemini.js';

// Consulta las tablas y columnas del esquema "public" para que Gemini conozca la estructura real
async function getDatabaseSchema() {
  const result = await pool.query(`
    SELECT table_name, column_name, data_type
    FROM information_schema.columns
    WHERE table_schema = 'public'
    ORDER BY table_name, ordinal_position
  `);

  const tables = {};
  for (const row of result.rows) {
    if (!tables[row.table_name]) {
      tables[row.table_name] = [];
    }
    tables[row.table_name].push(`${row.column_name} (${row.data_type})`);
  }

  return Object.entries(tables)
    .map(([table, cols]) => `- Tabla "${table}": ${cols.join(', ')}`)
    .join('\n');
}

// Limpia posibles bloques ```sql ... ``` que a veces agrega el modelo
function cleanSqlQuery(rawSql) {
  return rawSql
    .replace(/```sql/gi, '')
    .replace(/```/g, '')
    .trim()
    .replace(/;+$/, '');
}

// Revisa de forma sencilla que la consulta sea solo de lectura (SELECT)
function isSafeReadOnlyQuery(sql) {
  const normalized = sql.trim().toLowerCase();

  // Debe empezar por SELECT o WITH
  if (!normalized.startsWith('select') && !normalized.startsWith('with')) {
    return false;
  }

  // Evita múltiples sentencias encadenadas con punto y coma
  if (sql.includes(';')) {
    return false;
  }

  // Bloquea palabras clave de escritura o modificación de estructura
  const forbiddenWords = [
    'insert',
    'update',
    'delete',
    'drop',
    'alter',
    'truncate',
    'create',
    'grant',
    'revoke',
    'copy',
    'execute'
  ];

  for (const word of forbiddenWords) {
    const regex = new RegExp(`\\b${word}\\b`, 'i');
    if (regex.test(normalized)) {
      return false;
    }
  }

  return true;
}

/**
 * Convierte una pregunta en una consulta SELECT segura, la ejecuta y devuelve los datos.
 * @param {string} question - Pregunta del usuario sobre la base de datos.
 */
export async function askDatabase(question) {
  const schemaText = await getDatabaseSchema();

  const prompt = `
Esquema de la base de datos PostgreSQL:
${schemaText}

Pregunta del usuario:
${question}

Devuelve ÚNICAMENTE una consulta SQL de solo lectura (SELECT) válida para PostgreSQL.
No incluyas explicaciones ni bloques de código markdown.
`.trim();

  const rawSql = await askGemini(prompt, {
    systemInstruction:
      'Eres un generador de consultas SQL seguras de solo lectura (SELECT) para PostgreSQL.'
  });

  const sqlQuery = cleanSqlQuery(rawSql);

  if (!isSafeReadOnlyQuery(sqlQuery)) {
    throw new Error(`Consulta bloqueada por seguridad (solo se permiten consultas SELECT): ${sqlQuery}`);
  }

  // Ejecutamos la consulta en PostgreSQL
  const queryResult = await pool.query(sqlQuery);

  // Registramos la pregunta y la consulta SQL en sql_logs
  await pool.query(
    'INSERT INTO sql_logs (question, sql_query) VALUES ($1, $2)',
    [question, sqlQuery]
  );

  return {
    sqlQuery,
    rows: queryResult.rows,
    rowCount: queryResult.rowCount
  };
}
