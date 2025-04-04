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
            url: "instituciones-controlador.php",
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
            url: 'instituciones-controlador.php?accion=cambiarEstado',
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
            url: 'instituciones-controlador.php?accion=buscarPorId',
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
            url: 'instituciones-controlador.php?accion=editar',
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