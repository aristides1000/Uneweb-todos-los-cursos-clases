<?php
$ruta_base = "../";
require_once '../config/conexion.php';
require_once '../includes/datos_estaticos.php';
include '../includes/header.php';

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $codigo    = strtoupper(trim($_POST['codigo'] ?? ''));
    $nombre    = trim($_POST['nombre'] ?? '');
    $categoria = trim($_POST['categoria'] ?? '');
    $tarifa    = isset($_POST['tarifa_dia']) ? (float)$_POST['tarifa_dia'] : -1;
    $stock     = isset($_POST['stock']) ? (int)$_POST['stock'] : -1;
    $estado    = trim($_POST['estado'] ?? 'Disponible');

    // Validación básica con operadores lógicos
    if ($codigo === "" || $nombre === "" || !in_array($categoria, $categorias_equipos) || !in_array($estado, $estados_equipos) || $tarifa < 0 || $stock < 0) {
        $error = "Revisa los campos: código, nombre, categoría, estado, tarifa (>= 0) y stock (>= 0) son obligatorios.";
    } else {
        $codigo_sql    = mysqli_real_escape_string($conexion, $codigo);
        $nombre_sql    = mysqli_real_escape_string($conexion, $nombre);
        $categoria_sql = mysqli_real_escape_string($conexion, $categoria);
        $estado_sql    = mysqli_real_escape_string($conexion, $estado);

        $sql_insert = "INSERT INTO equipos (codigo, nombre, categoria, tarifa_dia, stock, estado)
                       VALUES ('$codigo_sql', '$nombre_sql', '$categoria_sql', $tarifa, $stock, '$estado_sql')";

        if (mysqli_query($conexion, $sql_insert)) {
            header("Location: index.php");
            exit;
        } else {
            $error = "Error al guardar el equipo (verifica que el código no esté repetido): " . mysqli_error($conexion);
        }
    }
}
?>

<div class="card">
    <h2>Registrar Nuevo Equipo</h2>

    <?php if ($error !== ""): ?>
        <div style="padding: 0.8rem 1rem; border-radius: 6px; margin-bottom: 1rem; background: #991b1b; color: #fff;">
            <?php echo htmlspecialchars($error); ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="crear.php">
        <label for="codigo">Código único (Ej: CAM-005)</label>
        <input type="text" name="codigo" id="codigo" value="<?php echo isset($_POST['codigo']) ? htmlspecialchars($_POST['codigo']) : ''; ?>" required>

        <label for="nombre">Nombre o modelo del equipo</label>
        <input type="text" name="nombre" id="nombre" value="<?php echo isset($_POST['nombre']) ? htmlspecialchars($_POST['nombre']) : ''; ?>" required>

        <label for="categoria">Categoría</label>
        <select name="categoria" id="categoria" required>
            <option value="">-- Selecciona --</option>
            <?php foreach ($categorias_equipos as $cat): ?>
                <option value="<?php echo htmlspecialchars($cat); ?>" <?php echo (($_POST['categoria'] ?? '') === $cat) ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($cat); ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label for="tarifa_dia">Tarifa por día ($)</label>
        <input type="number" step="0.01" min="0" name="tarifa_dia" id="tarifa_dia" value="<?php echo isset($_POST['tarifa_dia']) ? htmlspecialchars($_POST['tarifa_dia']) : '0.00'; ?>" required>

        <label for="stock">Cantidad en stock</label>
        <input type="number" min="0" name="stock" id="stock" value="<?php echo isset($_POST['stock']) ? (int)$_POST['stock'] : 1; ?>" required>

        <label for="estado">Estado</label>
        <select name="estado" id="estado" required>
            <?php foreach ($estados_equipos as $est): ?>
                <option value="<?php echo htmlspecialchars($est); ?>" <?php echo (($_POST['estado'] ?? 'Disponible') === $est) ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($est); ?>
                </option>
            <?php endforeach; ?>
        </select>

        <button type="submit" class="btn" style="background: #059669;">Guardar Equipo</button>
        <a href="index.php" class="btn" style="background: #475569;">Cancelar</a>
    </form>
</div>

<?php include '../includes/footer.php'; ?>
