document.addEventListener("DOMContentLoaded", function () {
  var calendarEl = document.getElementById("calendar");

  var calendar = new FullCalendar.Calendar(calendarEl, {
    initialView: "dayGridMonth",
    headerToolbar: {
      left: "prev,next today",
      center: "title",
      right: "dayGridMonth,timeGridWeek,timeGridDay,listWeek",
    },
    locale: "es",
    events: "Programador-Controlador.php",
    eventTimeFormat: {
      hour: "numeric",
      minute: "2-digit",
      meridiem: true,
      hour12: true,
    },

    eventDidMount: function (info) {
      console.log("Evento cargado:", info.event.title);

      const estado = info.event.extendedProps.estado;
      info.el.classList.remove(
        "btn",
        "btn-sm",
        "btn-primary",
        "btn-success",
        "btn-danger",
        "btn-warning"
      );
      info.el.classList.add("btn", "btn-sm", "w-100");

      switch (estado) {
        case "Perdida":
          info.el.classList.add("btn-danger");
          break;
        case "Pendiente":
          info.el.classList.add("btn-success");
          break;
        case "Reprogramada":
          info.el.classList.add("btn-warning", "text-dark");
          break;
        case "Vista":
          info.el.classList.add("btn-primary");
          break;
        default:
          info.el.classList.add("btn-primary");
      }

      info.el.style.color = "white";
    },

    eventClick: function (info) {
      const idProgramador = info.event.extendedProps.id_programador;
      const estado = info.event.extendedProps.estado;

      if (estado === "Perdida") {
        $.ajax({
          url: "Programador-Controlador.php?accion=BusquedaPorId",
          type: "POST",
          data: { id_programador: idProgramador },
          dataType: "json",
          success: function (response) {
            console.log("Respuesta recibida:", response);
            if (response.data && response.data.length > 0) {
              const programador = response.data[0];
              $("#id_programador").val(idProgramador);
              $("#numero_documento").val(programador.numero_documento);
              $("#id_salon").val(programador.id_salon);
              $("#id_modulo").val(programador.id_modulo);
              $("#id_periodo").val(programador.id_periodo);
              $("#modalReprogramar").modal("show");
            }
          },
          error: function (xhr) {
            console.error("Error en la petición AJAX:", xhr.responseText);
          },
        });
      } else if (estado === "Pendiente") {
        $.ajax({
          url: "Programador-Controlador.php?accion=BusquedaPorId",
          type: "POST",
          data: { id_programador: idProgramador },
          dataType: "json",
          success: function (response) {
            console.log("Respuesta recibida:", response);
            if (response.data && response.data.length > 0) {
              const programador = response.data[0];
              $('#editarClaseForm [name="id_programador"]').val(
                programador.id_programador
              );
              $('#editarClaseForm [name="fecha"]').val(programador.fecha);
              $('#editarClaseForm [name="hora_inicio"]').val(
                programador.hora_inicio
              );
              $('#editarClaseForm [name="hora_salida"]').val(
                programador.hora_salida
              );
              $('#editarClaseForm [name="id_salon"]').val(programador.id_salon);
              $('#editarClaseForm [name="numero_documento"]').val(
                programador.numero_documento
              );
              $('#editarClaseForm [name="id_modulo"]').val(
                programador.id_modulo
              );
              $('#editarClaseForm [name="modalidad"]').val(
                programador.modalidad
              );
              $('#editarClaseForm [name="estado"]').prop(
                "checked",
                String(programador.estado) === "1"
              );

              $("#modalEditarClase").modal("show");
            } else {
              alert("No se encontraron datos para esta clase.");
            }
          },
          error: function (xhr) {
            console.error("Error en la petición AJAX:", xhr.responseText);
          },
        });
      } else {
        console.log("No se permite editar este evento con estado:", estado);
        // alert('Este evento no se puede editar.');
      }
    },
  });

  calendar.render();
});
