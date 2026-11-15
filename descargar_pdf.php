<?php
session_start();
include("../includes/conexion.php");

if(!isset($_GET['id']) || empty($_GET['id'])) {
    die("ID de reporte no especificado.");
}

$id = (int)$_GET['id'];

require_once('../tcpdf/tcpdf.php');

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
        WHERE r.id = $id";
$resultado = $conexion->query($sql);
if($resultado->num_rows == 0) die("Reporte no encontrado.");
$reporte = $resultado->fetch_assoc();

$condiciones = [];
$sql_cond = "SELECT condicion, cantidad FROM condiciones WHERE reporte_id = $id";
$res_cond = $conexion->query($sql_cond);
while($cond = $res_cond->fetch_assoc()) {
    $condiciones[] = $cond;
}

$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

$pdf->SetCreator('Qualtum System');
$pdf->SetAuthor('Sistema de Reportes Qualtum');
$pdf->SetTitle('Reporte de Inspección - ' . $reporte['folio']);
$pdf->SetSubject('Detalle del Reporte de Inspección');

$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);

$pdf->AddPage();

$pdf->SetFont('helvetica', '', 10);

$html = '
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        h4 { color: #1e3c72; margin-top: 15px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .header { text-align: center; margin-bottom: 20px; }
        .firma { margin-top: 30px; border-top: 1px solid #000; width: 200px; text-align: center; }
    </style>
</head>
<body>
    <div class="header">
        <h3>Qualtum - Reporte de Inspección</h3>
        <p>Folio: ' . htmlspecialchars($reporte['folio']) . ' | Fecha: ' . $reporte['fecha'] . '</p>
    </div>

    <h4>1. Datos Generales</h4>
    <table>
        <tr><th>Turno</th><td>' . $reporte['turno'] . '</td></tr>
        <tr><th>Parte</th><td>' . $reporte['nombre_parte'] . '</td></tr>
        <tr><th>Área</th><td>' . $reporte['nombre_area_tipo'] . '</td></tr>';

if(!empty($reporte['linea'])) {
    $html .= '<tr><th>Línea</th><td>' . $reporte['linea'] . '</td></tr>';
}

$html .= '
    </table>

    <h4>2. Producción</h4>
    <table>
        <tr><th>Cantidad inspeccionada</th><td>' . (int)$reporte['cantidad'] . '</td></tr>
        <tr><th>Piezas OK</th><td>' . (int)$reporte['piezas_ok'] . '</td></tr>
        <tr><th>Piezas Scrap</th><td>' . (int)$reporte['piezas_scrap'] . '</td></tr>';

if(!empty($condiciones)) {
    $html .= '<tr><th>Condiciones de scrap</th><td>';
    foreach($condiciones as $c) {
        $html .= $c['condicion'] . ": " . $c['cantidad'] . "<br>";
    }
    $html .= '</td></tr>';
}

$html .= '
        <tr><th>Rebajadas</th><td>' . (int)$reporte['rebajadas'] . '</td></tr>
    </table>

    <h4>3. Tiempos</h4>
    <table>
        <tr><th>Inspector</th><td>' . $reporte['inspector_nombre'] . '</td></tr>
        <tr><th>Hora inicio</th><td>' . $reporte['hora_inicio'] . '</td></tr>
        <tr><th>Hora fin</th><td>' . $reporte['hora_fin'] . '</td></tr>
        <tr><th>Horas efectivas</th><td>' . $reporte['horas_efectivas'] . '</td></tr>
        <tr><th>Tiempo comida</th><td>' . $reporte['tiempo_comida'] . '</td></tr>
    </table>

    <h4>4. Información Final</h4>
    <table>
        <tr><th>Descripción</th><td>' . nl2br($reporte['descripcion']) . '</td></tr>
        <tr><th>Motivo</th><td>' . nl2br($reporte['motivo']) . '</td></tr>
        <tr><th>Comentarios</th><td>' . nl2br($reporte['comentarios']) . '</td></tr>
    </table>';

if($reporte['estado'] == 'firmado') {
    $html .= '
    <div class="firma">
        <p>Firmado por: ' . $reporte['supervisor_nombre'] . '</p>
        <p>Fecha firma: ' . date("d/m/Y H:i", strtotime($reporte['fecha_firma'])) . '</p>
    </div>';
} else {
    $html .= '
    <div class="firma">
        <p>Pendiente de firma</p>
    </div>';
}

$html .= '
</body>
</html>';

$pdf->writeHTML($html, true, false, true, false, '');

$pdf->Output('reporte_' . $reporte['folio'] . '.pdf', 'I'); 
?>