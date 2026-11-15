<?php
session_start();
include("../includes/conexion.php");

if($_POST){
    $camposObligatorios = [
        'turno', 'nombre_parte', 'folio', 'fecha', 
        'cantidad', 'piezas_ok', 'piezas_scrap',
        'hora_inicio', 'hora_fin', 'horas_efectivas',
        'area', 'descripcion', 'motivo'
    ];
    
    foreach($camposObligatorios as $campo) {
        if(empty($_POST[$campo])) {
            die("El campo $campo es obligatorio");
        }
    }
    
    if($_POST['piezas_scrap'] > 0 && empty($_POST['condicion'])) {
        die("Debe agregar condiciones de calidad para las piezas scrap");
    }


    $turno = $conexion->real_escape_string($_POST['turno']);
    $nombre_parte = $conexion->real_escape_string($_POST['nombre_parte']);
    $folio = $conexion->real_escape_string($_POST['folio']);
    $fecha = $conexion->real_escape_string($_POST['fecha']);
    $cantidad = (int)$_POST['cantidad'];
    $piezas_ok = (int)$_POST['piezas_ok'];
    $piezas_scrap = (int)$_POST['piezas_scrap'];
    $area = $conexion->real_escape_string($_POST['area']);
    $area_id = (int)$_POST['area_id'];
    $linea = isset($_POST['linea']) ? $conexion->real_escape_string($_POST['linea']) : '';
    $descripcion = $conexion->real_escape_string($_POST['descripcion']);
    $motivo = $conexion->real_escape_string($_POST['motivo']);
    $hora_inicio = $conexion->real_escape_string($_POST['hora_inicio']);
    $hora_fin = $conexion->real_escape_string($_POST['hora_fin']);
    $horas_efectivas = (float)$_POST['horas_efectivas'];
    $tiempo_comida = isset($_POST['tiempo_comida']) ? $conexion->real_escape_string($_POST['tiempo_comida']) : '';
    $rebajadas = isset($_POST['rebajadas']) ? (int)$_POST['rebajadas'] : 0;
    $comentarios = isset($_POST['comentarios']) ? $conexion->real_escape_string($_POST['comentarios']) : '';
    $inspector_id = (int)$_SESSION['id'];
    $rol_creador = $conexion->real_escape_string($_SESSION['rol']);

$sql = "INSERT INTO reportes (
    turno, nombre_parte, folio, fecha, inspector_id,
    piezas_ok, piezas_scrap, cantidad, area_id, linea, descripcion, motivo,
    hora_inicio, hora_fin, horas_efectivas, tiempo_comida,
    rebajadas, comentarios, rol_creador, usuario_id
) VALUES (
    '$turno', '$nombre_parte', '$folio', '$fecha', '$inspector_id',
    '$piezas_ok', '$piezas_scrap', '$cantidad', '$area_id', '$linea', '$descripcion', '$motivo',
    '$hora_inicio', '$hora_fin', '$horas_efectivas', '$tiempo_comida',
    '$rebajadas', '$comentarios', '$rol_creador', '$inspector_id'
)";
    if($conexion->query($sql)){
        $reporte_id = $conexion->insert_id;
        
        if(isset($_POST['condicion']) && is_array($_POST['condicion'])){
            foreach($_POST['condicion'] as $index => $condicion){
                if(!empty($condicion) && isset($_POST['cantidad_condicion'][$index])){
                    $condicion = $conexion->real_escape_string($condicion);
                    $cantidad_cond = (int)$_POST['cantidad_condicion'][$index];
                    
                    $sql_condicion = "INSERT INTO condiciones (reporte_id, condicion, cantidad)
                                     VALUES ('$reporte_id', '$condicion', '$cantidad_cond')";
                    $conexion->query($sql_condicion);
                }
            }
        }
        
        if($_SESSION['rol'] == 'supervisor') {
            header("Location: ../views/supervisor/reportes.php");
        } else {
            header("Location: ../views/inspector/reportes.php");
        }
    } else {
        die("Error al guardar el reporte: " . $conexion->error);
    }
}
?>