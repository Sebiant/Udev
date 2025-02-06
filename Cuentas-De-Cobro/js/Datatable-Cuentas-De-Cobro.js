$(document).ready(function() {
    var table = $('#datos_cuentacobro_admin').DataTable({
        "ajax": {
            "url": "Cuentas-De-Cobro-Controlador.php",
            "dataSrc": "data"
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
                    '<button class="btn btn-primary mx-1 btn-export-pdf"><i class="bi bi-file-earmark-pdf"></i> PDF</button>' +
                    '<button class="btn btn-primary mx-1 btn-export-csv"><i class="bi bi-file-earmark-spreadsheet"></i> CSV</button>' +
                    '</div>',
                "orderable": false,
                "className": 'text-center'
            }
        ]
    });

    // Evento para exportar TODOS los registros en PDF
    $('#botonPdf').on('click', function() {
        exportarTodo('pdf');
    });

    // Evento para exportar TODOS los registros en CSV
    $('#botonCsv').on('click', function() {
        exportarTodo('csv');
    });

    function exportarTodo(formato) {
        const totalRegistros = $('#datos_cuentacobro_admin').DataTable().data().count();
        if (totalRegistros > 0) {
            window.location.href = `Cuentas-De-Cobro-Controlador.php?accion=exportar_todos&formato=${formato}`;
        } else {
            alert('No hay registros para exportar.');
        }
    }
    

    // Evento para exportar cuenta individual en PDF
    $('#datos_cuentacobro_admin tbody').on('click', '.btn-export-pdf', function() {
        var data = table.row($(this).parents('tr')).data();
        window.location.href = 'Cuentas-De-Cobro-Controlador.php?accion=exportar&id_cuenta=' + data.id_cuenta + '&formato=pdf';
    });

    // Evento para exportar cuenta individual en CSV
    $('#datos_cuentacobro_admin tbody').on('click', '.btn-export-csv', function() {
        var data = table.row($(this).parents('tr')).data();
        window.location.href = 'Cuentas-De-Cobro-Controlador.php?accion=exportar&id_cuenta=' + data.id_cuenta + '&formato=csv';
    });
});
