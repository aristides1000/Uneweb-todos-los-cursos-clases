<?php
require_once 'conexion.php';
require_once 'funciones.php';

$mensaje = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = limpiar_dato($_POST['nombre']);
    $email = limpiar_dato($_POST['email']);
    $password_raw = $_POST['password'];

    // Seguridad en PHP: Cifrado de contraseña
    $password = password_hash($password_raw, PASSWORD_BCRYPT);

    try {
        $stmt = $pdo->prepare("INSERT INTO usuarios (nombre, email, password, rol) VALUES (?, ?, ?, 'cliente')");
        $stmt->execute([$nombre, $email, $password]);
        $mensaje = "¡Registro exitoso! Ya puedes iniciar sesión.";
    } catch (PDOException $e) {
        $mensaje = "Error: El correo electrónico ya está registrado.";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro de Cliente | Aroma y Grano</title>
    <link rel="stylesheet" href="assets/css/estilos.css">
</head>
<body>
    <header>
        <h1>Aroma <span>& Grano</span></h1>
        <nav><a href="index.php">Catálogo</a><a href="login.php">Ingresar</a></nav>
    </header>
    <div class="container" style="max-width: 500px; margin-top: 3rem;">
        <div style="background: white; padding: 2.5rem; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.05);">
            <h2>Crear Cuenta de Usuario</h2>
            <?php if($mensaje): ?><p style="color: var(--primary-color); margin: 1rem 0;"><?php echo $mensaje; ?></p><?php endif; ?>
            <form action="registro.php" method="POST" style="margin-top: 1.5rem;">
                <div style="margin-bottom: 1rem;">
                    <label>Nombre Completo:</label>
                    <input type="text" name="nombre" required style="width:100%; padding:0.7rem; border:1px solid #ddd; border-radius:4px;">
                </div>
                <div style="margin-bottom: 1rem;">
                    <label>Correo Electrónico:</label>
                    <input type="email" name="email" required style="width:100%; padding:0.7rem; border:1px solid #ddd; border-radius:4px;">
                </div>
                <div style="margin-bottom: 1.5rem;">
                    <label>Contraseña (Usuario y Clave):</label>
                    <input type="password" name="password" required style="width:100%; padding:0.7rem; border:1px solid #ddd; border-radius:4px;">
                </div>
                <button type="submit" class="btn" style="width:100%;">Registrarse</button>
            </form>
            <p style="margin-top: 1rem; text-align: center;"><a href="login.php" style="color: var(--secondary-color);">¿Ya tienes cuenta? Inicia sesión aquí</a></p>
        </div>
    </div>
</body>
</html>