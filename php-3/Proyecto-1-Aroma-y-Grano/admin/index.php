<?php
require_once '../conexion.php';
require_once '../funciones.php';

if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['rol'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

// Estadísticas rápidas para el Dashboard
$total_productos = $pdo->query("SELECT COUNT(*) FROM productos")->fetchColumn();
$total_pedidos = $pdo->query("SELECT COUNT(*) FROM pedidos")->fetchColumn();
$total_usuarios = $pdo->query("SELECT COUNT(*) FROM usuarios WHERE rol='cliente'")->fetchColumn();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Admin | Aroma y Grano</title>
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
        <h2>Panel de Control Principal</h2>
        <p style="color: #666; margin-bottom: 2rem;">Bienvenido, <?php echo $_SESSION['usuario']['nombre']; ?>. Aquí tienes un resumen del e-commerce.</p>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1.5rem;">
            <div style="background: white; padding: 2rem; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); text-align: center;">
                <h3 style="color: var(--secondary-color); font-size: 2.5rem;"><?php echo $total_productos; ?></h3>
                <p style="color: #666; font-weight: bold; margin-top: 0.5rem;">Productos en Catálogo</p>
                <a href="productos.php" class="btn" style="display: inline-block; margin-top: 1rem; text-decoration: none; font-size: 0.9rem;">Gestionar Productos</a>
            </div>
            <div style="background: white; padding: 2rem; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); text-align: center;">
                <h3 style="color: var(--secondary-color); font-size: 2.5rem;"><?php echo $total_pedidos; ?></h3>
                <p style="color: #666; font-weight: bold; margin-top: 0.5rem;">Pedidos Registrados</p>
                <a href="pedidos.php" class="btn" style="display: inline-block; margin-top: 1rem; text-decoration: none; font-size: 0.9rem;">Ver Pedidos</a>
            </div>
            <div style="background: white; padding: 2rem; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); text-align: center;">
                <h3 style="color: var(--secondary-color); font-size: 2.5rem;"><?php echo $total_usuarios; ?></h3>
                <p style="color: #666; font-weight: bold; margin-top: 0.5rem;">Clientes Registrados</p>
            </div>
        </div>
    </div>

</body>
</html>