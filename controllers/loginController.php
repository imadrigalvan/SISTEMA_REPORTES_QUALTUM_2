<?php
session_start();
include("../includes/conexion.php");

$numero_empleado = $_POST['numero_empleado'];
$password = $_POST['password'];

$sql = "SELECT * FROM usuarios WHERE numero_empleado='$numero_empleado'";
$resultado = $conexion->query($sql);

if ($resultado->num_rows > 0) {
    $user = $resultado->fetch_assoc();
    if (password_verify($password, $user['password'])) {
        $_SESSION['id'] = $user['id'];
        $_SESSION['nombre'] = $user['nombre'];
        $_SESSION['rol'] = $user['rol'];

        if ($user['rol'] == "supervisor") {
            header("Location: ../views/supervisor/dashboard.php");
        } else {
            header("Location: ../views/inspector/dashboard.php");
        }
    } else {
        echo "Contraseña incorrecta.";
    }
} else {
    echo "Número de empleado no registrado.";
}
?>