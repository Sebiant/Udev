$(document).ready(function() {
    var table = $('#datos_docente').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "Docentes-Controlador.php",
            type: "POST",
            dataSrc: 'data'
        },
        columns: [
            { "data": "numero_documento" },
            { "data": "nombre_completo" },
            { "data": "especialidad" },
            { "data": "descripcion_especialidad" },
            { "data": "telefono" },
            { "data": "direccion" },
            { "data": "email" },
            { "data": "estado" },
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

    // Evento para cambiar el estado dinámico (Eliminar/Cambiar Estado)
    $('#datos_docente').on('click', '.btn-toggle-state', function () {  // Cambié '#datos_materia' por '#datos_docente'
        var data = table.row($(this).parents('tr')).data();
        var idDocente = data.id_docente;
        var nuevoEstado = data.estado === "Activo" ? 0 : 1; // Cambiar estado (Activo -> Inactivo o viceversa)

        $.ajax({
            url: 'Docentes-Controlador.php?accion=cambiarEstado',
            type: 'POST',
            data: { id_docente: idDocente, estado: nuevoEstado },
            success: function (response) {
                alert(`El estado del docente se ha actualizado a ${nuevoEstado === 1 ? "Activo" : "Inactivo"}.`);
                table.ajax.reload();
            },
            error: function () {
                alert("Hubo un error al cambiar el estado.");
            }
        });
    });

    $('#datos_docente').on('click', '.btn-modify', function() {
        var data = table.row($(this).parents('tr')).data();
        var idDocente = data.id_docente;

        $.ajax({
            url: 'Docentes-Controlador.php?accion=buscarPorId',
            type: 'POST',
            data: { id_docente: idDocente },
            dataType: 'json',
            success: function(response) {
                if (response.data && response.data.length > 0) {
                    var docente = response.data[0];
                    $('#editForm [name="id_docente"]').val(docente.id_docente);
                    $('#editForm [name="tipo_documento"]').val(docente.tipo_documento);
                    $('#editForm [name="numero_documento"]').val(docente.numero_documento);
                    $('#editForm [name="nombres"]').val(docente.nombres);
                    $('#editForm [name="apellidos"]').val(docente.apellidos);
                    $('#editForm [name="especialidad"]').val(docente.especialidad);
                    $('#editForm [name="descripcion_especialidad"]').val(docente.descripcion_especialidad);
                    $('#editForm [name="telefono"]').val(docente.telefono);
                    $('#editForm [name="direccion"]').val(docente.direccion);
                    $('#editForm [name="email"]').val(docente.email);
                    $('#editForm [name="declara_renta"]').prop('checked', docente.declara_renta === "Sí");
                    $('#editForm [name="retenedor_iva"]').prop('checked', docente.retenedor_iva === "Sí");
                    $('#editForm [name="estado"]').prop('checked', docente.estado === "Si");

                    $('#editModal').modal('show');
                } else {
                    alert('No se encontraron datos para el docente.');
                }
            },
            error: function() {
                alert('Error al obtener los datos del docente.');
            }
        });
    });
});

function guardarCambiosDocente() {
    // Obtener todos los valores de los campos del formulario
    var id_docente = $('#editForm [name="id_docente"]').val();
    var tipo_documento = $('#editForm [name="tipo_documento"]').val();
    var numero_documento = $('#editForm [name="numero_documento"]').val();
    var nombres = $('#editForm [name="nombres"]').val();
    var apellidos = $('#editForm [name="apellidos"]').val();
    var especialidad = $('#editForm [name="especialidad"]').val();
    var descripcion_especialidad = $('#editForm [name="descripcion_especialidad"]').val();
    var telefono = $('#editForm [name="telefono"]').val();
    var direccion = $('#editForm [name="direccion"]').val();
    var email = $('#editForm [name="email"]').val();
    var declara_renta = $('#editForm [name="declara_renta"]').prop('checked') ? 'Sí' : 'No';
    var retenedor_iva = $('#editForm [name="retenedor_iva"]').prop('checked') ? 'Sí' : 'No';
    var estado = $('#editForm [name="estado"]').prop('checked') ? 'Si' : 'No';

    // Validar campos obligatorios
    var camposFaltantes = [];
    if (!tipo_documento) camposFaltantes.push("Tipo de documento");
    if (!numero_documento) camposFaltantes.push("Número de documento");
    if (!nombres) camposFaltantes.push("Nombres");
    if (!apellidos) camposFaltantes.push("Apellidos");
    if (!especialidad) camposFaltantes.push("Especialidad");
    if (!descripcion_especialidad) camposFaltantes.push("Descripcion Especialidad");
    if (!telefono) camposFaltantes.push("Teléfono");
    if (!direccion) camposFaltantes.push("Dirección");
    if (!email) camposFaltantes.push("Correo electrónico");

    if (camposFaltantes.length > 0) {
        alert(`Por favor completa los siguientes campos:\n- ${camposFaltantes.join("\n- ")}`);
        return;
    }

    const formElement = document.getElementById('formDocente');

    if (!formElement) {
        alert('No se encontró el formulario.');
        return;
    }

    // Crear un objeto FormData y agregar todos los datos
    const formData = new FormData(formElement);

    // Agregar los valores manualmente al FormData
    formData.append('id_docente', id_docente);
    formData.append('tipo_documento', tipo_documento);
    formData.append('numero_documento', numero_documento);
    formData.append('nombres', nombres);
    formData.append('apellidos', apellidos);
    formData.append('especialidad', especialidad);
    formData.append('descripcion_especialidad', descripcion_especialidad);
    formData.append('telefono', telefono);
    formData.append('direccion', direccion);
    formData.append('email', email);
    formData.append('declara_renta', declara_renta);
    formData.append('retenedor_iva', retenedor_iva);
    formData.append('estado', estado);

    console.log('Acción: Modificar');
    console.log('Datos del Formulario:', Object.fromEntries(formData.entries()));

    // Realizar la solicitud Fetch
    fetch('Docentes-Controlador.php?accion=Modificar', {
        method: 'POST',
        body: formData,
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`Error en la solicitud: ${response.status}`);
        }
        return response.text();
    })
    .then(data => {
        console.log('Respuesta del servidor:', data);
        try {
            const jsonData = JSON.parse(data);
            if (jsonData.error) {
                alert(`Error: ${jsonData.error}`);
            } else {
                alert('Cambios guardados exitosamente.');
                location.reload();
            }
        } catch {
            alert('Cambios guardados exitosamente.');
            location.reload();
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Ocurrió un error al guardar los cambios. Por favor, inténtalo de nuevo.');
    });
}

$('#datos_docente').on('click', '.btn-delete', function() {
    var data = table.row($(this).parents('tr')).data();
    var idDocente = data.id_docente;

    if (confirm('¿Estás seguro de que quieres desactivar a este docente?')) {
        $.ajax({
            url: 'Docentes-Controlador.php?accion=eliminar',
            type: 'POST',
            data: { id_docente: idDocente },
            success: function(response) {
                table.ajax.reload();
                alert('Docente desactivado exitosamente.');
            },
            error: function() {
                alert('Error al desactivar el docente.');
            }
        });
    }
});
