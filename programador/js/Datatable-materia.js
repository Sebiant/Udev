$(document).ready(function() {
    var table = $('#datos_modulo').DataTable({
        language: {
            url: "//cdn.datatables.net/plug-ins/1.11.5/i18n/es-ES.json"
        },
        searching: true,
        paging: true,
        lengthChange: false,
        pageLength: 5,
        processing: true,
        serverSide: true,
        ajax: {
            url: "Modulos-Controlador.php",
            type: "POST",
            dataSrc: 'data'  
        },
        columns: [
            { "data": "tipo" },
            { "data": "nombre" },
            { "data": "programa" },
            { "data": "descripcion" },
            {
                "data": "id_modulo",
                "render": function(data, type, row) {
                    return `<input type="radio" name="moduloSeleccionado" value="${data}">`;
                },
                "orderable": false
            }
        ]
    });
});


function CrearClase() {
    var idMateria = $("input[name='materiaSeleccionada']:checked").val();

    if (!idMateria) {
        alert("Por favor, selecciona una materia.");
        return;
    }

    const formData = new FormData(document.getElementById('programadorForm'));
    formData.append("id_materia", idMateria);

    console.log('Datos del formulario:', ...formData.entries());

    $.ajax({
        url: 'Programador-Controlador.php?accion=crear',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
            console.log('Respuesta del servidor:', response);
            location.reload();
        },
        error: function(xhr, status, error) {
            console.error('Error:', error);
        }
    });
}
