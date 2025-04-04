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
                "data": "duracion_meses",
                "render": function(data) {
                    return data + " Meses";
                }
            },
            { "data": "valor_total_formateado" },
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
                console.log("Respuesta del servidor:", response);
            
                if (response.data && response.data.length > 0) {

                    console.log('Datos del programa:');
                    for (var i = 0; i < response.data.length; i++) {
                        console.log(response.data[i]);
                    }
    
                    var programa = response.data[0]; 
                    $('#editForm [name="id_programa"]').val(programa.id_programa);
                    $('#editForm [name="tipo"]').val(programa.tipo);
                    $('#editForm [name="nombre"]').val(programa.nombre);
                    $('#editForm [name="duracion_mes"]').val(programa.duracion_meses);
                    $('#editForm [name="valor_total_programa"]').val(programa.valor_total_programa);
                    $('#editForm [name="descripcion"]').val(programa.descripcion);
                    $('#editModal').modal('show');

                } else {
                    alert('No se encontraron datos para editar.');
                }
            },
            error: function (xhr, status, error) {
                console.error("Error al obtener los datos:", error);
                alert("Hubo un problema al intentar cargar los datos.");
            }            
        });
    });
});
