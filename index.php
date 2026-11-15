<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Login - Qualtum</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #f5af19 0%, #f12711 100%);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            height: 100vh;
            margin: 0;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 35px rgba(0,0,0,0.2);
            width: 380px;
            padding: 2rem;
            transition: transform 0.3s;
        }
        .login-card:hover {
            transform: translateY(-5px);
        }
        .logo-area {
            text-align: center;
            margin-bottom: 1.5rem;
        }
        .logo-area img {
            max-width: 80px;
            margin-bottom: 10px;
        }
        .logo-area h3 {
            color: #1A4A5F;
            font-weight: 600;
            margin: 0;
        }
        .form-label {
            font-weight: 500;
            color: #2c3e50;
        }
        .btn-login {
            background: #2C7DA0;
            border: none;
            padding: 10px;
            font-weight: bold;
            font-size: 1rem;
            transition: 0.2s;
        }
        .btn-login:hover {
            background: #1A4A5F;
            transform: scale(1.02);
        }
        .register-link {
            text-align: center;
            margin-top: 1.2rem;
        }
        .register-link a {
            color: #2C7DA0;
            text-decoration: none;
            font-weight: 500;
        }
        .register-link a:hover {
            text-decoration: underline;
        }
        .input-group-text {
            background: #f0f2f5;
            border-right: none;
        }
        .form-control:focus {
            border-color: #2C7DA0;
            box-shadow: 0 0 0 0.2rem rgba(44,125,160,0.25);
        }
    </style>
</head>
<body>

<div class="login-card">
    <div class="logo-area">
        <img src="/assets/logo.png" alt="Logo Qualtum" onerror="this.style.display='none'">
        <h3>Qualtum</h3>
    </div>

    <form action="controllers/loginController.php" method="POST">
        <div class="mb-3">
            <label class="form-label"><i class="fas fa-id-badge"></i> Número de empleado</label>
            <input type="text" name="numero_empleado" class="form-control" placeholder="Ej: 5517" required autofocus>
        </div>

        <div class="mb-3">
            <label class="form-label"><i class="fas fa-lock"></i> Contraseña</label>
            <input type="password" name="password" class="form-control" placeholder="******" required>
        </div>

        <button type="submit" class="btn btn-primary w-100 btn-login">
            <i class="fas fa-sign-in-alt"></i> Iniciar sesión
        </button>

        <div class="register-link">
            <a href="registro.php"><i class="fas fa-user-plus"></i> Crear cuenta</a>
        </div>
    </form>
</div>

</body>
</html>