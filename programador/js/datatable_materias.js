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
            { "data": "radio_button", "orderable": false }
        ]
    });
});
