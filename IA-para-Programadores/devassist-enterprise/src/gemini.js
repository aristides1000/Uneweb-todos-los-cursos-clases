import 'dotenv/config';
import { GoogleGenAI } from '@google/genai';

if (!process.env.GEMINI_API_KEY) {
  throw new Error('Falta GEMINI_API_KEY en el archivo .env');
}

// Creamos el cliente oficial de Google Gemini con nuestra clave del .env
const ai = new GoogleGenAI({
  apiKey: process.env.GEMINI_API_KEY
});

// Modelo por defecto (puede cambiarse desde el archivo .env)
const DEFAULT_MODEL = process.env.GEMINI_MODEL || 'gemini-2.5-flash';

/**
 * Envía un prompt a Gemini y devuelve solo el texto de respuesta.
 * @param {string} prompt - Pregunta o instrucción para el modelo.
 * @param {object} options - Opciones extra (instrucción del sistema o modelo).
 */
export async function askGemini(prompt, options = {}) {
  const response = await ai.models.generateContent({
    model: options.model || DEFAULT_MODEL,
    contents: prompt,
    config: {
      systemInstruction:
        options.systemInstruction ||
        'Eres un asistente técnico claro, paciente y didáctico para estudiantes de programación.'
    }
  });

  return response.text;
}
