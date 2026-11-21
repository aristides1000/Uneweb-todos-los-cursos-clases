import 'dotenv/config';
import pg from 'pg';

const { Pool } = pg;

if (!process.env.DATABASE_URL) {
  throw new Error('Falta DATABASE_URL en el archivo .env');
}

export const pool = new Pool({
  connectionString: process.env.DATABASE_URL
});

// Función auxiliar sencilla para probar si la conexión funciona
export async function testDbConnection() {
  const client = await pool.connect();
  try {
    const result = await client.query('SELECT NOW() AS hora_servidor');
    return result.rows[0];
  } finally {
    client.release();
  }
}
