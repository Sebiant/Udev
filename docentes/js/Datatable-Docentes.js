$(document).ready(function() {
    var table = $('#datos_docente').DataTable({
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
                d.searchTerm = d.search.value;  // Enviamos el término de búsqueda
            },
            dataSrc: 'data'
        },
        columns: [
            { "data": "numero_documento" },
            { "data": "nombre_completo" },
            { "data": "especialidad" },
            { "data": "descripcion_especialidad" },
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
        var idDocente = data.id_docente;
        var nuevoEstado = data.estado === "Activo" ? 0 : 1;

        $.ajax({
            url: 'Docentes-Controlador.php?accion=cambiarEstado',
            type: 'POST',
            data: { id_docente: idDocente, estado: nuevoEstado },
            success: function () {
                alert(`El estado del docente se ha actualizado a ${nuevoEstado === 1 ? "Activo" : "Inactivo"}.`);
                table.ajax.reload();
            },
            error: function () {
                alert("Hubo un error al cambiar el estado.");
            }
        });
    });

    $('#datos_docente').on('click', '.btn-modify', function() {
        var data = table.row($(this).parents('tr')).data();
        var idDocente = data.id_docente;

        $.ajax({
            url: 'Docentes-Controlador.php?accion=buscarPorId',
            type: 'POST',
            data: { id_docente: idDocente },
            dataType: 'json',
            success: function(response) {
                if (response.data && response.data.length > 0) {
                    var docente = response.data[0];
                    $('#editForm [name="id_docente"]').val(docente.id_docente);
                    $('#editForm [name="tipo_documento"]').val(docente.tipo_documento);
                    $('#editForm [name="numero_documento"]').val(docente.numero_documento);
                    $('#editForm [name="nombres"]').val(docente.nombres);
                    $('#editForm [name="apellidos"]').val(docente.apellidos);
                    $('#editForm [name="especialidad"]').val(docente.especialidad);
                    $('#editForm [name="descripcion_especialidad"]').val(docente.descripcion_especialidad);
                    $('#editForm [name="telefono"]').val(docente.telefono);
                    $('#editForm [name="direccion"]').val(docente.direccion);
                    $('#editForm [name="email"]').val(docente.email);
                    $('#editForm [name="declara_renta"]').prop('checked', docente.declara_renta === "Sí");
                    $('#editForm [name="retenedor_iva"]').prop('checked', docente.retenedor_iva === "Sí");
                    $('#editForm [name="estado"]').prop('checked', docente.estado === "Sí");
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

    $('#datos_docente').on('click', '.btn-delete', function() {
        var data = table.row($(this).parents('tr')).data();
        var idDocente = data.id_docente;

        if (confirm('¿Estás seguro de que quieres desactivar a este docente?')) {
            $.ajax({
                url: 'Docentes-Controlador.php?accion=eliminar',
                type: 'POST',
                data: { id_docente: idDocente },
                success: function() {
                    table.ajax.reload();
                    alert('Docente desactivado exitosamente.');
                },
                error: function() {
                    alert('Error al desactivar el docente.');
                }
            });
        }
    });
});
