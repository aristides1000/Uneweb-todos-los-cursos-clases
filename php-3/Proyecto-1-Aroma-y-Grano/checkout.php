<?php
require_once 'conexion.php';
require_once 'funciones.php';

// Verificar que el usuario haya iniciado sesión
if (!isset($_SESSION['usuario'])) {
    header("Location: login.php?redirect=checkout");
    exit();
}

// Verificar que el carrito no esté vacío
if (empty($_SESSION['carrito'])) {
    header("Location: index.php");
    exit();
}

$usuario_id = $_SESSION['usuario']['id'];
$total_general = 0;

foreach ($_SESSION['carrito'] as $item) {
    $total_general += $item['precio'] * $item['cantidad'];
}

try {
    $pdo->beginTransaction();

    // 1. Insertar en la tabla de Pedidos
    $stmt = $pdo->prepare("INSERT INTO pedidos (usuario_id, total, estado) VALUES (?, ?, 'Pendiente')");
    $stmt->execute([$usuario_id, $total_general]);
    $pedido_id = $pdo->lastInsertId();

    // 2. Insertar en la tabla Detalle de Pedidos
    $stmt_detalle = $pdo->prepare("INSERT INTO detalle_pedidos (pedido_id, producto_id, cantidad, precio_unitario) VALUES (?, ?, ?, ?)");
    
    foreach ($_SESSION['carrito'] as $id => $item) {
        $stmt_detalle->execute([$pedido_id, $id, $item['cantidad'], $item['precio']]);
    }

    $pdo->commit();

    // 3. Borrar variables de sesión del carrito
    unset($_SESSION['carrito']);

    $exito = true;
} catch (Exception $e) {
    $pdo->rollBack();
    $exito = false;
    $error_msg = $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Confirmación de Compra | Aroma y Grano</title>
    <link rel="stylesheet" href="assets/css/estilos.css">
</head>
<body>
    <header>
        <h1>Aroma <span>& Grano</span></h1>
        <nav><a href="index.php">Volver al Catálogo</a></nav>
    </header>
    <div class="container" style="max-width: 600px; margin-top: 4rem; text-align: center;">
        <div style="background: white; padding: 3rem; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.05);">
            <?php if (isset($exito) && $exito): ?>
                <h2 style="color: #5cb85c; margin-bottom: 1rem;">¡Compra Registrada con Éxito! 🎉</h2>
                <p style="font-size: 1.1rem; color: #666; margin-bottom: 1.5rem;">Tu número de orden es: <strong>#<?php echo $pedido_id; ?></strong></p>
                <p style="margin-bottom: 2rem;">Hemos guardado tu pedido correctamente. Nos pondremos en contacto contigo para coordinar el envío de tus cafés de especialidad.</p>
                <a href="index.php" class="btn" style="text-decoration: none;">Seguir Comprando</a>
            <?php else: ?>
                <h2 style="color: #d9534f; margin-bottom: 1rem;">Error al Procesar la Compra</h2>
                <p>Ocurrió un inconveniente al guardar tu pedido: <?php echo isset($error_msg) ? $error_msg : ''; ?></p>
                <a href="carrito.php" class="btn" style="text-decoration: none; margin-top: 1rem; display:inline-block;">Regresar al Carrito</a>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>