$(document).ready(function () {
    var table = $('#datos_docente').DataTable({
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
            url: "Docentes-Controlador.php",
            type: "POST",
            data: function (d) {
                d.page = d.start / d.length + 1;
                d.pageSize = d.length;
                d.searchTerm = d.search.value;
            },
            dataSrc: 'data'
        },
        columns: [
            { data: "numero_documento" },
            { data: "nombre_completo" },
            { data: "perfil_profesional" },
            { data: "telefono" },
            { data: "direccion" },
            { data: "email" },
            { data: "estado" },
            {
                data: null,
                defaultContent: '<button class="btn btn-primary w-100 btn-modify">Modificar</button>',
                orderable: false
            },
            {
                data: null,
                render: function (data, type, row) {
                    var buttonClass = row.estado === "Activo" ? "btn-danger" : "btn-success";
                    var buttonText = row.estado === "Activo" ? "Inactivar" : "Activar";
                    return `<button class="btn ${buttonClass} w-100 btn-toggle-state">${buttonText}</button>`;
                },
                orderable: false
            }
        ]
    });

    // Cambiar estado (Activar/Inactivar)
    $('#datos_docente').on('click', '.btn-toggle-state', function () {
        var data = table.row($(this).parents('tr')).data();
        var numeroDocumento = data.numero_documento;
        var nuevoEstado = data.estado === "Activo" ? 0 : 1;

        $.ajax({
            url: 'Docentes-Controlador.php?accion=cambiarEstado',
            type: 'POST',
            data: { numero_documento: numeroDocumento, estado: nuevoEstado },
            success: function () {
                table.ajax.reload();
            },
            error: function () {
                alert("Hubo un error al cambiar el estado.");
            }
        });
    });

    // Modificar datos
    $('#datos_docente').on('click', '.btn-modify', function () {
        var data = table.row($(this).parents('tr')).data();
        var numeroDocumento = data.numero_documento;

        $.ajax({
            url: 'Docentes-Controlador.php?accion=buscarPorId',
            type: 'POST',
            data: { numero_documento: numeroDocumento },
            dataType: 'json',
            success: function (response) {
                console.log('Respuesta del servidor:', response);
                if (response.data && response.data.length > 0) {
                    var docente = response.data[0];
                
                    $('#editForm [name="tipo_documento"]').val(docente.tipo_documento);
                    $('#editForm [name="numero_documento"]').val(docente.numero_documento);
                    $('#editForm [name="documento_anterior"]').val(docente.numero_documento); // <-- ESTA ES LA CLAVE
                    $('#editForm [name="nombres"]').val(docente.nombres);
                    $('#editForm [name="apellidos"]').val(docente.apellidos);
                    $('#editForm [name="perfil_profesional"]').val(docente.perfil_profesional);
                    $('#editForm [name="telefono"]').val(docente.telefono);
                    $('#editForm [name="direccion"]').val(docente.direccion);
                    $('#editForm [name="correo"]').val(docente.correo);
                    $('#editForm [name="declara_renta"]').prop('checked', String(docente.declara_renta) === "1");
                    $('#editForm [name="retenedor_iva"]').prop('checked', String(docente.retenedor_iva) === "1");
                
                    cargarMateriasParaEditar(docente.numero_documento, function () {
                        $('.modal').modal('hide');
                        setTimeout(() => $('#editModal').modal('show'), 300);
                    });
                }
                else {
                    alert('No se encontraron datos para el docente.');
                }
            },
            error: function () {
                alert('Error al obtener los datos del docente.');
            }
        });
    });

    // Función para cargar materias en el modal de edición
    function cargarMateriasParaEditar(idDocente, callback) {
        $.ajax({
            url: "Docentes-Controlador.php?accion=traerMateriasDocente",
            method: "POST",
            data: { numero_documento: idDocente },
            dataType: "json",
            success: function (data) {
                const materias = data.materias;
                const asignadas = data.asignadas.map(id => parseInt(id));

                console.log('Todas las materias:', materias);
                console.log('IDs asignados:', asignadas);

                const container = $("#materiasContainerEditar");
                container.empty();

                materias.forEach(materia => {
                    const id = `editar_materia_${materia.id_modulo}`;
                    const isChecked = asignadas.includes(parseInt(materia.id_modulo)) ? 'checked' : '';
                    const checkbox = `<input type="checkbox" class="btn-check" id="${id}" name="id_modulo[]" value="${materia.id_modulo}" ${isChecked} autocomplete="off">`;
                    const label = `<label class="btn btn-outline-primary" for="${id}">${materia.nombre}</label>`;
                    container.append(checkbox + label);
                });

                if (callback) callback();
            },
            error: function () {
                $("#materiasContainerEditar").html("<p class='text-danger'>Error al cargar materias.</p>");
            }
        });
    }
});
