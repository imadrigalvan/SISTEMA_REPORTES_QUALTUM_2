<?php
include("../../includes/layout.php");
include("../../includes/conexion.php");

// VALIDAR ID
if(!isset($_GET['id'])){
    die("ID no proporcionado");
}

$id = $_GET['id'];

// 🔧 CORRECCIÓN: usar a.area en lugar de a.nombre_area
$sql = "SELECT r.*, 
               a.area AS nombre_area,
               u.nombre AS inspector,
               s.nombre AS supervisor
        FROM reportes r
        LEFT JOIN areas a ON r.area_id = a.id
        LEFT JOIN usuarios u ON r.inspector_id = u.id
        LEFT JOIN usuarios s ON r.firmado_por = s.id
        WHERE r.id = $id";

$result = $conexion->query($sql);

if($result->num_rows == 0){
    die("Reporte no encontrado");
}

$reporte = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Detalle del Reporte - Qualtum</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .signature-pad {
            border: 1px solid #ddd;
            border-radius: 4px;
            background-color: white;
        }
        #signature-canvas {
            display: block;
            width: 100%;
            height: 100%;
        }
    </style>
</head>
<body class="bg-light">

<div class="container mt-4">

    <div class="card shadow">

        <div class="card-header bg-primary text-white">
            <h4>Detalle del Reporte - Folio: <?php echo $reporte['folio']; ?></h4>
        </div>

        <div class="card-body">

            <div class="row mb-4">
                <div class="col-md-6">
                    <p><strong>Fecha:</strong> <?php echo $reporte['fecha']; ?></p>
                    <p><strong>Turno:</strong> <?php echo $reporte['turno']; ?></p>
                    <p><strong>Área:</strong> <?php echo $reporte['nombre_area']; ?></p>
                    <?php if(!empty($reporte['linea'])): ?>
                        <p><strong>Línea:</strong> <?php echo $reporte['linea']; ?></p>
                    <?php endif; ?>
                </div>
                <div class="col-md-6">
                    <p><strong>Inspector:</strong> <?php echo $reporte['inspector']; ?></p>
                    <p><strong>Supervisor:</strong> 
                        <?php echo $reporte['supervisor'] ? $reporte['supervisor'] : "Pendiente"; ?>
                    </p>
                    <p><strong>Estado:</strong> 
                        <?php if($reporte['estado'] == 'pendiente'){ ?>
                            <span class="badge bg-warning">Pendiente</span>
                        <?php } else { ?>
                            <span class="badge bg-success">Firmado</span>
                        <?php } ?>
                    </p>
                </div>
            </div>

            <hr>

            <h5 class="mb-3">Datos de Producción</h5>
            <div class="row">
                <div class="col-md-4">
                    <p><strong>Piezas OK:</strong> <?php echo $reporte['piezas_ok']; ?></p>
                </div>
                <div class="col-md-4">
                    <p><strong>Piezas Scrap:</strong> <?php echo $reporte['piezas_scrap']; ?></p>
                </div>
                <div class="col-md-4">
                    <p><strong>Rebajadas:</strong> <?php echo $reporte['rebajadas'] ?? '0'; ?></p>
                </div>
            </div>

            <div class="mt-3">
                <h6>Condiciones de Calidad</h6>
                <?php
                $sql_condiciones = "SELECT * FROM condiciones WHERE reporte_id = $id";
                $condiciones = $conexion->query($sql_condiciones);
                
                if($condiciones->num_rows > 0) {
                    echo '<ul>';
                    while($cond = $condiciones->fetch_assoc()) {
                        echo '<li>'.$cond['condicion'].': '.$cond['cantidad'].'</li>';
                    }
                    echo '</ul>';
                } else {
                    echo '<p class="text-muted">No se registraron condiciones</p>';
                }
                ?>
            </div>

            <hr>

            <h5 class="mb-3">Tiempos</h5>
            <div class="row">
                <div class="col-md-3">
                    <p><strong>Hora inicio:</strong> <?php echo $reporte['hora_inicio']; ?></p>
                </div>
                <div class="col-md-3">
                    <p><strong>Hora término:</strong> <?php echo $reporte['hora_fin']; ?></p>
                </div>
                <div class="col-md-3">
                    <p><strong>Horas efectivas:</strong> <?php echo $reporte['horas_efectivas'] ?? '0'; ?></p>
                </div>
                <div class="col-md-3">
                    <p><strong>Tiempo comida:</strong> <?php echo $reporte['tiempo_comida'] ?? '0'; ?></p>
                </div>
            </div>

            <hr>

            <div class="mb-3">
                <h5>Descripción</h5>
                <p><?php echo nl2br($reporte['descripcion']); ?></p>
            </div>

            <div class="mb-3">
                <h5>Motivo</h5>
                <p><?php echo nl2br($reporte['motivo']); ?></p>
            </div>

            <?php if(!empty($reporte['comentarios'])): ?>
            <div class="mb-3">
                <h5>Comentarios</h5>
                <p><?php echo nl2br($reporte['comentarios']); ?></p>
            </div>
            <?php endif; ?>

            <hr>

            <h5 class="mt-4">Firma del Supervisor</h5>
            
            <?php if($reporte['estado'] == 'pendiente' && $_SESSION['rol'] == 'supervisor') { ?>
                <div id="firmaContainer">
                    <div id="signature-pad" class="signature-pad mb-2">
                        <canvas id="signature-canvas"></canvas>
                    </div>
                    <button id="clear-signature" class="btn btn-secondary btn-sm">Limpiar Firma</button>
                    
                    <form method="POST" action="../../controllers/firmar_reporte.php" class="mt-3">
                        <input type="hidden" name="id" value="<?php echo $reporte['id']; ?>">
                        <input type="hidden" name="firma_data" id="firma_data">
                        <button type="submit" class="btn btn-success">Guardar Firma</button>
                    </form>
                </div>
            <?php } elseif($reporte['estado'] == 'firmado' && !empty($reporte['firma_path'])) { 
                // Convertir ruta relativa a absoluta
                $ruta_firma = ltrim(str_replace('..', '', $reporte['firma_path']), '/');
                $ruta_firma = '/SISTEMA_REPORTES_QUALTUM_2/' . $ruta_firma;
            ?>
                <div class="mt-3">
                    <?php if(file_exists($_SERVER['DOCUMENT_ROOT'] . $ruta_firma)): ?>
                        <img src="<?php echo $ruta_firma; ?>" alt="Firma del supervisor" 
                             style="max-width: 300px; border: 1px solid #ddd; background: white; padding: 10px;">
                        <p class="text-muted mt-2">
                            Firmado por: <?php echo $reporte['supervisor']; ?> el <?php echo $reporte['fecha_firma']; ?>
                        </p>
                    <?php else: ?>
                        <p class="text-danger">La imagen de la firma no se encuentra en el servidor.</p>
                    <?php endif; ?>
                </div>
            <?php } else { ?>
                <p class="text-muted">Pendiente de firma</p>
            <?php } ?>

            <div class="mt-4">
                <a href="<?php echo ($_SESSION['rol'] == 'supervisor') ? 'reportes.php' : '../inspector/reportes.php'; ?>" 
                   class="btn btn-primary">
                    Volver a Reportes
                </a>
                <a href="../../controllers/descargar_pdf_fpdf.php?id=<?php echo $reporte['id']; ?>" class="btn btn-danger" target="_blank">📄 Descargar PDF</a>
            </div>

        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/signature_pad@4.0.0/dist/signature_pad.umd.min.js"></script>
<script>
    const canvas = document.getElementById('signature-canvas');
    const signaturePad = new SignaturePad(canvas, { backgroundColor: 'rgb(255, 255, 255)' });
    
    function resizeCanvas() {
        const ratio = Math.max(window.devicePixelRatio || 1, 1);
        canvas.width = canvas.offsetWidth * ratio;
        canvas.height = canvas.offsetHeight * ratio;
        canvas.getContext('2d').scale(ratio, ratio);
        signaturePad.clear();
    }
    
    window.addEventListener('resize', resizeCanvas);
    document.getElementById('clear-signature').addEventListener('click', function() {
        signaturePad.clear();
    });
    
    document.querySelector('form').addEventListener('submit', function(e) {
        if(signaturePad.isEmpty()) {
            alert('Por favor, realice su firma antes de guardar');
            e.preventDefault();
        } else {
            document.getElementById('firma_data').value = signaturePad.toDataURL('image/png');
        }
    });
    
    resizeCanvas();
</script>

</body>
</html>