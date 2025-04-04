<?php //include("./Convenios.datos.php"); ?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>CONVENIOS</title>
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
  <!-- DataTables CSS -->
  <link rel="stylesheet" href="https://cdn.datatables.net/1.10.25/css/jquery.dataTables.min.css">
  <!-- Bootstrap Icons CSS -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <!-- Custom CSS -->
  <link rel="stylesheet" href="../css/style.css">
</head>

<body>
  <?php include_once '../componentes/header.php';
  include("../Conexion.php");

  $sqlEstudiante="SELECT codigo_estudiante, nombre_estudiante, apellidos_estudiante FROM estudiantes WHERE estado = 1";
  $resultEstudiantes = $conn->query($sqlEstudiante);
  $sqlPrograma="SELECT id_programa, nombre FROM programas WHERE estado = 1";
  $resultPrograma = $conn->query($sqlPrograma);
  $sqlDescuento="SELECT codigo_tipo_convenio, descripcion_tipo_usuario, valor_descuento FROM tipo_convenio WHERE estado = 1";
  $resultDesct = $conn->query($sqlDescuento);
  
  
  ?>
  <h1 class="text-center">CONVENIOS</h1>

  <br>
  <div class="row">
    <div class="col-2 offset-10">
      <div class="text-center">
        <button type="button" class="btn btn-primary btn-block w-100" data-bs-toggle="modal" data-bs-target="#modalCrearConvenio" id="botonCrear">
        <i class="bi bi-plus-circle-fill"></i> Crear
        </button>
      </div>
    </div>
  </div>


  <br>
  <div class="table-responsive">
    <table id="datos_convenios" class="table table-bordered table-striped">
      <thead>
        <tr>
          <th>CODIGO</th>
          <!-- <th>CODIGO ESTUDIANTE</th>-->

          <th>ESTUDIANTE</th>
          <th>APELLIDOS</th>
          <!-- <th>CODIGO CARRERA</th>-->
          <th>CARRERA</th>
          <th>CONVENIO</th>
          <th>VALOR DESCUENTO</th>
          <th>VALOR TOTAL </th>
          <Th>TOTAL CALCULADO</Th>
          <th>SALDO TOTAL</th>
          <th>ESTADO</th>
          <th>Edición</th>
          <th>Acciones</th>
          <th>+Infos</th>
        </tr>
      </thead>
    </table>
  </div>
  </div>
  <div class="text-center">


    <!--  <button type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#modalEditarConvenio" id="botonEditar">
      Editar
    </button>-->
    <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalExcelConveni" id="botonExcel">
      Excel
    </button>
  </div>
  <?php
  include("../componentes/footer.php");
  ?>

  <!-- Modal Crear Convenio -->
  <div class="modal fade" id="modalCrearConvenio" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h1 class="modal-title fs-5" id="exampleModalLabel">CREAR CONVENIO</h1>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form method="POST" id="formulario" enctype="multipart/form-data">
          <div class="modal-content">
            <div class="modal-body">
              <label for="codigo_convenio">Codigo</label>
              <input type="text" name="codigo_convenio" id="codigo_convenio" class="form-control">
              <br>

              <label for="descripcion_convenio">descripcion_convenio</label>
              <input type="text" name="descripcion_convenio" id="descripcion_convenio" class="form-control">
              <br>

              <!--<label for="codigo_estudiante">codigo estudiante</label>
              <input type="text" name="codigo_estudiante" id="codigo_estudiante" class="form-control">
              <br> -->

              <label for="codigo_estudiante">codigo estudiante</label>
              <select name="codigo_estudiante" id="codigo_estudiante" class="form-control">
                <option value="">Seleccione una opciones</option>
                <?php

                
                if($resultEstudiantes->num_rows > 0){
                  while($row = $resultEstudiantes->fetch_assoc()){
                    echo "<option value='" . $row['codigo_estudiante'] ."'>" . $row['nombre_estudiante'] . " " . $row['apellidos_estudiante'] . "</option>";
                  }
                }

                ?>
                
              </select>
              <br>


              <!--
              <label for="codigo_In_servicio">codigo servicios</label>
              <input type="text" name="codigo_In_servicio" id="codigo_In_servicio" class="form-control">
              <br>-->


              <label  for="codigo_In_servicio">Codigo servicios</label>
              
              <select name="codigo_In_servicio" id="codigo_In_servicio" class="form-control">
                <option value="">Seleccione un servicio</option>
                <?php 
                  if($resultPrograma->num_rows >0){ 

                    while ($row =$resultPrograma->fetch_assoc()){
                      echo "<option value='" . $row['id_programa'] . "'>" . $row['nombre'] . "</option>";
                    }

                  }
                ?>

                                
                
              </select>
              <br>

              <label for="tipo_convenio">tipo convenio</label>
              <select name="tipo_convenio" id="tipo_convenio" class="form-control">
                <option value="">Seleccione el descuento</option>
                  <?php
                    if($resultDesct->num_rows>0){
                      while($row=$resultDesct->fetch_assoc()){
                          echo "<option value='" . $row['codigo_tipo_convenio'] . "'>" . $row['descripcion_tipo_usuario'] . " " . $row['valor_descuento'] . " %" . "</option>";
                      }
                    }
                  ?>


              </select>

              <!--
              <label for="tipo_convenio">tipo convenio</label>
              <input type="text" name="tipo_convenio" id="tipo_convenio" class="form-control">
              <br>-->

              <label for="valor_total_convenio">Valor total</label>
              <input type="number" name="valor_total_convenio" id="valor_total_convenio" class="form-control">
              <br>

              <label for="saldo_convenio">saldo total</label>
              <input type="number" name="saldo_convenio" id="saldo_convenio" class="form-control">
              <br>




              <br>
             <!-- 

              <label for="estado">Estado</label>
              <select name="estado" id="estado" class="form-control">
                <option value="">Seleccione una opciones</option>
                <option value="Activo">Activo</option>
                <option value="Inactivo">Inactivo</option>
              </select>
              <br>-->
             



            </div>
            <div class="modal-footer">
              <input type="hidden" name="id_convenio" id="id_convenio">
              <input type="hidden" method="POST" name="operacion" id="operacion">
              <input type="submit" name="action" id="action" class="btn btn-primary" value="crear">

            </div>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Modal -->
  <div class="modal fade" id="modalInfoEstudiante" tabindex="-1" aria-labelledby="nuevoModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-scrollable modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="nuevoModalLabel">Pagos</h5>
          <button type="button" class="btn btn-light btn-close" style="position: absolute; top: 0.5rem; right: 1.5rem; padding: 0.5rem 1rem; border: 0; background: 0; font-size: 1.5rem; cursor: pointer; outline: 0;" data-bs-dismiss="modal" aria-label="Close">
            <i class="bi bi-x-lg"></i>
          </button>
        </div>
        <form method="POST" id="formEstudiant" enctype="multipart/form-data">
          <div class="modal-content">
            <div class="modal-body">
              <div class="row w-100">
                <div class="col-md-6">

                  <label for="codigo_estudiant">Codigo estudiante</label>
                  <input type="text" name="codigo_estudiant" id="codigo_estudiant" class="form-control" readonly>

                </div>
                <div class="col-md-6 text-end">
                  <img src="" alt="Imagen del estudiante" name="imagen" id="imagen" class="img-fluid" height="50%" width="50%">
                </div>
              </div>
              <div class="row">
                <div class="col-md-12">
                  <label for="nombre_estudiante">Nombre estudiante</label>
                  <input type="text" name="nombre_estudiante" id="nombre_estudiante" class="form-control"  readonly>

                </div>
              </div>
              <div class="row">
                <div class="col-md-12">
                  <label for="apellidos_estudiante">Apellidos</label>
                  <input type="text" name="apellidos_estudiante" id="apellidos_estudiante" class="form-control"  readonly>

                </div>
              </div>
              <div class="row">
                <div class="col-md-12">
                  <label for="fecha_naci_estu">Fecha nacimiento</label>
                  <input type="text" name="fecha_naci_estu" id="fecha_naci_estu" class="form-control"  readonly>

                </div>
              </div>
              <div class="row">
                <div class="col-md-12">
                  <label for="carrera_estudiante">Carrera</label>
                  <input type="text" name="carrera_estudiante" id="carrera_estudiante" class="form-control" readonly>
                  <br>
                </div>
              </div>

              <div class="table-responsive">
                <table id="datos_pagos_estudiante" class="table table-bordered table-striped">
                  <thead>
                    
                    <tr>
                      <th>CODIGO <br>DE PAGO </th>
                      <th>FECHA</th>
                      <th>DESCRIPCION</th>
                      <th>VALOR CUOTAS </th>
                      <th>SELECCIONAR</th>
                    </tr>
                  </thead>
                  <tbody>
                    <!-- Aquí se mostrarán los pagos -->
                    <?php include("./Convenios-Util.php"); ?>
                  </tbody>
                </table>
              </div>

              <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="btn-pagar" data-bs-toggle="modal" data-bs-target="#modalExcelConvenio">Pagar</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                <input type="hidden" name="codigo_estudiante" id="codigo_estudiante">

              </div>
            </div>
          </div>
      </div>

      <!-- Modal Confirmar Pago Convenio -->
      <div class="modal fade" id="modalExcelConvenio" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <h1 class="modal-title fs-5" id="exampleModalLabel">CONFIRMA LA INFORMACION</h1>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" id="formulario" enctype="multipart/form-data">
              <div class="modal-content">
                <div class="modal-body">
                  <h3>Se pagara lo siguiente: </h3>

                  <div class="table-responsive">
                    <table id="datos_confirmacion_pago" class="table table-bordered table-striped">
                      <thead>
                        <tr>
                          <th>C</th>
                          <th>FECHA</th>
                          <th>VALOR</th>
                        </tr>
                      </thead>
                      <!-- Valores de prueba (cuota,fecha,valor) -->
                      <tbody>
                        <tr>
                          <td>1</td>
                          <td>01/01/24</td>
                          <td>120000</td>
                        </tr>
                        <tr>
                          <td>2</td>
                          <td>01/02/24</td>
                          <td>120000</td>
                        </tr>
                    </table>
                    <div class="col-5 offset-7">
                      <h6>TOTAL: </h6>
                      <div class="card">
                        <div class="card-body">
                          $ 240000
                        </div>
                      </div>
                    </div>


                  </div>
                  <div class="modal-footer">
                    <input type="submit" name="action" id="action" class="btn btn-success" value="Pagar">
                  </div>
                </div>
            </form>
          </div>
        </div>
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
            $(".modal-title").text("Crear Convenio");
            $("#action").val("crear").removeClass('btn-success').addClass('btn-primary');
            $("#operacion").val("crear");
          });

    
          //query dataTable convenios
          var dataTable = $('#datos_convenios').DataTable({
            language: {
              url: '//cdn.datatables.net/plug-ins/2.0.2/i18n/es-MX.json',
            },
            "processing": true,
            "serverSide": true,
            "order": [],
            "ajax": {
              url: "Convenios-Controller.php",
              type: "POST"
            },
            "columnDefs": [{
                "targets": "_all",
                "className": "text-center"
              },
              {
                "targets": [6, 7], // Índice de la columna "valor total"
                "render": function(data, type, row) {
                  // Formato de moneda con el símbolo "$" y puntuación de miles
                  return '$' + parseFloat(data).toLocaleString('es-ES', {
                    minimumFractionDigits: 2
                  });
                }
              }, {
                "targets": [3, 4],
                "orderable": false,
              }
            ]

          });


          $(document).on('submit', '#formulario', function(event) {
            event.preventDefault();
            var codigo_convenio = $("#codigo_convenio").val();
            var descripcion_convenio = $("#descripcion_convenio").val();
            var codigo_estudiante = $("#codigo_estudiante").val();
            var tipo_convenio = $("#tipo_convenio").val();
            var codigo_In_servicio = $("#codigo_In_servicio").val();
            var valor_total_convenio = $("#valor_total_convenio").val();
            var saldo_convenio = $("#saldo_convenio").val();

          


            //var estado = $("#estado").val();

            if (codigo_convenio != '' && descripcion_convenio != '' && valor_total_convenio != '' && codigo_estudiante != '') {
              
              $.ajax({
                url: "Convenios-Controller.php",
                method: "POST",
                data: new FormData(this),
                processData: false,
                contentType: false,
                success: function(data) {
                 // console.log("informacion del crear" , data);
                  $('#formulario')[0].reset();
                  $('#modalCrearConvenio').modal('hide');
                  $('.modal-backdrop').remove();
                  dataTable.ajax.reload();
                }


              });
            } else {
              alert("Algunos campos son obligatorios");
            }

          });

          /*Script ajax para la consulta de los registros mandando 
          como referencia el codigo de convenio*/
          $(document).on('click', '.info', function(){
            var codigo_convenio = $(this).attr("id");
            $.ajax({
              url: "Convenios-Function.php",

              method:"POST",
              data:{
                codigo_convenio: codigo_convenio,
                operacion:'registro_individual'
              },
              dataType: "json",
              success: function(data) {
                console.log("este es el nombre" + data.nombre_estudiante)
                
                /*valida y rellena la informacion con la data recibida de la DB*/
                $('#modalInfoEstudiante').modal('show');
                $('#codigo_estudiant').val(data.codigo_estudiante);
                $('#nombre_estudiante').val(data.nombre_estudiante);
                $('#apellidos_estudiante').val(data.apellidos_estudiante);
                $('#fecha_naci_estu').val(data.fecha_nacimiento_estudiante);
                $('#carrera_estudiante').val(data.nombre);
                //Actualiza la imagen del estudiante
                $('#imagen').attr('src', data.imagen); // Ruta de la imagen desde la base de datos
                


                $('#modal-title').text('info estudiante');
                $('#id_convenio').val(codigo_convenio);
                //$('#action').val('registro_individual').removeClass('btn-primary').addClass('btn-success');
                //$('#operacion').val("registro_individual");


              },
              error: function(jqXHR, textStatus, errorThrown) {
                console.log(textStatus, errorThrown);
              }


            })

            $.ajax({
              url:"Convenios-Util.php",
              method:"POST",
              data:{
                codigo_convenios: codigo_convenio,
                
              },
            })
          })

          $(document).on('click', '.btn-toggle-state', function(){
            var id = $(this).data('id');
            var estadoActual = $(this).data('estado');
            var nuevoEstado = (estadoActual == "Activo") ? 0 : 1;

            $.ajax({
              url:'Convenios-Controller.php',
              method:'POST',
              type:'json',
              data:{codigo_convenio:id, estado:nuevoEstado,
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

          $(document).on('click', '.editar', function() {
            //$("#botonEditar").click(function(){  
            var codigo_convenio = $(this).attr("id");
            $.ajax({

              url: "Convenios-Controller.php",
              method: "POST",
              data: {
                codigo_convenio: codigo_convenio,
                operacion: 'obtener_registro'

              },
              dataType: "json",
              success: function(data) {
                //onsole.log(data);

                $('#modalCrearConvenio').modal('show');
                $('#codigo_convenio').val(data.codigo_convenio);
                $('#descripcion_convenio').val(data.descripcion_convenio);
                $('#valor_total_convenio').val(data.valor_total_convenio);
                $('#saldo_convenio').val(data.saldo_convenio);
                $('#codigo_In_servicio').val(data.id_programa);
                $('#codigo_estudiante').val(data.codigo_estudiante);
                //$('#estado').val(data.estado);
                $('#tipo_convenio').val(data.tipo_fk_convenio);


                $('#modal-title').text('Editar estudiante');
                $('#id_convenio').val(codigo_convenio);
                $('#action').val('editar').removeClass('btn-primary').addClass('btn-success');
                $('#operacion').val("editar");


              },
              error: function(jqXHR, textStatus, errorThrown) {
                console.log(textStatus, errorThrown);
              }





            });



          });



         



        })
      </script>

</body>

</html>