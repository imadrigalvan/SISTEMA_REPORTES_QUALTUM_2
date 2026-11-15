<?php
session_start();
ob_clean(); // Limpiar buffers anteriores
ob_start(); // Iniciar buffer limpio

include("../includes/conexion.php");
require_once("../includes/fpdf.php");

if(!isset($_GET['id']) || empty($_GET['id'])) die("ID de reporte no especificado.");
$id = (int)$_GET['id'];

// Función alternativa a utf8_decode para PHP 8.2+
function convertir_utf8($texto) {
    if ($texto === null) return '';
    // Convertir a ISO-8859-1 si es necesario
    if (mb_detect_encoding($texto, 'UTF-8', true)) {
        return mb_convert_encoding($texto, 'ISO-8859-1', 'UTF-8');
    }
    return $texto;
}

// Obtener datos del reporte
$sql = "SELECT r.*, a.area AS nombre_area_tipo, u.nombre AS inspector_nombre, s.nombre AS supervisor_nombre
        FROM reportes r
        LEFT JOIN areas a ON r.area_id = a.id
        LEFT JOIN usuarios u ON r.inspector_id = u.id
        LEFT JOIN usuarios s ON r.firmado_por = s.id
        WHERE r.id = $id";
$resultado = $conexion->query($sql);
if($resultado->num_rows == 0) die("Reporte no encontrado.");
$reporte = $resultado->fetch_assoc();

// Obtener condiciones
$condiciones = [];
$sql_cond = "SELECT condicion, cantidad FROM condiciones WHERE reporte_id = $id";
$res_cond = $conexion->query($sql_cond);
while($cond = $res_cond->fetch_assoc()) $condiciones[] = $cond;

// Crear PDF
$pdf = new FPDF();
$pdf->AddPage();

// Logo (opcional)
if(file_exists('../assets/logo.png')) {
    $pdf->Image('../assets/logo.png', 10, 6, 30);
}
$pdf->SetFont('Arial', 'B', 16);
$pdf->Cell(0, 10, convertir_utf8('Qualtum - Reporte de Inspección'), 0, 1, 'C');
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(0, 6, convertir_utf8('Folio: ' . ($reporte['folio'] ?? '') . ' | Fecha: ' . ($reporte['fecha'] ?? '')), 0, 1, 'C');
$pdf->Ln(8);

// 1. Datos Generales
$pdf->SetFont('Arial', 'B', 11);
$pdf->Cell(0, 8, convertir_utf8('1. Datos Generales'), 0, 1, 'L');
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(40, 6, convertir_utf8('Turno:'), 0, 0);
$pdf->Cell(0, 6, convertir_utf8($reporte['turno'] ?? ''), 0, 1);
$pdf->Cell(40, 6, convertir_utf8('Parte:'), 0, 0);
$pdf->Cell(0, 6, convertir_utf8($reporte['nombre_parte'] ?? ''), 0, 1);
$pdf->Cell(40, 6, convertir_utf8('Área:'), 0, 0);
$pdf->Cell(0, 6, convertir_utf8($reporte['nombre_area_tipo'] ?? ''), 0, 1);
if(!empty($reporte['linea'])) {
    $pdf->Cell(40, 6, convertir_utf8('Línea:'), 0, 0);
    $pdf->Cell(0, 6, convertir_utf8($reporte['linea']), 0, 1);
}
$pdf->Ln(4);

// 2. Producción
$pdf->SetFont('Arial', 'B', 11);
$pdf->Cell(0, 8, convertir_utf8('2. Producción'), 0, 1, 'L');
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(50, 6, convertir_utf8('Cantidad inspeccionada:'), 0, 0);
$pdf->Cell(0, 6, (int)($reporte['cantidad'] ?? 0), 0, 1);
$pdf->Cell(50, 6, convertir_utf8('Piezas OK:'), 0, 0);
$pdf->Cell(0, 6, (int)($reporte['piezas_ok'] ?? 0), 0, 1);
$pdf->Cell(50, 6, convertir_utf8('Piezas Scrap:'), 0, 0);
$pdf->Cell(0, 6, (int)($reporte['piezas_scrap'] ?? 0), 0, 1);
$pdf->Cell(50, 6, convertir_utf8('Rebajadas:'), 0, 0);
$pdf->Cell(0, 6, (int)($reporte['rebajadas'] ?? 0), 0, 1);
if(!empty($condiciones)) {
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell(0, 6, convertir_utf8('Condiciones de scrap:'), 0, 1);
    $pdf->SetFont('Arial', '', 10);
    foreach($condiciones as $c) {
        $pdf->Cell(10, 5, '', 0, 0);
        $pdf->Cell(0, 5, convertir_utf8($c['condicion'] . ': ' . $c['cantidad']), 0, 1);
    }
}
$pdf->Ln(4);

// 3. Tiempo
$pdf->SetFont('Arial', 'B', 11);
$pdf->Cell(0, 8, convertir_utf8('3. Tiempo'), 0, 1, 'L');
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(40, 6, convertir_utf8('Inspector:'), 0, 0);
$pdf->Cell(0, 6, convertir_utf8($reporte['inspector_nombre'] ?? ''), 0, 1);
$pdf->Cell(40, 6, convertir_utf8('Hora inicio:'), 0, 0);
$pdf->Cell(0, 6, convertir_utf8($reporte['hora_inicio'] ?? ''), 0, 1);
$pdf->Cell(40, 6, convertir_utf8('Hora término:'), 0, 0);
$pdf->Cell(0, 6, convertir_utf8($reporte['hora_fin'] ?? ''), 0, 1);
$pdf->Cell(40, 6, convertir_utf8('Horas efectivas:'), 0, 0);
$pdf->Cell(0, 6, (float)($reporte['horas_efectivas'] ?? 0), 0, 1);
$pdf->Cell(40, 6, convertir_utf8('Tiempo comida:'), 0, 0);
$pdf->Cell(0, 6, convertir_utf8($reporte['tiempo_comida'] ?? ''), 0, 1);
$pdf->Ln(4);

// 4. Información Final
$pdf->SetFont('Arial', 'B', 11);
$pdf->Cell(0, 8, convertir_utf8('4. Información Final'), 0, 1, 'L');
$pdf->SetFont('Arial', '', 10);
$pdf->MultiCell(0, 6, convertir_utf8('Descripción: ' . ($reporte['descripcion'] ?? '')), 0, 1);
$pdf->MultiCell(0, 6, convertir_utf8('Motivo: ' . ($reporte['motivo'] ?? '')), 0, 1);
if(!empty($reporte['comentarios']))
    $pdf->MultiCell(0, 6, convertir_utf8('Comentarios: ' . $reporte['comentarios']), 0, 1);
$pdf->Ln(4);

// 5. FIRMA
if($reporte['estado'] == 'firmado' && !empty($reporte['firma_path'])) {
    $pdf->SetFont('Arial', 'B', 11);
    $pdf->Cell(0, 8, convertir_utf8('Firma del Supervisor'), 0, 1, 'L');
    
    $ruta_firma = str_replace('..', '', $reporte['firma_path']);
    $ruta_firma = $_SERVER['DOCUMENT_ROOT'] . '/SISTEMA_REPORTES_QUALTUM_2/' . ltrim($ruta_firma, '/');
    
    if(file_exists($ruta_firma)) {
        $pdf->Image($ruta_firma, $pdf->GetX(), $pdf->GetY(), 60);
        $pdf->Ln(40);
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(0, 6, convertir_utf8('Firmado por: ' . ($reporte['supervisor_nombre'] ?? '')), 0, 1);
        $pdf->Cell(0, 6, convertir_utf8('Fecha firma: ' . date("d/m/Y H:i", strtotime($reporte['fecha_firma'] ?? ''))), 0, 1);
    } else {
        $pdf->SetFont('Arial', 'I', 10);
        $pdf->Cell(0, 6, convertir_utf8('Firma no disponible en el servidor.'), 0, 1);
    }
} else {
    $pdf->SetFont('Arial', 'I', 10);
    $pdf->Cell(0, 6, convertir_utf8('Reporte pendiente de firma'), 0, 1);
}

// Limpiar buffer y enviar PDF
ob_end_clean();
$pdf->Output('I', "reporte_{$reporte['folio']}.pdf");
exit;
?>