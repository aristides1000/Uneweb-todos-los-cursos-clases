import pkg from 'pg';
const { Pool } = pkg;

// Configuración del pool de conexiones a PostgreSQL
export const pool = new Pool({
    connectionString: process.env.DATABASE_URL || 'postgres://postgres:tu_contrasena@localhost:5432/devassist_db'
});

// Definición de las funciones (Tools) que la IA puede invocar mediante Function Calling
export const dbToolsDefinitions = [
    {
        name: "consultarClientesActivos",
        description: "Consulta la lista de clientes activos en la base de datos PostgreSQL.",
        parameters: {
            type: "OBJECT",
            properties: {}
        }
    },
    {
        name: "consultarProyectosPorTecnologia",
        description: "Busca proyectos de desarrollo registrados según la tecnología utilizada (ej. PHP, Node.js).",
        parameters: {
            type: "OBJECT",
            properties: {
                tecnologia: {
                    type: "STRING",
                    description: "Nombre de la tecnología a buscar (PHP, Node.js, etc.)"
                }
            },
            required: ["tecnologia"]
        }
    }
];

// Implementación real de las funciones ejecutadas cuando la IA lo solicita
export async function ejecutarFuncionBD(nombreFuncion, argumentos) {
    try {
        if (nombreFuncion === "consultarClientesActivos") {
            const res = await pool.query("SELECT id, nombre, email, creado_en FROM clientes WHERE activo = true");
            return JSON.stringify(res.rows);
        } else if (nombreFuncion === "consultarProyectosPorTecnologia") {
            const tecnologia = argumentos.tecnologia || '';
            const res = await pool.query(
                "SELECT p.id, p.nombre_proyecto, p.tecnologia, p.estado, c.nombre as cliente FROM proyectos p JOIN clientes c ON p.cliente_id = c.id WHERE p.tecnologia ILIKE $1",
                [`%${tecnologia}%`]
            );
            return JSON.stringify(res.rows);
        }
        return JSON.stringify({ error: "Función no encontrada" });
    } catch (err) {
        console.error("Error ejecutando función de BD:", err);
        return JSON.stringify({ error: err.message });
    }
}
