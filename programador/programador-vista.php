<?php
include_once '../componentes/header.php';
include '../conexion.php';

$sql_docentes = "SELECT numero_documento, nombres, apellidos FROM docentes";
$result_docentes = $conn->query($sql_docentes);

$sql_salones = "SELECT id_salon, nombre_salon FROM salones";
$result_salones = $conn->query($sql_salones);

$sql_periodos = "SELECT id_periodo, nombre FROM periodos";
$result_periodos = $conn->query($sql_periodos);
?>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<style>
    .hidden { display: none; }
</style>

<div class="container mt-5">
    <div class="card">
        <div class="card-header">
            <h2 class="text-center">Programador</h2>
        </div>
        <div class="card-body">
            <form id="multiStepForm">
                <!-- Paso 1 -->
                <div class="step">
                    <h4>Paso 1: Selección de Período y Materias</h4>
                    <div class="mb-3">
                        <label for="periodo" class="form-label">Selecciona un periodo:</label>
                        <select class="form-select" id="periodo">
                            <option value="">Seleccione...</option>
                            <?php
                                if ($result_periodos->num_rows > 0) {
                                    while ($row_periodos = $result_periodos->fetch_assoc()) {
                                        echo '<option value="' . $row_periodos['id_periodo'] . '">' . $row_periodos['nombre'] . '</option>';
                                    }
                                } else {
                                    echo '<option value="">No hay periodos disponibles</option>';
                                }
                            ?>
                        </select>
                    </div>

                    <div class="card">
                        <div class="card-header"> 
                            <h5>Materias</h5>
                        </div>
                        <div class="card-body"> 
                            <div class="table-responsive">
                                <table id="datos_modulo" class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>Tipo</th>
                                            <th>Nombre</th>
                                            <th>Programa</th>
                                            <th>Descripción</th>
                                            <th>Seleccionar</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end mt-3">
                        <button type="button" class="btn btn-primary next">Siguiente</button>
                    </div>
                </div>

                <!-- Paso 2 -->
                <div class="step hidden">
                    <h4>Paso 2: Asignación de Docente y Salón</h4>

                    <div class="card">
                        <div class="card-header">
                            <h5>Docentes</h5>
                        </div>
                        <div class="card-body">
                            <div class="form-group">
                                <label for="docente">Selecciona un docente</label>
                                <select id="numero_documento" name="numero_documento" class="form-control" required>
                                    <option value="">-- Selecciona un docente --</option>
                                    <?php
                                    if ($result_docentes->num_rows > 0) {
                                        while ($row_docente = $result_docentes->fetch_assoc()) {
                                            echo '<option value="' . $row_docente['numero_documento'] . '">' . $row_docente['nombres'] . " " . $row_docente['apellidos'] . '</option>';
                                        }
                                    } else {
                                        echo '<option value="">No hay docentes disponibles</option>';
                                    }
                                    ?>
                                </select>
                            </div>

                            <div class="form-group mt-3">
                                <label for="salon">Selecciona un salón</label>
                                <select id="id_salon" name="id_salon" class="form-control" required>
                                    <option value="">-- Selecciona un salón --</option>
                                    <?php
                                    if ($result_salones->num_rows > 0) {
                                        while ($row_salon = $result_salones->fetch_assoc()) {
                                            echo '<option value="' . $row_salon['id_salon'] . '">' . $row_salon['nombre_salon'] . '</option>';
                                        }
                                    } else {
                                        echo '<option value="">No hay salones disponibles</option>';
                                    }
                                    ?>
                                </select>
                            </div>

                            <div class="row mt-3">
                                <!-- Columna izquierda: Hora de Inicio y Hora de Salida -->
                                <div class="col-md-6">
                                    <div class="row">
                                        <div class="col-12">
                                            <label for="hora_inicio">Hora de Inicio</label>
                                            <input type="time" id="hora_inicio" name="hora_inicio" class="form-control" step="900" required>
                                        </div>
                                        <div class="col-12 mt-2">
                                            <label for="hora_salida">Hora de Salida</label>
                                            <input type="time" id="hora_salida" name="hora_salida" class="form-control" step="900" required>
                                        </div>
                                    </div>
                                </div>

                                <!-- Columna derecha: Fecha -->
                                <div class="col-md-6 d-flex flex-column justify-content-center">
                                    <label for="fecha">Fecha de la clase</label>
                                    <input type="date" id="fecha" name="fecha" class="form-control" required>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end mt-3">
                        <button type="button" class="btn btn-secondary prev me-2">Anterior</button>
                        <button type="button" class="btn btn-success" onclick="CrearClase()">Programar Clase</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="container mt-4">
    <div class="card">
        <div class="card-header">
            <h5>Clases Programadas</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table id="datos_programador" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Fecha</th>
                            <th>Hora de Inicio</th>
                            <th>Hora de Salida</th>
                            <th>Salón</th>
                            <th>Docente</th>
                            <th>Materia</th>
                            <th>Estado</th>
                            <th>Modalidad</th>
                            <th>Modificar</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Aquí irían las filas de la tabla -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal de edición -->
<div class="modal fade" id="modalEditarClase" tabindex="-1" aria-labelledby="modalEditarClaseLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Modificar Clase</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editarClaseForm">
                    <input type="hidden" id="id_programador" name="id_programador">

                    <div class="mb-3">
                        <label for="fecha">Fecha</label>
                        <input type="date" id="fecha" name="fecha" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label for="hora_inicio">Hora de Inicio</label>
                        <input type="time" id="hora_inicio" name="hora_inicio" class="form-control">
                    </div>

                    <div class="mb-3">
                        <label for="hora_salida">Hora de Salida</label>
                        <input type="time" id="hora_salida" name="hora_salida" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label for="id_salon">Salón</label>
                        <select id="id_salon" name="id_salon" class="form-control">
                            <option value="">-- Selecciona un salón --</option>
                            <?php
                            $sql_salones = "SELECT id_salon, nombre_salon FROM salones";
                            $result_salones = $conn->query($sql_salones);

                            if ($result_salones->num_rows > 0) {
                                while ($row_salon = $result_salones->fetch_assoc()) {
                                    echo '<option value="' . $row_salon['id_salon'] . '">' . $row_salon['nombre_salon'] . '</option>';
                                }
                            } else {
                                echo '<option value="">No hay salones disponibles</option>';
                            }
                            ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="numero_documento">Docente</label>
                        <select id="numero_documento" name="numero_documento" class="form-control">
                            <option value="">-- Selecciona un docente --</option>
                            <?php
                            $sql_docentes = "SELECT numero_documento, nombres, apellidos FROM docentes";
                            $result_docentes = $conn->query($sql_docentes);

                            if ($result_docentes->num_rows > 0) {
                                while ($row_docente = $result_docentes->fetch_assoc()) {
                                    echo '<option value="' . $row_docente['numero_documento'] . '">' . $row_docente['nombres'] . " " . $row_docente['apellidos'] . '</option>';
                                }
                            } else {
                                echo '<option value="">No hay docentes disponibles</option>';
                            }
                            ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="id_modulo">Módulos</label>
                        <select id="id_asignacion_periodo" name="id_modulo" class="form-control">
                            <option value="">-- Selecciona un módulo --</option>
                            <?php
                            $sql_materias = "SELECT id_modulo, nombre FROM modulos";
                            $result_materias = $conn->query($sql_materias);

                            if ($result_materias->num_rows > 0) {
                                while ($row_materias = $result_materias->fetch_assoc()) {
                                    echo '<option value="' . $row_materias['id_modulo'] . '">' . $row_materias['nombre'] . '</option>';
                                }
                            } else {
                                echo '<option value="">No hay módulos disponibles</option>';
                            }
                            ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="modalidad">Modalidad</label>
                        <select name="modalidad" id="modalidad" class="form-control">
                            <option value="">-- Selecciona la Modalidad --</option>
                            <option value="Presencial">Presencial</option>
                            <option value="Virtual">Virtual</option>
                        </select>
                    </div>
                    <div class="d-flex justify-content-center">
                        <button id="estado" class="btn btn-danger fw-bold">
                            Marcar Clase como Perdida
                        </button>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" onclick="GuardarClase()">Guardar Cambios</button>
            </div>
        </div>
    </div>
</div>
<br>

<?php
include_once '../componentes/footer.php';
?>

<script src="js/Datatable-Programador.js"></script>
<script src="js/Datatable-materia.js"></script>
<script src="js/Validate.js"></script>

<script>
    const steps = document.querySelectorAll(".step");
    const nextBtns = document.querySelectorAll(".next");
    const prevBtns = document.querySelectorAll(".prev");
    let currentStep = 0;

    function showStep(index) {
        steps.forEach((step, i) => {
            step.classList.toggle("hidden", i !== index);
        });
    }

    nextBtns.forEach(btn => {
        btn.addEventListener("click", () => {
            if (currentStep < steps.length - 1) {
                currentStep++;
                showStep(currentStep);
            }
        });
    });

    prevBtns.forEach(btn => {
        btn.addEventListener("click", () => {
            if (currentStep > 0) {
                currentStep--;
                showStep(currentStep);
            }
        });
    });

    function CrearClase() {
        alert("Clase programada con éxito!");
    }

    function GuardarClase() {
        const formData = new FormData(document.getElementById('editarClaseForm'));

        console.log('Datos del formulario:', ...formData.entries());

        $.ajax({
            url: 'Programador-Controlador.php?accion=editar',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                console.log('Respuesta del servidor:', response);
                // location.reload();
            },
            error: function(xhr, status, error) {
                console.error('Error:', error);
            }
        });
    }

    document.getElementById("estado").addEventListener("click", function() {
        let confirmar = confirm("¿Estás seguro de marcar esta clase como perdida?");
        if (confirmar) {
            location.reload();
        }
    });

    showStep(currentStep);
</script>