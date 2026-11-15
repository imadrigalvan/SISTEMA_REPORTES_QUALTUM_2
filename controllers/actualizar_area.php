<?php
include("../includes/conexion.php");

$id = $_POST['id'];
$area = $_POST['area'];
$nombre_parte = $_POST['nombre_parte'];
$numero_parte = $_POST['numero_parte'] ?? '';
$numero_serie = $_POST['numero_serie'] ?? '';
$descripcion = $_POST['descripcion'];
$motivo = $_POST['motivo'];

$tiene_linea = ($area == "rotax" || $area == "pwc") ? "si" : "no";
$linea = ($tiene_linea == "si") ? $_POST['linea'] : '';

$archivo_nombre = $_POST['archivo_actual'] ?? '';

if(!empty($_FILES['archivo']['name'])) {
    $extension = strtolower(pathinfo($_FILES['archivo']['name'], PATHINFO_EXTENSION));
    $formatos_permitidos = ['pdf', 'jpg', 'jpeg', 'png'];
    
    if(!in_array($extension, $formatos_permitidos)) {
        die("Formato de archivo no permitido. Solo se aceptan PDF, JPG, JPEG o PNG.");
    }

    $archivo_nombre = uniqid() . '.' . $extension;
    $ruta = "../assets/" . $archivo_nombre;
    
    if(!move_uploaded_file($_FILES['archivo']['tmp_name'], $ruta)) {
        die("Error al subir el archivo");
    }
    
    // Eliminar el archivo anterior si existe
    if(!empty($_POST['archivo_actual']) && file_exists("../assets/".$_POST['archivo_actual'])) {
        unlink("../assets/".$_POST['archivo_actual']);
    }
}

$sql = "UPDATE areas SET 
        nombre_parte = ?,
        numero_parte = ?,
        numero_serie = ?,
        area = ?,
        linea = ?,
        tiene_linea = ?,
        descripcion = ?,
        motivo = ?,
        archivo = ?
        WHERE id = ?";

$stmt = $conexion->prepare($sql);
$stmt->bind_param("sssssssssi", 
    $nombre_parte,
    $numero_parte,
    $numero_serie,
    $area,
    $linea,
    $tiene_linea,
    $descripcion,
    $motivo,
    $archivo_nombre,
    $id
);

if($stmt->execute()) {
    header("Location: ../views/supervisor/areas.php?success=1");
} else {
    die("Error al actualizar el área: " . $conexion->error);
}

$stmt->close();
$conexion->close();
?>