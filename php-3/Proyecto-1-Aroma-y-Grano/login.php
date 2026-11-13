<?php
require_once 'conexion.php';
require_once 'funciones.php';

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = limpiar_dato($_POST['email']);
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = ?");
    $stmt->execute([$email]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    // Validación segura de credenciales
    if ($usuario && password_verify($password, $usuario['password'])) {
        // Guardar variables de sesión PHP
        $_SESSION['usuario'] = [
            'id' => $usuario['id'],
            'nombre' => $usuario['nombre'],
            'email' => $usuario['email'],
            'rol' => $usuario['rol']
        ];

        if ($usuario['rol'] === 'admin') {
            header("Location: admin/productos.php");
        } else {
            header("Location: index.php");
        }
        exit();
    } else {
        $error = "Correo o contraseña incorrectos.";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Iniciar Sesión | Aroma y Grano</title>
    <link rel="stylesheet" href="assets/css/estilos.css">
</head>
<body>
    <header>
        <h1>Aroma <span>& Grano</span></h1>
        <nav><a href="index.php">Catálogo</a><a href="registro.php">Registrarse</a></nav>
    </header>
    <div class="container" style="max-width: 500px; margin-top: 3rem;">
        <div style="background: white; padding: 2.5rem; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.05);">
            <h2>Iniciar Sesión</h2>
            <?php if($error): ?><p style="color: #d9534f; margin: 1rem 0;"><?php echo $error; ?></p><?php endif; ?>
            <form action="login.php" method="POST" style="margin-top: 1.5rem;">
                <div style="margin-bottom: 1rem;">
                    <label>Correo Electrónico:</label>
                    <input type="email" name="email" required style="width:100%; padding:0.7rem; border:1px solid #ddd; border-radius:4px;">
                </div>
                <div style="margin-bottom: 1.5rem;">
                    <label>Contraseña:</label>
                    <input type="password" name="password" required style="width:100%; padding:0.7rem; border:1px solid #ddd; border-radius:4px;">
                </div>
                <button type="submit" class="btn" style="width:100%;">Entrar</button>
            </form>
            <p style="margin-top: 1rem; text-align: center;"><a href="registro.php" style="color: var(--secondary-color);">¿No tienes cuenta? Regístrate aquí</a></p>
        </div>
    </div>
</body>
</html>