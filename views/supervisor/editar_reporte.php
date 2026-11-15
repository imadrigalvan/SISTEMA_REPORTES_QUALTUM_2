<?php
include("../../includes/conexion.php");

$id = $_GET['id'];

$sql = "SELECT r.*, a.nombre_area 
        FROM reportes r
        LEFT JOIN areas a ON r.area_id = a.id
        WHERE r.id = $id";

$result = $conexion->query($sql);
$reporte = $result->fetch_assoc();

if($reporte['estado'] == 'firmado'){
die("Este reporte ya está firmado");
}
?>

<h3>Editar Reporte</h3>

<form method="POST" action="../../controllers/actualizar_reporte.php">

<input type="hidden" name="id" value="<?php echo $reporte['id']; ?>">

<label>Área</label>
<input type="text" class="form-control" 
value="<?php echo $reporte['nombre_area']; ?>" disabled>

<label>Descripción</label>
<textarea name="descripcion" class="form-control"><?php echo $reporte['descripcion']; ?></textarea>

<label>Motivo</label>
<textarea name="motivo" class="form-control"><?php echo $reporte['motivo']; ?></textarea>

<button class="btn btn-success mt-3">Guardar cambios</button>

</form>