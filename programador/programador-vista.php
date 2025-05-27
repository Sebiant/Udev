<style>
.modulos-container {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(160px, 1fr)); /* Antes 200px */
    gap: 16px;
    justify-content: center;
    padding: 20px;
}

.materia-card, .modulo-card {
    height: 160px; /* Antes 200px */
    border: 1px solid #ddd;
    border-radius: 16px;
    background: linear-gradient(to bottom right, #ffffff, #f0f4f8);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
    text-align: center;
    padding: 16px;
    font-size: 16px; /* Un pelín más pequeño */
    cursor: pointer;
    transition: all 0.3s ease-in-out;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    width: 100%;
}

.materia-card:hover, .modulo-card:hover {
    background: linear-gradient(to bottom right, #e8f0fe, #dbe9ff);
    transform: scale(1.05);
    box-shadow: 0 6px 16px rgba(0, 123, 255, 0.3);
}

.materia-card.seleccionada, .modulo-card.seleccionada {
    border: 2px solid #007bff;
    background: linear-gradient(to bottom right, #d6e9ff, #c2ddff);
    transform: scale(1.08);
    box-shadow: 0 6px 18px rgba(0, 123, 255, 0.4);
}

.icono {
    font-size: 36px; /* Un poco más pequeño también */
    margin-bottom: 8px;
    color: #007bff;
}
</style>
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.17/index.global.min.js'></script>
<?php
include_once '../Componentes/header.php';
include '../Conexion.php';

$sql_docentes = "SELECT numero_documento, nombres, apellidos FROM docentes WHERE estado = 1";
$result_docentes = $conn->query($sql_docentes);

$sql_salones = "SELECT 
        salones.id_salon, 
        salones.nombre_salon, 
        instituciones.nombre
    FROM salones
    JOIN instituciones ON salones.id_institucion = instituciones.id_institucion
    WHERE salones.estado = '1'";
$result_salones = $conn->query($sql_salones);

$sql_periodos = "SELECT id_periodo, nombre FROM periodos WHERE estado = 1";
$result_periodos = $conn->query($sql_periodos);

$periodos = $conn->query($sql_periodos);

$sql_programas = "
    SELECT DISTINCT p.id_programa, p.nombre
    FROM modulos m
    JOIN programas p ON m.id_programa = p.id_programa
    WHERE m.estado = 1
";
$result_programas = $conn->query($sql_programas);

?>

<div class="container mt-5">
    <form id="formProgramador">
        <div class="card">
            <div class="card-header text-center">
                <h2>Programación de Clases</h2>
            </div>
            <div class="card-body">
                <div class="row">
                    <!-- Columna izquierda -->
                    <div class="col-md-6">
                        <div class="card h-100">
                            <div class="card-header">Selección de Datos</div>
                            <div class="card-body">
                                <div class="form-group">
                                    <label for="periodo">Periodo</label>
                                    <select id="periodo" name="periodo" class="form-control">
                                        <option value="">-- Seleccione Periodo --</option>
                                        <?php while ($row = $result_periodos->fetch_assoc()): ?>
                                            <option value="<?php echo $row['id_periodo']; ?>"><?php echo $row['nombre']; ?></option>
                                        <?php endwhile; ?>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="programa">Programa</label>
                                    <select id="programa" name="programa" class="form-control">
                                        <option value="">-- Seleccione un Programa --</option>
                                        <?php
                                        if ($result_programas->num_rows > 0) {
                                            while ($row = $result_programas->fetch_assoc()) {
                                                echo "<option value='" . htmlspecialchars($row['id_programa']) . "'>" . htmlspecialchars($row['nombre']) . "</option>";
                                            }
                                        } else {
                                            echo "<option value=''>No hay programas disponibles</option>";
                                        }
                                        ?>
                                    </select>
                                </div>

                                <br>
                                <div class="modulos-container" id="modulosContainer">
                                    <!-- Aquí se llenarán dinámicamente las tarjetas -->
                                </div>

                                <input type="hidden" name="modulo" id="moduloSeleccionado">
                            </div>
                        </div>
                    </div>

                    <!-- Columna derecha -->
                    <div class="col-md-6">
                        <div class="card h-100">
                            <div class="card-header">Programación de Horario y Modalidad</div>
                            <div class="card-body">
                                <h5 class="mb-3">Docente y Salón</h5>
                                <div class="form-group">
                                    <label for="docente">Docente</label>
                                    <select id="docente" name="docente" class="form-control">
                                        <option value="">-- Seleccione Docente --</option>
                                        <?php while ($row = $result_docentes->fetch_assoc()): ?>
                                            <option value="<?php echo $row['numero_documento']; ?>">
                                                <?php echo $row['nombres'] . " " . $row['apellidos']; ?>
                                            </option>
                                        <?php endwhile; ?>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="salon">Salón</label>
                                    <select id="salon" name="salon" class="form-control">
                                        <option value="">-- Seleccione Salón --</option>
                                        <?php while ($row = $result_salones->fetch_assoc()): ?>
                                            <option value="<?php echo $row['id_salon']; ?>">
                                                <?php echo $row['nombre_salon'] . ", " . $row['nombre']; ?>
                                            </option>
                                        <?php endwhile; ?>
                                    </select>
                                </div>

                                <hr>

                                <h5 class="mb-3">Horario</h5>
                                <div class="form-group">
                                    <label for="dia">-- Selecciona Día de la Semana --</label>
                                    <select id="dia" name="dia" class="form-control">
                                        <option value="">Seleccione Día</option>
                                        <option value="lunes">Lunes</option>
                                        <option value="martes">Martes</option>
                                        <option value="miercoles">Miércoles</option>
                                        <option value="jueves">Jueves</option>
                                        <option value="viernes">Viernes</option>
                                        <option value="sabado">Sábado</option>
                                    </select>
                                </div>

                                <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                    <label for="horaEntrada">Hora de Entrada</label>
                                    <input type="time" id="horaEntrada" name="horaEntrada" class="form-control">
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                    <label for="horaSalida">Hora de Salida</label>
                                    <input type="time" id="horaSalida" name="horaSalida" class="form-control">
                                    </div>
                                </div>
                                </div>

                                <hr>

                                <h5 class="mb-3">Modalidad</h5>
                                <div class="form-group">
                                    <label for="modalidad">Modalidad</label>
                                    <select id="modalidad" name="modalidad" class="form-control">
                                        <option value="">-- Selecciona la modalidad --</option>
                                        <option value="presencial">Presencial</option>
                                        <option value="virtual">Virtual</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div> <!-- Fin row -->
            </div> <!-- Fin card-body -->
            <div class="card-footer text-center">
                <button type="button" class="btn btn-primary" onclick="ProgramarClase()">Programar Clase</button>
            </div>
        </div>
    </form>
</div>

<div class="container mt-4">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Clases Programadas</h5>
            <div>
                <span id="badge-agendada" class="badge text-bg-success me-1">Agendadas: 0</span>
                <span id="badge-vista" class="badge text-bg-primary">Vistas: 0</span>
                <span id="badge-perdida" class="badge text-bg-danger me-1">Perdidas: 0</span>
                <span id="badge-reagendada" class="badge text-bg-warning me-1">Reagendadas: 0</span>
            </div>
        </div>
        <div class="container mt-4">
            <div id='calendar'></div>
        </div>
    </div>
</div>
<!-- Modal Reprogramación-->
<div class="modal fade" id="modalReprogramar" tabindex="-1" aria-labelledby="modalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Reprogramar Clase</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="formReprogramar">
                    <input type="hidden" name="id_programador" id="id_programador">
                    <input type="hidden" name="numero_documento" id="numero_documento">
                    <input type="hidden" name="id_salon" id="id_salon">
                    <input type="hidden" name="id_modulo" id="id_modulo">
                    <input type="hidden" name="id_periodo" id="id_periodo">
                    
                    <label>Fecha:</label>
                    <input type="date" name="nueva_fecha" class="form-control" required>

                    <label>Hora Inicio:</label>
                    <input type="time" name="nueva_hora_inicio" class="form-control" required>

                    <label>Hora Salida:</label>
                    <input type="time" name="nueva_hora_salida" class="form-control" required>

                    <p class="form-text text-muted mt-2" style="font-size: 0.85em;">
                        La clase se realizará de forma virtual en la fecha y hora establecidas.
                    </p>

                    <button type="button" class="btn btn-primary mt-3" onclick="reprogramarClase()">Guardar cambios</button>
                </form>
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
                        <select id="id_modulo" name="id_modulo" class="form-control">
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
                            <option value="presencial">Presencial</option>
                            <option value="virtual">Virtual</option>
                        </select>
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
include_once '../Componentes/footer.php';
?>
<script src="js/Datatable-Programador.js"></script>
<script>
    function ProgramarClase() {
  const form = document.getElementById("formProgramador");
  const formData = new FormData(form);

  console.log("Datos del formulario:");
  for (const [key, value] of formData.entries()) {
    console.log(`${key}: ${value}`);
  }

  $.ajax({
    url: "Programador-Controlador.php?accion=crear",
    type: "POST",
    data: formData,
    processData: false,
    contentType: false,
    success: function (response) {
      try {
        // Intenta interpretar la respuesta como JSON
        const data = JSON.parse(response);

        if (data.status === "success") {
          alert("✅ " + data.message);
          location.reload();
        } else if (data.status === "error") {
          alert("❌ " + data.message);
        } else {
          alert("⚠️ Respuesta inesperada del servidor.");
          console.log(data);
        }
      } catch (e) {
        console.error("❌ Error al procesar la respuesta:", response);
        alert("❌ Ocurrió un error inesperado. Verifica la consola.");
      }
    },
    error: function (xhr, status, error) {
      console.error("Error AJAX:", error);
      alert("🚫 Error en la conexión con el servidor: " + error);
    },
  });
}

function GuardarClase() {
  const formData = new FormData(document.getElementById("editarClaseForm"));

  console.log("Datos del formulario:", ...formData.entries());

  $.ajax({
    url: "Programador-Controlador.php?accion=editar",
    type: "POST",
    data: formData,
    processData: false,
    contentType: false,
    success: function (response) {
      console.log("Respuesta del servidor:", response);
      location.reload();
    },
    error: function (xhr, status, error) {
      console.error("Error:", error);
    },
  });
}
function reprogramarClase() {
  const formData = new FormData(document.getElementById("formReprogramar"));
  console.log(...formData);

  $.ajax({
    url: "Programador-Controlador.php?accion=reprogramar",
    type: "POST",
    data: formData,
    processData: false,
    contentType: false,
    success: function (response) {
      console.log("Respuesta del servidor:", response);
      alert("Respuesta del servidor:", response);
      location.reload();
    },
    error: function (xhr, status, error) {
      console.error("Error:", error);
      alert("Hubo un problema al procesar la solicitud.");
    },
  });
}

</script>
<script>
    // Función que se llama cuando haces clic en un módulo
    function seleccionarModulo(id) {
        // Quitar la clase 'seleccionada' de todos los módulos
        document.querySelectorAll(".materia-card").forEach(card => {
            card.classList.remove("seleccionada");
        });

        // Marcar el módulo clickeado como seleccionado
        const cardSeleccionada = document.getElementById("modulo_" + id);
        if (cardSeleccionada) {
            cardSeleccionada.classList.add("seleccionada");
        }

        // Guardar el ID del módulo seleccionado
        document.getElementById("moduloSeleccionado").value = id;
    }

    // Evento al cambiar el programa
    document.getElementById("programa").addEventListener("change", function () {
        const idPrograma = this.value;
        const container = document.getElementById("modulosContainer");

        if (idPrograma === "") {
            container.innerHTML = "<p>Seleccione un programa primero.</p>";
            return;
        }

        fetch("Programador-Controlador.php?accion=buscarMateriaPorPrograma", {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: "id_programa=" + encodeURIComponent(idPrograma)
        })
        .then(response => response.json())
        .then(data => {
            container.innerHTML = "";

            if (data.status === "success" && data.modulos.length > 0) {
                data.modulos.forEach(modulo => {
                    const card = document.createElement("div");
                    card.className = "materia-card";
                    card.id = "modulo_" + modulo.id;
                    card.innerHTML = `
                        <div class="icono">📚</div>
                        <h6>${modulo.nombre}</h6>
                        <p class="fs-7"></p>
                    `;
                    card.onclick = () => seleccionarModulo(modulo.id);
                    container.appendChild(card);
                });
            } else {
                container.innerHTML = "<p>No hay módulos disponibles para este programa.</p>";
            }
        })
        .catch(error => {
            console.error("Error al cargar módulos:", error);
            container.innerHTML = "<p>Error al cargar módulos.</p>";
        });
    });
</script>
<script>
    // Función para obtener los datos del servidor
    function cargarClasesEstado() {
        $.ajax({
            url: 'Programador-Controlador.php?accion=contarClasesEstado',
            type: 'POST',
            dataType: 'json',
            data: { action: 'contarClasesEstado' },
            success: function(data) {
                // Si la respuesta contiene los datos esperados, actualizamos las etiquetas
                if (data.pendiente !== undefined && data.reprogramada !== undefined && data.perdida !== undefined) {
                    $('#badge-agendada').text(`Agendadas: ${data.pendiente}`);
                    $('#badge-reagendada').text(`Reagendadas: ${data.reprogramada}`);
                    $('#badge-perdida').text(`Perdidas: ${data.perdida}`);
                    $('#badge-vista').text(`Vistas: ${data.vista}`);
                } else {
                    console.error('Error en los datos recibidos');
                }
            },
            error: function(xhr, status, error) {
                console.error('Error en la solicitud AJAX:', error);
            }
        });
    }

    // Llamamos a la función cuando la página esté cargada
    $(document).ready(function() {
        cargarClasesEstado();
    });
</script>


