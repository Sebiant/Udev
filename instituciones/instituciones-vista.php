<?php
    include_once '../Componentes/header.php';
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
    include_once '../Componentes/footer.php';
?>
<script src="js/Validation-Instituciones.js"></script>
<script>
    $(document).ready(function() {
    var table = $('#datos_instituciones').DataTable({
        processing: true,
        serverSide: true,
        paging: true,
        lengthMenu: [10, 25, 50, 100],
        pageLength: 10,
        ordering: true,
        searching: true,
        language: {
            url: "//cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json"
        },
        ajax: {
            url: "Instituciones-Controlador.php",
            type: "POST",
            dataSrc: 'data'
        },
        columns: [
            { "data": "nombre" },
            { "data": "direccion" },
            { "data": "estado" },
            {
                data: null,
                defaultContent: '<button class="btn btn-primary w-100 btn-modify">Modificar</button>',
                orderable: false
            },
            {
                data: null,
                render: function (data, type, row) {
                    var buttonClass = row.estado === "Activo" ? "btn-danger" : "btn-success";
                    var buttonText = row.estado === "Activo" ? "Inactivar" : "Activar";
                    return `<button class="btn ${buttonClass} w-100 btn-toggle-state">${buttonText}</button>`;
                },
                orderable: false
            }
        ]
    });

    $('#datos_instituciones').on('click', '.btn-toggle-state', function () {
        var data = table.row($(this).parents('tr')).data();
        var idInstitucion = data.id_institucion;
        var nuevoEstado = data.estado === "Activo" ? 0 : 1;

        $.ajax({
            url: 'Instituciones-Controlador.php?accion=cambiarEstado',
            type: 'POST',
            data: { id_institucion: idInstitucion, estado: nuevoEstado },
            success: function(response) {
                table.ajax.reload();
            },
            error: function() {
                alert("Hubo un error al cambiar el estado.");
            }
        });
    });

    $('#datos_instituciones').on('click', '.btn-modify', function() {
        var data = table.row($(this).parents('tr')).data();
        var idInstitucion = data.id_institucion;

        $.ajax({
            url: 'Instituciones-Controlador.php?accion=buscarPorId',
            type: 'POST',
            data: { id_institucion: idInstitucion },
            dataType: 'json',
            success: function(response) {
                var institucion = response.data[0];
                $('#editForm [name="id_institucion"]').val(institucion.id_institucion);
                $('#editForm [name="nombre"]').val(institucion.nombre);
                $('#editForm [name="direccion"]').val(institucion.direccion);
                $('#editForm [name="estado"]').prop('checked', institucion.estado === "Activo");
                $('#editModal').modal('show');
            },
            error: function() {
                alert('Error al obtener los datos de la institución.');
            }
        });
    });

    $('#editForm').on('submit', function(e) {
        e.preventDefault();

        $.ajax({
            url: 'Instituciones-Controlador.php?accion=editar',
            type: 'POST',
            data: $(this).serialize(),
            success: function(response) {
                table.ajax.reload();
                $('#editModal').modal('hide');
            },
            error: function() {
                alert('Error al actualizar la institución.');
            }
        });
    });

});
</script>
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
            url: 'Instituciones-Controlador.php?accion=editar',
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