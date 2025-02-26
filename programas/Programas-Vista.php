<?php
include_once '../componentes/header.php';
?>

<div class="container">
    <h1 class="text-center">Gestion Programas</h1>

    <div class="row">
        <div class="col-2 offset-10">
            <div class="text-center">
                <!-- Botón para abrir modal -->
                <button 
                    type="button" 
                    class="btn btn-primary w-100" 
                    data-bs-toggle="modal" 
                    data-bs-target="#modalPrograma" 
                    id="botonCrear">
                    <i class="bi bi-plus-circle"></i> Crear
                </button>
            </div>
        </div>
    </div>
    <br>
    <br>
    <div class="card">
        <div class="card-header">
            <h5>Programas</h5>
        </div>
        <div class="table-responsive">
            <table id="datos_programa" class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Tipo</th>
                        <th>Nombre</th>
                        <th>Duración</th>
                        <th>Valor Programa</th>
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

<!-- Modal Crear Programa -->
<div class="modal fade" id="modalPrograma" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Agregar Programa</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="formPrograma">
                    <input type="hidden" name="accion" value="crear" id="accion">
                    <input type="hidden" name="id_programa" id="id_programa">

                    <div class="mb-3">
                        <label for="tipo" class="form-label">Tipo de Programa:</label>
                        <input type="text" name="tipo" id="tipo" class="form-control" placeholder="Tipo de programa">
                    </div>
                    <div class="mb-3">
                        <label for="nombre" class="form-label">Nombre del programa:</label>
                        <input type="text" name="nombre" id="nombre" class="form-control" placeholder="Nombre del programa">
                    </div>
                    <div class="mb-3">
                        <label for="duracion_mes" class="form-label">Duración:</label>
                        <input type="number" name="duracion_mes" id="duracion_mes" class="form-control" placeholder="Duración en meses">
                    </div>
                    <div class="mb-3">
                        <label for="valor_total_programa" class="form-label">Valor Programa:</label>
                        <input type="text" name="valor_total_programa" id="valor_total_programa" class="form-control" placeholder="Valor Programa">
                    </div>
                    <div class="mb-3">
                        <label for="descripcion_crear" class="form-label">Descripción:</label>
                        <textarea name="descripcion" id="descripcion" maxlength="30" class="form-control" placeholder="Descripción"></textarea>
                        <small id="contadorCrear" class="contador-texto">30 caracteres disponibles</small>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success" onclick="crearPrograma()">Guardar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Editar Programa -->
<div id="editModal" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Editar Programa</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editForm">
                <div class="modal-body">
                    <input type="hidden" name="id_programa" id="id_programa_edit">

                    <div class="form-group">
                        <label for="tipo_edit" class="form-label">Tipo de Programa:</label>
                        <input type="text" name="tipo" id="tipo_edit" class="form-control" placeholder="Tipo de programa">
                    </div>
                    <div class="form-group">
                        <label for="nombre_edit" class="form-label">Nombre del programa:</label>
                        <input type="text" name="nombre" id="nombre_edit" class="form-control" placeholder="Nombre del programa">
                    </div>
                    <div class="form-group">
                        <label for="duracion_mes_edit" class="form-label">Duración:</label>
                        <input type="number" name="duracion_mes" id="duracion_mes_edit" class="form-control" placeholder="Duración en meses">
                    </div>
                    <div class="form-group">
                        <label for="valor_total_programa" class="form-label">Valor Programa:</label>
                        <input type="text" name="valor_total_programa" class="form-control" placeholder="Valor Programa">
                    </div>
                    <div class="form-group">
                        <label for="descripcion_edit" class="form-label">Descripción:</label>
                        <textarea name="descripcion" id="descripcion_edit" maxlength="30" class="form-control" placeholder="Descripción"></textarea>
                        <small id="contadorEditar" class="contador-texto">30 caracteres disponibles</small>
                    </div>
                </div>
                <div class="modal-footer">
                <button type="button" class="btn btn-primary" onclick="editarPrograma()">Guardar Cambios</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php
include_once '../componentes/footer.php';
?>
<script src="js/Validation-Programas.js"></script>
<script src="js/Datatable-Programas.js"></script>
<script>
   function crearPrograma() {
        if (!$("#formPrograma").valid()) {
            console.log("El formulario no es válido.");
            return; 
        }
    
        const formData = new FormData(document.getElementById('formPrograma'));
        console.log('Datos del formulario:', ...formData.entries());
    
        $.ajax({
            url: 'Programas-Controlador.php?accion=crear',
            type: 'POST',
            data: formData,
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

function editarPrograma() {
        if (!$("#editForm").valid()) {
            console.log("El formulario no es válido.");
            return; 
        }
    
        const formData = new FormData(document.getElementById('editForm'));
        console.log('Datos del formulario:', ...formData.entries());
    
        $.ajax({
            url: 'Programas-Controlador.php?accion=editar',
            type: 'POST',
            data: formData,
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

