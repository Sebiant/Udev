$(document).ready(function() {
    var table = $('#datos_programa').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "controlador-programas.php",
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
    
    $('#datos_programa').on('click', '.btn-modify', function() {
        var data = table.row($(this).parents('tr')).data();
        var idPrograma = data.id_programa;

        $.ajax({
            url: 'controlador-programas.php',
            type: 'POST',
            data: { id_programa: idPrograma },
            success: function(response) {
                var programa = response.data[0];

                $('#editForm [name="id_programa"]').val(programa.id_programa);
                $('#editForm [name="tipo"]').val(programa.tipo);
                $('#editForm [name="nombre"]').val(programa.nombre);
                $('#editForm [name="duracion_mes"]').val(programa.duracion_mes);
                $('#editForm [name="cant_modulos"]').val(programa.cant_modulos);
                $('#editForm [name="descripcion"]').val(programa.descripcion);
                $('#editModal').modal('show');
            },
            error: function() {
                alert('Error al obtener los datos.');
            }
        });
    });
    $('#editForm').on('submit', function(e) {
        e.preventDefault();

        $.ajax({
            url: 'controlador-programas.php?accion=editar',
            type: 'POST',
            data: $(this).serialize(),
            success: function(response) {
                alert(' actualizado exitosamente.');
                table.ajax.reload();
                $('#editModal').modal('hide');
            },
            error: function() {
                alert('Error al actualizar.');
            }
        });
    });


    $('#datos_programa').on('click', '.btn-delete', function() {
        var data = table.row($(this).parents('tr')).data();
        var idPrograma = data.id_programa;

        if (confirm('¿Estás seguro de que quieres desactivar a este programa?')) {
            $.ajax({
                url: 'controlador-programas.php?accion=eliminar',
                type: 'POST',
                data: { id_programa: idPrograma },
                success: function(response) {
                    table.ajax.reload();
                    alert('desactivado exitosamente.');
                },
                error: function() {
                    alert('Error al desactivar');
                }
            });
        }
    });
});
