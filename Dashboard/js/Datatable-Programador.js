$(document).ready(function () {
  var table = $("#datos_programador").DataTable({
    ajax: {
      url: "Clases-Controlador.php",
      dataSrc: "data",
    },
    columns: [
      { data: "horario" },
      { data: "nombre_completo" },
      { data: "nombre" },
      { data: "nombre_salon" },
    ],
  });
});
