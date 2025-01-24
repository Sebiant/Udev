$(document).ready(function () {
    var table = $('#datos_programa').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "Programas-Controlador.php",
            type: "POST",
            dataSrc: 'data'
        },
        columns: [
            { "data": "tipo" },
            { "data": "nombre" },
            {
                "data": "duracion_mes",
                "render": function(data) {
                    return data + " Meses";
                }
            },
            { "data": "cant_modulos" },
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
        ],
        language: {
            url: "//cdn.datatables.net/plug-ins/1.11.5/i18n/es-ES.json"
        },
        searching: true,
        paging: true,
        lengthChange: true,
        pageLength: 10,
    });

    $('#datos_programa').on('click', '.btn-toggle-state', function () {
        var data = table.row($(this).parents('tr')).data();
        var idPrograma = data.id_programa;
        var nuevoEstado = data.estado === "Activo" ? 0 : 1;

        $.ajax({
            url: 'Programas-Controlador.php?accion=cambiarEstado',
            type: 'POST',
            data: { id_programa: idPrograma, estado: nuevoEstado },
            success: function (response) {
                alert(`El estado del programa se ha actualizado a ${nuevoEstado === 1 ? "Activo" : "Inactivo"}.`);
                table.ajax.reload();
            },
            error: function () {
                alert("Hubo un error al cambiar el estado.");
            }
        });
    });

    $('#datos_programa').on('click', '.btn-modify', function () {
        var data = table.row($(this).parents('tr')).data();
        var idPrograma = data.id_programa;

        $.ajax({
            url: 'Programas-Controlador.php?accion=BusquedaPorId',
            type: 'POST',
            data: { id_programa: idPrograma },
            dataType: 'json',
            success: function (response) {
                if (response.data && response.data.length > 0) {
                    var programa = response.data[0];

                    $('#editForm [name="id_programa"]').val(programa.id_programa);
                    $('#editForm [name="tipo"]').val(programa.tipo);
                    $('#editForm [name="nombre"]').val(programa.nombre);
                    $('#editForm [name="duracion_mes"]').val(programa.duracion_mes);
                    $('#editForm [name="cant_modulos"]').val(programa.cant_modulos);
                    $('#editForm [name="descripcion"]').val(programa.descripcion);

                    $('#editModal').modal('show');
                } else {
                    alert('No se encontraron datos para editar.');
                }
            },
            error: function () {
                alert('Error al obtener los datos.');
            }
        });
    });

    $('#editForm').on('submit', function (event) {
        event.preventDefault();

        const formData = $(this).serialize(); 
        
        $.ajax({
            url: 'Programas-Controlador.php?accion=editar',
            type: 'POST',
            data: formData,
            dataType: 'json',
            success: function (response) {  
                if (response.success) {
                    alert('Programa actualizado correctamente.');
                    $('#editModal').modal('hide');
                    table.ajax.reload();
                } else {
                    alert(response.message || 'Error al actualizar el programa.');
                }
            },
            error: function (xhr, status, error) {
                console.error('Error en la solicitud:', error);
                alert('Ocurrió un error al realizar la solicitud. Intenta nuevamente.');
            }
        });
    });
});
