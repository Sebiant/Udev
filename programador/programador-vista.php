<?php
include_once '../componentes/header.php';
include '../conexion.php';

$sql_docentes = "SELECT numero_documento, nombres, apellidos FROM docentes";
$result_docentes = $conn->query($sql_docentes);

$sql_salones = "SELECT id_salon, nombre_salon FROM salones";
$result_salones = $conn->query($sql_salones);

$sql_periodos = "SELECT id_periodo, nombre FROM periodos";
$result_periodos = $conn->query($sql_periodos);
?>

  <div class="container mt-5">
    <!-- Card principal que engloba toda la interfaz -->
    <div class="card">
      <div class="card-header">
        <h2>Programación de Clases</h2>
      </div>
      <div class="card-body">
        <!-- Sección: Selección de Datos -->
        <div class="mb-4">
          <h4 class="mb-3">Selección de Datos</h4>
          <form>
            <!-- Periodo -->
            <div class="form-group">
              <label for="periodo">Periodo</label>
              <select id="periodo" class="form-control">
                <option value="">Seleccione Periodo</option>
                <option value="2023-1">2023-1</option>
                <option value="2023-2">2023-2</option>
              </select>
            </div>
            
            <!-- Materias en formato de cuadrícula -->
            <div class="form-group">
              <label>Seleccione Materia</label>
              <div class="row" id="materiasContainer">
                <!-- Las tarjetas se cargarán dinámicamente aquí -->
              </div>
            </div>
            
            <!-- Docentes -->
            <div class="form-group">
              <label for="docente">Docentes</label>
              <select id="docente" class="form-control">
                <option value="">Seleccione Docente</option>
                <option value="docente1">Docente 1</option>
                <option value="docente2">Docente 2</option>
              </select>
            </div>
          </form>
        </div>
        <hr>
        <!-- Sección: Programación de Horario y Modalidad -->
        <div>
          <h4 class="mb-3">Programación de Horario y Modalidad</h4>
          <form>
            <div class="form-group">
              <label for="dia">Día de la Semana</label>
              <select id="dia" class="form-control">
                <option value="">Seleccione Día</option>
                <option value="lunes">Lunes</option>
                <option value="martes">Martes</option>
                <option value="miercoles">Miércoles</option>
                <option value="jueves">Jueves</option>
                <option value="viernes">Viernes</option>
                <option value="sabado">Sábado</option>
                <option value="domingo">Domingo</option>
              </select>
            </div>
            <div class="form-group">
              <label for="horaEntrada">Hora de Entrada</label>
              <input type="time" id="horaEntrada" class="form-control">
            </div>
            <div class="form-group">
              <label for="horaSalida">Hora de Salida</label>
              <input type="time" id="horaSalida" class="form-control">
            </div>
            <div class="form-group">
              <label for="modalidad">Modalidad</label>
              <textarea id="modalidad" class="form-control" rows="3" placeholder="Ingrese la modalidad"></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Programar Clase</button>
          </form>
        </div>
      </div>
    </div>
  </div>
<div class="container mt-4">
    <div class="card">
        <div class="card-header">
            <h5>Clases Programadas</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table id="datos_programador" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Fecha</th>
                            <th>Hora de Inicio</th>
                            <th>Hora de Salida</th>
                            <th>Salón</th>
                            <th>Docente</th>
                            <th>Materia</th>
                            <th>Estado</th>
                            <th>Modalidad</th>
                            <th>Modificar</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Aquí irían las filas de la tabla -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal de edición -->
<div class="modal fade" id="modalEditarClase" tabindex="-1" aria-labelledby="modalEditarClaseLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Modificar Clase</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editarClaseForm">
                    <input type="hidden" id="id_programador" name="id_programador">

                    <div class="mb-3">
                        <label for="fecha">Fecha</label>
                        <input type="date" id="fecha" name="fecha" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label for="hora_inicio">Hora de Inicio</label>
                        <input type="time" id="hora_inicio" name="hora_inicio" class="form-control">
                    </div>

                    <div class="mb-3">
                        <label for="hora_salida">Hora de Salida</label>
                        <input type="time" id="hora_salida" name="hora_salida" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label for="id_salon">Salón</label>
                        <select id="id_salon" name="id_salon" class="form-control">
                            <option value="">-- Selecciona un salón --</option>
                            <?php
                            $sql_salones = "SELECT id_salon, nombre_salon FROM salones";
                            $result_salones = $conn->query($sql_salones);

                            if ($result_salones->num_rows > 0) {
                                while ($row_salon = $result_salones->fetch_assoc()) {
                                    echo '<option value="' . $row_salon['id_salon'] . '">' . $row_salon['nombre_salon'] . '</option>';
                                }
                            } else {
                                echo '<option value="">No hay salones disponibles</option>';
                            }
                            ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="numero_documento">Docente</label>
                        <select id="numero_documento" name="numero_documento" class="form-control">
                            <option value="">-- Selecciona un docente --</option>
                            <?php
                            $sql_docentes = "SELECT numero_documento, nombres, apellidos FROM docentes";
                            $result_docentes = $conn->query($sql_docentes);

                            if ($result_docentes->num_rows > 0) {
                                while ($row_docente = $result_docentes->fetch_assoc()) {
                                    echo '<option value="' . $row_docente['numero_documento'] . '">' . $row_docente['nombres'] . " " . $row_docente['apellidos'] . '</option>';
                                }
                            } else {
                                echo '<option value="">No hay docentes disponibles</option>';
                            }
                            ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="id_modulo">Módulos</label>
                        <select id="id_asignacion_periodo" name="id_modulo" class="form-control">
                            <option value="">-- Selecciona un módulo --</option>
                            <?php
                            $sql_materias = "SELECT id_modulo, nombre FROM modulos";
                            $result_materias = $conn->query($sql_materias);

                            if ($result_materias->num_rows > 0) {
                                while ($row_materias = $result_materias->fetch_assoc()) {
                                    echo '<option value="' . $row_materias['id_modulo'] . '">' . $row_materias['nombre'] . '</option>';
                                }
                            } else {
                                echo '<option value="">No hay módulos disponibles</option>';
                            }
                            ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="modalidad">Modalidad</label>
                        <select name="modalidad" id="modalidad" class="form-control">
                            <option value="">-- Selecciona la Modalidad --</option>
                            <option value="Presencial">Presencial</option>
                            <option value="Virtual">Virtual</option>
                        </select>
                    </div>
                    <div class="d-flex justify-content-center">
                        <button id="estado" class="btn btn-danger fw-bold">
                            Marcar Clase como Perdida
                        </button>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" onclick="GuardarClase()">Guardar Cambios</button>
            </div>
        </div>
    </div>
</div>
<br>

<?php
include_once '../componentes/footer.php';
?>

<script src="js/Datatable-Programador.js"></script>
<script src="js/Datatable-materia.js"></script>
<script src="js/Validate.js"></script>

<script>
    function GuardarClase() {
        const formData = new FormData(document.getElementById('editarClaseForm'));

        console.log('Datos del formulario:', ...formData.entries());

        $.ajax({
            url: 'Programador-Controlador.php?accion=editar',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                console.log('Respuesta del servidor:', response);
                // location.reload();
            },
            error: function(xhr, status, error) {
                console.error('Error:', error);
            }
        });
    }

    document.getElementById("estado").addEventListener("click", function() {
        let confirmar = confirm("¿Estás seguro de marcar esta clase como perdida?");
        if (confirmar) {
            location.reload();
        }
    });
</script>
<script>
    $(document).ready(function(){
      // Llamada AJAX para obtener las materias desde la BD
      $.ajax({
        url: 'Materias-Controlador.php', // Archivo que consulta la BD y retorna JSON
        method: 'GET',
        dataType: 'json',
        success: function(data){
          // Se espera que 'data' sea un arreglo de objetos: [{ id: 1, nombre: "Matemáticas" }, ...]
          var container = $('#materiasContainer');
          container.empty();
          // Array de clases de color para alternar estilos en las tarjetas
          var colores = ['bg-info', 'bg-success', 'bg-warning', 'bg-danger'];
          $.each(data, function(index, materia){
            var colorClass = colores[index % colores.length];
            var card = $('<div>', { class: 'col-md-3 mb-3' }).append(
              $('<div>', {
                class: 'card materia ' + colorClass + ' text-white text-center',
                'data-materia': materia.id_modulo
              }).append(
                $('<div>', { class: 'card-body' }).append(
                    $('<h5>', { class: 'card-title', text: materia.nombre }),
                    $('<p>', { class: 'card-text', text: 'Programa: ' + materia.programa })
                    )
              )
            );
            container.append(card);
          });
          
          // Manejo de selección de tarjeta
          $('.card.materia').on('click', function(){
            $('.card.materia').removeClass('materia-seleccionada');
            $(this).addClass('materia-seleccionada');
            console.log('Materia seleccionada: ', $(this).data('materia'));
          });
        },
        error: function(xhr, status, error){
          console.error('Error al cargar las materias:', error);
        }
      });
    });
  </script>