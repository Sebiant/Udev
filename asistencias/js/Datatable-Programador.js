$(document).ready(function () {
    var table = $('#datos_programador').DataTable({
        "ajax": {
            "url": "Programador-Controlador.php",
            "dataSrc": "data"  
        },
        "columns": [
            { "data": "fecha" },
            { "data": "hora_inicio" },
            { "data": "hora_salida" },
            { "data": "nombre_salon" },
            { "data": "nombre_completo" },
            { "data": "nombre" },
        ]
    });
});
