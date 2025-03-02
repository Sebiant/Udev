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
                    '<button class="btn btn-primary btn-sm btn-verify">Verificar</button>',
                "orderable": false,
                "className": "text-center"
            },
            {
                "data": null,
                "defaultContent": 
                    '<button class="btn btn-primary btn-sm btn-return">Devolver</button>',
                "orderable": false,
                "className": "text-center"
            }
        ]
    });

    $('#datos_cuentacobro_admin').on('click', '.btn-verify', function() {

        var data = table.row($(this).parents('tr')).data();
        var idCuenta = data.id_cuenta; // Cambio aquí
    
        $.ajax({
            url: 'Cuentas-De-Cobro-Controlador.php?accion=BusquedaPorId',
            type: 'POST',
            data: { id_cuenta: idCuenta }, // Cambio aquí
            dataType: 'json',
            success: function(response) {
                console.log('Respuesta del servidor:', response);
                if (response.data && response.data.length > 0) {
                    var cuenta = response.data[0];
                    $('#editForm [name="fecha"]').val(cuenta.fecha);
                    $('#editForm [name="numero_documento"]').val(cuenta.numero_documento);
                    $('#editForm [name="horas_trabajadas"]').val(cuenta.horas_trabajadas);
                    $('#editForm [name="valor_hora"]').val(cuenta.valor_hora);
                    $('#editForm [name="monto"]').val(cuenta.monto);                   
                    $('#modalCuentasCobro').modal('show');
                } else {
                    alert('No se encontraron datos para la cuenta de cobro.');
                }
            },
            error: function() {
                alert('Error al obtener los datos de la cuenta de cobro.');
            }
        });
    });
});
