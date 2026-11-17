import { GoogleGenAI } from '@google/genai';
import dotenv from 'dotenv';
dotenv.config();

const ai = new GoogleGenAI({ apiKey: process.env.GEMINI_API_KEY });

async function probarConexion() {
    try {
        const response = await ai.models.generateContent({
            model: 'gemini-3.5-flash-lite',
            contents: 'Hola, responde solo con la palabra: Funciona',
        });
        // Aquí quitamos los paréntesis: response.text en lugar de response.text()
        console.log('¡Prueba exitosa!', response.text);
    } catch (error) {
        console.error('Error detallado:', error);
    }
}

probarConexion();