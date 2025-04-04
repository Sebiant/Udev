<?php
    include_once '../componentes/header.php';
?>

<div class="container">
    <h1 class="text-center">Gestion Instituciones</h1>

    <div class="row">
        <div class="col-2 offset-10">
            <div class="text-center">
                <!-- Button trigger modal -->
                <button type="button" class="btn btn-primary w-100 " data-bs-toggle="modal" data-bs-target="#modalInstituciones" id="botonCrear">
                    <i class="bi bi-plus-circle"></i> Crear
                </button>
            </div>
        </div>
    </div>
    <br />
    <br />

    <div class="card">
        <div class="card-header">
            <h5>Instituciones</h5>
        </div>
        <div class="table-responsive card-body">
            <table id="datos_instituciones" class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Nombres</th>
                        <th>Dirección</th>
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
<div class="modal fade" id="modalInstituciones" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Agregar Instituciones</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="formInstituciones">
                    <div class="mb-3">
                        <input type="hidden" name="accion" value="crear" id="accion">
                        <input type="hidden" name="id_institucion" id="id_institucion">

                        <div class="mb-3">
                            <label for="nombre" class="form-label">Nombre:</label>
                            <input type="text" name="nombre" id="nombre" class="form-control" placeholder="Nombre">
                        </div>
                        <div class="mb-3">
                            <label for="direccion" class="form-label">Dirección:</label>
                            <input type="text" name="direccion" id="direccion" class="form-control" placeholder="Dirección">
                        </div>
                        
                        <div class="modal-footer">
                            <button type="button" class="btn btn-success" onclick="crearInstitucion()">Guardar</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal de edición -->
<div id="editModal" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Editar institucion</h5>
            </div>
            <form id="editForm">
                <div class="modal-body">
                    <input type="hidden" name="id_institucion">
                    
                    <div class="form-group">
                        <label for="nombre">Nombre</label>
                        <input type="text" class="form-control" name="nombre" placeholder="Nombre">
                    </div>
                    <div class="form-group">
                        <label for="direccion">Dirección</label>
                        <input type="text" class="form-control" name="direccion" placeholder="Dirección">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" onclick="GuardarInstitucion()">Guardar Cambios</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php
    include_once '../componentes/footer.php';
?>
<script src="js/Consultas-Instituciones.js"></script>
<script src="js/Datatable-Instituciones.js"></script>
<script>
      function crearInstitucion() {
        if (!$("#formInstituciones").valid()) {
            console.log("El formulario no es válido.");
            return; 
        }
    
        const formData = new FormData(document.getElementById('formInstituciones'));
        console.log('Datos del formulario:', ...formData.entries());
    
        $.ajax({
            url: 'Instituciones-Controlador.php?accion=crear',
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
      function GuardarInstitucion() {
        if (!$("#editForm").valid()) {
            console.log("El formulario no es válido.");
            return; 
        }
    
        const formData = new FormData(document.getElementById('editForm'));
        console.log('Datos del formulario:', ...formData.entries());
    
        $.ajax({
            url: 'instituciones-controlador.php?accion=editar',
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