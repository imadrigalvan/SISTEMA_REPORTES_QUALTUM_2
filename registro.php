<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro - Qualtum</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-dark d-flex justify-content-center align-items-center vh-100">

<div class="card p-4 shadow" style="width: 400px;">
    <h4 class="text-center mb-3">Registro</h4>

    <form action="controllers/registroController.php" method="POST">

        <div class="mb-3">
            <label>Nombre completo</label>
            <input type="text" name="nombre" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Número de empleado</label>
            <input type="text" name="numero_empleado" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Contraseña</label>
            <input type="password" name="password" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Rol</label>
            <select name="rol" class="form-control" required>
                <option value="">Seleccionar</option>
                <option value="inspector">Inspector</option>
                <option value="supervisor">Supervisor</option>
            </select>
        </div>

        <button class="btn btn-success w-100">Registrarse</button>

        <div class="text-center mt-2">
            <a href="index.php">¿Ya tienes cuenta? Inicia sesión</a>
        </div>

    </form>
</div>

</body>
</html>