$(document).ready(function () {
    var tabla = $('#tablaClases').DataTable({
        "paging": false,
        "searching": false,
        "info": false,
        "ordering": true,
        "ajax": "Cuentas-Docentes-Controlador.php?accion=listarClases",
        "columns": [
            { "data": "fecha" },
            { "data": "hora" },
            { "data": "nombre" },
            { "data": "nombre_salon" },
            { 
                "data": "estado",
                "render": function (data, type, row) {
                    if (data === 'Perdida') {
                        return '<button class="btn btn-danger reprogramar-btn" data-bs-toggle="modal" data-bs-target="#modalReprogramar" data-id="' + row.id_programador + '">Reprogramar</button>';
                    } else {
                        return '<span>' + data + '</span>';
                    }
                }
            }
        ],
        "language": {
            "url": "https://cdn.datatables.net/plug-ins/1.13.6/i18n/Spanish.json"
        },
        "order": [[0, "asc"]],
        "responsive": true
    });

    $('#tablaClases tbody').on('click', '.reprogramar-btn', function () {
        var data = tabla.row($(this).parents('tr')).data();
        var idProgramador = data.id_programador;
        $('#id_programador').val(idProgramador);
    });

    var table = $('#datos_CuentaCobroDocente').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "Cuentas-Docentes-Controlador.php",
            type: "POST",
            dataSrc: 'data'
        },
        columns: [
            { "data": "fecha" },
            { "data": null,
                "render": function(data, type, row) {
                    return row.nombres + ' ' + row.apellidos;
                }   
            },
            { "data": "valor_hora" },
            { "data": "horas_trabajadas" },
            { "data": "monto" },
            { "data": "estado" },
        ]
    });
});

function reprogramarClase() {
    const formData = new FormData(document.getElementById('formReprogramar'));
    console.log(...formData);

    $.ajax({
        url: "Cuentas-Docentes-Controlador.php?accion=reprogramar",
        type: "POST",
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
            console.log("Respuesta del servidor:", response);
            //location.reload();
        },
        error: function(xhr, status, error) {
            console.error("Error:", error);
            alert("Hubo un problema al procesar la solicitud.");
        }
    });
}

function aceptarCuenta() {
    const formData = new FormData(document.getElementById('formCuentaCobro'));
    console.log(...formData);

    $.ajax({
        url: "Cuentas-Docentes-Controlador.php?accion=Aceptar",
        type: "POST",
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
            alert("Cuenta aceptada correctamente.");
            location.reload();
        },
        error: function(xhr, status, error) {
            console.error("Error:", error);
            alert("Hubo un problema al procesar la solicitud.");
        }
    });
}
function rechazarCuenta() {
    const formData = new FormData(document.getElementById('formCuentaCobro'));
    console.log(...formData);

    $.ajax({
        url: "Cuentas-Docentes-Controlador.php?accion=Rechazar",
        type: "POST",
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
            alert("Cuenta rechazada correctamente.");
            location.reload();
        },
        error: function(xhr, status, error) {
            console.error("Error:", error);
            alert("Hubo un problema al procesar la solicitud.");
        }
    });
}
