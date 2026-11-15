<?php
include("../../includes/session.php");
include("../../includes/conexion.php");

// Capturar filtros
$filtro_inspector = isset($_GET['inspector_id']) && $_GET['inspector_id'] != '' ? (int)$_GET['inspector_id'] : null;
$filtro_estado = isset($_GET['estado']) && $_GET['estado'] != '' ? $_GET['estado'] : null;

// Construir consulta base con el campo área correcto
$sql = "SELECT r.*, a.area AS nombre_area, u.nombre AS inspector 
        FROM reportes r
        LEFT JOIN areas a ON r.area_id = a.id
        LEFT JOIN usuarios u ON r.inspector_id = u.id
        WHERE 1=1";

if($filtro_inspector) {
    $sql .= " AND r.inspector_id = $filtro_inspector";
}
if($filtro_estado) {
    $sql .= " AND r.estado = '$filtro_estado'";
}

$sql .= " ORDER BY r.id DESC";
$resultado = $conexion->query($sql);

$inspectores = $conexion->query("SELECT id, nombre FROM usuarios WHERE rol = 'inspector' ORDER BY nombre");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reportes - Supervisor</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-4">
    <div class="card shadow">
        <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
            <h4>Reportes de Inspección</h4>
            <a href="../inspector/nuevo_reporte.php" class="btn btn-success btn-sm">+ Nuevo Reporte</a>
        </div>
        <div class="card-body">

            <!-- Formulario de filtros -->
            <form method="GET" class="row g-3 mb-4 align-items-end">
                <div class="col-md-4">
                    <label for="inspector_id" class="form-label">Inspector</label>
                    <select name="inspector_id" id="inspector_id" class="form-select">
                        <option value="">-- Todos --</option>
                        <?php while($inv = $inspectores->fetch_assoc()): ?>
                            <option value="<?php echo $inv['id']; ?>" <?php echo ($filtro_inspector == $inv['id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($inv['nombre']); ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="estado" class="form-label">Estado</label>
                    <select name="estado" id="estado" class="form-select">
                        <option value="">-- Todos --</option>
                        <option value="pendiente" <?php echo ($filtro_estado == 'pendiente') ? 'selected' : ''; ?>>Pendiente</option>
                        <option value="firmado" <?php echo ($filtro_estado == 'firmado') ? 'selected' : ''; ?>>Firmado</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">Filtrar</button>
                </div>
                <div class="col-md-2">
                    <a href="reportes.php" class="btn btn-secondary w-100">Limpiar</a>
                </div>
            </form>

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
                    <?php if($resultado->num_rows == 0): ?>
                        <tr>
                            <td colspan="8" class="text-center">No hay reportes con esos filtros.</td>
                        </tr>
                    <?php else: ?>
                        <?php while($row = $resultado->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['folio']); ?></td>
                            <td><?php echo $row['fecha']; ?></td>
                            <td><?php echo htmlspecialchars($row['nombre_area'] ?? ''); ?></td>
                            <td><?php echo htmlspecialchars($row['inspector']); ?></td>
                            <td><?php echo (int)$row['piezas_ok']; ?></td>
                            <td><?php echo (int)$row['piezas_scrap']; ?></td>
                            <td>
                                <?php if($row['estado'] == 'pendiente'): ?>
                                    <span class="badge bg-warning">Pendiente</span>
                                <?php else: ?>
                                    <span class="badge bg-success">Firmado</span>
                                <?php endif; ?>
                             </td>
                            <td>
                                <a href="ver_reporte.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-primary">Ver</a>
                                <?php if($row['estado'] == 'pendiente'): ?>
                                    <a href="editar_reporte.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-warning">Editar</a>
                                <?php endif; ?>
                             </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

</body>
</html>