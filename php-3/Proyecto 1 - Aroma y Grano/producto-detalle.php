<?php
require_once 'conexion.php';
require_once 'funciones.php';

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

$stmt = $pdo->prepare("SELECT * FROM productos WHERE id = ?");
$stmt->execute([$id]);
$producto = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$producto) {
    header("Location: index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($producto['nombre']); ?> | Aroma y Grano</title>
    <link rel="stylesheet" href="assets/css/estilos.css">
</head>
<body>

    <header>
        <h1>Aroma <span>& Grano</span></h1>
        <nav>
            <a href="index.php">Catálogo</a>
            <a href="carrito.php">🛒 Carrito (<?php echo isset($_SESSION['carrito']) ? array_sum(array_column($_SESSION['carrito'], 'cantidad')) : 0; ?>)</a>
            <?php if (isset($_SESSION['usuario'])): ?>
                <a href="logout.php">Salir</a>
            <?php else: ?>
                <a href="login.php">Ingresar</a>
            <?php endif; ?>
        </nav>
    </header>

    <div class="container" style="margin-top: 3rem;">
        <div style="background: white; padding: 2.5rem; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); display: grid; grid-template-columns: 1fr 1fr; gap: 3rem; align-items: center;">
            <div>
                <img src="assets/uploads/<?php echo htmlspecialchars($producto['imagen']); ?>" alt="<?php echo htmlspecialchars($producto['nombre']); ?>" style="width: 100%; border-radius: 8px; object-fit: cover; max-height: 400px;">
            </div>
            <div>
                <span style="background: var(--secondary-color); color: white; padding: 0.3rem 0.8rem; border-radius: 20px; font-size: 0.85rem; font-weight: bold;">Tueste <?php echo htmlspecialchars($producto['tueste']); ?></span>
                <h2 style="font-size: 2.2rem; color: var(--primary-color); margin: 0.5rem 0;"><?php echo htmlspecialchars($producto['nombre']); ?></h2>
                <p style="font-size: 1.5rem; font-weight: bold; color: var(--primary-color); margin-bottom: 1rem;">$<?php echo number_format($producto['precio'], 2); ?></p>
                
                <p style="color: #666; margin-bottom: 1.5rem; line-height: 1.8;"><?php echo nl2br(htmlspecialchars($producto['descripcion'])); ?></p>
                
                <div style="background: var(--bg-light); padding: 1rem; border-radius: 6px; margin-bottom: 1.5rem; font-size: 0.95rem;">
                    <p>📍 <strong>Origen:</strong> <?php echo htmlspecialchars($producto['origen']); ?></p>
                    <p>⚖️ <strong>Presentación:</strong> <?php echo $producto['peso_gramos']; ?> gramos</p>
                    <p>📦 <strong>Stock Disponible:</strong> <?php echo $producto['stock']; ?> unidades</p>
                </div>

                <form action="carrito.php?accion=agregar" method="POST" style="display: flex; gap: 1rem; align-items: center;">
                    <input type="hidden" name="producto_id" value="<?php echo $producto['id']; ?>">
                    <input type="number" name="cantidad" value="1" min="1" max="<?php echo $producto['stock']; ?>" style="width: 80px; padding: 0.7rem; border: 1px solid #ddd; border-radius: 4px; font-size: 1rem;">
                    <button type="submit" class="btn" style="flex: 1; padding: 0.8rem;">Añadir al Carro</button>
                </form>
            </div>
        </div>
    </div>

</body>
</html>