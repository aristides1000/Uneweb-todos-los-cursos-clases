<?php
$ruta_base = "../";
require_once '../config/conexion.php';
include '../includes/header.php';

// Buscador de 1 campo por código usando $_GET
$buscar_codigo = isset($_GET['codigo']) ? trim($_GET['codigo']) : '';

if ($buscar_codigo !== "") {
    $codigo_seguro = mysqli_real_escape_string($conexion, $buscar_codigo);
    $sql = "SELECT * FROM equipos WHERE codigo LIKE '%$codigo_seguro%' ORDER BY id DESC";
} else {
    $sql = "SELECT * FROM equipos ORDER BY id DESC";
}

$resultado = mysqli_query($conexion, $sql);
?>

<div class="card">
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
        <div>
            <h2>Panel de Administración — Inventario</h2>
            <p style="margin: 0; color: #94a3b8;">Gestión CRUD y búsqueda de 1 campo (por código).</p>
        </div>
        <a href="crear.php" class="btn" style="background: #059669;">+ Nuevo Equipo</a>
    </div>

    <!-- Buscador de 1 campo con método GET -->
    <form method="GET" action="index.php" style="display: flex; gap: 0.75rem; align-items: end; margin-top: 1.2rem;">
        <div style="flex: 1;">
            <label for="codigo">Buscar por 1 campo (Código de inventario)</label>
            <input type="text" name="codigo" id="codigo" placeholder="Ej: CAM-001" value="<?php echo htmlspecialchars($buscar_codigo); ?>" style="margin-bottom: 0;">
        </div>
        <button type="submit" class="btn">Buscar</button>
        <a href="index.php" class="btn" style="background: #475569;">Ver todos</a>
    </form>

    <?php if ($resultado && mysqli_num_rows($resultado) > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Código</th>
                    <th>Nombre</th>
                    <th>Categoría</th>
                    <th>Tarifa/Día</th>
                    <th>Stock</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($fila = mysqli_fetch_assoc($resultado)): ?>
                    <tr>
                        <td><?php echo (int)$fila['id']; ?></td>
                        <td><strong><?php echo htmlspecialchars($fila['codigo']); ?></strong></td>
                        <td><?php echo htmlspecialchars($fila['nombre']); ?></td>
                        <td><?php echo htmlspecialchars($fila['categoria']); ?></td>
                        <td>$<?php echo number_format((float)$fila['tarifa_dia'], 2); ?></td>
                        <td><?php echo (int)$fila['stock']; ?></td>
                        <td>
                            <span class="badge <?php echo ($fila['estado'] === 'Disponible') ? 'disp' : 'alq'; ?>">
                                <?php echo htmlspecialchars($fila['estado']); ?>
                            </span>
                        </td>
                        <td style="white-space: nowrap;">
                            <a href="editar.php?id=<?php echo (int)$fila['id']; ?>" class="btn btn-warning">Editar</a>
                            <a href="eliminar.php?id=<?php echo (int)$fila['id']; ?>" class="btn btn-danger" onclick="return confirm('¿Seguro que deseas eliminar este equipo?');">Eliminar</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p style="margin-top: 1.5rem; color: #cbd5e1;">No se encontraron registros con ese criterio.</p>
    <?php endif; ?>
</div>

<?php include '../includes/footer.php'; ?>
