<?php include("../../includes/layout.php"); ?>
<?php
include("../../includes/conexion.php");
$areas = $conexion->query("SELECT * FROM areas");
?>
<div class="container mt-4">
    <div class="card shadow">
        <div class="card-header bg-success text-white">
            <h4>Nuevo Reporte de Inspección</h4>
        </div>
        <div class="card-body">
            <form action="../../controllers/reporteController.php" method="POST" id="reporteForm">
                <input type="hidden" name="area_id" id="area_id">

                <h5>1. Datos Generales</h5>
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <label>Turno *</label>
                        <select class="form-select" name="turno" required>
                            <option>A</option><option>B</option><option>C</option>
                        </select>
                    </div>
                    <div class="col-md-5 mb-3">
                        <label>Nombre de la parte *</label>
                        <input type="text" class="form-control" name="nombre_parte" id="nombre_parte" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label>Folio *</label>
                        <input type="text" class="form-control" name="folio" required>
                    </div>
                </div>
                <div class="mb-3">
                    <label>Fecha *</label>
                    <input type="date" class="form-control" name="fecha" required>
                </div>

                <h5 class="mt-4">2. Producción</h5>
                <div class="row">
                    <div class="col-md-4 mb-3"><label>Cantidad *</label><input type="number" name="cantidad" class="form-control" required></div>
                    <div class="col-md-4 mb-3"><label>Piezas OK *</label><input type="number" name="piezas_ok" class="form-control" required></div>
                    <div class="col-md-4 mb-3"><label>Piezas Scrap *</label><input type="number" name="piezas_scrap" id="piezas_scrap" class="form-control" required></div>
                </div>
                <div id="condicionesContainer" style="display:none;"><h6>Condiciones</h6><div class="row mb-2"><div class="col-md-6"><input type="text" name="condicion[]" class="form-control" placeholder="Condición"></div><div class="col-md-4"><input type="number" name="cantidad_condicion[]" class="form-control" placeholder="Cantidad"></div></div><button type="button" class="btn btn-sm btn-outline-primary" onclick="agregarCondicion()">+ Agregar</button></div>

                <h5 class="mt-4">3. Tiempo</h5>
                <div class="row">
                    <div class="col-md-3"><label>Hora inicio</label><input type="time" name="hora_inicio" class="form-control" required></div>
                    <div class="col-md-3"><label>Hora fin</label><input type="time" name="hora_fin" class="form-control" required></div>
                    <div class="col-md-3"><label>Horas efectivas</label><input type="number" name="horas_efectivas" class="form-control" required></div>
                    <div class="col-md-3"><label>Tiempo comida</label><input type="text" name="tiempo_comida" class="form-control"></div>
                </div>

    
                <h5 class="mt-4">4. Información Final</h5>
                <div class="mb-3">
                    <label>Área *</label>
                    <input list="listaAreas" id="areaReporte" name="area" class="form-control" required autocomplete="off">
                    <datalist id="listaAreas">
                        <?php while($a = $areas->fetch_assoc()): ?>
                            <option value="<?php echo htmlspecialchars($a['nombre_parte']); ?>"
                                data-id="<?php echo $a['id']; ?>"
                                data-descripcion="<?php echo htmlspecialchars($a['descripcion']); ?>"
                                data-motivo="<?php echo htmlspecialchars($a['motivo']); ?>"
                                data-linea="<?php echo $a['tiene_linea']; ?>"
                                data-archivo="<?php echo $a['archivo']; ?>">
                        <?php endwhile; ?>
                    </datalist>
                </div>
                <div class="mb-3" id="lineaReporteContainer" style="display:none;">
                    <label>Línea</label>
                    <select name="linea" id="lineaReporte" class="form-select">
                        <option value="">Seleccionar</option>
                        <option>Línea 1</option><option>Línea 2</option><option>Línea 3</option><option>Línea 4</option>
                    </select>
                </div>
                <div class="mb-3"><label>Descripción *</label><textarea name="descripcion" id="descripcion" class="form-control" required></textarea></div>
                <div class="mb-3"><label>Motivo *</label><textarea name="motivo" id="motivo" class="form-control" required></textarea></div>
                <div class="mb-3"><label>Instrucción de trabajo</label><div id="archivoContainer"><span class="text-muted">No disponible</span></div></div>
                <div class="mb-3"><label>Rebajadas</label><input type="number" name="rebajadas" class="form-control"></div>
                <div class="mb-3"><label>Comentarios</label><textarea name="comentarios" class="form-control"></textarea></div>
                <div class="text-end"><a href="dashboard.php" class="btn btn-secondary">Cancelar</a><button type="submit" class="btn btn-success">Guardar</button></div>
            </form>
        </div>
    </div>
</div>

<script>
function agregarCondicion() {
    const container = document.getElementById('condicionesContainer');
    const div = document.createElement('div');
    div.className = 'row mb-2';
    div.innerHTML = `<div class="col-md-6"><input type="text" name="condicion[]" class="form-control" placeholder="Condición"></div><div class="col-md-4"><input type="number" name="cantidad_condicion[]" class="form-control" placeholder="Cantidad"></div><div class="col-md-2"><button type="button" class="btn btn-sm btn-outline-danger" onclick="this.parentElement.parentElement.remove()">X</button></div>`;
    container.appendChild(div);
}


const inputArea = document.getElementById('areaReporte');
const nombreParte = document.getElementById('nombre_parte');
const areaId = document.getElementById('area_id');
const descripcion = document.getElementById('descripcion');
const motivo = document.getElementById('motivo');
const lineaContainer = document.getElementById('lineaReporteContainer');
const linea = document.getElementById('lineaReporte');
const archivoContainer = document.getElementById('archivoContainer');
const lista = document.getElementById('listaAreas');

nombreParte.addEventListener('input', function() {
    inputArea.value = this.value;
    buscarArea();
});

inputArea.addEventListener('input', buscarArea);

function buscarArea() {
    let valor = inputArea.value.trim().toLowerCase();
    console.log('Buscando:', valor);
    let encontrado = false;

    for (let i = 0; i < lista.options.length; i++) {
        let opt = lista.options[i];
        let textoOpt = opt.value.trim().toLowerCase();
        if (textoOpt === valor) {
            encontrado = true;
            console.log('Área encontrada:', opt.value);
            nombreParte.value = opt.value;
            areaId.value = opt.getAttribute('data-id') || '';
            descripcion.value = opt.getAttribute('data-descripcion') || '';
            motivo.value = opt.getAttribute('data-motivo') || '';

         
            if (opt.getAttribute('data-linea') === 'si') {
                lineaContainer.style.display = 'block';
                linea.setAttribute('required', 'required');
            } else {
                lineaContainer.style.display = 'none';
                linea.removeAttribute('required');
                linea.value = '';
            }

            let arch = opt.getAttribute('data-archivo');
            if (arch && arch !== '') {
                archivoContainer.innerHTML = `<a href="../../assets/${arch}" target="_blank" class="btn btn-info btn-sm">Ver instrucción</a>`;
            } else {
                archivoContainer.innerHTML = `<span class="text-muted">No disponible</span>`;
            }
            break;
        }
    }
    if (!encontrado) {
        console.log('Área no encontrada, limpiando');
        descripcion.value = '';
        motivo.value = '';
        areaId.value = '';
        archivoContainer.innerHTML = '<span class="text-muted">No disponible</span>';
        lineaContainer.style.display = 'none';
        linea.removeAttribute('required');
        linea.value = '';
    }
}

const scrapInput = document.getElementById('piezas_scrap');
scrapInput.addEventListener('change', function() {
    const container = document.getElementById('condicionesContainer');
    if (parseInt(this.value) > 0) {
        container.style.display = 'block';
    } else {
        container.style.display = 'none';
    }
});
</script>
<script>
    console.log("Script cargado correctamente");
    var lista = document.getElementById("listaAreas");
    console.log("Número de opciones en datalist: " + lista.options.length);
    for(var i=0; i<lista.options.length; i++) {
        console.log("Opción " + i + ": " + lista.options[i].value);
    }
</script>
</body>
</html>