<?php include("../../includes/layout.php"); ?>

<div class="container mt-4">
    <div class="card shadow">
        <div class="card-header bg-primary text-white">
            <h4>Gestión de Áreas</h4>
        </div>

        <div class="card-body">

            <form action="../../controllers/guardar_area.php" method="POST" enctype="multipart/form-data">

                <div class="mb-3">
                    <label>Área *</label>
                    <select class="form-select" name="area" id="area" required>
                        <option value="">Seleccionar área</option>
                        <option value="maquinado">Maquinado</option>
                        <option value="incoming">Incoming</option>
                        <option value="rotax">Rotax</option>
                        <option value="pwc">PWC</option>
                    </select>
                </div>

                <!-- Línea -->
                <div class="mb-3" id="lineaContainer" style="display:none;">
                    <label>Línea *</label>
                    <select class="form-select" name="linea" id="linea">
                        <option value="">Seleccionar línea</option>
                        <option>Línea 1</option>
                        <option>Línea 2</option>
                        <option>Línea 3</option>
                        <option>Línea 4</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label>Nombre de la parte *</label>
                    <input type="text" name="nombre_parte" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label>Número de parte</label>
                    <input type="text" name="numero_parte" class="form-control">
                </div>

                <div class="mb-3">
                    <label>Número de serie</label>
                    <input type="text" name="numero_serie" class="form-control">
                </div>

                <div class="mb-3">
                    <label>Descripción *</label>
                    <textarea name="descripcion" class="form-control" required></textarea>
                </div>

                <div class="mb-3">
                    <label>Motivo *</label>
                    <textarea name="motivo" class="form-control" required></textarea>
                </div>

                <div class="mb-3">
                    <label>Instrucción de trabajo *</label>
                    <input type="file" name="archivo" class="form-control" required>
                </div>

                <div class="text-end">
                    <button type="reset" class="btn btn-secondary">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar Área</button>
                </div>

            </form>

        </div>
    </div>
</div>

<h5 class="mt-4">Áreas registradas</h5>

<table class="table table-bordered">
    <thead>
        <tr>
            <th>Área</th>
            <th>Línea</th>
            <th>Nombre Parte</th>
            <th>Instrucción</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>

    <?php
    include("../../includes/conexion.php");
    $areas = $conexion->query("SELECT * FROM areas");

    while($a = $areas->fetch_assoc()){
    ?>
        <tr>
            <td><?php echo $a['area']; ?></td>
            <td><?php echo $a['linea']; ?></td>
            <td><?php echo $a['nombre_parte']; ?></td>
 <td>
    <?php if($a['archivo']){ ?>
        <a href="../../assets/<?php echo $a['archivo']; ?>" target="_blank" class="btn btn-info btn-sm">
            Ver
        </a>
    <?php } else { ?>
        Sin archivo
    <?php } ?>
</td>
            <td>
                <a href="editar_area.php?id=<?php echo $a['id']; ?>" class="btn btn-warning btn-sm">Editar</a>

<a href="../../controllers/eliminar_area.php?id=<?php echo $a['id']; ?>" 
   class="btn btn-danger btn-sm"
   onclick="return confirm('¿Eliminar esta área?')">
   Eliminar
</a>
            </td>
        </tr>

    <?php } ?>

    </tbody>
</table>


<script>
const area = document.getElementById("area");
const lineaContainer = document.getElementById("lineaContainer");
const linea = document.getElementById("linea");

area.addEventListener("change", function() {
    if (this.value === "rotax" || this.value === "pwc") {
        lineaContainer.style.display = "block";
        linea.setAttribute("required", "true");
    } else {
        lineaContainer.style.display = "none";
        linea.removeAttribute("required");
        linea.value = "";
    }
});
</script>

</body>
</html>