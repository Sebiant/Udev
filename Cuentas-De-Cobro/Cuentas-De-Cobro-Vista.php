<?php
include '../conexion.php';

$sql = "SELECT numero_documento, nombres, apellidos FROM docentes";
$result = $conn->query($sql);

include_once '../componentes/header.php';
?>

<div class="container">
    <h1 class="text-center">Cuentas de cobro</h1>
    <div class="row">
        <div class="col-2 offset-10">
            <div class="text-center"> 
                <button type="button" class="btn btn-primary mx-1" id="botonCsv" onclick="exportarTodo('csv')">
                    <i class="bi bi-file-earmark-spreadsheet"></i> Exportar todos
                </button>
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
                            <th>Docente</th>
                            <th>Horas trabajadas</th>
                            <th>Valor de la hora</th>
                            <th>Monto</th>
                            <th>Estado</th>
                            <th>Exportar</th>
                        </tr>
                    </thead>
                    <tbody>
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
