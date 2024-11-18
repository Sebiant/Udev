$(document).ready(function() {
    var table = $('#datos_asistencia').DataTable({
        "processing": true,
        "serverSide": true,
        "ajax": {
            "url": "Asistencias-Controlador.php",
            "type": "GET"
        },
        "columns": [
        
            { "data": "fecha" },
           
            { 
                "data": null,
                "render": function(data, type, row) {
                    return row.nombres + ' ' + row.apellidos;


                    
                    
                }
            },
            { "data": "horas_trabajadas" },
            { 
                "data": null, 
                "render": function(data, type, row) {
                    return `<button type='button' class='btn btn-primary' data-id='${row.id_asistencia}'>Editar</button>`;
                }
            }
            
        ]
    });
});
