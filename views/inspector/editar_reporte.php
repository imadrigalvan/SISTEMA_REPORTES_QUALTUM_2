<?php
include("../../includes/session.php");
include("../../includes/conexion.php");

$id = $_GET['id'];
$inspector_id = $_SESSION['id']; // <-- CAMBIO: obtener ID del inspector

$sql = "SELECT * FROM reportes WHERE id = $id AND inspector_id = $inspector_id AND estado = 'pendiente'"; // <-- CAMBIO: añadir filtro
$result = $conexion->query($sql);
$reporte = $result->fetch_assoc();

if(!$reporte){ // <-- CAMBIO: si no existe o no es suyo
    echo "
    <div style='margin:50px auto; max-width:600px;' class='alert alert-danger text-center'>
        <h4>No se puede editar</h4>
        <p>El reporte no existe, ya está firmado o no te pertenece.</p>
        <a href='reportes.php' class='btn btn-primary mt-3'>Volver</a>
    </div>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Reporte - Qualtum</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

<div class="container mt-4">

    <div class="card shadow">

        <div class="card-header bg-warning text-dark">
            <h4>Editar Reporte</h4>
        </div>

        <div class="card-body">

            <form action="../../controllers/actualizar_reporte.php" method="POST">

                <input type="hidden" name="id" value="<?php echo $reporte['id']; ?>">

             
                <h5>Datos Generales</h5>

                <div class="row">

                    <div class="col-md-4 mb-3">
                        <label>Folio</label>
                        <input type="text" name="folio" class="form-control"
                        value="<?php echo $reporte['folio']; ?>" required>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label>Fecha</label>
                        <input type="date" name="fecha" class="form-control"
                        value="<?php echo $reporte['fecha']; ?>" required>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label>Turno</label>
                        <select name="turno" class="form-select">
                            <option <?php if($reporte['turno']=='A') echo 'selected'; ?>>A</option>
                            <option <?php if($reporte['turno']=='P') echo 'selected'; ?>>P</option>
                            <option <?php if($reporte['turno']=='C') echo 'selected'; ?>>C</option>
                        </select>
                    </div>

                </div>

                <h5 class="mt-3">Producción</h5>

                <div class="row">

                    <div class="col-md-4 mb-3">
                        <label>Piezas OK</label>
                        <input type="number" name="piezas_ok" class="form-control"
                        value="<?php echo $reporte['piezas_ok']; ?>">
                    </div>

                    <div class="col-md-4 mb-3">
                        <label>Piezas Scrap</label>
                        <input type="number" name="piezas_scrap" class="form-control"
                        value="<?php echo $reporte['piezas_scrap']; ?>">
                    </div>

                    <div class="col-md-4 mb-3">
                        <label>Rebajadas</label>
                        <input type="number" name="rebajadas" class="form-control"
                        value="<?php echo $reporte['rebajadas']; ?>">
                    </div>

                </div>

                <h5 class="mt-3">Información Final</h5>

                <div class="mb-3">
                    <label>Descripción</label>
                    <textarea name="descripcion" class="form-control"><?php echo $reporte['descripcion']; ?></textarea>
                </div>

                <div class="mb-3">
                    <label>Motivo</label>
                    <textarea name="motivo" class="form-control"><?php echo $reporte['motivo']; ?></textarea>
                </div>

                <div class="mb-3">
                    <label>Comentarios</label>
                    <textarea name="comentarios" class="form-control"><?php echo $reporte['comentarios']; ?></textarea>
                </div>

                <div class="text-end">
                    <a href="reportes.php" class="btn btn-secondary">Cancelar</a>
                    <button class="btn btn-success">Guardar cambios</button>
                </div>

            </form>

        </div>

    </div>

</div>

</body>
</html>