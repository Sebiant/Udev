<?php
include_once '../componentes/header.php';
include '../conexion.php';

// Consulta para obtener los programas de la base de datos
$sql = "SELECT id_programa, nombre FROM programas";
$result = $conn->query($sql);
?>

<div class="container">
    <h1 class="text-center">Módulos</h1>

    <div class="row">
        <div class="col-2 offset-10">
            <div class="text-center">
                <!-- Button trigger modal -->
                <button type="button" class="btn btn-primary w-100" data-bs-toggle="modal" data-bs-target="#modalModulos" id="botonCrear">
                    <i class="bi bi-plus-circle"></i> Crear
                </button>
            </div>
        </div>
    </div>
    <br />
    <br />

    <div class="table-responsive">
        <table id="datos_modulo" class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Id Modulo</th>
                    <th>Fecha Inicio</th>
                    <th>Fecha Fin</th> <!-- Corregido -->
                    <th>Programa</th>
                    <th>Estado</th>
                    <th>Editar</th>
                    <th>Borrar</th>
                </tr>
            </thead>
        </table>
    </div>
</div>

<!-- Modal para crear un módulo -->
<div class="modal fade" id="modalModulos" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Agregar Módulo</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="formModulo">
                    <input type="hidden" name="accion" value="crear" id="accion">
                    <input type="hidden" name="id_modulo" id="id_modulo">

                    <div class="mb-3">
                        <label for="fecha_inicio" class="form-label">Fecha Inicio:</label>
                        <input type="date" name="fecha_inicio" id="fecha_inicio" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="fecha_fin" class="form-label">Fecha Fin:</label> <!-- Corregido -->
                        <input type="date" name="fecha_fin" id="fecha_fin" class="form-control" required> <!-- Corregido -->
                    </div>
                    <div class="mb-3">
                        <label for="estado" class="form-label">Estado:</label>
                        <select name="estado" id="estado" class="form-control">
                            <option value="Activo">Activo</option>
                            <option value="Inactivo">Inactivo</option>
                        </select>
                        <br>
                        <label for="id_programa">Programa:</label>
                        <select id="id_programa" name="id_programa" class="form-control" required>
                            <option value="">-- Selecciona un programa --</option>
                            <?php
                            if ($result->num_rows > 0) {
                                while ($row = $result->fetch_assoc()) {
                                    echo '<option value="' . $row['id_programa'] . '">' . $row['nombre'] . '</option>';
                                }
                            } else {
                                echo '<option value="">No hay programas disponibles</option>';
                            }
                            ?>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success" onclick="crearModulo()">Guardar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal de edición -->
<div id="editModuloModal" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Editar Módulo</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editForm">
                <div class="modal-body">
                    <input type="hidden" name="id_modulo">
                    <div class="form-group">
                        <label for="edit_fecha_inicio">Fecha Inicio</label>
                        <input type="date" class="form-control" id="edit_fecha_inicio" name="fecha_inicio" required>
                    </div>
                    <div class="form-group">
                        <label for="edit_fecha_fin">Fecha Fin</label> 
                        <input type="date" class="form-control" id="edit_fecha_fin" name="fecha_fin" required>
                    </div>
                    <div class="form-group">
                        <label for="edit_estado">Estado</label>
                        <select class="form-control" id="edit_estado" name="estado">
                            <option value="Activo">Activo</option>
                            <option value="Inactivo">Inactivo</option>
                        </select>
                    </div>
                    <label for="id_programa">Programa:</label>
                    <select id="id_programa" name="id_programa" class="form-control" required>
                        <option value="">-- Selecciona un programa --</option>
                        <?php
                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                echo '<option value="' . $row['id_programa'] . '">' . $row['nombre'] . '</option>';
                            }
                        } else {
                            echo '<option value="">No hay programas disponibles</option>';
                        }
                        ?>
                    </select>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php
include_once '../componentes/footer.php';
?>
<script src="js/Datatable-Modulo.js"></script>
