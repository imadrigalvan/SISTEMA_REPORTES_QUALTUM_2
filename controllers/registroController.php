<?php
include("../includes/conexion.php");

$nombre = $_POST['nombre'];
$numero_empleado = $_POST['numero_empleado'];
$password = $_POST['password'];
$rol = $_POST['rol'];

$sql_check = "SELECT * FROM usuarios WHERE numero_empleado='$numero_empleado'";
$result = $conexion->query($sql_check);

$titulo = "";
$mensaje = "";
$tipo_mensaje = ""; // success o danger

if ($result->num_rows > 0) {
    $titulo = "Registro fallido";
    $mensaje = "Este número de empleado ya está registrado.";
    $tipo_mensaje = "danger";
} else {

    $password_hash = password_hash($password, PASSWORD_DEFAULT);
    
    $sql = "INSERT INTO usuarios (nombre, numero_empleado, password, rol)
            VALUES ('$nombre', '$numero_empleado', '$password_hash', '$rol')";
    
    if ($conexion->query($sql)) {
        $titulo = "Registro exitoso";
        $mensaje = "¡Tu cuenta ha sido creada correctamente!";
        $tipo_mensaje = "success";
    } else {
        $titulo = "Error en el registro";
        $mensaje = "No se pudo registrar el usuario: " . $conexion->error;
        $tipo_mensaje = "danger";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro - Qualtum</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: #f0f2f5;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }
        .btn-primary {
            background: #2C7DA0;
            border: none;
        }
        .btn-primary:hover {
            background: #1A4A5F;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow">
                    <div class="card-header bg-<?php echo $tipo_mensaje; ?> text-white text-center">
                        <h4><?php echo $titulo; ?></h4>
                    </div>
                    <div class="card-body text-center">
                        <div class="alert alert-<?php echo $tipo_mensaje; ?>" role="alert">
                            <?php echo $mensaje; ?>
                        </div>
                        <?php if ($tipo_mensaje == "success"): ?>
                            <a href="../index.php" class="btn btn-primary">Iniciar sesión</a>
                        <?php else: ?>
                            <a href="../registro.php" class="btn btn-secondary">Intentar de nuevo</a>
                            <a href="../index.php" class="btn btn-outline-primary">Volver al inicio</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>