<?php
include_once '../componentes/header.php';
include '../conexion.php';

?>

<div class="container">
    <h1 class="text-center">Gestion Periódos</h1>

    <div class="row">
        <div class="col-2 offset-10">
            <div class="text-center">
                <!-- Button trigger modal -->
                <button type="button" class="btn btn-primary w-100" data-bs-toggle="modal" data-bs-target="#modalPeriodos" id="botonCrear">
                    <i class="bi bi-plus-circle"></i> Crear
                </button>
            </div>
        </div>
    </div>
    <br />
    <br />
    <div class="card">
        <div class="card-header">
            <h5>Periódos</h5>
        </div>
        <div class="table-responsive card-body">
            <table id="datos_periodo" class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Fecha Inicio</th>
                        <th>Fecha Fin</th>
                        <th>Estado</th>
                        <th>Modificar</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>    
</div>

<!-- Modal de creación -->
<div class="modal fade" id="modalPeriodos" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Agregar Periódo</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="formPeriodo">
                    <input type="hidden" name="accion" value="crear" id="accion">
                    <input type="hidden" name="id_periodo" id="id_periodo">

                    <div class="mb-3">
                        <label for="nombre" class="form-label">Nombre</label>
                        <input type="text" name="nombre" id="nombre" class="form-control" placeholder="Nombre">
                    </div>
                    <div class="mb-3">
                        <label for="fecha_inicio" class="form-label">Fecha Inicio:</label>
                        <input type="date" name="fecha_inicio" id="fecha_inicio" class="form-control" min="2024-11-29" max="2026-12-31" placeholder="Fecha Incio">
                    </div>
                    <div class="mb-3">
                        <label for="fecha_fin" class="form-label">Fecha Fin:</label>
                        <input type="date" name="fecha_fin" id="fecha_fin" class="form-control" min="2024-11-29" max="2026-12-31" placeholder="Fecha Fin">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-success" onclick="crearPeriodo()">Guardar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal de edición -->
<div id="editPeriodoModal" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Editar Periódo</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editForm">
                <div class="modal-body">
                    <input type="hidden" name="id_periodo">
                    <div class="form-group">
                        <label for="nombre" class="form-label">Nombre</label>
                        <input type="text" name="nombre" class="form-control" placeholder="Nombre">
                    </div>
                    <div class="form-group">
                        <label for="fecha_inicio">Fecha Inicio</label>
                        <input type="date" class="form-control" name="fecha_inicio" placeholder="Fecha Inicio">
                    </div>
                    <div class="form-group">
                        <label for="fecha_fin">Fecha Fin</label> 
                        <input type="date" class="form-control" name="fecha_fin" placeholder="Fecha Fin">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" onclick="GuardarPeriodo()">Guardar Cambios</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php
include_once '../componentes/footer.php';
?>

<script src="js/Datatables-Periodos.js"></script>
<script src="js/Consultas-Periodos.js"></script>
<script>
    function crearPeriodo() {
        if (!$("#formPeriodo").valid()) {
            console.log("El formulario no es válido.");
            return; 
        }
    
        const formData = new FormData(document.getElementById('formPeriodo'));
        console.log('Datos del formulario:', ...formData.entries());
    
        $.ajax({
            url: 'Periodos-Controlador.php?accion=crear',
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
    function GuardarPeriodo() {
        if (!$("#editForm").valid()) {
            console.log("El formulario no es válido.");
            return; 
        }
    
        const formData = new FormData(document.getElementById('editForm'));
        console.log('Datos del formulario:', ...formData.entries());
    
        $.ajax({
            url: 'Periodos-Controlador.php?accion=editar',
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
