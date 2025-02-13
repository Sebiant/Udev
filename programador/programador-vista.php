<?php
    include_once '../componentes/header.php';
    
    include '../conexion.php';

    $sql_docentes = "SELECT numero_documento, nombres, apellidos FROM docentes";
    $result_docentes = $conn->query($sql_docentes);

    $sql_salones = "SELECT id_salon, nombre_salon FROM salones";
    $result_salones = $conn->query($sql_salones);
?>
<div class="container mt-4">
    <div class="card">
        <div class="card-header text-center">
            <h5>Programador</h5>
        </div>
        <div class="card-body">
            <form id="programadorForm">
                <div class="row">
                    <div class="col-md-8 d-flex flex-column">
                        <div class="card flex-fill">
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
                                                <th>Descripción</th>
                                                <th>Seleccionar</th>
                                            </tr>
                                        </thead>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4 d-flex flex-column">
                        <div class="card flex-fill">
                        <div class="card-header">
                                <h5>Docentes</h5>
                            </div>
                            <div class="card-body">
                                <br>
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

                                <div class="form-group mt-3">
                                    <label for="fecha">Fecha de la clase</label>
                                    <input type="date" id="fecha" name="fecha" class="form-control" required>
                                </div>
                                <div class="form-group mt-3">
                                    <label for="hora_inicio">Hora de Inicio</label>
                                    <input type="time" id="hora_inicio" name="hora_inicio" class="form-control" required>
                                </div>
                                <div class="form-group mt-3">
                                    <label for="hora_salida">Hora de Salida</label>
                                    <input type="time" id="hora_salida" name="hora_salida" class="form-control" required>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <div class="col-2 offset-10">
            <div class="text-center">
                <button type="button" class="btn btn-success" onclick="CrearClase()">Programar Clase</button>
            </div>
        </div>
        <br>
    </div>
</div>

<br>
<div class="container mt-4">
    <div class="card">
        <div class="card-header">
            <h5>Clases Programadas</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table id="datos_docente" class="table table-bordered table-striped">
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
                </table>
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
                    <label for="id_modulo">Modulos</label>
                        <select id="id_asignacion_periodo" name="id_modulo" class="form-control">
                            <option value="">-- Selecciona una modulos --</option>
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
</div>




<?php
    include_once '../componentes/footer.php';
?>
<script src="js/Datatable-Programador.js"></script>

<script src="js/datatable_materias.js"></script>


<script>
    $(document).ready(function() {
    var table = $('#datos_modulo').DataTable({
        language: {
            url: "//cdn.datatables.net/plug-ins/1.11.5/i18n/es-ES.json"
        },
        searching: true,
        paging: true,
        lengthChange: false,
        pageLength: 5,
        processing: true,
        serverSide: true,
        ajax: {
            url: "Modulos-Controlador.php",
            type: "POST",
            dataSrc: 'data'  
        },
        columns: [
            { "data": "tipo" },
            { "data": "nombre" },
            { "data": "descripcion" },
            {
                "data": "id_modulo",
                "render": function(data, type, row) {
                    return `<input type="radio" name="moduloSeleccionado" value="${data}">`;
                },
                "orderable": false
            }
        ]
    });
});


function CrearClase() {
    var idMateria = $("input[name='materiaSeleccionada']:checked").val(); // Obtiene el ID de la materia seleccionada

    if (!idMateria) {
        alert("Por favor, selecciona una materia.");
        return;
    }

    const formData = new FormData(document.getElementById('programadorForm'));
    formData.append("id_materia", idMateria);

    console.log('Datos del formulario:', ...formData.entries());

    $.ajax({
        url: 'Programador-Controlador.php?accion=crear',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
            console.log('Respuesta del servidor:', response);
            location.reload();
        },
        error: function(xhr, status, error) {
            console.error('Error:', error);
        }
    });
}

</script>
<script>
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
// Confirmación antes de recargar la página
let confirmar = confirm("¿Estás seguro de marcar esta clase como perdida?");
if (confirmar) {
    location.reload(); // Recargar la página
}
});
</script>
