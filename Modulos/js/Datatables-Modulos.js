$(document).ready(function() {
    var table = $('#datos_modulo').DataTable({
        "ajax": {
            "url": "Modulos-Controlador.php",
            "type": "GET",
            "data": { "accion": "default" },
            "dataSrc": "data"
        },
        "columns": [
            { "data": "fecha_inicio" },
            { "data": "fecha_fin" },
            { "data": "nombre_programa" },
            {
                "data": "id_modulo",
                "render": function(data) {
                    return `<button class="btn btn-primary w-100 btn-modify" onclick="editarModulo(${data})">Editar</button>`;
                }
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
        ],
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.11.5/i18n/es-ES.json"
        }
    });
});

$('#datos_modulo').on('click', '.btn-toggle-state', function () {
    var data = $('#datos_modulo').DataTable().row($(this).parents('tr')).data();
    var idModulo = data.id_modulo;
    var nuevoEstado = data.estado === "Activo" ? 0 : 1;

    $.ajax({
        url: 'Modulos-Controlador.php?accion=cambiarEstado',
        type: 'POST',
        data: { id_modulo: idModulo, estado: nuevoEstado },
        success: function (response) {
            alert(`El estado del módulo se ha actualizado a ${nuevoEstado === 1 ? "Activo" : "Inactivo"}.`);
            $('#datos_modulo').DataTable().ajax.reload();
        },
        error: function () {
            alert("Hubo un error al cambiar el estado.");
        }
    });
});

function editarModulo(id) {
    if (!id) {
        alert("ID no válido");
        return;
    }
    $.ajax({
        url: 'Modulos-Controlador.php?accion=BusquedaPorId',
        type: 'POST',
        data: {id_modulo: id },
        dataType: 'json',
        success: function(response) {
            if (response.data && response.data.length > 0) {
                var modulo = response.data[0];
                $('#editForm input[name="id_modulo"]').val(modulo.id_modulo);
                $('#editForm input[name="fecha_inicio"]').val(modulo.fecha_inicio);
                $('#editForm input[name="fecha_fin"]').val(modulo.fecha_fin);
                $('#editForm select[name="id_programa"]').val(modulo.id_programa);
                $('#editModuloModal').modal('show');
            } else {
                alert("No se encontraron datos para este módulo.");
            }
        },
        error: function() {
            alert("Hubo un error al obtener los datos del módulo.");
        }
    });
}

$('#editForm').on('submit', function(event) {
    event.preventDefault();
    $.ajax({
        url: 'Modulos-Controlador.php?accion=editar',
        type: 'POST',
        data: $(this).serialize(),
        success: function(response) {
            alert('Módulo actualizado exitosamente.');
            $('#editModuloModal').modal('hide');
            $('#datos_modulo').DataTable().ajax.reload();
        },
        error: function() {
            alert('Error al actualizar el módulo.');
        }
    });
});

function borrarModulo(id) {
    $.ajax({
        url: 'Modulos-Controlador.php?accion=eliminar',
        type: 'POST',
        data: { id_modulo: id },
        success: function(response) {
            alert('Módulo desactivado exitosamente.');
            $('#datos_modulo').DataTable().ajax.reload();
        },
        error: function() {
            alert('Error al eliminar el módulo.');
        }
    });
}

function crearModulo() {
    const datosFormulario = $('#formModulo').serialize();
    $.ajax({
        url: 'Modulos-Controlador.php?accion=crear', 
        type: 'POST',
        data: datosFormulario,
        success: function(response) {
            alert('Módulo creado exitosamente.');
            $('#modalModulos').modal('hide');
            $('#datos_modulo').DataTable().ajax.reload();
        },
        error: function() {
            alert('Error al crear el módulo.');
        }
    });
}
