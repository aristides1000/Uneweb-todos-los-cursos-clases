<?php
require_once '../conexion.php';
require_once '../funciones.php';

if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['rol'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

// Actualizar estado del pedido si se envía el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['pedido_id'], $_POST['estado'])) {
    $pedido_id = intval($_POST['pedido_id']);
    $estado = limpiar_dato($_POST['estado']);
    
    $stmt = $pdo->prepare("UPDATE pedidos SET estado = ? WHERE id = ?");
    $stmt->execute([$estado, $pedido_id]);
    header("Location: pedidos.php");
    exit();
}

// Consultar pedidos con información del cliente
$sql = "SELECT p.*, u.nombre AS cliente_nombre, u.email AS cliente_email 
        FROM pedidos p 
        JOIN usuarios u ON p.usuario_id = u.id 
        ORDER BY p.id DESC";
$pedidos = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Pedidos | Aroma y Grano</title>
    <link rel="stylesheet" href="../assets/css/estilos.css">
</head>
<body style="background: #f4ece1;">

    <header>
        <h1>Panel Admin <span>- Aroma & Grano</span></h1>
        <nav>
            <a href="index.php">Dashboard</a>
            <a href="productos.php">Productos</a>
            <a href="pedidos.php">Pedidos</a>
            <a href="../index.php" target="_blank">Ver Sitio Web</a>
            <a href="salir.php">Cerrar Sesión</a>
        </nav>
    </header>

    <div class="container" style="margin-top: 3rem;">
        <h2>Gestión de Pedidos de Clientes</h2>
        
        <div style="background: white; padding: 2rem; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); margin-top: 1.5rem; overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse; text-align: left;">
                <thead>
                    <tr style="background: #3b2314; color: white;">
                        <th style="padding: 0.8rem;">ID</th>
                        <th style="padding: 0.8rem;">Cliente</th>
                        <th style="padding: 0.8rem;">Total</th>
                        <th style="padding: 0.8rem;">Fecha</th>
                        <th style="padding: 0.8rem;">Estado Actual</th>
                        <th style="padding: 0.8rem;">Cambiar Estado</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($pedidos) > 0): ?>
                        <?php foreach ($pedidos as $ped): ?>
                        <tr style="border-bottom: 1px solid #eee;">
                            <td style="padding: 0.8rem;">#<?php echo $ped['id']; ?></td>
                            <td style="padding: 0.8rem;">
                                <strong><?php echo htmlspecialchars($ped['cliente_nombre']); ?></strong><br>
                                <small style="color: #666;"><?php echo htmlspecialchars($ped['cliente_email']); ?></small>
                            </td>
                            <td style="padding: 0.8rem; font-weight: bold; color: var(--primary-color);">$<?php echo number_format($ped['total'], 2); ?></td>
                            <td style="padding: 0.8rem;"><?php echo $ped['fecha_pedido']; ?></td>
                            <td style="padding: 0.8rem;">
                                <span style="padding: 0.3rem 0.6rem; border-radius: 4px; font-size: 0.85rem; font-weight: bold; background: <?php echo $ped['estado']=='Enviado'?'#d4edda':($ped['estado']=='Procesado'?'#d1ecf1':'#fff3cd'); ?>; color: <?php echo $ped['estado']=='Enviado'?'#155724':($ped['estado']=='Procesado'?'#0c5460':'#856404'); ?>;">
                                    <?php echo $ped['estado']; ?>
                                </span>
                            </td>
                            <td style="padding: 0.8rem;">
                                <form action="pedidos.php" method="POST" style="display: flex; gap: 0.5rem;">
                                    <input type="hidden" name="pedido_id" value="<?php echo $ped['id']; ?>">
                                    <select name="estado" style="padding: 0.4rem; border: 1px solid #ddd; border-radius: 4px;">
                                        <option value="Pendiente" <?php if($ped['estado']=='Pendiente') echo 'selected'; ?>>Pendiente</option>
                                        <option value="Procesado" <?php if($ped['estado']=='Procesado') echo 'selected'; ?>>Procesado</option>
                                        <option value="Enviado" <?php if($ped['estado']=='Enviado') echo 'selected'; ?>>Enviado</option>
                                    </select>
                                    <button type="submit" class="btn" style="padding: 0.4rem 0.8rem; font-size: 0.85rem;">Actualizar</button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="6" style="text-align: center; padding: 2rem; color: #666;">No hay pedidos registrados todavía.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</body>
</html>