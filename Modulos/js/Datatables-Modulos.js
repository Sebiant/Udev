$(document).ready(function () {
    var table = $('#datos_modulo').DataTable({
        language: {
            url: "//cdn.datatables.net/plug-ins/1.11.5/i18n/es-ES.json"
        },
        searching: true,
        paging: true,
        lengthChange: true,
        pageLength: 10,
        processing: true,
        serverSide: true,
        ajax: {
            url: "Modulos-Controlador.php",
            type: "POST",
            dataSrc: 'data'
        },
        columns: [
            { "data": "tipo", "title": "Tipo" },
            { "data": "nombre", "title": "Nombre" },
            { "data": "programa", "title": "Programa" },
            { "data": "descripcion", "title": "Descripción" },
            { "data": "estado", "title": "Estado" },
            {
                data: null,
                defaultContent: '<button class="btn btn-primary w-100 btn-modify">Modificar</button>',
                orderable: false
            },
            {
                data: null,
                render: function (data, type, row) {
                    var buttonClass = row.estado == "Activo" ? "btn-danger" : "btn-success";
                    var buttonText = row.estado == "Activo" ? "Inactivar" : "Activar";
                    return `<button class="btn ${buttonClass} w-100 btn-toggle-state">${buttonText}</button>`;
                },
                orderable: false
            }
        ]
    });

    $('#datos_modulo').on('click', '.btn-toggle-state', function () {
        var data = table.row($(this).parents('tr')).data();
        var idModulo = data.id_modulo;
        var nuevoEstado = data.estado == "Activo" ? 0 : 1;
        console.log(idModulo)
        console.log(nuevoEstado)

        $.ajax({
            url: 'Modulos-Controlador.php?accion=cambiarEstado',
            type: 'POST',
            data: { id_modulo: idModulo, estado: nuevoEstado },
            success: function (response) {
                table.ajax.reload();
            },
            error: function () {
                alert("Hubo un error al cambiar el estado.");
            }
        });
    });

    $('#datos_modulo').on('click', '.btn-modify', function () {
        var data = table.row($(this).parents('tr')).data();
        var idModulo = data.id_modulo;

        $.ajax({
            url: 'Modulos-Controlador.php?accion=busquedaPorId',
            type: 'POST',
            data: { id_modulo: idModulo },
            dataType: 'json',
            success: function (response) {
                if (response.data && response.data.length > 0) {
                    var modulo = response.data[0];
                    $('#editForm [name="id_modulo"]').val(modulo.id_modulo);
                    $('#editForm [name="tipo"]').val(modulo.tipo);
                    $('#editForm [name="nombre"]').val(modulo.nombre);
                    $('#editForm [name="id_programa"]').val(modulo.id_programa);
                    $('#editForm [name="descripcion"]').val(modulo.descripcion);
                    $('#editForm [name="estado"]').prop('checked', modulo.estado == 1);
                    $('#editModal').modal('show');
                } else {
                    alert('No se encontraron datos para este módulo.');
                }
            }
        });
    });

    $('#editForm').on('submit', function (e) {
        e.preventDefault();

        $.ajax({
            url: 'Modulos-Controlador.php?accion=editar',
            type: 'POST',
            data: $(this).serialize(),
            success: function (response) {
                table.ajax.reload();
                $('#editModal').modal('hide');
            },
            error: function () {
                alert('Error al actualizar el módulo.');
            }
        });
    });
});
