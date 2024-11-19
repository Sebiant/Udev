$(document).ready(function() {
    var table = $('#datos_cuentacobro_admin').DataTable({
        "processing": true,
        "serverSide": true,
        "ajax": {
            "url": "controlador-cuentascobro-adm.php?accion=default",
            "type": "GET"
        },
        "columns": [
            { "data": "id_cuenta" },
            { "data": "fecha" },
            { "data": "pago_excepcional" },
            { "data": "valor_hora" },
            { "data": "horas_trabajadas" },
            { "data": "monto" },
            { "data": "estado" },
            { 
                "data": null,
                "render": function(data, type, row) {
                    return row.nombres + ' ' + row.apellidos;
                }
            },
            { 
                "data": null, 
                "render": function(data, type, row) {
                    return `<button type='button' class='btn btn-danger btn-delete' data-id='${row.id_asistencia}'>Eliminar</button>`;
                }
            }
        ]
    });
});
