<?php
include("../../includes/conexion.php");

$id = $_GET['id'];
$area = $conexion->query("SELECT * FROM areas WHERE id=$id")->fetch_assoc();

$permite_linea = ($area['area'] == 'rotax' || $area['area'] == 'pwc');
?>

<?php include("../../includes/layout.php"); ?>

<div class="container mt-4">
    <div class="card">
        <div class="card-header bg-warning">
            Editar Área
        </div>

        <div class="card-body">
            <form action="../../controllers/actualizar_area.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="id" value="<?php echo $area['id']; ?>">

                <div class="mb-3">
                    <label>Área</label>
                    <select name="area" class="form-control" required>
                        <option value="maquinado" <?php echo ($area['area'] == 'maquinado') ? 'selected' : ''; ?>>Maquinado</option>
                        <option value="incoming" <?php echo ($area['area'] == 'incoming') ? 'selected' : ''; ?>>Incoming</option>
                        <option value="rotax" <?php echo ($area['area'] == 'rotax') ? 'selected' : ''; ?>>Rotax</option>
                        <option value="pwc" <?php echo ($area['area'] == 'pwc') ? 'selected' : ''; ?>>PWC</option>
                    </select>
                </div>

                <?php if($permite_linea): ?>
                <div class="mb-3">
                    <label>Línea</label>
                    <select name="linea" class="form-select">
                        <option value="">Seleccionar línea</option>
                        <option value="Línea 1" <?php echo ($area['linea'] == 'Línea 1') ? 'selected' : ''; ?>>Línea 1</option>
                        <option value="Línea 2" <?php echo ($area['linea'] == 'Línea 2') ? 'selected' : ''; ?>>Línea 2</option>
                        <option value="Línea 3" <?php echo ($area['linea'] == 'Línea 3') ? 'selected' : ''; ?>>Línea 3</option>
                        <option value="Línea 4" <?php echo ($area['linea'] == 'Línea 4') ? 'selected' : ''; ?>>Línea 4</option>
                    </select>
                </div>
                <?php else: ?>
                <input type="hidden" name="linea" value="">
                <?php endif; ?>

                <div class="mb-3">
                    <label>Nombre de la parte</label>
                    <input type="text" name="nombre_parte" class="form-control" value="<?php echo $area['nombre_parte']; ?>" required>
                </div>

                <div class="mb-3">
                    <label>Descripción</label>
                    <textarea name="descripcion" class="form-control" required><?php echo $area['descripcion']; ?></textarea>
                </div>

                <div class="mb-3">
                    <label>Motivo</label>
                    <textarea name="motivo" class="form-control" required><?php echo $area['motivo']; ?></textarea>
                </div>

                <div class="mb-3">
                    <label>Archivo actual</label><br>
                    <?php if($area['archivo']): ?>
                        <a href="../../assets/<?php echo $area['archivo']; ?>" target="_blank" class="btn btn-info btn-sm">
                            Ver archivo actual
                        </a>
                    <?php else: ?>
                        <span class="text-muted">No hay archivo adjunto</span>
                    <?php endif; ?>
                </div>

                <div class="mb-3">
                    <label>Actualizar archivo (PDF o imagen)</label>
                    <input type="file" name="archivo" class="form-control" accept=".pdf,.jpg,.jpeg,.png">
                    <small class="text-muted">Formatos permitidos: PDF, JPG, PNG. Tamaño máximo: 2MB</small>
                </div>

                <div class="text-end">
                    <a href="areas.php" class="btn btn-secondary">Cancelar</a>
                    <button type="submit" class="btn btn-success">Guardar Cambios</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
const areaSelect = document.querySelector('select[name="area"]');
const lineaContainer = document.querySelector('.mb-3[label="Línea"]');

areaSelect.addEventListener('change', function() {
    const selectedArea = this.value;
    
    if(selectedArea === 'rotax' || selectedArea === 'pwc') {
        lineaContainer.style.display = 'block';
        lineaContainer.querySelector('select').setAttribute('required', true);
    } else {
        lineaContainer.style.display = 'none';
        lineaContainer.querySelector('select').removeAttribute('required');
    }
});
</script>

</body>
</html>