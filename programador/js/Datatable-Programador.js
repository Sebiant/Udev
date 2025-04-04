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
            { data: "nombre_salon" },
            { data: null, render: function (data, type, row) { return row.nombres + ' ' + row.apellidos; } },
            { data: "nombre_modulo" },
            { data: "modalidad" },
            { 
                data: "estado",
                render: function (data, type, row) {
                    if (data === 'Perdida') {
                        return `<button class="btn btn-danger reprogramar-btn" data-bs-toggle="modal" data-bs-target="#modalReprogramar" data-id="${row.id_programador}">Reprogramar</button>`;
                    } else {
                        return `<span>${data}</span>`;
                    }
                }
            },
            {
                data: "id_programador",
                render: function (data) {
                    return `<button class="btn btn-primary w-100 btn-modify" data-id="${data}">Editar</button>`;
                }
            }
        ]
    });

    // Evento para el botón de reprogramar
    $('#datos_programador').on('click', '.reprogramar-btn', function () {
        var data = table.row($(this).closest('tr')).data();

        if (data) {
            $('#id_programador').val(data.id_programador);
            $('#id_salon').val(data.id_salon);
            $('#numero_documento').val(data.numero_documento);
            $('#id_modulo').val(data.id_modulo);
            $('#id_periodo').val(data.id_periodo);
            $('#m').val(data.modalidad);
        } else {
            console.error("No se pudieron obtener los datos de la fila.");
        }
    });

    // Evento para el botón de editar
    $('#datos_programador').on('click', '.btn-modify', function () {
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

                    $('#editarClaseForm [name="id_programador"]').val(programador.id_programador    );
                    $('#editarClaseForm [name="fecha"]').val(programador.fecha);
                    $('#editarClaseForm [name="hora_inicio"]').val(programador.hora_inicio);
                    $('#editarClaseForm [name="hora_salida"]').val(programador.hora_salida);
                    $('#editarClaseForm [name="id_salon"]').val(programador.id_salon);
                    $('#editarClaseForm [name="numero_documento"]').val(programador.numero_documento);
                    $('#editarClaseForm [name="id_modulo"]').val(programador.id_modulo);
                    $('#editarClaseForm [name="modalidad"]').val(programador.modalidad);
                    $('#editarClaseForm [name="estado"]').prop('checked', String(programador.estado) === "1");

                    $('#modalEditarClase').modal('show');
                } else {
                    alert('No se encontraron datos para esta clase.');
                }
            },
            error: function (xhr) {
                console.error("Error en la petición AJAX:", xhr.responseText);
            }
        });
    });

    // Evento para el formulario de edición
    $('#editarClaseForm').on('submit', function (e) {
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
            error: function (xhr) {
                console.error("Error al actualizar la clase:", xhr.responseText);
                alert('Error al actualizar la clase.');
            }
        });
    });
});
