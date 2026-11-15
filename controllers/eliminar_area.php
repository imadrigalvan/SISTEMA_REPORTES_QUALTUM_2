<?php
include("../includes/conexion.php");

$id = $_GET['id'];

$sql = "DELETE FROM areas WHERE id=$id";
$conexion->query($sql);

header("Location: ../views/supervisor/areas.php");
?>