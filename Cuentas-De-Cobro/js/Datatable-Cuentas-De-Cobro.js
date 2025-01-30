$(document).ready(function() {
    var table = $('#datos_cuentacobro_admin').DataTable({
        "processing": true,
        "serverSide": true,
        "ajax": {
            "url": "Cuentas-De-Cobro-Controlador.php",
            "type": "POST"
        },
        "columns": [
            { "data": "fecha" },
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
                "defaultContent": 
                    '<div class="d-flex justify-content-center">' +
                    '<button class="btn btn-primary mx-1"><i class="bi bi-file-earmark-pdf"></i> PDF</button>' +
                    '<button class="btn btn-primary mx-1"><i class="bi bi-file-earmark-spreadsheet"></i> CSV</button>' +
                    '</div>',
                "orderable": false,
                "className": 'text-center'
            }
        ],
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.11.5/i18n/Spanish.json"
        }
    });
});
