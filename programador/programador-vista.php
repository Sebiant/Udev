<?php
    include_once '../componentes/header.php';
    
    include '../conexion.php';

    $sql_docentes = "SELECT id_docente, nombres, apellidos FROM docentes";
    $result_docentes = $conn->query($sql_docentes);

    $sql_salones = "SELECT id_salon, nombre_salon FROM salones";
    $result_salones = $conn->query($sql_salones);
?>

<div class="container mt-4">
    <h2 class="text-center mb-4">Programador</h2>
    <form id="programadorForm">
        <div class="row">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-body">
                        <h5>Materias</h5>
                        <div class="table-responsive">
                            <table id="datos_materia" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>ID materia</th>
                                        <th>Nombre</th>
                                        <th>Descripción</th>
                                        <th>Frecuencia</th>
                                        <th>Modalidad</th>
                                        <th>Seleccionar</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h5>Docentes Disponibles</h5>
                        <br>
                        <div class="form-group">
                            <label for="docente">Selecciona un docente</label>
                            <select id="docente" name="id_docente" class="form-control" required>
                                <option value="">-- Selecciona un docente --</option>
                                <?php
                                if ($result_docentes->num_rows > 0) {
                                    while ($row_docente = $result_docentes->fetch_assoc()) {
                                        echo '<option value="' . $row_docente['id_docente'] . '">' . $row_docente['nombres'] . " " . $row_docente['apellidos'] . '</option>';
                                    }
                                } else {
                                    echo '<option value="">No hay docentes disponibles</option>';
                                }
                                ?>
                            </select>
                        </div>

                        <div class="form-group mt-3">
                            <label for="salon">Selecciona un salón</label>
                            <select id="salon" name="id_salon" class="form-control" required>
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
        <div class="mt-4 d-flex justify-content-end">
            <button type="submit" class="btn btn-success">Programar Clase</button>
        </div>
    </form>

    <h2 class="text-center mb-4 mt-5">Clases Programadas</h2>
    <div class="card">
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
                            <th>Borrar</th>
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
<script src="js/datatable_programador.js"></script>
<script src="js/datatable_materias.js"></script>
