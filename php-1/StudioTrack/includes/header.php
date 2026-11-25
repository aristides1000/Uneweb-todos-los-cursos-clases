<?php
// Si no se define $ruta_base antes de incluir el header, asume la raíz
if (!isset($ruta_base)) {
    $ruta_base = "./";
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StudioTrack | Alquiler de Equipos Audiovisuales</title>
    <style>
        body { font-family: system-ui, -apple-system, sans-serif; margin: 0; background: #0f172a; color: #e2e8f0; }
        header { background: #1e293b; padding: 1rem 2rem; border-bottom: 1px solid #334155; display: flex; justify-content: space-between; align-items: center; }
        header a { color: #38bdf8; text-decoration: none; font-weight: bold; font-size: 1.2rem; }
        nav a { color: #cbd5e1; margin-left: 1.5rem; font-size: 0.95rem; font-weight: 500; }
        nav a:hover { color: #38bdf8; }
        .container { max-width: 1000px; margin: 2rem auto; padding: 0 1.5rem; }
        .card { background: #1e293b; border: 1px solid #334155; border-radius: 8px; padding: 1.5rem; margin-bottom: 1.5rem; }
        .btn { display: inline-block; padding: 0.5rem 1rem; background: #0284c7; color: #fff; text-decoration: none; border-radius: 6px; border: none; cursor: pointer; font-size: 0.9rem; }
        .btn:hover { background: #0369a1; }
        .btn-danger { background: #dc2626; }
        .btn-warning { background: #d97706; }
        table { width: 100%; border-collapse: collapse; margin-top: 1rem; }
        th, td { padding: 0.75rem; text-align: left; border-bottom: 1px solid #334155; }
        th { background: #0f172a; color: #38bdf8; }
        input, select, textarea { width: 100%; padding: 0.6rem; margin-top: 0.3rem; margin-bottom: 1rem; background: #0f172a; border: 1px solid #475569; color: #f8fafc; border-radius: 5px; box-sizing: border-box; }
        .badge { padding: 0.25rem 0.6rem; border-radius: 999px; font-size: 0.75rem; font-weight: bold; }
        .disp { background: #065f46; color: #6ee7b7; }
        .alq { background: #991b1b; color: #fca5a5; }
        footer { text-align: center; padding: 2rem; color: #64748b; font-size: 0.85rem; border-top: 1px solid #1e293b; margin-top: 3rem; }
    </style>
</head>
<body>
<header>
    <a href="<?php echo $ruta_base; ?>index.php">🎬 StudioTrack</a>
    <nav>
        <a href="<?php echo $ruta_base; ?>index.php">Inicio</a>
        <a href="<?php echo $ruta_base; ?>catalogo.php">Catálogo</a>
        <a href="<?php echo $ruta_base; ?>cotizar.php">Cotizar</a>
        <a href="<?php echo $ruta_base; ?>admin/index.php">Panel Admin</a>
    </nav>
</header>
<main class="container">