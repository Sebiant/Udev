$(document).ready(function() {
    var table = $('#datos_salones').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "Salones-Controlador.php",
            type: "POST",
            dataSrc: 'data'
        },
        columns: [
            { "data": "nombre_salon" },
            { "data": "capacidad" },
            { "data": "descripcion" },
            { 
                "data": "id_institucion",
                render: function(data, type, row) {
                    return row.nombre || "Institución no encontrada"; // Muestra el nombre de la institución directamente
                }
            },
            { "data": "estado" },
            {
                data: null,
                defaultContent: '<button class="btn btn-primary w-100 btn-modificar">Modificar</button>',
                orderable: false
            },
            {
                data: null,
                render: function (data, type, row) {
                    var buttonClass = row.estado === "Activo" ? "btn-danger" : "btn-success";
                    var buttonText = row.estado === "Activo" ? "Inactivar" : "Activar";
                    return `<button class="btn ${buttonClass} w-100 btn-cambiar-estado">${buttonText}</button>`;
                },
                orderable: false
            }
        ]
    });

    // Evento para cambiar el estado del salón (activar/inactivar)
    $('#datos_salones').on('click', '.btn-cambiar-estado', function () {
        var data = table.row($(this).parents('tr')).data();
        var idSalon = data.id_salon;
        var nuevoEstado = data.estado === "Activo" ? 0 : 1;

        $.ajax({
            url: 'Salones-Controlador.php?accion=cambiarEstado',
            type: 'POST',
            data: { id_salon: idSalon, estado: nuevoEstado },
            success: function (response) {
                alert(`El estado del salón ha sido actualizado a ${nuevoEstado === 1 ? "Activo" : "Inactivo"}.`);
                table.ajax.reload();
            },
            error: function () {
                alert("Hubo un error al cambiar el estado del salón.");
            }
        });
    });

    // Evento para obtener los datos de un salón y mostrarlos en el formulario de edición
    $('#datos_salones').on('click', '.btn-modificar', function() {
        var data = table.row($(this).parents('tr')).data();
        var idSalon = data.id_salon;

        $.ajax({
            url: 'Salones-Controlador.php?accion=modificar',
            type: 'POST',
            data: { id_salon: idSalon },
            success: function(response) {
                var salon = response.data[0];
                $('#editForm [name="id_salon"]').val(salon.id_salon);
                $('#editForm [name="nombre_salon"]').val(salon.nombre_salon);
                $('#editForm [name="capacidad"]').val(salon.capacidad);
                $('#editForm [name="descripcion"]').val(salon.descripcion);
                $('#editForm [name="id_institucion"]').val(salon.id_institucion);
                $('#editForm [name="estado"]').prop('checked', salon.estado === "Activo");
                $('#editModal').modal('show');
            },
            error: function() {
                alert('Error al obtener los datos del salón.');
            }
        });
    });

    // Evento para guardar la edición del salón
    $('#editForm').on('submit', function(e) {
        e.preventDefault();

        $.ajax({
            url: 'Salones-Controlador.php?accion=editar',
            type: 'POST',
            data: $(this).serialize(),
            success: function(response) {
                alert('Salón actualizado exitosamente.');
                table.ajax.reload();
                $('#editModal').modal('hide');
            },
            error: function() {
                alert('Error al actualizar el salón.');
            }
        });
    });
});
