$(document).ready(function () {
    var table = $('#datos_materia').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "Materias-Controlador.php",
            type: "POST",
            dataSrc: 'data'
        },
        columns: [
            { "data": "id_materia" },
            { "data": "nombre" },
            { "data": "descripcion" },
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

    $('#datos_materia').on('click', '.btn-toggle-state', function () {
        var data = table.row($(this).parents('tr')).data();
        var idMateria = data.id_materia;
        var nuevoEstado = data.estado === "Activo" ? 0 : 1; 

        $.ajax({
            url: 'Materias-Controlador.php?accion=cambiarEstado',
            type: 'POST',
            data: { id_materia: idMateria, estado: nuevoEstado },
            success: function (response) {
                alert(`El estado de la materia se ha actualizado a ${nuevoEstado === 1 ? "Activo" : "Inactivo"}.`);
                table.ajax.reload();
            },
            error: function () {
                alert("Hubo un error al cambiar el estado.");
            }
        });
    });

    $('#datos_materia').on('click', '.btn-modify', function () {
        var data = table.row($(this).parents('tr')).data();
        var idMateria = data.id_materia;

        $.ajax({
            url: 'Materias-Controlador.php?accion=busquedaPorId',
            type: 'POST',
            data: { id_materia: idMateria },
            dataType: 'json',
            success: function (response) {
                if (response.data && response.data.length > 0) {
                    var materia = response.data[0];
                    $('#editForm [name="id_materia"]').val(materia.id_materia);
                    $('#editForm [name="nombre"]').val(materia.nombre);
                    $('#editForm [name="descripcion"]').val(materia.descripcion);
                    $('#editForm [name="estado"]').prop('checked', materia.estado === "1");
                    $('#editModal').modal('show');
                } else {
                    alert('No se encontraron datos para esta materia.');
                }
            }
        });
    });

    $('#editForm').on('submit', function (e) {
        e.preventDefault();

        $.ajax({
            url: 'Materias-Controlador.php?accion=editar',
            type: 'POST',
            data: $(this).serialize(),
            success: function (response) {
                alert('Materia actualizada exitosamente.');
                table.ajax.reload();
                $('#editModal').modal('hide');
            },
            error: function () {
                alert('Error al actualizar la materia.');
            }
        });
    });
});
