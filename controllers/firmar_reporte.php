<?php
session_start();
include("../includes/conexion.php");

if(!isset($_SESSION['id'])){
    die("Acceso no autorizado");
}
if($_SESSION['rol'] != 'supervisor'){
    die("No tienes permisos para firmar");
}

$id = $_POST['id'];

$check = $conexion->query("SELECT estado FROM reportes WHERE id = $id");
$data = $check->fetch_assoc();

if($data['estado'] == 'firmado'){
    die("Este reporte ya está firmado");
}

if(!is_dir("../assets/firmas")) {
    mkdir("../assets/firmas");
}

$firma_path = "assets/firmas/firma_".$id.".png";
$firma_data = str_replace('data:image/png;base64,', '', $_POST['firma_data']);
$firma_data = str_replace(' ', '+', $firma_data);
$firma_binario = base64_decode($firma_data);
file_put_contents($firma_path, $firma_binario);

$supervisor_id = $_SESSION['id'];
$sql = "UPDATE reportes SET
estado = 'firmado',
firmado_por = '$supervisor_id',
fecha_firma = NOW(),
firma_path = '$firma_path'
WHERE id = $id";

$conexion->query($sql);
header("Location: ../views/supervisor/reportes.php");
?>