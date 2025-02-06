$(document).ready(function () {
    var table = $('#datos_programador').DataTable({
        "ajax": {
            "url": "Programador-Controlador.php",
            "dataSrc": "data"  
        },
        "columns": [
            { "data": "fecha" },
            { "data": "hora_inicio" },
            { "data": "hora_salida" },
            { "data": "nombre_salon" },
            { "data": "nombre_completo" },
            { "data": "nombre" },
            {
                "data": null,
                "render": function (data, type, row) {
                    return `<button type='button' class='btn btn-success btn-validar' data-id='${row.id_programador}'>Validar</button>`;
                }
            }
        ]
    });

    $('#datos_programador tbody').on('click', '.btn-validar', function () {
        var id = $(this).data('id'); 
        cambiarEstado(id);
    }); 
});

async function cambiarEstado(id) {
    if (confirm("Deseas confirmar si el docente " + id + " asistió?")) {
        try {
            
            let respuestaAsistencia = await $.ajax({
                url: 'Programador-Controlador.php?accion=crearAsistencia',
                type: 'POST',
                data: { id_programador: id },
                dataType: 'json'
            });

            
            console.log('Respuesta Asistencia:', respuestaAsistencia);

            if (!respuestaAsistencia.success) {
                alert(respuestaAsistencia.error); 
                return; 
            }

            let respuestaEstado = await $.ajax({
                url: 'Programador-Controlador.php?accion=cambiarEstado',
                type: 'POST',
                data: { id_programador: id },
                dataType: 'json'
            });

            
            console.log('Respuesta Estado:', respuestaEstado);

            if (respuestaEstado.success) {
                alert(respuestaEstado.mensaje);
                $('#datos_programador').DataTable().ajax.reload();
            } else {
                alert(respuestaEstado.error); 
            }

        } catch (error) {
            console.error("Error en la petición AJAX: ", error); 
            alert('Ocurrió un error en el proceso. Detalles en la consola.');
        }
    }
}

