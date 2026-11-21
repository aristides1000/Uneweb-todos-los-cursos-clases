-- Tabla para guardar los documentos procesados (por ejemplo, los .md convertidos desde PDF)
CREATE TABLE IF NOT EXISTS documents (
  id SERIAL PRIMARY KEY,
  filename TEXT NOT NULL UNIQUE,
  content TEXT NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabla sencilla para registrar consultas realizadas por el asistente SQL
CREATE TABLE IF NOT EXISTS sql_logs (
  id SERIAL PRIMARY KEY,
  question TEXT NOT NULL,
  sql_query TEXT NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
