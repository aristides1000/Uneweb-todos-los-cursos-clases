import fs from 'fs';
import path from 'path';

// Sistema RAG básico: Lee archivos locales en la carpeta /docs y busca coincidencias por palabras clave
export function buscarDocumentacionRAG(query) {
    const docsDir = path.join(process.cwd(), 'docs');
    if (!fs.existsSync(docsDir)) return 'No hay documentación local disponible.';

    const archivos = fs.readdirSync(docsDir);
    let contextoRelevante = '';

    const keywords = query.toLowerCase().split(' ').filter(w => w.length > 3);

    for (const archivo of archivos) {
        if (archivo.endsWith('.md') || archivo.endsWith('.txt')) {
            const contenido = fs.readFileSync(path.join(docsDir, archivo), 'utf8');

            const matchCount = keywords.filter(kw => contenido.toLowerCase().includes(kw)).length;

            if (matchCount > 0 || query.toLowerCase().includes('php') || query.toLowerCase().includes('seguridad')) {
                contextoRelevante += `\n--- Documento: ${archivo} ---\n${contenido}\n`;
            }
        }
    }

    return contextoRelevante || 'No se encontró documentación interna específica para esta consulta.';
}