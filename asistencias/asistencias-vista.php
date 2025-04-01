<?php 
include_once '../componentes/header.php'; 
include_once '../conexion.php';

$conn->query("SET lc_time_names = 'es_ES'");

$sql = "SELECT DATE_FORMAT(CURDATE(), '%W %d de %M de %Y') AS fecha_hoy;";
$resultado = $conn->query($sql);

$sql = "SELECT numero_documento, nombres, apellidos FROM docentes";
$result = $conn->query($sql);

$fila = $resultado->fetch_assoc();
?>

<div class="container">
    <br>
    <div class="card">
        <div class="card-header text-center">
            <h5>Gestion de Asistencias</h5>
        </div>
        <div class="card-body">
            <div class="card">
                <div class="card-header">
                    <h5>Clases Programadas: <?php echo $fila['fecha_hoy']; ?></h5>
                </div>
                <div class="card-body">
                    <div class="overflow-auto" style="max-height: 300px;">
                        <div class="table-responsive">
                            <table id="datos_programador" class="table table-striped table-bordered">
                                <thead class="thead-dark">
                                    <tr>
                                        <th>Hora</th>
                                        <th>docente</th>
                                        <th>Materia</th>
                                        <th>Salón</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
                <br>
            <!-- Card: Asistencias del día de hoy -->
            <div class="card">
    <div class="card-header">
        <h5>Historial de asistencias</h5>
    </div>
    <div class="card-body">
    <form id="formHistorial" class="row g-3 align-items-center">
    <div class="col-md-4">
        <label for="docente" class="form-label">Docente:</label>
        <select id="docente" name="numero_documento" class="form-select" required>
            <option value="">Seleccione un docente</option>
            <?php while ($row = $result->fetch_assoc()) : ?>
                <option value="<?= $row['numero_documento']; ?>">
                    <?= $row['nombres'] . ' ' . $row['apellidos']; ?>
                </option>
            <?php endwhile; ?>
        </select>
    </div>

    <div class="col-md-3">
        <label for="mes" class="form-label">Mes:</label>
        <select id="mes" name="mes" class="form-select" required>
            <option value="">Seleccione un mes</option>
            <option value="1">Enero</option>
            <option value="2">Febrero</option>
            <option value="3">Marzo</option>
            <option value="4">Abril</option>
            <option value="5">Mayo</option>
            <option value="6">Junio</option>
            <option value="7">Julio</option>
            <option value="8">Agosto</option>
            <option value="9">Septiembre</option>
            <option value="10">Octubre</option>
            <option value="11">Noviembre</option>
            <option value="12">Diciembre</option>
        </select>
    </div>

    <div class="col-md-3">
        <label for="año" class="form-label">Año:</label>
        <select id="año" name="año" class="form-select" required>
            <option value="">Seleccione un año</option>
        </select>
    </div>

    <div class="col-md-2 text-md-end">
        <button class="btn btn-primary w-100" type="submit">Buscar</button>
    </div>
</form>

<br>

<!-- Mensaje de advertencia -->
<div id="mensaje-docente" class="alert alert-warning text-center mt-3" role="alert">
    ⚠️ Por favor, seleccione un docente para ver las asistencias.
</div>

<!-- Tabla de asistencias -->
<div class="overflow-auto" style="max-height: 300px;">
    <div class="table-responsive">
        <table id="datos_asistencias" class="table table-striped table-bordered d-none">
            <thead class="thead-dark">
                <tr>
                    <th>Fecha</th>
                    <th>Hora Entrada</th>
                    <th>Hora Salida</th>
                    <th>Docente</th>
                    <th>Estado</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div>
</div>

<script>
    // Generar años dinámicamente
    let yearSelect = document.getElementById("año");
    let currentYear = new Date().getFullYear();
    for (let i = currentYear; i >= currentYear - 10; i--) {
        let option = document.createElement("option");
        option.value = i;
        option.textContent = i;
        yearSelect.appendChild(option);
    }
</script>


<?php include_once '../componentes/footer.php'; ?>
<script src="js/Datatable-Programador.js"></script>
<script src="js/Datatable-Asistencias.js"></script>
