let estado = "";

function handleClick(valor) {
  estado = valor;
  console.log("Estado seleccionado:", estado);
}

$(document).ready(function () {
  var table = $("#datos_cuentacobro_admin").DataTable({
    language: {
      url: "//cdn.datatables.net/plug-ins/1.11.5/i18n/es-ES.json",
    },
    searching: true,
    paging: true,
    lengthChange: true,
    pageLength: 10,
    processing: true,
    serverSide: true,
    ajax: {
      url: "Cuentas-De-Cobro-Controlador.php",
      type: "GET",
      data: function (d) {
        d.estado = estado;
      },
      dataSrc: "data",
    },
    columns: [
      { data: "fecha" },
      {
        data: null,
        render: function (data, type, row) {
          return row.nombres + " " + row.apellidos;
        },
      },
      { data: "horas_trabajadas" },
      { data: "valor_hora" },
      { data: "monto" },
      { data: "total_abonado" },
      {
        data: "estado",
        render: function (data, type, row) {
          if (data === "Aceptada por el docente") {
            return `<button class="btn btn-success w-100 text-white" disabled>Aceptada por docente</button>`;
          } else if (data === "Pendiente de firma") {
            return `<button class="btn btn-warning w-100 text-white" disabled>Pendiente de firma</button>`;
          } else if (data === "En proceso de pago") {
            return `<button class="btn btn-info w-100 text-white" disabled>En proceso de pago</button>`;
          } else if (data === "Pagada") {
            return `<button class="btn btn-success w-100 text-white" disabled>Pagada</button>`;
          } else if (data === "Rechazada por el docente") {
            return `<button class="btn btn-danger w-100 text-white" disabled>Rechazada por docente</button>`;
          } else if (data === "Rechazada por la institucion") {
            return `<button class="btn btn-danger w-100 text-white" disabled>Rechazada por institución</button>`;
          } else {
            return `<span>${data}</span>`;
          }
        },
      },
      {
        data: null,
        defaultContent:
          '<button class="btn btn-primary btn-sm btn-verify">Verificar</button>',
        orderable: false,
        className: "text-center",
      },
    ],
  });

  // Evento para el botón "Verificar"
  $("#datos_cuentacobro_admin").on("click", ".btn-verify", function () {
    var data = table.row($(this).parents("tr")).data();
    verificarCuenta(data.id_cuenta);
  });

  // Evento para el botón "Devolver"
  $("#datos_cuentacobro_admin").on("click", ".btn-return", function () {
    var data = table.row($(this).parents("tr")).data();
    devolverCuenta(data.id_cuenta);
  });

  // Evento para cambiar estado al hacer clic en un botón con clase 'filtro-estado'
  $(document).on("click", ".filtro-estado", function () {
    const nuevoEstado = $(this).data("estado");

    // Toggle: si vuelven a hacer clic en el mismo, se limpia el filtro
    if (estado === nuevoEstado) {
      estado = "";
    } else {
      estado = nuevoEstado;
    }

    console.log("Estado seleccionado:", estado);

    table.ajax.reload(); // 🔄 Forzamos recarga de la tabla
  });
});

function verificarCuenta(idCuenta) {
  $.ajax({
    url: "Cuentas-De-Cobro-Controlador.php?accion=BusquedaPorId",
    type: "POST",
    data: { id_cuenta: idCuenta },
    dataType: "json",
    success: function (response) {
      console.log("Respuesta del servidor:", response);
      if (response.data && response.data.length > 0) {
        var cuenta = response.data[0];

        $("#id_cuenta").val(cuenta.id_cuenta);
        $("#btnFirmado").attr("data-id", cuenta.id_cuenta);
        $("#btnExportar").attr("data-id", cuenta.id_cuenta);
        $("#btnDevolver").attr("data-id", cuenta.id_cuenta);
        $("#btnAbonar").attr("data-id", cuenta.id_cuenta);

        $('[name="fecha"]').text("Cuenta: " + cuenta.fecha);
        $('[name="modalCuentasCobroLabel"]').text(
          cuenta.nombres + " " + cuenta.apellidos
        );

        $('#formCuentaCobro [name="horas_trabajadas"]').val(
          cuenta.horas_trabajadas
        );
        $('[name="cant_horas"]').text(cuenta.horas_trabajadas + " h");

        $('#formCuentaCobro [name="valor_hora"]').val(cuenta.valor_hora);
        $('[name="valor"]').text("$ " + cuenta.valor_hora);

        $('[name="monto"]').text(cuenta.horas_trabajadas * cuenta.valor_hora);
        $('[name="saldo"]').text(
          cuenta.horas_trabajadas * cuenta.valor_hora -
            (cuenta.total_abonado || 0)
        );

        // Llamar a la función para actualizar los botones según el estado
        actualizarBotones(cuenta.estado);

        // Mostrar el modal
        $("#modalCuentasCobro").modal("show");
      } else {
        alert("No se encontraron datos para la cuenta de cobro.");
      }
    },
    error: function () {
      alert("Error al obtener los datos de la cuenta de cobro.");
    },
  });
}

function actualizarBotones(estado) {
  $(
    "#btnModificar, #btnExportar, #btnFirmado, #btnDevolver, #btnAbonar, #horas_trabajadas, #valor_hora, #cant_horas, #valor, #monto_mostrado, #saldo_mostrado, #label_monto_mostrado, #label_saldo_mostrado, #abono, #label_abonar, #label_cant_horas, #label_valor, #valor_abonado"
  ).hide();

  if (estado === "aceptada_docente" || estado === "rechazada_por_docente") {
    $("#btnModificar").show();
    $("#btnDevolver").show();
    $("#horas_trabajadas, #valor_hora").show();
    $("#label_cant_horas, #label_valor").show();
  }
  if (estado === "aceptada_docente") {
    $("#btnExportar").show();
    $("#horas_trabajadas, #valor_hora").show();
    $("#label_cant_horas, #label_valor").show();
  }
  if (estado === "pendiente_firma") {
    $("#btnFirmado").show();
    $("#cant_horas, #valor").show();
    $("#label_cant_horas, #label_valor").show();
    $("#monto_mostrado, #label_monto_mostrado").show();
  }
  if (estado === "proceso_pago") {
    $("#btnAbonar").show();
    $("#monto_mostrado, #saldo_mostrado").show();
    $("#label_monto_mostrado, #label_saldo_mostrado").show();
    $("#valor_abonado, #label_abonar").show();
  }
  if (estado === "pagada") {
    $("#label_cant_horas, #cant_horas").show();
    $("#label_valor, #valor").show(); // Mostrar en estado pagado también
    $("#monto_mostrado, #label_monto_mostrado").show();
  }
}

function Firmar() {
  const btnFirmado = document.getElementById("btnFirmado");
  const idCuenta = btnFirmado ? btnFirmado.getAttribute("data-id") : null;

  if (!idCuenta) {
    console.error("Error: ID de cuenta no encontrado en el botón.");
    return;
  }

  const formData = new FormData();
  formData.append("id_cuenta", idCuenta);

  console.log("Datos enviados:", ...formData.entries());

  $.ajax({
    url: "Cuentas-De-Cobro-Controlador.php?accion=Firmar",
    type: "POST",
    data: formData,
    processData: false,
    contentType: false,
    success: function (response) {
      console.log("Respuesta del servidor:", response);
      location.reload();
    },
    error: function (xhr, status, error) {
      console.error("Error en la solicitud AJAX:", error);
    },
  });
}

function Devolver() {
  const btnDevolver = document.getElementById("btnDevolver");
  const idCuenta = btnDevolver ? btnDevolver.getAttribute("data-id") : null;

  if (!idCuenta) {
    console.error("Error: ID de cuenta no encontrado en el botón.");
    return;
  }

  const formData = new FormData();
  formData.append("id_cuenta", idCuenta);

  console.log("Datos enviados:", ...formData.entries());

  $.ajax({
    url: "Cuentas-De-Cobro-Controlador.php?accion=Devolver",
    type: "POST",
    data: formData,
    processData: false,
    contentType: false,
    success: function (response) {
      console.log("Respuesta del servidor:", response);
      location.reload();
    },
    error: function (xhr, status, error) {
      console.error("Error en la solicitud AJAX:", error);
    },
  });
}

function Abonar() {
  const btnAbonar = document.getElementById("btnAbonar");
  const idCuenta = btnAbonar ? btnAbonar.getAttribute("data-id") : null;

  if (!idCuenta) {
    console.error("Error: ID de cuenta no encontrado en el botón.");
    return;
  }

  const form = document.getElementById("formCuentaCobro");
  const formData = new FormData(form);

  console.log("Datos enviados:", ...formData.entries(form));
  formData.append("id_cuenta", idCuenta);

  $.ajax({
    url: "Cuentas-De-Cobro-Controlador.php?accion=abonar",
    type: "POST",
    data: formData,
    processData: false,
    contentType: false,
    success: function (response) {
      console.log("Respuesta del servidor:", response);
      location.reload();
    },
    error: function (xhr, status, error) {
      console.error("Error en la solicitud AJAX:", error);
    },
  });
}
