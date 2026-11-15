<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Función para sanitizar entradas (Seguridad en PHP)
function limpiar_dato($dato) {
    return htmlspecialchars(trim($dato), ENT_QUOTES, 'UTF-8');
}

// Función para procesar y optimizar imágenes con la Librería GD
function procesar_imagen_gd($archivo, $carpeta_destino) {
    if (!isset($archivo) || $archivo['error'] !== UPLOAD_ERR_OK) {
        return false;
    }

    $nombre_tmp = $archivo['tmp_name'];
    $info_imagen = getimagesize($nombre_tmp);
    
    if ($info_imagen === false) {
        return false; // No es una imagen válida
    }

    $ancho_orig = $info_imagen[0];
    $alto_orig = $info_imagen[1];
    $tipo = $info_imagen[2];

    // Crear imagen según su tipo
    switch ($tipo) {
        case IMAGETYPE_JPEG:
            $imagen_orig = imagecreatefromjpeg($nombre_tmp);
            break;
        case IMAGETYPE_PNG:
            $imagen_orig = imagecreatefrompng($nombre_tmp);
            break;
        case IMAGETYPE_WEBP:
            $imagen_orig = imagecreatefromwebp($nombre_tmp);
            break;
        default:
            return false;
    }

    // Definir nuevo tamaño máximo (ej. 800x800 manteniendo proporción)
    $ancho_nuevo = 800;
    $alto_nuevo = floor($alto_orig * ($ancho_nuevo / $ancho_orig));

    $imagen_nueva = imagecreatetruecolor($ancho_nuevo, $alto_nuevo);

    // Mantener transparencia si es PNG o WEBP
    if ($tipo == IMAGETYPE_PNG || $tipo == IMAGETYPE_WEBP) {
        imagecolortransparent($imagen_nueva, imagecolorallocatealpha($imagen_nueva, 0, 0, 0, 127));
        imagealphablending($imagen_nueva, false);
        imagesavealpha($imagen_nueva, true);
    }

    imagecopyresampled($imagen_nueva, $imagen_orig, 0, 0, 0, 0, $ancho_nuevo, $alto_nuevo, $ancho_orig, $alto_orig);

    // Generar nombre único para evitar colisiones
    $nombre_archivo = uniqid('cafe_') . '.jpg';
    $ruta_completa = $carpeta_destino . $nombre_archivo;

    // Guardar imagen optimizada en calidad 85%
    imagejpeg($imagen_nueva, $ruta_completa, 85);

    // Liberar memoria
    imagedestroy($imagen_orig);
    imagedestroy($imagen_nueva);

    return $nombre_archivo;
}
?>