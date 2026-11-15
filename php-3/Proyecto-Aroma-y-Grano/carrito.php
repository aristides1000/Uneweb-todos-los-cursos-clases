<?php
require_once 'conexion.php';
require_once 'funciones.php';

// Manejo de acciones del carrito (Agregar, Modificar cantidad, Vaciar)
if (isset($_GET['accion'])) {
    $accion = $_GET['accion'];
    
    if ($accion === 'agregar' && isset($_POST['producto_id'])) {
        $id = intval($_POST['producto_id']);
        $cantidad = intval($_POST['cantidad']);
        
        // Consultar datos del producto
        $stmt = $pdo->prepare("SELECT * FROM productos WHERE id = ?");
        $stmt->execute([$id]);
        $prod = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($prod) {
            // Guardar variables en Sesión PHP
            if (!isset($_SESSION['carrito'])) {
                $_SESSION['carrito'] = [];
            }
            
            if (isset($_SESSION['carrito'][$id])) {
                $_SESSION['carrito'][$id]['cantidad'] += $cantidad; // Operador aritmético de incremento
            } else {
                $_SESSION['carrito'][$id] = [
                    'nombre' => $prod['nombre'],
                    'precio' => $prod['precio'],
                    'peso' => $prod['peso_gramos'],
                    'imagen' => $prod['imagen'],
                    'cantidad' => $cantidad
                ];
            }
        }
        header("Location: carrito.php");
        exit();
    }
    
    if ($accion === 'eliminar' && isset($_GET['id'])) {
        $id = intval($_GET['id']);
        unset($_SESSION['carrito'][$id]); // Borrar variables de sesión específicas
        header("Location: carrito.php");
        exit();
    }
    
    if ($accion === 'vaciar') {
        unset($_SESSION['carrito']); // Borrar todas las variables del carrito
        header("Location: carrito.php");
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Carro de Compras | Aroma y Grano</title>
    <link rel="stylesheet" href="assets/css/estilos.css">
</head>
<body>

    <header>
        <h1>Aroma <span>& Grano</span></h1>
        <nav>
            <a href="index.php">Catálogo</a>
            <a href="carrito.php">🛒 Carrito (<?php echo isset($_SESSION['carrito']) ? array_sum(array_column($_SESSION['carrito'], 'cantidad')) : 0; ?>)</a>
            <a href="login.php">Ingresar</a>
        </nav>
    </header>

    <div class="container" style="margin-top: 3rem;">
        <h2>Tu Carro de Compras Online</h2>
        
        <?php if (!empty($_SESSION['carrito'])): ?>
            <div style="background: white; padding: 2rem; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); margin-top: 1.5rem;">
                <table style="width: 100%; border-collapse: collapse; text-align: left;">
                    <thead>
                        <tr style="border-bottom: 2px solid #ddd;">
                            <th style="padding: 1rem;">Producto</th>
                            <th style="padding: 1rem;">Precio Unitario</th>
                            <th style="padding: 1rem;">Cantidad</th>
                            <th style="padding: 1rem;">Subtotal</th>
                            <th style="padding: 1rem;">Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $total_general = 0;
                        foreach ($_SESSION['carrito'] as $id => $item): 
                            // Operación Aritmética: Precio x Cantidad
                            $subtotal = $item['precio'] * $item['cantidad'];
                            $total_general += $subtotal;
                        ?>
                        <tr style="border-bottom: 1px solid #eee;">
                            <td style="padding: 1rem; display: flex; align-items: center; gap: 1rem;">
                                <img src="assets/uploads/<?php echo $item['imagen']; ?>" width="60" style="border-radius: 6px; object-fit: cover;">
                                <div>
                                    <strong><?php echo htmlspecialchars($item['nombre']); ?></strong><br>
                                    <small style="color: #666;"><?php echo $item['peso']; ?>g</small>
                                </div>
                            </td>
                            <td style="padding: 1rem;">$<?php echo number_format($item['precio'], 2); ?></td>
                            <td style="padding: 1rem;"><?php echo $item['cantidad']; ?></td>
                            <td style="padding: 1rem; font-weight: bold; color: var(--primary-color);">$<?php echo number_format($subtotal, 2); ?></td>
                            <td style="padding: 1rem;">
                                <a href="carrito.php?accion=eliminar&id=<?php echo $id; ?>" style="color: #d9534f; text-decoration: none; font-weight: bold;">Quitar</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 2rem; border-top: 2px solid #ddd; padding-top: 1.5rem;">
                    <a href="carrito.php?accion=vaciar" style="color: #666; text-decoration: none;">Vaciar Carrito</a>
                    <div style="text-align: right;">
                        <h3 style="font-size: 1.5rem; margin-bottom: 0.5rem;">Total a Pagar: $<?php echo number_format($total_general, 2); ?></h3>
                        <a href="checkout.php" class="btn" style="text-decoration: none; display: inline-block;">Finalizar Compra (Checkout)</a>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <div style="background: white; padding: 4rem; text-align: center; border-radius: 8px; margin-top: 2rem;">
                <p style="font-size: 1.2rem; color: #666; margin-bottom: 1.5rem;">Tu carro de compras está vacío.</p>
                <a href="index.php" class="btn" style="text-decoration: none;">Explorar el Catálogo de Cafés</a>
            </div>
        <?php endif; ?>
    </div>

</body>
</html>