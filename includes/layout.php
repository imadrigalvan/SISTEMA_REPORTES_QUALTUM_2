<?php include(__DIR__ . "/session.php"); ?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Qualtum - Sistema de Reportes</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            margin: 0;
            background: #f0f2f5;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        /* SIDEBAR MODERNO */
        .sidebar {
            width: 260px;
            height: 100vh;
            position: fixed;
            background: linear-gradient(180deg, #0B2B40 0%, #1A4A5F 100%);
            color: white;
            padding: 20px 0;
            box-shadow: 2px 0 10px rgba(0,0,0,0.1);
            z-index: 1000;
            transition: all 0.3s;
            overflow-y: auto;
        }

        .sidebar .logo-area {
            text-align: center;
            padding: 0 20px 20px 20px;
            border-bottom: 1px solid rgba(255,255,255,0.2);
            margin-bottom: 20px;
        }
        .sidebar .logo-area img {
            max-width: 100px;
            max-height: 80px;
            margin-bottom: 10px;
            background: transparent;
            padding: 0;
            border: none;
            border-radius: 0;
        }
        .sidebar .logo-area h4 {
            font-size: 1.4rem;
            margin: 0;
            font-weight: 600;
            letter-spacing: 1px;
        }

        .sidebar a {
            display: flex;
            align-items: center;
            gap: 12px;
            color: #e0e0e0;
            padding: 12px 24px;
            margin: 4px 12px;
            text-decoration: none;
            border-radius: 12px;
            transition: all 0.2s;
            font-weight: 500;
        }

        .sidebar a i {
            width: 24px;
            text-align: center;
            font-size: 1.2rem;
        }

        .sidebar a:hover {
            background: rgba(255,255,255,0.15);
            color: white;
            transform: translateX(5px);
        }

        .sidebar a.active {
            background: #2C7DA0;
            color: white;
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        }

        .navbar {
            margin-left: 260px;
            padding: 12px 30px;
            background: white;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
            border-bottom: 1px solid #e9ecef;
        }

        .navbar .welcome {
            font-size: 1.2rem;
            font-weight: 500;
            color: #1e3c72;
        }

        .navbar .welcome i {
            color: #2C7DA0;
            margin-right: 8px;
        }

        .navbar .user-badge {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .navbar .user-badge .role {
            background: #2C7DA0;
            color: white;
            padding: 5px 12px;
            border-radius: 30px;
            font-size: 0.8rem;
            font-weight: bold;
        }

        .navbar .user-badge .logout {
            color: #dc3545;
            text-decoration: none;
            font-weight: 500;
            transition: 0.2s;
        }

        .navbar .user-badge .logout:hover {
            text-decoration: underline;
        }

        .content {
            margin-left: 260px;
            padding: 30px;
            min-height: 100vh;
        }


        @media (max-width: 768px) {
            .sidebar {
                width: 80px;
            }
            .sidebar .logo-area h4, .sidebar a span {
                display: none;
            }
            .sidebar .logo-area img {
                max-width: 45px;
                max-height: 40px;
            }
            .sidebar a {
                justify-content: center;
                padding: 12px;
            }
            .sidebar a i {
                margin: 0;
                font-size: 1.5rem;
            }
            .navbar, .content {
                margin-left: 80px;
            }
        }
    </style>
</head>
<body>

<div class="sidebar">
    <div class="logo-area">
        <img src="/assets/logo.png" alt="Logo Qualtum" onerror="this.src='https://via.placeholder.com/80?text=Qualtum'">
        <h4>Qualtum</h4>
    </div>

    <a href="/views/inspector/dashboard.php">
        <i class="fas fa-tachometer-alt"></i> <span>Dashboard</span>
    </a>
    <a href="/views/inspector/nuevo_reporte.php">
        <i class="fas fa-file-alt"></i> <span>Nuevo Reporte</span>
    </a>
    <a href="/views/supervisor/reportes.php">
        <i class="fas fa-list"></i> <span>Reportes</span>
    </a>
    <?php if($_SESSION['rol'] == 'supervisor'): ?>
    <a href="/views/supervisor/areas.php">
        <i class="fas fa-building"></i> <span>Áreas</span>
    </a>
    <?php endif; ?>
    <a href="/logout.php">
        <i class="fas fa-sign-out-alt"></i> <span>Cerrar sesión</span>
    </a>
</div>

<div class="navbar">
    <div class="welcome">
        <i class="fas fa-user-check"></i> Bienvenido, <strong><?php echo htmlspecialchars($_SESSION['nombre']); ?></strong>
    </div>
    <div class="user-badge">
        <span class="role"><i class="fas fa-user-tag"></i> <?php echo ucfirst($_SESSION['rol']); ?></span>
        <a href="/logout.php" class="logout"><i class="fas fa-power-off"></i> Salir</a>
    </div>
</div>

<div class="content">
