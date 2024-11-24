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
            { "data": "id_salon" },
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
                defaultContent: '<button class="btn btn-primary w-100 btn-modify">Modificar</button>',
                orderable: false
            },
            {
                data: null,
                render: console.log(row.estado), function(data, type, row) {
                    // Comprobar el estado del salón para determinar el texto del botón
                    let buttonText = row.estado === 'Sí' ? 'Desactivar' : 'Activar';
                    let statusValue = row.estado === 'Sí' ? 1 : 0; // 1 para activo y 0 para inactivo
                    return `<button class="btn btn-warning w-100 toggle-btn" data-status="${statusValue}">${buttonText}</button>`;
                    
                },
                orderable: false
            }
        ]
    });

    $('#datos_salones').on('click', '.btn-modify', function() {
        var data = table.row($(this).parents('tr')).data();
        var idSalon = data.id_salon;

        $.ajax({
            url: 'Salones-Controlador.php?accion=modificar',
            type: 'POST',
            data: { id_salon: idSalon },
            success: function(response) {
                var Salon = response.data[0];
                $('#editForm [name="id_salon"]').val(Salon.id_salon);
                $('#editForm [name="nombre_salon"]').val(Salon.nombre_salon);
                $('#editForm [name="capacidad"]').val(Salon.capacidad);
                $('#editForm [name="descripcion"]').val(Salon.descripcion);
                $('#editForm [name="id_institucion"]').val(Salon.id_institucion);
                $('#editForm [name="estado"]').prop('checked', Salon.estado === "Sí");
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

    $('#datos_salones').on('click', '.toggle-btn', function() {
        var btn = $(this);
        var status = btn.data('status');

        var data = table.row(btn.parents('tr')).data();
        
        if (status === 0) { // Si está inactivo (estado 'No')
            btn.text('Desactivar');
            btn.data('status', 1);

            $.ajax({
                url: 'Salones-Controlador.php?accion=activar',
                type: 'POST',
                data: { id_salon: data.id_salon, estado: 1 }, // Enviar estado activado
                success: function(response) {
                    alert('Salón activado exitosamente.');
                    table.ajax.reload(); // Recargar tabla para reflejar cambios
                    console.log(data);
                }
            });
        } else { // Si está activo (estado 'Sí')
            btn.text('Activar');
            btn.data('status', 0);

            $.ajax({
                url: 'Salones-Controlador.php?accion=desactivar',
                type: 'POST',
                data: { id_salon: data.id_salon, estado: 0 }, // Enviar estado desactivado
                success: function(response) {
                    alert('Salón desactivado exitosamente.');
                    table.ajax.reload(); // Recargar tabla para reflejar cambios
                    console.log(data);
                }
            });
        }
    });
});