<?php
    include_once '../componentes/header.php';
    include_once '../conexion.php';
    include 'idDocente.php';

    $conn->query("SET lc_time_names = 'es_ES'");

    $sql = "SELECT MONTHNAME(c.fecha) AS fecha, 
        SUM(c.horas_trabajadas) AS total_horas, 
        (c.valor_hora * SUM(c.horas_trabajadas)) AS total_monto, 
        c.valor_hora,
        d.nombres, 
        d.apellidos 
    FROM cuentas_cobro c 
    JOIN docentes d ON c.numero_documento = d.numero_documento
    WHERE c.numero_documento = '$docente'
    AND c.estado = 'creada'
    GROUP BY MONTHNAME(c.fecha), c.valor_hora, d.nombres, d.apellidos
    ORDER BY MIN(c.fecha) ASC
    LIMIT 1";

    $resultado = $conn->query($sql);
    $fila = $resultado->fetch_assoc(); // Obtener datos una sola vez
?>

<div class="container">
    <h1 class="text-center">Cuenta Docente</h1>
    
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Información Cuenta Docente</h5>
                </div>
                <div class="card-body">
                    <div class="row d-flex align-items-stretch">
                        <div class="col-md-6">
                            <div class="card h-100">
                                <div class="card-header">
                                    <?php if ($fila) : ?>
                                        <h5>Cuenta de cobro de <?php echo ucfirst($fila['fecha']); ?></h5>
                                    <?php else : ?>
                                        <h5>No hay cuentas de cobro pendientes.</h5>
                                    <?php endif; ?>
                                </div>
                                <div class="card-body">
                                    <?php if ($fila) : ?>
                                        <h5>Nombre: <?php echo $fila['nombres'] . ' ' . $fila['apellidos']; ?></h5>
                                        <h5>Horas: <?php echo $fila['total_horas']; ?></h5>
                                        <h5>Total: <span class="text-success">
                                            <?php echo '$' . number_format($fila['total_monto'], 0, ',', '.'); ?>
                                        </span></h5>
                                        <br>
                                        <div class="d-grid gap-2 d-md-block">
                                            <button class="btn btn-primary" onclick="btnAceptar()">Aceptar</button>
                                            <button class="btn btn-danger" onclick="btnRechazar()">Rechazar</button>
                                        </div>
                                    <?php else : ?>
                                        <p class="alert text-center"> No hay cuentas de cobro pendientes. ¡Todo está al día!</p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>


                        <!-- Card 2: Clases Programadas -->
                        <div class="col-md-6">
                            <div class="card h-100">
                                <div class="card-header">
                                    <h5>Clases Programadas</h5>
                                </div>
                                <div class="card-body">
                                    <div class="overflow-auto" style="max-height: 300px;">
                                        <div class="table-responsive">
                                            <table class="table table-striped table-bordered">
                                                <thead class="thead-dark">
                                                    <tr>
                                                        <th>Fecha</th>
                                                        <th>Hora Inicio</th>
                                                        <th>Hora Salida</th>
                                                        <th>Materia</th>
                                                        <th>Salón</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php
                                                    $sql = "SELECT p.fecha, DATE_FORMAT(p.hora_inicio, '%h:%i%p') AS hora_inicio, 
                                                            DATE_FORMAT(p.hora_salida, '%h:%i%p') AS hora_salida, 
                                                            m.nombre, s.nombre_salon
                                                            FROM programador p
                                                            JOIN modulos m ON p.id_modulo = m.id_modulo
                                                            JOIN salones s ON p.id_salon = s.id_salon
                                                            WHERE p.numero_documento = $docente";

                                                    $resultado = $conn->query($sql);

                                                    if (!$resultado) {
                                                        die("Error en la consulta SQL: " . $conn->error);
                                                    }

                                                    if ($resultado->num_rows > 0) {
                                                        while ($fila = $resultado->fetch_assoc()) {
                                                            echo '<tr>';
                                                            echo '<td>' . $fila['fecha'] . '</td>';
                                                            echo '<td>' . $fila['hora_inicio'] . '</td>';
                                                            echo '<td>' . $fila['hora_salida'] . '</td>';
                                                            echo '<td>' . $fila['nombre'] . '</td>';
                                                            echo '<td>' . $fila['nombre_salon'] . '</td>';
                                                            echo '</tr>';
                                                        }
                                                    } else {
                                                        echo '<tr><td colspan="5" class="text-center">No hay módulos disponibles</td></tr>';
                                                    }
                                                    ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div> 
                        </div> 
                    </div> 
                </div> 
            </div> 
        </div> 
    </div> 
</div>

<div class="container mt-4">
    <div class="card">
        <div class="card-header">
            <h5>Cuentas de cobro</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-12">
                    <div class="table-responsive">
                        <table id="datos_CuentaCobroDocente" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Fecha</th>
                                    <th>Docente</th>
                                    <th>Valor Hora</th>
                                    <th>Horas Trabajadas</th>
                                    <th>Monto</th>
                                    <th>Estado</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="modalCuentaDocente" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Agregar cuenta de cobro</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="formCuenta">
                    <input type="hidden" name="accion" value="crear" id="accion">
                    <input type="hidden" name="id_cuenta" id="id_cuenta">

                    <div class="mb-3">
                        <label for="fecha" class="form-label">Fecha:</label>
                        <input type="date" name="fecha" id="fecha" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label for="valor_hora" class="form-label">Valor hora:</label>
                        <input type="number" name="valor_hora" id="valor_hora" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label for="horas_trabajadas" class="form-label">Horas trabajadas:</label>
                        <input type="number" name="horas_trabajadas" id="horas_trabajadas" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label for="monto" class="form-label">Monto:</label>
                        <input type="number" name="monto" id="monto" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label for="estado" class="form-label">Estado:</label>
                        <select name="estado" id="estado" class="form-control">
                            <option value="creada">Creada</option>
                            <option value="aceptada_docente">Aceptada Docente</option>
                            <option value="pendiente_firma">Pendiente Firma</option>
                            <option value="proceso_pago">Proceso de pago</option>
                            <option value="pagada">Pagada</option>
                            <option value="rechazada_por_institucion">Rechazada por institución</option>
                            <option value="rechazada_por_docente">Rechazada por docente</option>
                        </select>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-success" onclick="crearInstitucion()">Guardar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include_once '../componentes/footer.php'; ?>

<script src="js/Datatable-Cuentas-Docentes.js"></script>

<script>
function btnAceptar() {
    $.ajax({
        url: 'Cuentas-Docentes-Controlador.php?accion=Aceptar',
        type: 'POST',
        success: function(response) {
            alert('Cuenta aceptada correctamente');
            location.reload();
        },
        error: function(xhr, status, error) {
            console.error('Error:', error);
            alert('Hubo un problema al aceptar la cuenta.');
        }
    });
}
function btnRechazar() {
    $.ajax({
        url: "Cuentas-Docentes-Controlador.php?accion=Rechazar",
        type: "POST",
        success: function(response) {
            alert("Cuenta rechazada correctamente");
            location.reload();
        },
        error: function(xhr, status, error) {
            console.error("Error:", error);
            alert("Hubo un problema al rechazar la cuenta.");
        }
    });
}

</script>
