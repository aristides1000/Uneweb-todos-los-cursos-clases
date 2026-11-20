// Middleware de seguridad y saneamiento contra Prompt Injection
export function sanitizerMiddleware(req, res, next) {
    const { prompt, code } = req.body;

    const maliciousPatterns = [
        /ignora (todas |tus |las )?instrucciones/i,
        /ignore (all |previous )?instructions/i,
        /muestrame las claves/i,
        /system prompt/i,
        /borrar base de datos/i,
        /drop table/i,
        /truncate/i
    ];

    const textToCheck = (prompt || '') + ' ' + (code || '');

    for (const pattern of maliciousPatterns) {
        if (pattern.test(textToCheck)) {
            console.warn(`[SEGURIDAD] Intento de Prompt Injection detectado: "${textToCheck}"`);
            return res.status(403).json({
                success: false,
                error: 'Solicitud bloqueada por motivos de seguridad: Se detectó un patrón prohibido o intento de manipulación del sistema.'
            });
        }
    }

    next();
}
