<?php
include("../includes/conexion.php");

$area = $_POST['area'];
$linea = $_POST['linea'] ?? "no";
$nombre = $_POST['nombre_parte'];
$numero_parte = $_POST['numero_parte'];
$numero_serie = $_POST['numero_serie'];
$descripcion = $_POST['descripcion'];
$motivo = $_POST['motivo'];

$archivo_nombre = $_FILES['archivo']['name'];
$archivo_tmp = $_FILES['archivo']['tmp_name'];

$ruta = "../assets/" . $archivo_nombre;
move_uploaded_file($archivo_tmp, $ruta);

$tiene_linea = ($area == "rotax" || $area == "pwc") ? "si" : "no";

if(empty($area) || empty($nombre) || empty($descripcion) || empty($motivo)){
    echo "Faltan campos obligatorios";
    exit;
}

if(($area == "rotax" || $area == "pwc") && empty($linea)){
    echo "La línea es obligatoria para esta área";
    exit;
}

$sql = "INSERT INTO areas 
(area, linea, nombre_parte, numero_parte, numero_serie, descripcion, motivo, archivo, tiene_linea)
VALUES 
('$area','$linea','$nombre','$numero_parte','$numero_serie','$descripcion','$motivo','$archivo_nombre','$tiene_linea')";

$conexion->query($sql);

header("Location: ../views/supervisor/areas.php");
?>