<?php
    include_once '../componentes/header.php';
    include_once '../conexion.php';
    include 'idDocente.php';

    $conn->query("SET lc_time_names = 'es_ES'");

    $sql = "SELECT MONTHNAME(c.fecha) AS fecha, 
                   SUM(c.horas_trabajadas) AS total_horas, 
                   SUM(c.valor_hora * c.horas_trabajadas) AS total_monto, 
                   c.valor_hora,
                   d.nombres, 
                   d.apellidos,
                   c.id_cuenta
            FROM cuentas_cobro c 
            JOIN docentes d ON c.numero_documento = d.numero_documento
            WHERE c.numero_documento = ? 
              AND c.estado = 'creada'
            GROUP BY fecha, c.valor_hora, d.nombres, d.apellidos, c.id_cuenta
            ORDER BY MIN(c.fecha) ASC
            LIMIT 1";

    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("s", $docente);

        if ($stmt->execute()) {
            $resultado = $stmt->get_result();
            $fila = $resultado->fetch_assoc();
        } else {
            die("Error en la ejecución de la consulta: " . $stmt->error);
        }

        $stmt->close();
    } else {
        die("Error en la preparación de la consulta: " . $conn->error);
    }
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
                        <div class="col-md-4">
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
                                        <form id="formCuentaCobro">
                                            <input type="hidden" name="id_cuenta" value="<?php echo $fila['id_cuenta']; ?>">
                                            <div class="d-grid gap-2 d-md-block">
                                                <button type="button" class="btn btn-primary" onclick="aceptarCuenta()">Aceptar</button>
                                                <button type="button" class="btn btn-danger" onclick="rechazarCuenta()">Rechazar</button>
                                            </div>
                                        </form>
                                    <?php else : ?>
                                        <p class="alert text-center"> No hay cuentas de cobro pendientes. ¡Todo está al día!</p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="card h-100">
                                <div class="card-header">
                                    <h5>Clases Programadas</h5>
                                </div>
                                <div class="card-body">
                                    <div class="overflow-auto" style="max-height: 300px;">
                                        <div class="table-responsive">
                                            <table id="tablaClases" class="table table-striped table-bordered">
                                                <thead class="thead-dark">
                                                    <tr>
                                                        <th>Fecha</th>
                                                        <th>Hora</th>
                                                        <th>Materia</th>
                                                        <th>Salón</th>
                                                        <th>Acciones</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
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
                    <input type="hidden" name="modalidad" id="modalidad">

                    <label>Fecha:</label>
                    <input type="date" name="nueva_fecha" class="form-control" required>

                    <label>Hora Inicio:</label>
                    <input type="time" name="nueva_hora_inicio" class="form-control" required>

                    <label>Hora Salida:</label>
                    <input type="time" name="nueva_hora_salida" class="form-control" required>

                    <button type="button" class="btn btn-primary mt-3" onclick="reprogramarClase()">Guardar cambios</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include_once '../componentes/footer.php'; ?>
<script src="js/Datatable-Cuentas-Docentes.js"></script>
