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

    
     
});
