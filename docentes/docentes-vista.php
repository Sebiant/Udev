<?php
include_once '../componentes/header.php';
?>
<div class="container">
    <h1 class="text-center">Gestion Docentes</h1>

    <div class="row">
        <div class="col-2 offset-10">
            <div class="text-center">
                <!-- Button trigger modal -->
                <button type="button" class="btn btn-primary w-100" data-bs-toggle="modal" data-bs-target="#modalDocentes" id="botonCrear">
                    <i class="bi bi-plus-circle"></i> Crear
                </button>
            </div>
        </div>
    </div>
    <br />
    <br />
    <div class="card">
        <div class="card-header">
            <h5>Docentes</h5>
        </div>
         <div class="table-responsive card-body">
            <table id="datos_docente" class="table table-bordered table-striped">
                 <thead>
                    <tr>
                        <th>Documento</th>
                        <th>Docente</th>
                        <th>Especialidad</th>
                        <th>Teléfono</th>
                        <th>Dirección</th>
                        <th>Email</th>
                        <th>Estado</th>
                        <th>Modificar</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
            </table>
        </div>        
    </div>

<!-- Modal para crear docente -->
<div class="modal fade" id="modalDocentes" tabindex="-1" aria-labelledby="modalDocentesLabel">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalDocentesLabel">Agregar Docente</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="formDocente">
                        <input type="hidden" name="accion" value="crear" id="accion">
                        <input type="hidden" name="numero_documento" id="numero_documento">

                        <div class="mb-3">
                            <label for="tipo_documento" class="form-label">Tipo de Documento:</label>
                            <select name="tipo_documento" id="tipo_documento" class="form-control">
                                <option value="">-- Selecciona Tipo de Documento --</option>
                                <option value="cedula_ciudadania">Cédula de ciudadanía</option>
                                <option value="cedula_extranjeria">Cédula de Extranjería</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="numero_documento">Número de Documento:</label>
                            <input type="text" name="numero_documento" id="numero_documento" class="form-control" placeholder="Número de documento">
                        </div>

                        <div class="mb-3">
                            <label for="nombres">Nombres:</label>
                            <input type="text" name="nombres" id="nombres" class="form-control" placeholder="Nombres">
                        </div>
                        <div class="mb-3">
                            <label for="apellidos">Apellidos:</label>
                            <input type="text" name="apellidos" id="apellidos" class="form-control" placeholder="Apellidos">
                        </div>
                        <div class="mb-3">
                            <label for="perfil">Especialidad:</label>
                            <input type="text" name="perfil_profesional" id="perfil_profesional" class="form-control" placeholder="Especialidad">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Seleccione materias a dictar:</label>
                            <div class="d-flex flex-wrap gap-2" id="materiasContainer" aria-label="Materias">
                                <!-- Los botones dinámicos se agregarán aquí -->
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="telefono">Teléfono:</label>
                            <input type="text" name="telefono" id="telefono" class="form-control" maxlength="10" pattern="\d{10}" placeholder="Teléfono">
                        </div>
                        <div class="mb-3">
                            <label for="direccion" class="form-label">Dirección:</label>
                            <input type="text" name="direccion" id="direccion" class="form-control" placeholder="Direccion">
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email:</label>
                            <input type="email" name="email" id="email" class="form-control" placeholder="Email">
                        </div>
                        <div class="form-check mb-3">
                            <input type="checkbox" class="form-check-input" name="declara_renta" id="declara_renta">
                            <label class="form-check-label" for="declara_renta">Declara Renta</label>
                        </div>
                        <div class="form-check mb-3">
                            <input type="checkbox" class="form-check-input" name="retenedor_iva" id="retenedor_iva">
                            <label class="form-check-label" for="retenedor_iva">Retenedor IVA</label>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-success" onclick="crearDocente()">Guardar</button>
                        </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal de edición -->
<div id="editModal" class="modal fade" tabindex="-1" aria-labelledby="editModalLabel">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editModalLabel">Editar Docente</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editForm">
                <input type="hidden" name="documento_anterior" id="documento_anterior">

                    <div class="mb-3">
                        <label for="tipo_documento_editar" class="form-label">Tipo de Documento:</label>
                        <select name="tipo_documento" id="tipo_documento_editar" class="form-control">
                            <option value="">-- Selecciona Tipo de Documento --</option>
                            <option value="cedula_ciudadania">Cédula de ciudadanía</option>
                            <option value="cedula_extranjeria">Cédula de Extranjería</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="numero_documento">Número de Documento:</label>
                        <input type="text" name="numero_documento" class="form-control" placeholder="Número de documento">
                    </div>

                    <div class="mb-3">
                        <label for="nombres">Nombres:</label>
                        <input type="text" name="nombres" class="form-control" placeholder="Nombres">
                    </div>
                    <div class="mb-3">
                        <label for="apellidos">Apellidos:</label>
                        <input type="text" name="apellidos" class="form-control" placeholder="Apellidos">
                    </div>
                    <div class="mb-3">
                            <label for="perfil">Especialidad:</label>
                            <input type="text" name="perfil_profesional" id="perfil_profesional" class="form-control" placeholder="Especialidad">
                        </div>
                    <div class="mb-3">
                        <label class="form-label">Seleccione materias a dictar:</label>
                        <div class="d-flex flex-wrap gap-2" id="materiasContainerEditar" aria-label="Materias">
                            <!-- Aquí van las materias para editar -->
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="telefono">Teléfono:</label>
                        <input type="text" name="telefono" class="form-control" maxlength="10" pattern="\d{10}" placeholder="Teléfono">
                    </div>
                    <div class="mb-3">
                        <label for="direccion" class="form-label">Dirección:</label>
                        <input type="text" name="direccion" class="form-control" placeholder="Direccion">
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email:</label>
                        <input type="email" name="correo" class="form-control" pattern="^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$" placeholder="Email">
                    </div>                    
                        <div class="form-check mb-3">
                        <input type="checkbox" class="form-check-input" id="declara_renta_editar" name="declara_renta">
                        <label class="form-check-label" for="declara_renta_editar">Declara Renta</label>
                    </div>
                    <div class="form-check mb-3">
                        <input type="checkbox" class="form-check-input" id="retenedor_iva_editar" name="retenedor_iva">
                        <label class="form-check-label" for="retenedor_iva_editar">Retenedor IVA</label>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-success" onclick="guardarCambiosDocente()">Guardar Cambios</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php
include_once '../componentes/footer.php';
?>
<script src="js/Validation-Docentes.js"></script>
<script src="js/Datatable-Docentes.js"></script>
<script>
    function crearDocente() {
        if (!$("#formDocente").valid()) {
            console.log("El formulario no es válido.");
            return;
        }

        const formData = new FormData(document.getElementById('formDocente'));
        console.log('Datos del formulario:', ...formData.entries());

        $.ajax({
            url: 'Docentes-Controlador.php?accion=crear',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                console.log('Respuesta del servidor:', response);
                try {
                    // Si la respuesta es un JSON, asegúrate de parsearlo
                    var data = typeof response === 'string' ? JSON.parse(response) : response;

                    if (data.status === "error") {
                        alert(data.message); // Muestra el mensaje de error

                        // Si hay un error específico, añade la clase 'is-invalid' y muestra el mensaje de error
                        if (data.message.includes("documento ya está registrado")) {
                            $("#numero_documento").addClass("is-invalid");
                            $("#error-documento").text(data.message).show();
                        } 
                        else if (data.message.includes("teléfono ya está registrado")) {
                            $("#telefono").addClass("is-invalid");
                            $("#error-telefono").text(data.message).show();
                        }

                    } else {
                        alert(data.message); // Muestra el mensaje de éxito
                        $("#formDocente")[0].reset();
                        $("#modalUsuario").modal("hide");
                        location.reload();
                    }
                } catch (e) {
                    console.error("Error en el parseo de JSON:", e);
                    alert("Error al procesar la respuesta del servidor.");
                }
            },
            error: function(xhr, status, error) {
                console.error('Error en la petición AJAX:', error);
                alert("Hubo un error al procesar la solicitud.");
            }
        });
    }
</script>
<script>
    function guardarCambiosDocente() {
        if (!$("#editForm").valid()) {
            console.log("El formulario no es válido.");
            return;
        }

        const formData = new FormData(document.getElementById('editForm'));
        console.log('Datos del formulario:', ...formData.entries());

        $.ajax({
            url: 'Docentes-Controlador.php?accion=Modificar',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                console.log('Respuesta del servidor:', response);

                // Mostrar la respuesta del servidor en un alert
                alert(response.message);  // Suponiendo que el servidor responde con un objeto que contiene una propiedad 'message'

                location.reload();
            },
            error: function(xhr, status, error) {
                console.error('Error:', error);
            }
        });
    }
</script>

<script>
$(document).ready(function () {
  $.ajax({
    url: "Docentes-Controlador.php?accion=traerMaterias",
    method: "GET",
    dataType: "json",
    success: function (materias) {
      materias.forEach(function (materia) {
        const checkboxId = `materia_${materia.id_modulo}`;

        const checkbox = `<input type="checkbox" class="btn-check" id="${checkboxId}" name="id_modulo[]" value="${materia.id_modulo}" autocomplete="off">`;

        const label = `<label class="btn btn-outline-primary" for="${checkboxId}">${materia.nombre}</label>`;

        $("#materiasContainer").append(checkbox + label);
      });
    },
    error: function () {
      $("#materiasContainer").html("<p class='text-danger'>No se pudieron cargar las materias.</p>");
    }
  });
});
</script>