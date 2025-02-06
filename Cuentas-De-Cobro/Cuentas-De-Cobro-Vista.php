<?php
include '../conexion.php';

$sql = "SELECT numero_documento, nombres, apellidos FROM docentes";
$result = $conn->query($sql);

include_once '../componentes/header.php';
?>

<div class="container">
    <h1 class="text-center">Cuentas de cobro</h1>

    <div class="card">
        <div class="card-header">
            <h5>Seleccione los campos necesarios</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <!-- Columna izquierda: Filtro por docente -->
                <div class="col-md-6">
                    <label for="docente" class="form-label"><strong>Filtrar por docente</strong></label>
                    <select id="docente" name="id_docente" required class="form-control mt-2">
                        <option value="">Seleccione un docente</option>
                        <?php
                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                echo '<option value="' . $row['id_docente'] . '">' . $row['nombres'] . " " . $row['apellidos'] . '</option>';
                            }
                        } else {
                            echo '<option value="">No hay docentes disponibles</option>';
                        }
                        ?>
                    </select>
                </div>

                <!-- Columna derecha: Exportar todos -->
                <div class="col-md-6 d-flex align-items-center justify-content-end">
                    <div>
                        <label class="form-label"><strong>Exportar todos</strong></label>
                        <div class="d-flex">
                            <button type="button" class="btn btn-primary mx-1" id="botonPdf" onclick="exportarTodo('pdf')">
                                <i class="bi bi-file-earmark-pdf"></i> PDF
                            </button>
                            <button type="button" class="btn btn-primary mx-1" id="botonCsv" onclick="exportarTodo('csv')">
                                <i class="bi bi-file-earmark-spreadsheet"></i> CSV
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <br />

    <div class="card">
        <div class="card-header">
            <h5>Información de Cuenta de Cobro</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table id="datos_cuentacobro_admin" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Fecha</th>
                            <th>Valor de la hora</th>
                            <th>Horas trabajadas</th>
                            <th>Monto</th>
                            <th>Estado</th>
                            <th>Docente</th>
                            <th>Exportar</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Aquí se insertarán los datos dinámicamente -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php
include_once '../componentes/footer.php';
?>

<script src="js/Datatable-Cuentas-De-Cobro.js"></script>
