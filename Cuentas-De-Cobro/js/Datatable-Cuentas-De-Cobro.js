$(document).ready(function() {
    var table = $('#datos_cuentacobro_admin').DataTable({
        language: {
            url: "//cdn.datatables.net/plug-ins/1.11.5/i18n/es-ES.json"
        },
        "searching": true,
        "paging": true,
        "lengthChange": true,
        "pageLength": 10,
        "processing": true,
        "serverSide": true,
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
                "defaultContent": '<button class="btn btn-primary btn-sm btn-verify">Verificar</button>',
                "orderable": false,
                "className": "text-center"
            },
        ]
    });

    // Evento para el botón "Verificar"
    $('#datos_cuentacobro_admin').on('click', '.btn-verify', function() {
        var data = table.row($(this).parents('tr')).data();
        verificarCuenta(data.id_cuenta);
    });

    // Evento para el botón "Devolver"
    $('#datos_cuentacobro_admin').on('click', '.btn-return', function() {
        var data = table.row($(this).parents('tr')).data();
        devolverCuenta(data.id_cuenta);
    });
});

// 📌 Ahora estas funciones están en el ámbito global

function verificarCuenta(idCuenta) {
    $.ajax({
        url: 'Cuentas-De-Cobro-Controlador.php?accion=BusquedaPorId',
        type: 'POST',
        data: { id_cuenta: idCuenta }, 
        dataType: 'json',
        success: function(response) {
            console.log('Respuesta del servidor:', response);
            if (response.data && response.data.length > 0) {
                var cuenta = response.data[0];

                $('#id_cuenta').val(cuenta.id_cuenta);
                $('#btnFirmado').attr('data-id', cuenta.id_cuenta);
                $('#btnExportar').attr('data-id', cuenta.id_cuenta);
                $('#btnDevolver').attr('data-id', cuenta.id_cuenta);
                $('[name="fecha"]').text('Cuenta: ' + cuenta.fecha);
                $('[name="modalCuentasCobroLabel"]').text(cuenta.nombres + " " + cuenta.apellidos);
                $('#formCuentaCobro [name="horas_trabajadas"]').val(cuenta.horas_trabajadas);
                $('#formCuentaCobro [name="valor_hora"]').val(cuenta.valor_hora);

                // Llamar a la función para actualizar los botones según el estado
                actualizarBotones(cuenta.estado);

                // Mostrar el modal
                $('#modalCuentasCobro').modal('show');
            } else {
                alert('No se encontraron datos para la cuenta de cobro.');
            }
        },
        error: function() {
            alert('Error al obtener los datos de la cuenta de cobro.');
        }
    });
}

function actualizarBotones(estado) {
    $("#btnModificar, #btnExportar, #btnFirmado, #btnDevolver").hide();

    if (estado === 'aceptada_docente' || estado === 'rechazada_por_docente') {
        $("#btnModificar").show(); 
        $("#btnDevolver").show(); 
    } 
    if (estado === 'aceptada_docente') {
        $("#btnExportar").show();  
    } 
    if (estado === 'pendiente_firma') {
        $("#btnFirmado").show();  
    }
}

function Firmar() {
    const btnFirmado = document.getElementById('btnFirmado');
    const idCuenta = btnFirmado ? btnFirmado.getAttribute('data-id') : null;

    if (!idCuenta) {
        console.error('Error: ID de cuenta no encontrado en el botón.');
        return;
    }

    const formData = new FormData();
    formData.append('id_cuenta', idCuenta);

    console.log('Datos enviados:', ...formData.entries());

    $.ajax({
        url: 'Cuentas-De-Cobro-Controlador.php?accion=Firmar',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
            console.log('Respuesta del servidor:', response);
            location.reload();
        },
        error: function(xhr, status, error) {
            console.error('Error en la solicitud AJAX:', error);
        }
    });
}

function Devolver() {
    const btnDevolver = document.getElementById('btnDevolver');
    const idCuenta = btnDevolver ? btnDevolver.getAttribute('data-id') : null;

    if (!idCuenta) {
        console.error('Error: ID de cuenta no encontrado en el botón.');
        return;
    }

    const formData = new FormData();
    formData.append('id_cuenta', idCuenta);

    console.log('Datos enviados:', ...formData.entries());

    $.ajax({
        url: 'Cuentas-De-Cobro-Controlador.php?accion=Devolver',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
            console.log('Respuesta del servidor:', response);
            location.reload();
        },
        error: function(xhr, status, error) {
            console.error('Error en la solicitud AJAX:', error);
        }
    });
}



