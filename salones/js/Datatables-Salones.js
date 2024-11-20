$(document).ready(function() {
    var table = $('#datos_salones').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "salones-controlador.php",
            type: "POST",
            dataSrc: 'data'
        },
        columns: [
            { "data": "id_salon" },
            { "data": "nombre_salon" },
            { "data": "cantidad" },
            { "data": "descripcion" },
            { "data": "id_institucion" },
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
    
    $('#datos_salones').on('click', '.btn-modify', function() {
        var data = table.row($(this).parents('tr')).data();
        var idSalon = data.id_salon;

        $.ajax({
            url: 'salones-controlador.php?accion=modificar',
            type: 'POST',
            data: { id_salon: idSalon },
            success: function(response) {
                var Salon = response.data[0];
                $('#editForm [name="id_salon"]').val(Salon.id_salon);
                $('#editForm [name="nombre_salon"]').val(Salon.nombre_salon);
                $('#editForm [name="cantidad"]').val(Salon.cantidad);
                $('#editForm [name="descripcion"]').val(Salon.descripcion);
                $('#editForm [name="id_institucion"]').val(Salon.id_institucion);
                $('#editForm [name="estado"]').prop('checked', Salon.estado === "Sí");
                $('#editModal').modal('show');
            },
            error: function() {
                alert('Error al obtener los datos del salon.');
            }
        });
    });
    $('#editForm').on('submit', function(e) {
        e.preventDefault();

        $.ajax({
            url: 'salones-controlador.php?accion=editar',
            type: 'POST',
            data: $(this).serialize(),
            success: function(response) {
                alert('Salon actualizado exitosamente.');
                table.ajax.reload();
                $('#editModal').modal('hide');
            },
            error: function() {
                alert('Error al actualizar el salon.');
            }
        });
    });


    $('#datos_salones').on('click', '.btn-delete', function() {
        var data = table.row($(this).parents('tr')).data();
        var idSalon = data.id_salon;

        if (confirm('¿Estás seguro de que quieres desactivar el salon?')) {
            $.ajax({
                url: 'salones-controlador.php?accion=eliminar',
                type: 'POST',
                data: { id_salon: idSalon },
                success: function(response) {
                    table.ajax.reload();
                    alert('Salon desactivado exitosamente.');
                },
                error: function() {
                    alert('Error al desactivar el salon.');
                }
            });
        }
    });
});
