<?php
require_once 'config/conexion.php';
include 'includes/header.php';

// Consulta rápida para contar equipos disponibles
$sql_total = "SELECT COUNT(*) AS total FROM equipos WHERE estado = 'Disponible'";
$resultado = mysqli_query($conexion, $sql_total);
$fila      = mysqli_fetch_assoc($resultado);
$disponibles = $fila['total'];
?>

<div class="card">
    <h1>Bienvenido a StudioTrack</h1>
    <p>Sistema de gestión de inventario y alquiler de equipos profesionales para cine, fotografía y producción audiovisual.</p>
    <p>Actualmente contamos con <strong><?php echo $disponibles; ?> equipos listos para rodar</strong>.</p>
    <br>
    <a href="catalogo.php" class="btn">Explorar Catálogo</a>
    <a href="cotizar.php" class="btn" style="background:#059669;">Solicitar Cotización</a>
</div>

<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1rem;">
    <div class="card">
        <h3>1. Catálogo en Vivo</h3>
        <p>Consulta tarifas diarias, disponibilidad de stock y filtra equipos por categoría.</p>
    </div>
    <div class="card">
        <h3>2. Cotizador Automático</h3>
        <p>Calcula presupuestos según días de alquiler, descuentos e IVA con envío directo al estudio.</p>
    </div>
    <div class="card">
        <h3>3. Administración CRUD</h3>
        <p>Gestiona altas, búsquedas, modificaciones y bajas de inventario conectadas a MySQL.</p>
    </div>
</div>

<?php include 'includes/footer.php'; ?>