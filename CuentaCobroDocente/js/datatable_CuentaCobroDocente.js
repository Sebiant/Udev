$(document).ready(function() {
    var table = $('#datos_CuentaCobroDocente').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "CuentaCobroDocente_controlador.php",
            type: "POST",
            dataSrc: 'data'
        },
        columns: [
            { "data": "id_cuenta" },
            { "data": "fecha" },
            { "data": "pago_excepcional" },
            { "data": "valor_hora" },
            { "data": "horas_trabajadas" },
            { "data": "monto" },
            { "data": null,
                "render": function(data,type,row) {
                    return row.nombres + ' ' + row.apellidos;
                }
            },
            { "data": "estado" },
                        
        ]
    });
    
    $('#datos_CuentaCobroDocente').on('click', '.btn-modify', function() {
        var data = table.row($(this).parents('tr')).data();
        var idCuenta = data.id_cuenta;

        $.ajax({
            url: 'CuentaCobroDocente_controlador.php?accion=modificar',
            type: 'POST',
            data: { id_cuenta: idCuenta},
            success: function(response) {
                var cuenta = response.data[0];
                $('#editForm [name="id_cuenta"]').val(cuenta.id_cuenta);
                $('#editForm [name="nombres"]').val(cuenta.fecha);
                $('#editForm [name="pago_excepcional"]').val(cuenta.pago_excepcional);
                $('#editForm [name="valor_hora"]').val(cuenta.valor_hora);
                $('#editForm [name="horas_trabajadas"]').val(cuenta.horas_trabajadas);
                $('#editForm [name="monto"]').val(cuenta.monto);
                $('#editForm [name="id_docente"]').val(cuenta.id_docente);
                $('#editForm [name="estado"]').val(cuenta.estado);
                
                
                $('#editModal').modal('show');
            },
            error: function() {
                alert('Error al obtener los datos de la cuenta.');
            }
        });
    });
    $('#editForm').on('submit', function(e) {
        e.preventDefault();

        $.ajax({
            url: 'CuentaCobroDocente_controlador.php?accion=editar',
            type: 'POST',
            data: $(this).serialize(),
            success: function(response) {
                alert('Cuenta actualizada exitosamente.');
                table.ajax.reload();
                $('#editModal').modal('hide');
            },
            error: function() {
                alert('Error al actualizar la cuenta.');
            }
        });
    });
     // Agregar evento de clic al botón de aceptar
     $('#btn-aceptar').on('click', function() {
        // Llamar a la función que actualiza el DataTable
        actualizarDataTable();
    });

    // Función que actualiza el DataTable
    function actualizarDataTable() {
        // Obtener los datos de la cuenta de cobro
        var fecha = $('#fecha').val();
        var pago_excepcional = $('#pago_excepcional').val();
        var valor_hora = $('#valor_hora').val();
        var horas_trabajadas = $('#horas_trabajadas').val();
        var monto = $('#monto').val();
        var id_docente = $('#id_docente').val();
        var estado = $('#estado').val();
        // Actualizar el DataTable con los datos de la cuenta de cobro
        table.ajax.reload();
    }


    
});
