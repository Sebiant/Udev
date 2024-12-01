$(document).ready(function() {
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
                defaultContent: '<button class="btn btn-danger w-100 btn-delete">Borrar</button>',
                orderable: false
            }
        ]
    });

    $('#datos_materia').on('click', 'btn-modify', function() {
        var data = table.row($(this).parents('tr')).data();
        var idMateria = data.id_materia;

        $.ajax({
            url: 'Materias-Controlador.php?accion=modificar',
            type: 'POST',
            data: { id_salon: idSalon },
            success: function(response) {
                var Materia = response.data[0];
                $('#editForm [name="id_materia"]').val(Materia.id_materia);
                $('#editForm [name="nombre"]').val(Materia.nombre);
                $('#editForm [name="descripcion"]').val(Materia.descripcion);
                $('#editForm [name="estado"]').prop('checked', Materia.estado === "Sí");
                $('#editModal').modal('show');
            },
            error: function() {
                alert('Error al obtener los datos del salón.');
            }
        });
    });

    $('#editForm').on('submit', function(e) {
        e.preventDefault();

        $.ajax({
            url: 'Materias-Controlador.php?accion=editar',
            type: 'POST',
            data: $(this).serialize(),
            success: function(response) {
                alert('Materia actualizada exitosamente.');
                table.ajax.reload();
                $('#editModal').modal('hide');
            },
            error: function() {
                alert('Error al actualizar el salón.');
            }
        });
    });
});
