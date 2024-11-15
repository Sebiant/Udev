$(document).ready(function() {
    var table = $('#datos_materia').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "materias-controlador.php",
            type: "POST",
            dataSrc: 'data'  
        },
        columns: [
            { "data": "id_materia" },
            { "data": "nombre" },
            { "data": "descripcion" },
            { "data": "frecuencia" },
            { "data": "modalidad" },
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
});