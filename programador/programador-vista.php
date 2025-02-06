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
                            <div class="card-body">
                                <h5>Materias</h5>
                                <div class="table-responsive">
                                    <table id="datos_materia" class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>ID materia</th>
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
                            <div class="card-body">
                                <h5>Docentes Disponibles</h5>
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
                <button type="button" class="btn btn-success" onclick="funcion()">Programar Clase</button>
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
                            <th>ID Programador</th>
                            <th>Fecha</th>
                            <th>Hora de Inicio</th>
                            <th>Hora de Salida</th>
                            <th>Salón</th>
                            <th>Docente</th>
                            <th>Materia</th>
                            <th>Modificar</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                </table>
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
    var table = $('#datos_materia').DataTable({
        language: {
            url: "//cdn.datatables.net/plug-ins/1.11.5/i18n/es-ES.json"
        },
        searching: true,
        paging: true,
        lengthChange: true,
        pageLength: 5,
        processing: true,
        serverSide: true,
        ajax: {
            url: "materias-controlador.php",
            type: "POST",
            dataSrc: 'data'  
        },
        columns: [
            { "data": "id_materia" },
            { "data": "nombre" },
            { "data": "descripcion" },
            { "data": "radio_button", "orderable": false }
        ]
    });
});


      function funcion() {

        var Data = table.row($(this).parents('tr')).data();
        var idMateria = data.id_materia;
    
        const formData = new FormData(document.getElementById('programadorForm'));
        console.log('Datos del formulario:', ...formData.entries());
    
        $.ajax({
            url: 'Programador-Controlador.php?accion=crear',
            type: 'POST',
            data: {formData, id_materia:idMateria},
            processData: false,
            contentType: false,
            success: function(response) {
                console.log('Respuesta del servidor:', response);
                //location.reload();
            },
            error: function(xhr, status, error) {
                console.error('Error:', error);
            }
        });
    }
</script>
