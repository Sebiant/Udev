$(document).ready(function() {
    var table = $('#datos_docente').DataTable({
        language: {
            url: "//cdn.datatables.net/plug-ins/1.11.5/i18n/es-ES.json"
        },
        "searching": true,
        "paging": true,
        "lengthChange": true,
        "pageLength": 10,
        "processing": true,
        "serverSide": true,
        "ajax": {
            url: "Docentes-Controlador.php",
            type: "POST",
            data: function(d) {
                d.page = d.start / d.length + 1;
                d.pageSize = d.length;
                d.searchTerm = d.search.value;
            },
            dataSrc: 'data'
        },
        columns: [
            { "data": "numero_documento" },
            { "data": "nombre_completo" },
            { "data": "perfil_profesional" },
            { "data": "telefono" },
            { "data": "direccion" },
            { "data": "email" },
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

    $('#datos_docente').on('click', '.btn-toggle-state', function () {
        var data = table.row($(this).parents('tr')).data();
        var numeroDocumento = data.numero_documento;
        var nuevoEstado = data.estado === "Activo" ? 0 : 1;

        $.ajax({
            url: 'Docentes-Controlador.php?accion=cambiarEstado',
            type: 'POST',
            data: { numero_documento: numeroDocumento, estado: nuevoEstado },
            success: function () {
                table.ajax.reload();
            },
            error: function () {
                alert("Hubo un error al cambiar el estado.");
            }
        });
    });

    $('#datos_docente').on('click', '.btn-modify', function() {
        var data = table.row($(this).parents('tr')).data();
        var numeroDocumento = data.numero_documento; // Cambio aquí
    
        $.ajax({
            url: 'Docentes-Controlador.php?accion=buscarPorId',
            type: 'POST',
            data: { numero_documento: numeroDocumento }, // Cambio aquí
            dataType: 'json',
            success: function(response) {
                console.log('Respuesta del servidor:', response);
                if (response.data && response.data.length > 0) {
                    var docente = response.data[0];
                    $('#editForm [name="tipo_documento"]').val(docente.tipo_documento);
                    $('#editForm [name="numero_documento"]').val(docente.numero_documento);
                    $('#editForm [name="nombres"]').val(docente.nombres);
                    $('#editForm [name="apellidos"]').val(docente.apellidos);
                    $('#editForm [name="perfil_profesional"]').val(docente.perfil_profesional);
                    $('#editForm [name="telefono"]').val(docente.telefono);
                    $('#editForm [name="direccion"]').val(docente.direccion);
                    $('#editForm [name="email"]').val(docente.email);
                    $('#editForm [name="declara_renta"]').prop('checked', String(docente.declara_renta) === "1");
                    $('#editForm [name="retenedor_iva"]').prop('checked', String(docente.retenedor_iva) === "1");                    
                    $('#editModal').modal('show');
                } else {
                    alert('No se encontraron datos para el docente.');
                }
            },
            error: function() {
                alert('Error al obtener los datos del docente.');
            }
        });
    });

    
});
