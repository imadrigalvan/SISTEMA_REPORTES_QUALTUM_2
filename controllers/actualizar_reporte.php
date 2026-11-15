<?php
session_start();
include("../includes/conexion.php");

// Verificar autenticación
if(!isset($_SESSION['id'])){
    die("Acceso no autorizado.");
}

$id = (int)$_POST['id'];
$rol = $_SESSION['rol'];
$usuario_id = $_SESSION['id'];

// Verificar que el reporte existe y obtener su estado e inspector_id
$check = $conexion->query("SELECT estado, inspector_id FROM reportes WHERE id = $id");
if($check->num_rows == 0){
    die("Reporte no encontrado.");
}
$data = $check->fetch_assoc();

// 1. No se puede editar si ya está firmado
if($data['estado'] == 'firmado'){
    die("No se puede editar un reporte que ya está firmado.");
}

// 2. Si es inspector, solo puede editar sus propios reportes
if($rol == 'inspector' && $data['inspector_id'] != $usuario_id){
    die("No tienes permiso para editar este reporte.");
}

// Si es supervisor, puede editar cualquier reporte pendiente (ya verificado que no esté firmado)

// Recibir y sanitizar datos del formulario
$folio = $conexion->real_escape_string($_POST['folio']);
$fecha = $conexion->real_escape_string($_POST['fecha']);
$turno = $conexion->real_escape_string($_POST['turno']);
$piezas_ok = (int)$_POST['piezas_ok'];
$piezas_scrap = (int)$_POST['piezas_scrap'];
$rebajadas = (int)$_POST['rebajadas'];
$descripcion = $conexion->real_escape_string($_POST['descripcion']);
$motivo = $conexion->real_escape_string($_POST['motivo']);
$comentarios = isset($_POST['comentarios']) ? $conexion->real_escape_string($_POST['comentarios']) : '';

// Actualizar reporte
$sql = "UPDATE reportes SET 
        folio = '$folio',
        fecha = '$fecha',
        turno = '$turno',
        piezas_ok = $piezas_ok,
        piezas_scrap = $piezas_scrap,
        rebajadas = $rebajadas,
        descripcion = '$descripcion',
        motivo = '$motivo',
        comentarios = '$comentarios'
        WHERE id = $id";

if($conexion->query($sql)){
    // Redirigir según rol con mensaje de éxito
    if($rol == 'supervisor'){
        header("Location: ../views/supervisor/reportes.php?success=1");
    } else {
        header("Location: ../views/inspector/reportes.php?success=1");
    }
    exit;
} else {
    die("Error al actualizar el reporte: " . $conexion->error);
}
?>