$(document).ready(function() {
    var table = $('#datos_cuentacobro_admin').DataTable({
        "ajax": {
            "url": "Cuentas-De-Cobro-Controlador.php",
            "dataSrc": "data"
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
            { "data": "valor_hora" },
            { "data": "monto" },
            { "data": "estado" },
            {
                "data": null,
                "defaultContent": 
                    '<div class="d-flex justify-content-center">' +
                    '<button class="btn btn-primary mx-1 btn-export-pdf"><i class="bi bi-file-earmark-pdf"></i> PDF</button>' +
                    '</div>',
                "orderable": false,
                "className": 'text-center'
            }
        ]
    });

    $('#datos_cuentacobro_admin tbody').on('click', '.btn-export-pdf', function() {
        var data = table.row($(this).parents('tr')).data();
        window.location.href = `Cuentas-De-Cobro-Controlador.php?accion=exportar_todos`;
    });

    $('#datos_cuentacobro_admin tbody').on('click', '.btn-export-pdf', function() {
        var data = table.row($(this).parents('tr')).data();
        window.location.href = 'Cuentas-De-Cobro-Controlador.php?accion=exportar&id_cuenta=' + data.id_cuenta + '&formato=pdf';
    });
});
