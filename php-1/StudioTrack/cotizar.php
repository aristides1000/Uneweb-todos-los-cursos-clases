<?php
require_once 'config/conexion.php';
require_once 'includes/datos_estaticos.php';
include 'includes/header.php';

// Obtener todos los equipos para el selector
$sql_equipos = "SELECT id, codigo, nombre, tarifa_dia, stock, estado FROM equipos ORDER BY nombre ASC";
$res_equipos = mysqli_query($conexion, $sql_equipos);
$lista_equipos = [];
while ($fila = mysqli_fetch_assoc($res_equipos)) {
    $lista_equipos[$fila['id']] = $fila;
}

// Preseleccionar equipo si viene por $_GET desde el catálogo
$equipo_preseleccionado = isset($_GET['equipo_id']) ? (int)$_GET['equipo_id'] : 0;

$mensaje_alerta = "";
$tipo_alerta    = "";
$resumen_cotizacion = null;

// Procesar envío del formulario mediante $_POST
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $equipo_id      = isset($_POST['equipo_id']) ? (int)$_POST['equipo_id'] : 0;
    $dias           = isset($_POST['dias']) ? (int)$_POST['dias'] : 0;
    $nombre_cliente = isset($_POST['nombre_cliente']) ? trim($_POST['nombre_cliente']) : '';
    $correo_cliente = isset($_POST['correo_cliente']) ? trim($_POST['correo_cliente']) : '';
    $notas          = isset($_POST['notas']) ? trim($_POST['notas']) : '';

    // Validación con operadores lógicos y de comparación
    if (!isset($lista_equipos[$equipo_id]) || $dias <= 0 || $nombre_cliente === "" || !filter_var($correo_cliente, FILTER_VALIDATE_EMAIL)) {
        $mensaje_alerta = "Por favor completa todos los campos obligatorios correctamente (mínimo 1 día y correo válido).";
        $tipo_alerta    = "error";
    } else {
        $eq_sel = $lista_equipos[$equipo_id];
        $tarifa = (float)$eq_sel['tarifa_dia'];

        // Operadores aritméticos para cálculo de presupuesto
        $subtotal = $tarifa * $dias;
        $descuento = ($dias >= 7) ? ($subtotal * $config_tarifas['descuento_semanal']) : 0.00;
        $base_imponible = $subtotal - $descuento;
        $monto_iva = $base_imponible * $config_tarifas['iva'];
        $total_estimado = $base_imponible + $monto_iva;

        $resumen_cotizacion = [
            'equipo'         => $eq_sel['codigo'] . " - " . $eq_sel['nombre'],
            'tarifa_dia'     => $tarifa,
            'dias'           => $dias,
            'subtotal'       => $subtotal,
            'descuento'      => $descuento,
            'iva'            => $monto_iva,
            'total'          => $total_estimado,
            'cliente'        => $nombre_cliente,
            'correo_cliente' => $correo_cliente
        ];

        // Preparar envío a buzón de correo del estudio
        $correo_estudio = "cotizaciones@studiotrack.local";
        $asunto = "Nueva Cotización StudioTrack - " . $eq_sel['codigo'];
        $cuerpo = "Se ha recibido una solicitud de cotización:\n\n"
                . "Cliente: $nombre_cliente\n"
                . "Correo: $correo_cliente\n"
                . "Equipo: " . $resumen_cotizacion['equipo'] . "\n"
                . "Días de alquiler: $dias\n"
                . "Subtotal: $" . number_format($subtotal, 2) . "\n"
                . "Descuento: -$" . number_format($descuento, 2) . "\n"
                . "IVA (16%): $" . number_format($monto_iva, 2) . "\n"
                . "Total estimado: $" . number_format($total_estimado, 2) . "\n"
                . "Notas: " . ($notas !== "" ? $notas : "Ninguna") . "\n";

        $cabeceras = "From: noreply@studiotrack.local\r\n"
                   . "Reply-To: " . $correo_cliente . "\r\n"
                   . "X-Mailer: PHP/" . phpversion();

        // @mail() evita advertencias ruidosas si el servidor local no tiene sendmail configurado
        $enviado = @mail($correo_estudio, $asunto, $cuerpo, $cabeceras);

        if ($enviado) {
            $mensaje_alerta = "Cotización calculada y enviada correctamente al buzón del estudio.";
            $tipo_alerta    = "ok";
        } else {
            $mensaje_alerta = "Cotización calculada en pantalla. (Nota: en entornos locales sin servidor SMTP configurado, mail() puede requerir configurar sendmail/SMTP o probar en hosting).";
            $tipo_alerta    = "ok";
        }
    }
}
?>

<div class="card">
    <h2>Solicitar Cotización de Alquiler</h2>
    <p>Calcula tu presupuesto estimado y envía la solicitud al buzón de correo mediante <code>$_POST</code>.</p>

    <?php if ($mensaje_alerta !== ""): ?>
        <div style="padding: 0.8rem 1rem; border-radius: 6px; margin-bottom: 1rem; background: <?php echo ($tipo_alerta === 'ok') ? '#065f46' : '#991b1b'; ?>; color: #fff;">
            <?php echo htmlspecialchars($mensaje_alerta); ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="cotizar.php">
        <label for="equipo_id">Selecciona un equipo</label>
        <select name="equipo_id" id="equipo_id" required>
            <option value="">-- Elige un equipo --</option>
            <?php foreach ($lista_equipos as $eq): ?>
                <?php
                $seleccionado = "";
                if (isset($_POST['equipo_id']) && (int)$_POST['equipo_id'] === (int)$eq['id']) {
                    $seleccionado = "selected";
                } elseif (!isset($_POST['equipo_id']) && $equipo_preseleccionado === (int)$eq['id']) {
                    $seleccionado = "selected";
                }
                ?>
                <option value="<?php echo (int)$eq['id']; ?>" <?php echo $seleccionado; ?>>
                    <?php echo htmlspecialchars($eq['codigo'] . " - " . $eq['nombre'] . " ($" . number_format((float)$eq['tarifa_dia'], 2) . "/día)"); ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label for="dias">Cantidad de días de alquiler (7 días o más tienen 10% de descuento)</label>
        <input type="number" name="dias" id="dias" min="1" value="<?php echo isset($_POST['dias']) ? (int)$_POST['dias'] : 1; ?>" required>

        <label for="nombre_cliente">Tu nombre o productora</label>
        <input type="text" name="nombre_cliente" id="nombre_cliente" value="<?php echo isset($_POST['nombre_cliente']) ? htmlspecialchars($_POST['nombre_cliente']) : ''; ?>" required>

        <label for="correo_cliente">Correo electrónico de contacto</label>
        <input type="email" name="correo_cliente" id="correo_cliente" value="<?php echo isset($_POST['correo_cliente']) ? htmlspecialchars($_POST['correo_cliente']) : ''; ?>" required>

        <label for="notas">Notas adicionales (opcional)</label>
        <textarea name="notas" id="notas" rows="3" placeholder="Locación, horario de retiro o accesorios requeridos..."><?php echo isset($_POST['notas']) ? htmlspecialchars($_POST['notas']) : ''; ?></textarea>

        <button type="submit" class="btn">Calcular y Enviar Cotización</button>
    </form>
</div>

<?php if ($resumen_cotizacion !== null): ?>
    <div class="card">
        <h3>Resumen de Cotización Estimada</h3>
        <p><strong>Cliente:</strong> <?php echo htmlspecialchars($resumen_cotizacion['cliente']); ?> (<?php echo htmlspecialchars($resumen_cotizacion['correo_cliente']); ?>)</p>
        <p><strong>Equipo:</strong> <?php echo htmlspecialchars($resumen_cotizacion['equipo']); ?></p>
        <p><strong>Días:</strong> <?php echo (int)$resumen_cotizacion['dias']; ?> días × $<?php echo number_format($resumen_cotizacion['tarifa_dia'], 2); ?></p>
        <p><strong>Subtotal:</strong> $<?php echo number_format($resumen_cotizacion['subtotal'], 2); ?></p>
        <p><strong>Descuento semanal:</strong> -$<?php echo number_format($resumen_cotizacion['descuento'], 2); ?></p>
        <p><strong>IVA (16%):</strong> $<?php echo number_format($resumen_cotizacion['iva'], 2); ?></p>
        <hr style="border-color: #334155;">
        <p style="font-size: 1.2rem; color: #38bdf8;"><strong>Total Estimado: $<?php echo number_format($resumen_cotizacion['total'], 2); ?></strong></p>
    </div>
<?php endif; ?>

<?php include 'includes/footer.php'; ?>
