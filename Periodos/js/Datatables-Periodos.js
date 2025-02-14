$(document).ready(function() {
    var table = $('#datos_periodo').DataTable({
        "ajax": {
            "url": "Periodos-Controlador.php",
            "type": "GET",
            "data": { "accion": "default" },
            "dataSrc": "data"
        },
        "columns": [
            { "data": "nombre" },
            { "data": "fecha_inicio" },
            { "data": "fecha_fin" },
            { "data": "estado" },
            {
                "data": "id_periodo",
                "render": function(data) {
                    return `<button class="btn btn-primary w-100 btn-modify" onclick="editarPeriodo(${data})">Modificar</button>`;
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

$('#datos_periodo').on('click', '.btn-toggle-state', function () {
    var data = $('#datos_periodo').DataTable().row($(this).parents('tr')).data();
    var idPeriodos = data.id_periodo;
    var nuevoEstado = data.estado === "Activo" ? 0 : 1;

    $.ajax({
        url: 'Periodos-Controlador.php?accion=cambiarEstado',
        type: 'POST',
        data: { id_periodo: idPeriodos, estado: nuevoEstado },
        success: function (response) {
            $('#datos_periodo').DataTable().ajax.reload();
        },
        error: function () {
            alert("Hubo un error al cambiar el estado.");
        }
    });
});

function editarPeriodo(id) {
    if (!id) {
        alert("ID no válido");
        return;
    }
    $.ajax({
        url: 'Periodos-Controlador.php?accion=BusquedaPorId',
        type: 'POST',
        data: {id_periodo: id },
        dataType: 'json',
        success: function(response) {
            if (response.data && response.data.length > 0) {
                var modulo = response.data[0];
                $('#editForm input[name="id_periodo"]').val(modulo.id_periodo);
                $('#editForm input[name="nombre"]').val(modulo.nombre);
                $('#editForm input[name="fecha_inicio"]').val(modulo.fecha_inicio);
                $('#editForm input[name="fecha_fin"]').val(modulo.fecha_fin);
                $('#editPeriodoModal').modal('show');
            } else {
                alert("No se encontraron datos para este periódo.");
            }
        },
    });
}

$('#editForm').on('submit', function(event) {
    event.preventDefault();
    $.ajax({
        url: 'Periodos-Controlador.php?accion=editar',
        type: 'POST',
        data: $(this).serialize(),
        success: function(response) {
            $('#editPeriodoModal').modal('hide');
            $('#datos_periodo').DataTable().ajax.reload();
        },
        error: function() {
            alert('Error al actualizar el periódo.');
        }
    });
});




