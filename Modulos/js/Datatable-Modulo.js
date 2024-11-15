$(document).ready(function() {
    // Inicializar DataTable
    $('#datos_modulo').DataTable({
        "ajax": {
            "url": "modulo-controlador.php",
            "type": "GET",
            "data": { "accion": "default" },
            "dataSrc": "data"
        },
        "columns": [
            { "data": "id_modulo" },
            { "data": "fecha_inicio" },
            { "data": "fecha_fin" },
            { "data": "id_programa" },
            { "data": "estado" },
            {
                "data": "id_modulo",
                "render": function(data) {
                    return `<button class="btn btn-primary w-100 btn-modify" onclick="editarModulo(${data})">Editar</button>`;
                }
            },
            {
                "data": "id_modulo",
                "render": function(data) {
                    return `<button class="btn btn-danger w-100 btn-delete" onclick="borrarModulo(${data})">Borrar</button>`;
                }
            }
        ],
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.11.5/i18n/es-ES.json"
        }
    });
});

// Función para editar un módulo
function editarModulo(id) {
    if (confirm('¿Está seguro de que desea editar este módulo ' + id + '?')) {
        $.ajax({
            url: 'modulo-controlador.php', // URL del controlador
            type: 'POST', // Cambiado a POST
            data: { accion: 'obtener', id_modulo: id }, // Enviar el ID
            success: function(response) {
                if (response.data && response.data.length > 0) {
                    var modulo = response.data[0]; // Suponiendo que recibes un objeto con un array 'data'
                    $('#editForm input[name="id_modulo"]').val(modulo.id_modulo);
                    $('#editForm input[name="fecha_inicio"]').val(modulo.fecha_inicio);
                    $('#editForm input[name="fecha_final"]').val(modulo.fecha_fin);
                    $('#editForm select[name="estado"]').val(modulo.estado);
                    $('#editModuloModal').modal('show'); // Mostrar modal
                } else {
                    alert("No se encontraron datos para este módulo.");
                }
            },
            error: function() {
                alert("Hubo un error al obtener los datos del módulo.");
            }
        });
    }
}

// Manejar el envío del formulario de edición
$('#editForm').on('submit', function(event) {
    event.preventDefault();
    $.ajax({
        url: 'modulo-controlador.php?accion=editar',
        type: 'POST',
        data: $(this).serialize(),
        success: function(response) {
            alert('Módulo actualizado exitosamente.');
            $('#editModuloModal').modal('hide'); // Cerrar modal
            $('#datos_modulo').DataTable().ajax.reload(); // Recargar datos
        },
        error: function() {
            alert('Error al actualizar el módulo.');
        }
    });
});

// Función para borrar un módulo
function borrarModulo(id) {
    if (confirm('¿Está seguro de que desea eliminar este módulo?')) {
        $.ajax({
            url: 'modulo-controlador.php?accion=eliminar',
            type: 'POST',
            data: { id_modulo: id },
            success: function(response) {
                alert('Módulo eliminado exitosamente.');
                $('#datos_modulo').DataTable().ajax.reload(); // Recargar datos
            },
            error: function() {
                alert('Error al eliminar el módulo.');
            }
        });
    }
}

// Función para crear un módulo
function crearModulo() {
    const datosFormulario = $('#formModulo').serialize();
    $.ajax({
        url: 'modulo-controlador.php?accion=crear',
        type: 'POST',
        data: datosFormulario,
        success: function(response) {
            alert('Módulo creado exitosamente.');
            $('#modalModulos').modal('hide'); // Cerrar modal
            $('#datos_modulo').DataTable().ajax.reload(); // Recargar datos
        },
        error: function() {
            alert('Error al crear el módulo.');
        }
    });
}
