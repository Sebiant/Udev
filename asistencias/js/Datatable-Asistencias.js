document.getElementById("formHistorial").addEventListener("submit", function (e) {
    e.preventDefault(); // Evita el envío automático del formulario

    let docenteSeleccionado = document.getElementById("docente").value;
    let mesSeleccionado = document.getElementById("mes").value;
    let añoSeleccionado = document.getElementById("año").value;
    let mensaje = document.getElementById("mensaje-docente");
    let tabla = document.getElementById("datos_asistencias");

    if (docenteSeleccionado === "") {
        mensaje.classList.remove("d-none"); // Muestra el mensaje
        tabla.classList.add("d-none"); // Oculta la tabla
    } else {
        mensaje.classList.add("d-none"); // Oculta el mensaje
        tabla.classList.remove("d-none"); // Muestra la tabla
        cargarAsistencias(docenteSeleccionado, mesSeleccionado, añoSeleccionado);
    }
});

function cargarAsistencias(docente, mes, año) {
    // Verifica si DataTable ya está inicializado y lo destruye antes de recargarlo
    if ($.fn.DataTable.isDataTable("#datos_asistencias")) {
        $("#datos_asistencias").DataTable().destroy();
    }
    
    // Inicializa DataTables con la consulta filtrada por docente, mes y año
    $('#datos_asistencias').DataTable({
        "processing": true,
        "serverSide": true,
        "paging": false,  // Desactiva la paginación
        "searching": false, // Desactiva la barra de búsqueda
        "info": false,
        "ajax": {
            "url": "Asistencias-Controlador.php?accion=Historial",
            "type": "POST", // Usamos POST porque los datos vienen en $_POST
            "data": {
                numero_documento: docente,
                mes: mes,
                año: año
            }
        },
        "columns": [
            { "data": "fecha" },
            { "data": "hora_entrada" },
            { "data": "hora_salida" },
            { 
                "data": null,
                "render": function(data, type, row) {
                    return row.nombres + ' ' + row.apellidos;   
                }
            },
            { "data": "estado" }

        ]
    });
}
