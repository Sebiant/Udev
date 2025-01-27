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
            { "data": "estado" },
            {
                "data": "id_modulo",
                "render": function(data) {
                    return `<button class="btn btn-primary w-100 btn-modify" onclick="editarModulo(${data})">Modificar</button>`;
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
        },
        "searching": true,
        "paging": true,
        "lengthChange": true,
        "pageLength": 10,
        "processing": true,
        "serverSide": true
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
    });
}

$('#editForm').on('submit', function(event) {
    event.preventDefault();
    $.ajax({
        url: 'Modulos-Controlador.php?accion=editar',
        type: 'POST',
        data: $(this).serialize(),
        success: function(response) {
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
            $('#modalModulos').modal('hide');
            $('#datos_modulo').DataTable().ajax.reload();
        },
        error: function() {
            alert('Error al crear el módulo.');
        }
    });
}
