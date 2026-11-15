<?php
include("../../includes/session.php");
include("../../includes/conexion.php");

if(!isset($_GET['id']) || empty($_GET['id'])) {
    die("ID de reporte no especificado.");
}

$id = (int)$_GET['id'];
$inspector_id = $_SESSION['id']; // <-- CAMBIO: obtener ID del inspector

$sql = "SELECT r.*, 
               a.nombre_area, 
               a.area AS nombre_area_tipo,
               a.linea AS area_linea,
               u.nombre AS inspector_nombre,
               s.nombre AS supervisor_nombre
        FROM reportes r
        LEFT JOIN areas a ON r.area_id = a.id
        LEFT JOIN usuarios u ON r.inspector_id = u.id
        LEFT JOIN usuarios s ON r.firmado_por = s.id
        WHERE r.id = $id AND r.inspector_id = $inspector_id"; // <-- CAMBIO: añadir filtro

$resultado = $conexion->query($sql);

if($resultado->num_rows == 0) {
    die("Reporte no encontrado.");
}

$reporte = $resultado->fetch_assoc();

$condiciones = [];
$sql_cond = "SELECT condicion, cantidad FROM condiciones WHERE reporte_id = $id";
$res_cond = $conexion->query($sql_cond);
while($cond = $res_cond->fetch_assoc()) {
    $condiciones[] = $cond;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Detalle del Reporte - Qualtum</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-4">
    <div class="card shadow">
        <div class="card-header bg-primary text-white d-flex justify-content-between">
            <h4>Detalle del Reporte - Folio: <?php echo htmlspecialchars($reporte['folio']); ?></h4>
            <span class="badge <?php echo ($reporte['estado'] == 'pendiente') ? 'bg-warning' : 'bg-success'; ?>">
                <?php echo ucfirst($reporte['estado']); ?>
            </span>
        </div>
        <div class="card-body">

            <h5>1. Datos Generales</h5>
            <div class="row">
                <div class="col-md-3"><strong>Turno:</strong> <?php echo htmlspecialchars($reporte['turno']); ?></div>
                <div class="col-md-5"><strong>Parte:</strong> <?php echo htmlspecialchars($reporte['nombre_parte']); ?></div>
                <div class="col-md-4"><strong>Folio:</strong> <?php echo htmlspecialchars($reporte['folio']); ?></div>
            </div>
            <div class="mt-2"><strong>Fecha:</strong> <?php echo htmlspecialchars($reporte['fecha']); ?></div>
            <hr>

            <h5>2. Producción</h5>
            <div class="row">
                <div class="col-md-4"><strong>Cantidad inspeccionada:</strong> <?php echo (int)$reporte['cantidad']; ?></div>
                <div class="col-md-4"><strong>Piezas OK:</strong> <?php echo (int)$reporte['piezas_ok']; ?></div>
                <div class="col-md-4"><strong>Piezas Scrap:</strong> <?php echo (int)$reporte['piezas_scrap']; ?></div>
            </div>
            <?php if(!empty($condiciones)): ?>
                <div class="mt-3">
                    <strong>Condiciones de Scrap:</strong>
                    <ul>
                        <?php foreach($condiciones as $c): ?>
                            <li><?php echo htmlspecialchars($c['condicion']) . ": " . (int)$c['cantidad']; ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
            <hr>

            <h5>3. Tiempo</h5>
            <div class="row">
                <div class="col-md-4"><strong>Inspector:</strong> <?php echo htmlspecialchars($reporte['inspector_nombre']); ?></div>
                <div class="col-md-4"><strong>Hora inicio:</strong> <?php echo htmlspecialchars($reporte['hora_inicio']); ?></div>
                <div class="col-md-4"><strong>Hora término:</strong> <?php echo htmlspecialchars($reporte['hora_fin']); ?></div>
            </div>
            <div class="row mt-2">
                <div class="col-md-4"><strong>Horas efectivas:</strong> <?php echo (float)$reporte['horas_efectivas']; ?></div>
                <div class="col-md-4"><strong>Tiempo comida (min):</strong> <?php echo htmlspecialchars($reporte['tiempo_comida']); ?></div>
            </div>
            <hr>

            <h5>4. Información Final</h5>
            <div class="row">
                <div class="col-md-4"><strong>Área:</strong> <?php echo htmlspecialchars($reporte['nombre_area_tipo']); ?></div>
                <?php if(!empty($reporte['linea'])): ?>
                    <div class="col-md-4"><strong>Línea:</strong> <?php echo htmlspecialchars($reporte['linea']); ?></div>
                <?php endif; ?>
            </div>
            <div class="mt-2">
                <strong>Descripción:</strong>
                <p><?php echo nl2br(htmlspecialchars($reporte['descripcion'])); ?></p>
            </div>
            <div>
                <strong>Motivo:</strong>
                <p><?php echo nl2br(htmlspecialchars($reporte['motivo'])); ?></p>
            </div>
            <div>
                <strong>Rebajadas:</strong> <?php echo (int)$reporte['rebajadas']; ?>
            </div>
            <?php if(!empty($reporte['comentarios'])): ?>
                <div>
                    <strong>Comentarios:</strong>
                    <p><?php echo nl2br(htmlspecialchars($reporte['comentarios'])); ?></p>
                </div>
            <?php endif; ?>
            <hr>

            <h5>Firma del Supervisor</h5>
            <?php if($reporte['estado'] == 'firmado' && !empty($reporte['firma_path'])): 
                $ruta_firma = ltrim(str_replace('..', '', $reporte['firma_path']), '/');
                $ruta_firma = '/SISTEMA_REPORTES_QUALTUM_2/' . $ruta_firma;
            ?>
                <?php if(file_exists($_SERVER['DOCUMENT_ROOT'] . $ruta_firma)): ?>
                    <img src="<?php echo $ruta_firma; ?>" alt="Firma" style="max-width: 250px; border:1px solid #ddd;">
                    <p class="text-muted">Firmado por: <?php echo htmlspecialchars($reporte['supervisor_nombre']); ?> el <?php echo date("d/m/Y H:i", strtotime($reporte['fecha_firma'])); ?></p>
                <?php else: ?>
                    <p class="text-danger">La imagen de la firma no está disponible.</p>
                <?php endif; ?>
            <?php else: ?>
                <p class="text-muted">Pendiente de firma</p>
            <?php endif; ?>

            <div class="d-flex justify-content-between mt-4">
                <a href="<?php echo ($_SESSION['rol'] == 'supervisor') ? 'reportes.php' : '../inspector/reportes.php'; ?>" class="btn btn-secondary">Volver a Reportes</a>

                <a href="../../controllers/descargar_pdf_fpdf.php?id=<?php echo $reporte['id']; ?>" class="btn btn-danger" target="_blank">📄 PDF</a>
                <?php if($reporte['estado'] == 'pendiente' && $_SESSION['rol'] == 'supervisor'): ?>
                    <a href="firmar_reporte.php?id=<?php echo $reporte['id']; ?>" class="btn btn-success">Firmar Reporte</a>
                <?php endif; ?>
            </div>

        </div>
    </div>
</div>

</body>
</html>