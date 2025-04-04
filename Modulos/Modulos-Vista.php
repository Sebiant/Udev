<?php
include_once '../componentes/header.php';
include '../conexion.php';
$sql_programa = "SELECT id_programa, nombre FROM programas";
$result_programa = $conn->query($sql_programa);
if (!$result_programa) {
    die("Error en la consulta: " . $conn->error);
}
?>

<div class="container">
    <h1 class="text-center">Gestión Materias</h1>

    <div class="row">
        <div class="col-2 offset-10">
            <div class="text-center">
                <!-- Button trigger modal -->
                <button type="button" class="btn btn-primary w-100" data-bs-toggle="modal" data-bs-target="#modalModulo" id="botonCrear">
                    <i class="bi bi-plus-circle"></i> Crear
                </button>
            </div>
        </div>
    </div>
    <br />
    <br />
    <div class="card">
        <div class="card-header">
            <h5>Módulos</h5>
        </div>
        <div class="table-responsive card-body">
            <table id="datos_modulo" class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Tipo</th>
                        <th>Nombre</th>
                        <th>Programa</th>
                        <th>Descripción</th>
                        <th>Estado</th>
                        <th>Modificar</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="modalModulo" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
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
                        <label for="tipo" class="form-label">Tipo de módulo:</label>
                        <select name="tipo" id="tipo" class="form-control" required>
                            <option value="">Seleccione un tipo...</option>
                            <option value="Obligatorio Específico">Obligatorio Específico</option>
                            <option value="Obligatorio General">Obligatorio General</option>
                            <option value="Módulo Electivo">Módulo Electivo</option>
                            <option value="Módulo Transversal">Módulo Transversal</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="nombre" class="form-label">Nombre del módulo:</label>
                        <input type="text" name="nombre" id="nombre" class="form-control" maxlength="50" placeholder="Nombre del módulo" required>
                    </div>
                    <label for="programa">Selecciona un programa</label>
                    <select name="id_programa"  id="id_programa" class="form-control" required>
                        <option value="">-- Selecciona un programa --</option>
                        <?php
                            if ($result_programa->num_rows > 0) {
                                while ($row_programa = $result_programa->fetch_assoc()) {
                                    echo '<option value="' . $row_programa['id_programa'] . '">' . $row_programa['nombre'] . '</option>';
                                }
                            } else {
                                echo '<option value="">No hay programas disponibles</option>';
                            }
                        ?>
                    </select>
                    <div class="mb-3">
                        <label for="descripcion" class="form-label">Descripción:</label>
                        <textarea name="descripcion" id="descripcion" maxlength="30" class="form-control" placeholder="Máximo 30 caracteres"></textarea>
                        <small id="contadorCrear" class="contador-texto">30 caracteres disponibles</small>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-success" onclick="crearModulo()">Guardar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal de edición -->
<div id="editModal" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Editar Módulo</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editForm">
                <div class="modal-body">
                    <input type="hidden" name="id_modulo" id="id_modulo_edit">
                    <div class="mb-3">
                        <label for="tipo_edit" class="form-label">Tipo de módulo:</label>
                        <select name="tipo" id="tipo_edit" class="form-control" required>
                            <option value="">Seleccione un tipo...</option>
                            <option value="Obligatorio Específico">Obligatorio Específico</option>
                            <option value="Obligatorio General">Obligatorio General</option>
                            <option value="Módulo Electivo">Módulo Electivo</option>
                            <option value="Módulo Transversal">Módulo Transversal</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="nombre_edit" class="form-label">Nombre del módulo:</label>
                        <input type="text" name="nombre" id="nombre_edit" class="form-control" maxlength="50" placeholder="Nombre del módulo" required>
                    </div>
                    <div class="form-group">
                    <label for="programa">Selecciona un programa</label>
                    <select id="id_programa" name="id_programa" class="form-control" required>
                        <option value="">-- Selecciona un programa --</option>
                        <?php
                            $sql_programa = "SELECT id_programa, nombre FROM programas";
                            $result_programa = $conn->query($sql_programa);

                            if ($result_programa->num_rows > 0) {
                                while ($row_programa = $result_programa->fetch_assoc()) {
                                    echo '<option value="' . $row_programa['id_programa'] . '">' . $row_programa['nombre'] . '</option>';
                                }
                            } else {
                                echo '<option value="">No hay programas disponibles</option>';
                            }
                        ?>
                    </select>
                    <div class="mb-3">
                        <label for="descripcion_edit" class="form-label">Descripción:</label>
                        <textarea name="descripcion" id="descripcion_edit" maxlength="30" class="form-control" placeholder="Máximo 30 caracteres"></textarea>
                        <small id="contadorEditar" class="contador-texto">30 caracteres disponibles</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary" onclick="guardarModulo()">Guardar Cambios</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php
include_once '../componentes/footer.php';
?>

<script src="js/Consultas-Modulos.js"></script>
<script src="js/Datatables-Modulos.js"></script>
<script>
   function crearModulo() {
        if (!$("#formModulo").valid()) {
            console.log("El formulario no es válido.");
            return; 
        }
    
        const formData = new FormData(document.getElementById('formModulo'));
        console.log('Datos del formulario:', ...formData.entries());
    
        $.ajax({
            url: 'Modulos-Controlador.php?accion=crear',
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
    function guardarModulo() {
        if (!$("#editForm").valid()) {
            console.log("El formulario no es válido.");
            return; 
        }
    
        const formData = new FormData(document.getElementById('editForm'));
        console.log('Datos del formulario:', ...formData.entries());
    
        $.ajax({
            url: 'Modulos-Controlador.php?accion=editar',
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
