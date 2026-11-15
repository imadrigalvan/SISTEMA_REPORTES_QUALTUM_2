<?php
die("RUTA: " . __FILE__);
<?php
include("../../includes/session.php");
include("../../includes/conexion.php");

echo "<div style='background:red; color:white; padding:10px;'>ARCHIVO ACTUALIZADO - Versión con filtro</div>";

$inspector_id = $_SESSION['id']; // <-- CAMBIO: obtener ID del inspector

$sql = "SELECT r.*, a.area AS nombre_area, u.nombre AS inspector   // <-- CAMBIO: a.area AS nombre_area
        FROM reportes r
        INNER JOIN areas a ON r.area_id = a.id
        INNER JOIN usuarios u ON r.inspector_id = u.id
        WHERE r.inspector_id = $inspector_id   // <-- CAMBIO: filtrar por inspector
        ORDER BY r.id DESC";

$resultado = $conexion->query($sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reportes - Qualtum</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

<div class="container mt-4">

    <div class="card shadow">

        <div class="card-header bg-dark text-white d-flex justify-content-between">
            <h4>Reportes de Inspección</h4>
            <a href="../inspector/nuevo_reporte.php" class="btn btn-success btn-sm">+ Nuevo Reporte</a>
        </div>

        <div class="card-body">

            <div class="table-responsive">

                <table class="table table-bordered table-hover">

                    <thead class="table-dark">
                        <tr>
                            <th>Folio</th>
                            <th>Fecha</th>
                            <th>Área</th>
                            <th>Inspector</th>
                            <th>OK</th>
                            <th>Scrap</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>

                    <tbody>

<?php while($row = $resultado->fetch_assoc()) { ?>

<tr>
    <td><?php echo $row['folio']; ?></td>
    <td><?php echo $row['fecha']; ?></td>
    <td><?php echo $row['nombre_area']; ?></td>
    <td><?php echo $row['inspector']; ?></td>
    <td><?php echo $row['piezas_ok']; ?></td>
    <td><?php echo $row['piezas_scrap']; ?></td>

    <!-- ESTADO -->
    <td>
        <?php if($row['estado'] == 'pendiente') { ?>
            <span class="badge bg-warning">Pendiente</span>
        <?php } else { ?>
            <span class="badge bg-success">Firmado</span>
        <?php } ?>
    </td>

    <td>

        <a href="ver_reporte.php?id=<?php echo $row['id']; ?>" 
           class="btn btn-sm btn-primary">
           Ver
        </a>

        <?php if($row['estado'] == 'pendiente') { ?>
            <a href="editar_reporte.php?id=<?php echo $row['id']; ?>" 
               class="btn btn-sm btn-warning">
               Editar
            </a>
        <?php } ?>

    </td>

</tr>

<?php } ?>

</tbody>

                </table>

            </div>

        </div>
    </div>

</div>

</body>
</html>