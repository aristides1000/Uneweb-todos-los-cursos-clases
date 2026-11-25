<?php
require_once '../config/conexion.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id > 0) {
    $sql_delete = "DELETE FROM equipos WHERE id = $id";
    mysqli_query($conexion, $sql_delete);
}

header("Location: index.php");
exit;