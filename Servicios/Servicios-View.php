<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>SERVICIOS</title>
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
  <!-- DataTables CSS -->
  <link rel="stylesheet" href="https://cdn.datatables.net/1.10.25/css/jquery.dataTables.min.css">
  <!-- Bootstrap Icons CSS -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <!-- Custom CSS 
  <link rel="stylesheet" href="../css/style.css">-->
</head>
<body>
<?php include_once '../Componentes/header.php' ?>
  <div>
    <h1 class="text-center">SERVICIOS</h1>
    <div class="row">
      <div class="col-2 offset-10">
        <div class="text-center">
          <button type="button" class="btn btn-primary w-100" data-bs-toggle="modal" data-bs-target="#modalServicio" id="botonCrear">
            <i class="bi bi-plus-circle-fill"></i> Crear
          </button>
        </div>
      </div>        
    </div>
    <br>
    <br>
    <div class="table-responsive">
      <table id="datos_servicio" class="table table-bordered table-striped">
        <thead>
          <tr>
            <th>Codigo</th>
            <th>Descripcion</th>
            <th>Valor total</th>
            <th>Estado</th>
            <th>Editar</th>
            <th>Acciones</th>
          </tr>
        </thead>
      </table>
    </div>
  </div>
  <!-- Modal -->
  <div class="modal fade" id="modalServicio" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h1 class="modal-title fs-5" id="exampleModalLabel">Crear servicio</h1>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form method="POST" id="formulario" enctype="multipart/form-data">
          <div class="modal-content">
            <div class="modal-body">
              
              <label for="descripcion_servicio">Descripcion</label>
              <input type="text" name="descripcion_servicio" id="descripcion_servicio" class="form-control">
              <br>
              <label for="valor_total_servicio">Valor total</label>
              <input type="number" name="valor_total_servicio" id="valor_total_servicio" class="form-control">
              <br>
              <label for="estado">Estado</label>
              <select name="estado" id="estado" class="form-control">
                <option value="2">Seleccione una de las siguientes opciones...</option>
                <option value="Activo">Activo</option>
                <option value="Inactivo">Inactivo</option>
              </select>
              <br>
            </div>
            <div class="modal-footer">
              <input type="hidden" name="codigo_servicio" id="codigo_servicio">
              <input type="hidden" name="operacion" id="operacion">
              <input type="submit" name="action" id="action" class="btn btn-primary" value="Crear">
            </div>
          </div>
        </form>
      </div>
    </div>
    <?php include_once '../Componentes/footer.php' ?>
  </div>

  <!-- jQuery -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
  <!-- DataTables JavaScript -->
  <script src="//cdn.datatables.net/1.10.25/js/jquery.dataTables.min.js"></script>
  <!-- Bootstrap JavaScript -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
  <script type="text/javascript">
    $(document).ready(function() {
      $("#botonCrear").click(function() {
        $("#formulario")[0].reset();
        $(".modal-title").text("Crear servicio");
        $("#action").val("Crear").removeClass('btn-success').addClass('btn-primary');
        $("#operacion").val("crear");
      });

      var dataTable = $('#datos_servicio').DataTable({
  "processing": true,
  "serverSide": true,
  "order": [],
  "ajax": {
    url: "Servicios-Controller.php",
    type: "POST"
  },
  "columnDefs": [
    { "targets": "_all", "className": "text-center" },
    {
    "targets": 2, // Índice de la columna "valor total"
    "render": function (data, type, row) {
      // Formatea el valor numérico a formato de moneda con el símbolo "$" y puntuación de miles
      return '$' + parseFloat(data).toLocaleString('es-ES', {minimumFractionDigits: 2});
    }
  }, {
    "targets": [4],
    "orderable": false,
  }]
});

$(document).on('click', '.btn-toggle-state', function(){
  var id = $(this).data('id');
    var estadoActual = $(this).data('estado');
    var nuevoEstado = (estadoActual === "Activo") ? 0 : 1;

     // Ejemplo de cómo podrías usar estos datos
    // console.log("ID:", id, "Estado actual:", estadoActual, "Nuevo estado:", nuevoEstado);
    $.ajax({

      url:'Servicios-Controller.php',
      method:'POST',
      type:'json',
      data:{id_programa: id, estado: nuevoEstado,
        operacion:'cambiarEstado'
      },
      success:function(data){
        alert(`El estado del programa se ha actualizado a ${nuevoEstado === 1 ? "Activo" : "Inactivo"}.`);
                dataTable.ajax.reload();
      },
      error:function(){
        alert("Hubo un error al cambiar el estado.");
      }


    });

});


      $(document).on('submit', '#formulario', function(event) {
        event.preventDefault();
        var codigo_servicio = $("#codigo_servicio").val();
        var descripcion_servicio = $("#descripcion_servicio").val();
        var valor_total_servicio = $("#valor_total_servicio").val();
        var estado = $("#estado").val();
        
        if (descripcion_servicio != '' && valor_total_servicio != '' && estado != '') {
          $.ajax({
            url: "Servicios-Controller.php",
            method: 'POST',
            data: new FormData(this),
            contentType: false,
            processData: false,
            success: function(data) {
              alert(data);//muestra la respuesta del servidor
             
             //limpia y cierra el modal
              $('#formulario')[0].reset();
              $('#modalServicio').modal('hide');
              $('.modal-backdrop').remove();//Se asegura que se quite la pantalla gris 
              
              //Recarga los datos de la tabla
              dataTable.ajax.reload(null, false);// sin reiniciar la paginacion
            },

            error: function(jqXHR, textStatus, errorThrown) {
                console.error('Error:', textStatus, errorThrown);
                alert("Hubo un error al procesar la solicitud.");
            }

          });
        } else {
          alert("Todos los campos son obligatorios");
        }
      });

      // Funcionalidad de editar
      $(document).on('click', '.editar', function() {
        var codigo_servicio = $(this).attr("id");
        
        $.ajax({
          url: "Servicios-Controller.php",
          method: "POST",
          data: {codigo_programa: codigo_servicio,
            operacion:'obtener_registro'},
          dataType: "json",
          success: function(data) {
            console.log(data);
            $('#modalServicio').modal('show');
            $('#descripcion_servicio').val(data.nombre);
            $('#valor_total_servicio').val(data.cant_modulos);
            $('#estado').val(data.estado);
            $('.modal-title').text("Editar servicio");
            console.log("Código de servicio asignado:", codigo_servicio);
            $('#codigo_servicio').val(codigo_servicio);
            $('#action').val("Editar").removeClass('btn-primary').addClass('btn-success');
            $('#operacion').val("editar");
          },
          error: function(jqXHR, textStatus, errorThrown) {
            console.log(textStatus, errorThrown);
          }
        });
      });

      // Funcionalidad de borrar
      /*$(document).on('click', '.borrar', function() {
        var codigo_servicio = $(this).attr("id");
        if (confirm("¿Estás seguro de borrar este registro: " + codigo_servicio + "?")) {
          $.ajax({
            url: "servicios.controller.php",
            method: "POST",
            data: {codigo_servicio: codigo_servicio,operacion:'borrar'},
            success: function(data) {
              alert(data);
              dataTable.ajax.reload();
            }
          });
        }
      });*/
    });
  </script>
  
</body>
</html>