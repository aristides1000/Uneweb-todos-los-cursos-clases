<?php
require_once 'config/conexion.php';
require_once 'includes/datos_estaticos.php';
include 'includes/header.php';

// Capturar filtros desde la URL usando $_GET (con validación básica)
$categoria_filtro = isset($_GET['categoria']) ? trim($_GET['categoria']) : '';
$busqueda_filtro  = isset($_GET['buscar']) ? trim($_GET['buscar']) : '';

// Construir consulta SQL base
$sql = "SELECT * FROM equipos WHERE 1=1";

// Operador lógico && y comparación != para filtrar por categoría si se seleccionó
if ($categoria_filtro != "" && in_array($categoria_filtro, $categorias_equipos)) {
    $cat_segura = mysqli_real_escape_string($conexion, $categoria_filtro);
    $sql .= " AND categoria = '$cat_segura'";
}

// Filtrar por código o nombre si el usuario escribió algo en el buscador
if ($busqueda_filtro != "") {
    $busq_segura = mysqli_real_escape_string($conexion, $busqueda_filtro);
    $sql .= " AND (codigo LIKE '%$busq_segura%' OR nombre LIKE '%$busq_segura%')";
}

$sql .= " ORDER BY nombre ASC";
$resultado = mysqli_query($conexion, $sql);
?>

<div class="card">
    <h2>Catálogo de Equipos</h2>
    <p>Filtra por categoría o busca por código/nombre usando parámetros <code>$_GET</code>.</p>

    <!-- Formulario con método GET para filtrar sin recargar estado POST -->
    <form method="GET" action="catalogo.php" style="display: grid; grid-template-columns: 1fr 1fr auto; gap: 1rem; align-items: end;">
        <div>
            <label for="categoria">Categoría</label>
            <select name="categoria" id="categoria">
                <option value="">Todas las categorías</option>
                <?php foreach ($categorias_equipos as $cat): ?>
                    <option value="<?php echo htmlspecialchars($cat); ?>" <?php echo ($categoria_filtro === $cat) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($cat); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div>
            <label for="buscar">Buscar por código o nombre</label>
            <input type="text" name="buscar" id="buscar" placeholder="Ej: CAM-001 o Sony" value="<?php echo htmlspecialchars($busqueda_filtro); ?>">
        </div>

        <div style="margin-bottom: 1rem;">
            <button type="submit" class="btn">Filtrar</button>
            <a href="catalogo.php" class="btn" style="background:#475569;">Limpiar</a>
        </div>
    </form>
</div>

<?php if ($resultado && mysqli_num_rows($resultado) > 0): ?>
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1rem;">
        <?php while ($equipo = mysqli_fetch_assoc($resultado)): ?>
            <?php
            // Estructura switch para definir color/etiqueta según estado
            switch ($equipo['estado']) {
                case 'Disponible':
                    $clase_badge = 'disp';
                    break;
                case 'Alquilado':
                    $clase_badge = 'alq';
                    break;
                default:
                    $clase_badge = 'alq';
                    break;
            }

            // Operador lógico && para verificar disponibilidad real
            $puede_alquilar = ($equipo['estado'] === 'Disponible' && (int)$equipo['stock'] > 0);
            ?>
            <div class="card">
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <small style="color: #94a3b8;"><?php echo htmlspecialchars($equipo['codigo']); ?></small>
                    <span class="badge <?php echo $clase_badge; ?>">
                        <?php echo htmlspecialchars($equipo['estado']); ?>
                    </span>
                </div>

                <h3 style="margin: 0.5rem 0;"><?php echo htmlspecialchars($equipo['nombre']); ?></h3>
                <p style="color: #cbd5e1; margin: 0.25rem 0;">Categoría: <strong><?php echo htmlspecialchars($equipo['categoria']); ?></strong></p>
                <p style="color: #38bdf8; font-size: 1.1rem; font-weight: bold; margin: 0.5rem 0;">
                    $<?php echo number_format((float)$equipo['tarifa_dia'], 2); ?> <span style="font-size: 0.8rem; color: #94a3b8;">/ día</span>
                </p>
                <p style="font-size: 0.9rem; color: #94a3b8;">Unidades disponibles: <?php echo (int)$equipo['stock']; ?></p>

                <?php if ($puede_alquilar): ?>
                    <a href="cotizar.php?equipo_id=<?php echo (int)$equipo['id']; ?>" class="btn">Cotizar este equipo</a>
                <?php else: ?>
                    <button type="button" class="btn" style="background:#334155; cursor:not-allowed;" disabled>No disponible</button>
                <?php endif; ?>
            </div>
        <?php endwhile; ?>
    </div>
<?php else: ?>
    <div class="card">
        <p>No se encontraron equipos con los criterios seleccionados.</p>
    </div>
<?php endif; ?>

<?php include 'includes/footer.php'; ?>
