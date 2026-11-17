import express from 'express';
import cors from 'cors';
import dotenv from 'dotenv';
import { GoogleGenAI } from '@google/genai';

// Cargar variables de entorno del archivo .env
dotenv.config();

const app = express();
const port = process.env.PORT || 3000;

// Middlewares
app.use(cors());
app.use(express.json());
app.use(express.static('public')); // Servir archivos estáticos del frontend

// Inicializar el cliente oficial de Google GenAI
const ai = new GoogleGenAI({ apiKey: process.env.GEMINI_API_KEY });

// Endpoint para procesar código y consultas técnicas
app.post('/api/analyze', async (req, res) => {
    try {
        const { prompt, code } = req.body;

        const fullPrompt = `
            Actúa como un Senior Software Engineer y experto en desarrollo.
            El usuario ha enviado la siguiente consulta o código:
            "${prompt}"
            
            Código adjunto (si lo hay):
            \`\`\`
            ${code || 'No hay código adjunto'}
            \`\`\`
            
            Proporciona una respuesta estructurada que incluya:
            1. Explicación técnica (NLP).
            2. Detección de posibles errores o mejoras de seguridad.
            3. Sugerencia de refactorización o buenas prácticas.
        `;

        // Llamada al modelo Gemini con el modelo estable y alta disponibilidad
        const response = await ai.models.generateContent({
            model: 'gemini-3.5-flash-lite',
            contents: fullPrompt,
        });

        // Enviar la respuesta utilizando la propiedad directa .text
        res.json({ success: true, analysis: response.text });

    } catch (error) {
        console.error('Error al conectar con la API de Gemini:', error);
        res.status(500).json({ success: false, error: 'Ocurrió un error al procesar la solicitud con la IA.' });
    }
});

app.listen(port, () => {
    console.log(`Servidor de DevAssist corriendo en http://localhost:${port}`);
});