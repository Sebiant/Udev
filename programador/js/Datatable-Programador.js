$(document).ready(function () {
    var table = $('#datos_programador').DataTable({
        language: {
            url: "//cdn.datatables.net/plug-ins/1.11.5/i18n/es-ES.json"
        },
        searching: true,
        paging: true,
        lengthChange: true,
        pageLength: 10,
        processing: true,
        serverSide: true,
        ajax: {
            url: "Programador-Controlador.php",
            type: "POST",
            dataSrc: "data"
        },
        columns: [
            { data: "fecha" },
            { data: "hora_inicio" },
            { data: "hora_salida" },
            {
                data: null,
                render: function (data, type, row) {
                    return row.nombre_salon;
                }
            },
            {
                data: null,
                render: function (data, type, row) {
                    return row.nombres + ' ' + row.apellidos;
                }
            },
            {
                data: null,
                render: function (data, type, row) {
                    return row.nombre_modulo;
                }
            },
            { data: "estado" },
            { data: "modalidad" },
            {
                data: "id_programador",
                render: function (data) {
                    return `<button class="btn btn-primary w-100 btn-modify"(${data})">Editar</button>`;
                }
            },
        ],
    });

    $('#datos_docente').on('click', '.btn-modify', function () {
        var data = table.row($(this).parents('tr')).data();
        var idProgramador = data.id_programador;

        $.ajax({
            url: 'Programador-Controlador.php?accion=BusquedaPorId',
            type: 'POST',
            data: { id_programador: idProgramador },
            dataType: 'json',
            success: function (response) {
                console.log(response);
                if (response.data && response.data.length > 0) {
                    var programador = response.data[0];

                    $('#editarClaseForm [name="fecha"]').val(programador.fecha);
                    $('#editarClaseForm [name="hora_inicio"]').val(programador.hora_inicio);
                    $('#editarClaseForm [name="hora_salida"]').val(programador.hora_salida);
                    $('#editarClaseForm [name="id_salon"]').val(programador.id_salon);
                    $('#editarClaseForm [name="numero_documento"]').val(programador.numero_documento);
                    $('#editarClaseForm [name="id_materia"]').val(programador.id_materia);
                    $('#editarClaseForm [name="modalidad"]').val(programador.modalidad);
                    $('#editarClaseForm [name="estado"]').prop('checked', String(programador.estado) === "1");
                    

                    $('#modalEditarClase').modal('show');
                } else {
                    alert('No se encontraron datos para esta clase.');
                }
            }
        });
    });

    $('#editarClaseForm').on('button', function (e) {
        e.preventDefault();

        $.ajax({
            url: 'Programador-Controlador.php?accion=editar',
            type: 'POST',
            data: $(this).serialize(),
            success: function (response) {
                console.log(response);
                table.ajax.reload();
                $('#modalEditarClase').modal('hide');
            },
            error: function () {
                alert('Error al actualizar la clase.');
            }
        });
    });
});
