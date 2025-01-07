<?php
    include_once '../componentes/header.php';
    ?>
<div class="container">
        <h1 class="text-center">Materias</h1>

        <div class="row">
            <div class="col-2 offset-10">
                <div class="text-center">
                    <!-- Button trigger modal -->
                        <button type="button" class="btn btn-primary w-100 " data-bs-toggle="modal" data-bs-target="#modalMateria" id="botonCrear">
                            <i class="bi bi-plus-circle"></i> Crear
                        </button>
                </div>

            </div>
        </div>
        <br />
        <br />
\
        <div class="table-responsive">
            <table id="datos_materia" class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Id</th>
                        <th>Nombre</th>
                        <th>Descripcion</th>
                        <th>estado</th>
                        <th>Modificar</th>
                        <th>Borrar</th>
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
            <input type="text" name="nombre" id="nombre" class="form-control" required>
          </div>
          <div class="mb-3">
            <label for="descripcion" class="form-label">Descripcion de la materia:</label>
            <input type="text" name="descripcion" id="descripcion" class="form-control" required>
          </div>
        </form>

      </div>
        </div>
        <div class="modal-footer">
            <input type="hidden" name="id_materia" id="id_materia">
            <input type="hidden" name="operacion" id="operacion">
            <button type="submit" class="btn btn-success" onclick="crearMateria()">Guardar</button>
        </div>
        </div>
        </div>
    </div>
    </div>

    <!-- Modal de edición -->
    <div id="editModal" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Editar materia</h5>
                </div>
                <form id="editForm">
                    <div class="modal-body">
                        <input type="hidden" name="id_materia">
                        <div class="form-group">
                            <label for="tipo_documento">Nombre de la materia</label>
                            <input type="text" class="form-control" name="tipo_documento">
                        </div>
                        <div class="form-group">
                            <label for="numero_documento">Descripcion de la materia</label>
                            <input type="text" class="form-control" name="numero_documento">
                        </div>
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
    <script src="js/Consultas-Materias.js"></script>
    <script src="js/Datatables-Materias.js"></script>

    <script>
    function crearMateria() {
    // Validar el formulario antes de enviar
    const form = document.getElementById('formMateria');
    
    if (form.checkValidity()) {
        // Si el formulario es válido, proceder a enviar los datos
        $('#modalMaterias').modal('hide'); // Cierra el modal antes de enviar
        // Aquí puedes agregar tu lógica para enviar el formulario, por ejemplo, usando AJAX
        $.ajax({
            url: 'Materias-Controlador.php',
            type: 'POST',
            data: $(form).serialize(), // Serializa el formulario
            success: function(response) {
                // Manejar la respuesta del servidor
                alert('Módulo creado exitosamente.');
            },
            error: function() {
                alert('Hubo un error al crear el módulo.');
            }
        });
    } else {
        // Si el formulario no es válido, mostrar los mensajes de error
        form.classList.add('was-validated');
    }
}
</script>
