$(document).ready(function() {
    var table = $('#datos_salones').DataTable({
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
                    return row.nombre || "Institución no encontrada";
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
                    if (row.estado_institucion == 0) {
                        return `<button class="btn btn-secondary w-100" disabled>Institucion inactiva</button>`;
                    }

                    var buttonClass = row.estado === "Activo" ? "btn-danger" : "btn-success";
                    var buttonText = row.estado === "Activo" ? "Inactivar" : "Activar";
                    return `<button class="btn ${buttonClass} w-100 btn-cambiar-estado">${buttonText}</button>`;
                },
                orderable: false
            }
        ]
    });

    // Cambiar el estado del salón
    $('#datos_salones').on('click', '.btn-cambiar-estado', function () {
        var data = table.row($(this).parents('tr')).data();
        var idSalon = data.id_salon;
        var nuevoEstado = data.estado === "Activo" ? 0 : 1;

        $.ajax({
            url: 'Salones-Controlador.php?accion=cambiarEstado',
            type: 'POST',
            data: { id_salon: idSalon, estado: nuevoEstado },
            success: function () {
                table.ajax.reload();
            },
            error: function () {
                alert("Hubo un error al cambiar el estado del salón.");
            }
        });
    });

    // Modificar un salón
    $('#datos_salones').on('click', '.btn-modificar', function() {
        var data = table.row($(this).parents('tr')).data();
        var idSalon = data.id_salon;

        $.ajax({
            url: 'Salones-Controlador.php?accion=buscarPorId',
            type: 'POST',
            data: { id_salon: idSalon },
            dataType: 'json',
            success: function(response) {
                if (response && response.data && Array.isArray(response.data) && response.data.length > 0) {
                    var salon = response.data[0];
                    $('#editForm [name="id_salon"]').val(salon.id_salon);
                    $('#editForm [name="nombre_salon"]').val(salon.nombre_salon);
                    $('#editForm [name="capacidad"]').val(salon.capacidad);
                    $('#editForm [name="descripcion"]').val(salon.descripcion);
                    $('#editForm [name="id_institucion"]').val(salon.id_institucion);
                    $('#editForm [name="estado"]').prop('checked', salon.estado === "Activo");
                    $('#editModal').modal('show');
                } else {
                    alert("No se encontró el salón.");
                }
            },            
            error: function(xhr, status, error) {
                console.log('Status: ' + status);
                console.log('Error: ' + error);
                console.log('Response Text: ' + xhr.responseText);
                alert('Error al obtener los datos del salón.');
            }
            
        });
    });
});
