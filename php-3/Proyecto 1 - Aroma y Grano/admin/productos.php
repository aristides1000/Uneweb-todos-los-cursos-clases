<?php
require_once '../conexion.php';
require_once '../funciones.php';

// Verificar que sea Administrador (Seguridad en PHP)
if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['rol'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

$mensaje = '';

// Procesar Formulario: Insertar o Modificar Producto
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = isset($_POST['id']) ? limpiar_dato($_POST['id']) : '';
    $nombre = limpiar_dato($_POST['nombre']);
    $descripcion = limpiar_dato($_POST['descripcion']);
    $precio = limpiar_dato($_POST['precio']);
    $peso = limpiar_dato($_POST['peso_gramos']);
    $tueste = limpiar_dato($_POST['tueste']);
    $origen = limpiar_dato($_POST['origen']);
    $stock = limpiar_dato($_POST['stock']);

    $imagen_nombre = '';

    // Manejo de Upload de Archivos y Librería GD
    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
        $imagen_nombre = procesar_imagen_gd($_FILES['imagen'], '../assets/uploads/');
    }

    if (!empty($id)) {
        // MODIFICAR PRODUCTO
        if ($imagen_nombre) {
            $sql = "UPDATE productos SET nombre=?, descripcion=?, precio=?, peso_gramos=?, tueste=?, origen=?, imagen=?, stock=? WHERE id=?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$nombre, $descripcion, $precio, $peso, $tueste, $origen, $imagen_nombre, $stock, $id]);
        } else {
            $sql = "UPDATE productos SET nombre=?, descripcion=?, precio=?, peso_gramos=?, tueste=?, origen=?, stock=? WHERE id=?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$nombre, $descripcion, $precio, $peso, $tueste, $origen, $stock, $id]);
        }
        $mensaje = "¡Producto actualizado exitosamente!";
    } else {
        // INSERTAR PRODUCTO NUEVO
        if ($imagen_nombre) {
            $sql = "INSERT INTO productos (nombre, descripcion, precio, peso_gramos, tueste, origen, imagen, stock) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$nombre, $descripcion, $precio, $peso, $tueste, $origen, $imagen_nombre, $stock]);
            $mensaje = "¡Producto registrado correctamente!";
        } else {
            $mensaje = "Error: Es obligatoria una imagen válida procesada con GD.";
        }
    }
}

// ELIMINAR PRODUCTO
if (isset($_GET['eliminar'])) {
    $id_eliminar = limpiar_dato($_GET['eliminar']);
    $stmt = $pdo->prepare("DELETE FROM productos WHERE id = ?");
    $stmt->execute([$id_eliminar]);
    header("Location: productos.php");
    exit();
}

// CONSULTAR DATOS (Listar productos para el admin)
$stmt = $pdo->query("SELECT * FROM productos ORDER BY id DESC");
$productos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Admin - Gestión de Productos | Aroma y Grano</title>
    <link rel="stylesheet" href="../assets/css/estilos.css">
</head>
<body style="background: #f4ece1;">

    <header>
        <h1>Panel Admin <span>- Aroma & Grano</span></h1>
        <nav>
            <a href="productos.php">Productos</a>
            <a href="../index.php" target="_blank">Ver Sitio Web</a>
            <a href="../logout.php">Cerrar Sesión</a>
        </nav>
    </header>

    <div class="container" style="margin-top: 2rem;">
        <h2>Gestión de Catálogo e Inventario</h2>
        <?php if($mensaje): ?>
            <div style="background: #d4edda; color: #155724; padding: 1rem; border-radius: 5px; margin: 1rem 0;"><?php echo $mensaje; ?></div>
        <?php endif; ?>

        <!-- Formulario de Registro / Edición -->
        <div style="background: white; padding: 2rem; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); margin-bottom: 2rem;">
            <h3>Agregar / Modificar Producto</h3>
            <form action="productos.php" method="POST" enctype="multipart/form-data" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1rem; margin-top: 1rem;">
                <input type="hidden" name="id" id="form-id">
                <div>
                    <label>Nombre del Café / Accesorio:</label>
                    <input type="text" name="nombre" id="form-nombre" required style="width:100%; padding: 0.5rem; border:1px solid #ddd; border-radius:4px;">
                </div>
                <div>
                    <label>Precio ($):</label>
                    <input type="number" step="0.01" name="precio" id="form-precio" required style="width:100%; padding: 0.5rem; border:1px solid #ddd; border-radius:4px;">
                </div>
                <div>
                    <label>Peso (Gramos):</label>
                    <input type="number" name="peso_gramos" id="form-peso" value="250" required style="width:100%; padding: 0.5rem; border:1px solid #ddd; border-radius:4px;">
                </div>
                <div>
                    <label>Tueste:</label>
                    <select name="tueste" id="form-tueste" style="width:100%; padding: 0.5rem; border:1px solid #ddd; border-radius:4px;">
                        <option value="Claro">Claro</option>
                        <option value="Medio">Medio</option>
                        <option value="Oscuro">Oscuro</option>
                    </select>
                </div>
                <div>
                    <label>Origen:</label>
                    <input type="text" name="origen" id="form-origen" placeholder="Ej. Etiopía Yirgacheffe" required style="width:100%; padding: 0.5rem; border:1px solid #ddd; border-radius:4px;">
                </div>
                <div>
                    <label>Stock Disponible:</label>
                    <input type="number" name="stock" id="form-stock" value="10" required style="width:100%; padding: 0.5rem; border:1px solid #ddd; border-radius:4px;">
                </div>
                <div style="grid-column: 1/-1;">
                    <label>Descripción del Producto:</label>
                    <textarea name="descripcion" id="form-descripcion" rows="3" required style="width:100%; padding: 0.5rem; border:1px solid #ddd; border-radius:4px;"></textarea>
                </div>
                <div style="grid-column: 1/-1;">
                    <label>Imagen del Producto (Procesada automáticamente con GD):</label>
                    <input type="file" name="imagen" accept="image/*" style="width:100%; padding: 0.5rem;">
                </div>
                <div>
                    <button type="submit" class="btn">Guardar Producto</button>
                </div>
            </form>
        </div>

        <!-- Tabla de Consulta de Datos -->
        <div style="background: white; padding: 2rem; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); overflow-x: auto;">
            <h3>Listado de Productos en Inventario</h3>
            <table style="width: 100%; border-collapse: collapse; margin-top: 1rem; text-align: left;">
                <thead>
                    <tr style="background: #3b2314; color: white;">
                        <th style="padding: 0.8rem;">Img</th>
                        <th style="padding: 0.8rem;">Nombre</th>
                        <th style="padding: 0.8rem;">Precio</th>
                        <th style="padding: 0.8rem;">Origen</th>
                        <th style="padding: 0.8rem;">Stock</th>
                        <th style="padding: 0.8rem;">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($productos as $p): ?>
                    <tr style="border-bottom: 1px solid #eee;">
                        <td style="padding: 0.8rem;"><img src="../assets/uploads/<?php echo $p['imagen']; ?>" width="45" style="border-radius: 4px; object-fit: cover;"></td>
                        <td style="padding: 0.8rem;"><?php echo htmlspecialchars($p['nombre']); ?></td>
                        <td style="padding: 0.8rem;">$<?php echo number_format($p['precio'], 2); ?></td>
                        <td style="padding: 0.8rem;"><?php echo htmlspecialchars($p['origen']); ?></td>
                        <td style="padding: 0.8rem;"><?php echo $p['stock']; ?></td>
                        <td style="padding: 0.8rem;">
                            <a href="productos.php?eliminar=<?php echo $p['id']; ?>" onclick="return confirm('¿Estás seguro de eliminar este producto?');" style="color: #d9534f; text-decoration: none; font-weight: bold;">Eliminar</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

</body>
</html>