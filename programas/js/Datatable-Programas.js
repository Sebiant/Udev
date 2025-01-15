$(document).ready(function() {
    var table = $('#datos_programa').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "Programas-Controlador.php",
            type: "POST",
            dataSrc: 'data'
        },
        columns: [
            { "data": "id_programa" },
            { "data": "tipo" },
            { "data": "nombre" },
            { "data": "duracion_mes" },
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
                defaultContent: '<button class="btn btn-danger w-100 btn-delete">Borrar</button>',
                orderable: false
            }
        ]
    });

    // Acción de "Modificar" (Editar)
    $('#datos_programa').on('click', '.btn-modify', function() {
        var data = table.row($(this).parents('tr')).data();
        var idPrograma = data.id_programa;

        $.ajax({
            url: 'Programas-Controlador.php?accion=editar',
            type: 'POST',
            data: { id_programa: idPrograma },
            success: function(response) {
                console.log(response); // Verifica la respuesta completa del servidor

                if (response.data && response.data.length > 0) {
                    var programa = response.data[0];

                    // Rellenar el formulario de edición con los datos
                    $('#editForm [name="id_programa"]').val(programa.id_programa);
                    $('#editForm [name="tipo"]').val(programa.tipo);
                    $('#editForm [name="nombre"]').val(programa.nombre);
                    $('#editForm [name="duracion_mes"]').val(programa.duracion_mes);
                    $('#editForm [name="cant_modulos"]').val(programa.cant_modulos);
                    $('#editForm [name="descripcion"]').val(programa.descripcion);
                    
                    // Mostrar el modal de edición
                    $('#editModal').modal('show');
                } else {
                    alert('No se encontraron datos para editar.');
                }
            },
            error: function() {
                alert('Error al obtener los datos.');
            }
        });
    });

    // Guardar los cambios (cuando se edita un programa)
    $('#editForm').on('submit', function(e) {
        e.preventDefault();

        var formData = $(this).serialize(); // Obtener los datos del formulario

        $.ajax({
            url: 'Programas-Controlador.php?accion=actualizar',
            type: 'POST',
            data: formData,
            success: function(response) {
                if (response.success) {
                    alert('Programa actualizado correctamente.');
                    $('#editModal').modal('hide'); // Ocultar el modal
                    table.ajax.reload(); // Recargar la tabla
                } else {
                    alert('Error al actualizar el programa.');
                }
            },
            error: function() {
                alert('Error al realizar la solicitud.');
            }
        });
    });
});
