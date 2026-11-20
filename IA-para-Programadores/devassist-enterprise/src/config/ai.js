import { GoogleGenAI } from '@google/genai';
import { env } from './env.js';

let aiClient = null;

export function getGeminiClient() {
  if (!env.gemini.apiKey) {
    throw new Error('GEMINI_API_KEY no está configurada en el archivo .env');
  }
  if (!aiClient) {
    aiClient = new GoogleGenAI({ apiKey: env.gemini.apiKey });
  }
  return aiClient;
}

export const SYSTEM_INSTRUCTION = `
Eres DevAssist Enterprise, un asistente técnico de ingeniería de software.
Reglas obligatorias:
1. Basa tus respuestas en la documentación técnica local recuperada y en los resultados reales de consultas de solo lectura a PostgreSQL.
2. Nunca inventes tablas, columnas ni comportamientos de código que no estén respaldados por las fuentes consultadas.
3. Cuando cites documentación, indica el documento y el capítulo correspondiente.
4. Cuando ejecutes consultas de base de datos, limita la exposición a los datos estrictamente necesarios para responder la pregunta técnica.
`.trim();