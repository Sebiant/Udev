$(document).ready(function() {
    var table = $('#datos_docente').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "Docentes-Controlador.php",
            type: "POST",
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
                defaultContent: '<button class="btn btn-danger w-100 btn-delete">Desactivar</button>',
                orderable: false
            }
        ]
    });
    
    $('#datos_docente').on('click', '.btn-modify', function() {
        var data = table.row($(this).parents('tr')).data();
        var idDocente = data.id_docente;
    
    
        $.ajax({
            url: 'Docentes-Controlador.php',
            type: 'POST',
            data: { id_docente: idDocente },
            success: function(response) {
                    
    
                if (response.data && response.data.length > 0) {
                    var docente = response.data[0];
    
                    $('#editForm [name="id_docente"]').val(docente.id_docente);
                    $('#editForm [name="tipo_documento"]').val(docente.tipo_documento);
                    $('#editForm [name="numero_documento"]').val(docente.numero_documento);
                    $('#editForm [name="nombres"]').val(docente.nombre_completo);
                    $('#editForm [name="especialidad"]').val(docente.especialidad);
                    $('#editForm [name="descripcion_especialidad"]').val(docente.descripcion_especialidad);
                    $('#editForm [name="telefono"]').val(docente.telefono);
                    $('#editForm [name="direccion"]').val(docente.direccion);
                    $('#editForm [name="email"]').val(docente.email);
                    $('#editForm [name="declara_renta"]').prop('checked', docente.declara_renta === "Sí");
                    $('#editForm [name="retenedor_iva"]').prop('checked', docente.retenedor_iva === "Sí");
                    $('#editForm [name="estado"]').prop('checked', docente.estado === "Si");
    
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
    
    $('#editForm').on('submit', function(e) {
        e.preventDefault();

        $.ajax({
            url: 'Docentes-Controlador.php?accion=editar',
            type: 'POST',
            data: $(this).serialize(),
            success: function(response) {
                alert('Docente actualizado exitosamente.');
                table.ajax.reload(); // Recargar la tabla para reflejar los cambios
                $('#editModal').modal('hide');
            },
            error: function() {
                alert('Error al actualizar el docente.');
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
                success: function(response) {
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