<?php

?>
<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>UDEV</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">

  <link rel="stylesheet" href="https://cdn.datatables.net/1.10.25/css/jquery.dataTables.min.css">

  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css">

 

</head>


</head>

<body>
  <?php include_once '../Componentes/header.php' ?>

  
  <div class ="container">
  <h1 class="text-center">Gestion de estudiantes</h1>
    <div class="row">
      <div class="col-2 offset-10">
        <div class="text-center">
          <!-- Button trigger modal -->
          <button type="button" class="btn btn-primary w-100" data-bs-toggle="modal" data-bs-target="#modalUsuario"
            id="botonCrear">
            <i class="bi bi-plus-circle-fill"></i> Crear
          </button>



        </div>
      </div>
    </div>
    <br>
    <div class="card">
  <div class="card-header">
    <h5>Estudiantes</h5>
  </div>
  <div class="card-body">
    <div class="tabla-responsive">
      <table id="Estudiantes" name="Estudiantes" class="table table-bordered table-striped">
        <thead>
          <tr>
            <th>Codigo</th>
            <th>Nombre</th>
            <th>Apellido</th>
            <th>Fecha nacimiento</th>
            <th>Imagen</th>
            <th>Estado</th>
            <th>Editar</th>
            <th>Acciones</th>
          </tr>
        </thead>
      </table>
    </div>
  </div>
</div>


    <!-- Modal -->
    <div class="modal fade" id="modalUsuario" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h1 class="modal-title fs-5" id="exampleModalLabel">Crear estudiante</h1>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>

          <form method="post" id="formulario" enctype="multipart/form-data">
            <div class="modal-contant">
              <div class="modal-body">

                <!--
                <label for="codigo_estudiante">Codigo</label>
                <input type="number" name="codigo_estudiante" id="codigo_estudiante" class="form-control">
                <br>
-->

                <label for="nombre">Nombre</label>
                <input type="text" name="nombre" id="nombre" class="form-control">
                <br>
                <label for="apellidos"> Apellidos</label>
                <input type="text" name="apellidos" id="apellidos" class="form-control">
                <br>
                <label for="fecha_nacimiento_estudiante">Fecha de nacimiento</label>
                <input type="date" name="fecha_nacimiento_estudiante" id="fecha_nacimiento_estudiante"
                  class="form-control">
                <br>

                <label for="imagen_estudiante">Seleccione una imagen</label>
                <input type="file" name="imagen_estudiante" id="imagen_estudiante" class="form-control">
                <span id="imagen_subida"></span>


              </div>
              <div class="modal-footer">
                <input type="hidden" name="imagen_estudiante_oculta" id="imagen_estudiante_oculta"
                  value="<?php echo $fila['imagen']; ?>">

                <input type="hidden" name="codigo_estudiante" id="codigo_estudiante">
                <input type="hidden" method="POST" name="operacion" id="operacion">
                <input type="button" class="btn btn-secondary" data-bs-dismiss="modal" value="Cancelar">
                <input type="submit" name="action" id="action" class="btn btn-primary" value="Ingresar">
                <?php $operacion = "mostrar" ?>



              </div>
          </form>
        </div>

      </div>
    </div>
  </div>

  <?php


  include ("../Componentes/footer.php");

  ?>



  </div>
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"
    integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>

  <script src="//cdn.datatables.net/1.10.25/js/jquery.dataTables.min.js"></script>

  <!-- Optional JavaScript; choose one of the two! -->

  <!-- Option 1: Bootstrap Bundle with Popper -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM"
    crossorigin="anonymous"></script>

  <script type="text/javascript">
    $(document).ready(function () {
      $("#botonCrear").click(function () {
        $("#formulario")[0].reset();
        $(".modal-title").text("crear estudiante");
        $("#action").val("crear").removeClass('btn-success').addClass('btn-primary');
        $("#operacion").val("crear");
        $("#imagen_subida").html("");



      });

      var dataTable = $('#Estudiantes').DataTable({
        "processing": true,
        "serverSide": true,
        "order": [],
        "ajax": {
          url: "Estudiantes-Controller.php",
          type: "POST"

        },

        "columnDefs": [{
          "targets": [0, 3, 4],
          "orderable": false,
        },]
      });
//funcionalidad de creacion 
      $(document).on('submit', '#formulario', function (event) {
        event.preventDefault();
        var codigo = $("#codigo_estudiante").val();
        var nombres = $("#nombre_estudiante").val();
        var apellidos = $("#apellidos_estudiante").val();
        var fecha_nacimiento_estudiante = $("#fecha_nacimiento_estudiante").val();
        var extension = $('#imagen_estudiante').val().split('.').pop().toLowerCase();
        if (extension != '') {
          if (jQuery.inArray(extension, ['gif', 'png', 'jpg', 'jpeg']) == -1) {
            alert("formato de imagen invalido");
            $('#imagen_estudiante').val('');
            return false;

          }


        }
        if (nombres != '' && apellidos != '' && fecha_nacimiento_estudiante != '') {
          $.ajax({
            url: "Estudiantes-Controller.php",
            method: 'POST',
            data: new FormData(this),
            contentType: false,
            processData: false,
            success: function (data) {

              alert(data);
              $('#formulario')[0].reset();
              $('#modalUsuario').modal('hide');
              $('.modal-backdrop').remove();
              dataTable.ajax.reload();
            }

          });
        } else {
          alert("algunos campos son obligatorios");
        }
      });

      //funcionalidad cambio de estado
      $(document).on('click', '.btn-toggle-state', function(){
        var id = $(this).data('id');
        var estadoActual = $(this).data('estado');
        var nuevoEstado = (estadoActual == "Activo") ? 0: 1;
        
        $.ajax({

          url:'Estudiantes-Controller.php',
          method:'POST',
          type:'json',
          data:{codigo_estudiante: id, estado: nuevoEstado,
          operacion:'cambiarEstado'
        },
        success:function(data){
          alert(`El estado del programa se ha actualizado a ${nuevoEstado === 1 ? "Activo" : "Inactivo"}.`);
          location.reload();
                    
        },
        error:function(){
            alert("Hubo un error al cambiar el estado.");
        }
        });
      });


      //funcionanlidad editar
      $(document).on('click', '.editar', function () {
        var codigo_estudiante = $(this).attr("id");

        $.ajax({
          url: "Estudiantes-Controller.php",
          method: "POST",
          data:
          {
            codigo_estudiante: codigo_estudiante,
            operacion: 'obtener_registro'
          },

          dataType: "json",
          success: function (data) {
            console.log(data);
            $('#modalUsuario').modal('show');
            $('#nombre').val(data.nombre_estudiante);
            $('#apellidos').val(data.apellidos_estudiante);
            $('#fecha_nacimiento_estudiante').val(data.fecha_nacimiento_estudiante);
            $(".modal-title").text("Editar estudiante");
            $('#imagen_subida').val(data.imagen_estudiante);
            $('#estado').val(data.estado);
            $('#action').val("editar").removeClass('btn-primary').addClass('btn-success');
            $('#codigo_estudiante').val(codigo_estudiante);
            $('#operacion').val("editar");

            // Asegúrate de reiniciar el campo de imagen oculta si es necesario
            $('#imagen_estudiante_oculta').val(data.imagen_estudiante);
          },
          error: function (jqXHR, textStatus, errorThrown) {
            console.error("Error en la solicitud AJAX:", textStatus, errorThrown);
          }
        });
      });

      //Funcionalida de borrar
      $(document).on('click', '.borrar', function () {
        var codigo_estudiante = $(this).attr("id");
        if (confirm("¿Estás seguro de borrar este registro: " + codigo_estudiante + "?")) {
          $.ajax({
            url: "estudiantes.controller.php",
            method: "POST",
            data: {
              codigo_estudiante: codigo_estudiante,
              operacion: 'borrar'
            },
            success: function (data) {
              alert(data);
              dataTable.ajax.reload();
            }
          });
        }
      });
    });
  </script>


</body>

</html>