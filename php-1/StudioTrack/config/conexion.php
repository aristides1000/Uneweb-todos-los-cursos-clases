<?php
// Variables de configuración (tipo string)
$servidor   = "localhost";
$usuario    = "root";
$contrasena = ""; // En XAMPP suele ser vacío; en Laragon también
$base_datos = "studiotrack_db";

// Crear conexión con MySQLi
$conexion = mysqli_connect($servidor, $usuario, $contrasena, $base_datos);

// Verificar conexión (estructura de control if y operador lógico !)
if (!$conexion) {
    die("Error de conexión a la base de datos: " . mysqli_connect_error());
}

// Establecer codificación de caracteres para acentos y eñes
mysqli_set_charset($conexion, "utf8mb4");
?>
