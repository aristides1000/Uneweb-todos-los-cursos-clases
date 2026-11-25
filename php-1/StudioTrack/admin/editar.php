<?php
$ruta_base = "../";
require_once '../config/conexion.php';
require_once '../includes/datos_estaticos.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
    header("Location: index.php");
    exit;
}

$error = "";

// Procesar actualización mediante $_POST
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $codigo    = strtoupper(trim($_POST['codigo'] ?? ''));
    $nombre    = trim($_POST['nombre'] ?? '');
    $categoria = trim($_POST['categoria'] ?? '');
    $tarifa    = isset($_POST['tarifa_dia']) ? (float)$_POST['tarifa_dia'] : -1;
    $stock     = isset($_POST['stock']) ? (int)$_POST['stock'] : -1;
    $estado    = trim($_POST['estado'] ?? 'Disponible');

    if ($codigo === "" || $nombre === "" || !in_array($categoria, $categorias_equipos) || !in_array($estado, $estados_equipos) || $tarifa < 0 || $stock < 0) {
        $error = "Verifica que todos los campos sean válidos antes de guardar.";
    } else {
        $codigo_sql    = mysqli_real_escape_string($conexion, $codigo);
        $nombre_sql    = mysqli_real_escape_string($conexion, $nombre);
        $categoria_sql = mysqli_real_escape_string($conexion, $categoria);
        $estado_sql    = mysqli_real_escape_string($conexion, $estado);

        $sql_update = "UPDATE equipos
                       SET codigo = '$codigo_sql',
                           nombre = '$nombre_sql',
                           categoria = '$categoria_sql',
                           tarifa_dia = $tarifa,
                           stock = $stock,
                           estado = '$estado_sql'
                       WHERE id = $id";

        if (mysqli_query($conexion, $sql_update)) {
            header("Location: index.php");
            exit;
        } else {
            $error = "Error al actualizar: " . mysqli_error($conexion);
        }
    }
}

// Consultar datos actuales del equipo
$sql_buscar = "SELECT * FROM equipos WHERE id = $id";
$res_buscar = mysqli_query($conexion, $sql_buscar);
$equipo     = mysqli_fetch_assoc($res_buscar);

if (!$equipo) {
    header("Location: index.php");
    exit;
}

include '../includes/header.php';
?>

<div class="card">
    <h2>Editar Equipo #<?php echo (int)$equipo['id']; ?></h2>

    <?php if ($error !== ""): ?>
        <div style="padding: 0.8rem 1rem; border-radius: 6px; margin-bottom: 1rem; background: #991b1b; color: #fff;">
            <?php echo htmlspecialchars($error); ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="editar.php?id=<?php echo (int)$equipo['id']; ?>">
        <label for="codigo">Código único</label>
        <input type="text" name="codigo" id="codigo" value="<?php echo htmlspecialchars($equipo['codigo']); ?>" required>

        <label for="nombre">Nombre o modelo del equipo</label>
        <input type="text" name="nombre" id="nombre" value="<?php echo htmlspecialchars($equipo['nombre']); ?>" required>

        <label for="categoria">Categoría</label>
        <select name="categoria" id="categoria" required>
            <?php foreach ($categorias_equipos as $cat): ?>
                <option value="<?php echo htmlspecialchars($cat); ?>" <?php echo ($equipo['categoria'] === $cat) ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($cat); ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label for="tarifa_dia">Tarifa por día ($)</label>
        <input type="number" step="0.01" min="0" name="tarifa_dia" id="tarifa_dia" value="<?php echo htmlspecialchars($equipo['tarifa_dia']); ?>" required>

        <label for="stock">Cantidad en stock</label>
        <input type="number" min="0" name="stock" id="stock" value="<?php echo (int)$equipo['stock']; ?>" required>

        <label for="estado">Estado</label>
        <select name="estado" id="estado" required>
            <?php foreach ($estados_equipos as $est): ?>
                <option value="<?php echo htmlspecialchars($est); ?>" <?php echo ($equipo['estado'] === $est) ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($est); ?>
                </option>
            <?php endforeach; ?>
        </select>

        <button type="submit" class="btn btn-warning">Guardar Cambios</button>
        <a href="index.php" class="btn" style="background: #475569;">Cancelar</a>
    </form>
</div>

<?php include '../includes/footer.php'; ?>
