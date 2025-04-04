$(document).ready(function () {
    var table = $('#datos_programador').DataTable({
        "ajax": {
            "url": "Clases-Controlador.php",
            "dataSrc": "data"  
        },
        "columns": [
            { "data": "horario" },
            { "data": "nombre_completo" },
            { "data": "nombre" },
            { "data": "nombre_salon" },
            {
                data: null,
                render: function (data, type, row) {
                    var buttonClass = row.estado === "Pendiente" ? "btn-danger" : "btn-success";
                    var buttonText = row.estado === "Pendiente" ? "Marcar clase como perdida" : "Activar clase";
                    return `<button class="btn ${buttonClass} w-100 btn-toggle-state">${buttonText}</button>`;
                },
                orderable: false
            }
        ]
    });

    $('#datos_programador').on('click', '.btn-toggle-state', function () {
        var data = table.row($(this).parents('tr')).data();
        var idProgramador = data.id_programador;
        var nuevoEstado = data.estado === "Pendiente" ? "Perdida" : "Pendiente";

        console.log(idProgramador);
        console.log(nuevoEstado);

        $.ajax({
            url: 'Clases-Controlador.php?accion=cambiarEstado',
            type: 'POST',
            data: { id_programador: idProgramador, estado: nuevoEstado },
            success: function () {
                table.ajax.reload();
            },
            error: function () {
                alert("Hubo un error al cambiar el estado.");
            }
        });
    });
});
