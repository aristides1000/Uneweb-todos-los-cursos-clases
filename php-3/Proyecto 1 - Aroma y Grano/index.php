<?php
require_once 'conexion.php';
require_once 'funciones.php';

// Capturar parámetros de búsqueda y filtros
$busqueda = isset($_GET['q']) ? limpiar_dato($_GET['q']) : '';
$tueste = isset($_GET['tueste']) ? limpiar_dato($_GET['tueste']) : '';

// Consulta dinámica con filtros
$sql = "SELECT * FROM productos WHERE 1=1";
$params = [];

if (!empty($busqueda)) {
    $sql .= " AND (nombre LIKE ? OR descripcion LIKE ? OR origen LIKE ?)";
    $params[] = "%$busqueda%";
    $params[] = "%$busqueda%";
    $params[] = "%$busqueda%";
}

if (!empty($tueste)) {
    $sql .= " AND tueste = ?";
    $params[] = $tueste;
}

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$productos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aroma y Grano - Café de Especialidad</title>
    <link rel="stylesheet" href="assets/css/estilos.css">
</head>
<body>

    <header>
        <h1>Aroma <span>& Grano</span></h1>
        <nav>
            <a href="index.php">Catálogo</a>
            <a href="carrito.php">🛒 Carrito (<?php echo isset($_SESSION['carrito']) ? count($_SESSION['carrito']) : 0; ?>)</a>
            <?php if (isset($_SESSION['usuario'])): ?>
                <a href="logout.php">Salir (<?php echo $_SESSION['usuario']['nombre']; ?>)</a>
            <?php else: ?>
                <a href="login.php">Ingresar</a>
            <?php endif; ?>
        </nav>
    </header>

    <div class="hero">
        <h2>Despierta tus sentidos con el mejor café de origen</h2>
        <p>Granos seleccionados tostados artesanalmente y llevados a la puerta de tu casa.</p>
    </div>

    <div class="container">
        <!-- Módulo de Búsqueda y Filtros -->
        <form method="GET" class="search-filter-bar">
            <input type="text" name="q" placeholder="Buscar por nombre, origen o notas..." value="<?php echo htmlspecialchars($busqueda); ?>">
            <select name="tueste">
                <option value="">Todos los tuestes</option>
                <option value="Claro" <?php if($tueste=='Claro') echo 'selected'; ?>>Tueste Claro</option>
                <option value="Medio" <?php if($tueste=='Medio') echo 'selected'; ?>>Tueste Medio</option>
                <option value="Oscuro" <?php if($tueste=='Oscuro') echo 'selected'; ?>>Tueste Oscuro</option>
            </select>
            <button type="submit" class="btn">Filtrar</button>
        </form>

        <!-- Módulo de Productos -->
        <div class="product-grid">
            <?php if (count($productos) > 0): ?>
                <?php foreach ($productos as $p): ?>
                    <div class="product-card">
                        <img src="assets/uploads/<?php echo htmlspecialchars($p['imagen']); ?>" alt="<?php echo htmlspecialchars($p['nombre']); ?>">
                        <div class="product-info">
                            <h3><?php echo htmlspecialchars($p['nombre']); ?></h3>
                            <p><?php echo substr(htmlspecialchars($p['descripcion']), 0, 90); ?>...</p>
                            <div style="font-size: 0.85rem; color: #888; margin-bottom: 0.5rem;">
                                📍 Origen: <?php echo htmlspecialchars($p['origen']); ?> | ⚖️ <?php echo $p['peso_gramos']; ?>g
                            </div>
                            <div class="product-footer">
                                <span class="price">$<?php echo number_format($p['precio'], 2); ?></span>
                                <a href="producto-detalle.php?id=<?php echo $p['id']; ?>" class="btn">Ver Detalles</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p style="grid-column: 1/-1; text-align: center; padding: 2rem;">No se encontraron cafés o accesorios con esos criterios.</p>
            <?php endif; ?>
        </div>
    </div>

    <footer class="main-footer">
        <p>&copy; 2026 Aroma y Grano. Todos los derechos reservados. | Desarrollado para PHP Nivel III Uneweb.</p>
    </footer>

</body>
</html>