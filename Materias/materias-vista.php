<?php
include_once '../componentes/header.php';
?>

<div class="container">
    <h1 class="text-center">Materias</h1>

    <div class="row">
        <div class="col-2 offset-10">
            <div class="text-center">
                <!-- Button trigger modal -->
                <button type="button" class="btn btn-primary w-100" data-bs-toggle="modal" data-bs-target="#modalMateria" id="botonCrear">
                    <i class="bi bi-plus-circle"></i> Crear
                </button>
            </div>
        </div>
    </div>
    <br />
    <br />

    <div class="table-responsive">
        <table id="datos_materia" class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Id</th>
                    <th>Nombre</th>
                    <th>Descripcion</th>
                    <th>Estado</th>
                    <th>Modificar</th>
                    <th>Acciones</th>
                </tr>
            </thead>
        </table>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="modalMateria" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Agregar Materias</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="formMateria">
                    <div class="mb-3">
                        <input type="hidden" name="accion" value="crear" id="accion">
                        <input type="hidden" name="id_materia" id="id_materia">

                        <div class="mb-3">
                            <label for="nombre" class="form-label">Nombre de la materia:</label>
                            <input type="text" name="nombre" id="nombre" class="form-control" placeholder="Nombre de la materia">
                        </div>
                        <div class="mb-3">
                            <label for="descripcion" class="form-label">Descripcion de la materia:</label>
                            <textarea name="descripcion" id="descripcion" maxlength="30" class="form-control"  placeholder="Descripción de la materia"></textarea>
                            <small id="contadorCrear" class="contador-texto">30 caracteres disponibles</small>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <input type="hidden" name="id_materia" id="id_materia">
                <input type="hidden" name="operacion" id="operacion">
                <button type="submit" class="btn btn-success" onclick="crearMateria()">Guardar</button>
            </div>
        </div>
    </div>
</div>

 <!-- Modal de edición -->
<div id="editModal" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Editar Materia</h5>
            </div>
            <form id="editForm">
                <div class="modal-body">
                        <input type="hidden" name="id_materia">
                        
                        <div class="mb-3">
                            <label for="nombre" class="form-label">Nombre de la materia:</label>
                            <input type="text" name="nombre" id="nombre" class="form-control" placeholder="Nombre de la materia">
                        </div>
                        <div class="mb-3">
                            <label for="descripcion" class="form-label">Descripcion de la materia:</label>
                            <textarea name="descripcion" id="descripcion_edit" maxlength="30" class="form-control"  placeholder="Descripción de la materia"></textarea>
                            <small id="contadorEditar" class="contador-texto">30 caracteres disponibles</small>
                        </div>
                    </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-primary" onclick="GuardarMateria()">Guardar Cambios</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

<?php
include_once '../componentes/footer.php';
?>

<script src="js/Consultas-Materias.js"></script>
<script src="js/Datatables-Materias.js"></script>
<script>
   function crearMateria() {
        if (!$("#formMateria").valid()) {
            console.log("El formulario no es válido.");
            return; 
        }
    
        const formData = new FormData(document.getElementById('formMateria'));
        console.log('Datos del formulario:', ...formData.entries());
    
        $.ajax({
            url: 'Materias-Controlador.php?accion=crear',
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
    function GuardarMateria() {
        if (!$("#editForm").valid()) {
            console.log("El formulario no es válido.");
            return; 
        }
    
        const formData = new FormData(document.getElementById('editForm'));
        console.log('Datos del formulario:', ...formData.entries());
    
        $.ajax({
            url: 'Materias-Controlador.php?accion=editar',
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