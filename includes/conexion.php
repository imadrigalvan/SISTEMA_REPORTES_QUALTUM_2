<?php
$conexion = new mysqli("mysql-imadri65.alwaysdata.net", "imadri65", "@22307065", "imadri65_bd1");

if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}
?>