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
                            <th>Verificar</th>
                            <th>Devolver Petición</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <!-- Modal de modificación -->
    <div class="modal fade" id="modalCuentasCobro" tabindex="-1" aria-labelledby="modalCuentasCobroLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalCuentasCobroLabel">Modificar Cuenta de Cobro</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <form id="formCuentaCobro">
                        <input type="hidden" name="accion" value="editar">
                        <input type="hidden" name="id_cuenta" id="id_cuenta">

                        <div class="mb-3">
                            <label for="fecha" class="form-label">Fecha</label>
                            <input type="date" name="fecha" id="fecha" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label for="numero_documento" class="form-label">Docente</label>
                            <input type="text" name="numero_documento" id="numero_documento" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label for="horas_trabajadas" class="form-label">Horas Trabajadas</label>
                            <input type="number" name="horas_trabajadas" id="horas_trabajadas" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label for="valor_hora" class="form-label">Valor Hora</label>
                            <input type="text" name="valor_hora" id="valor_hora" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label for="monto" class="form-label">Monto</label>
                            <input type="text" name="monto" id="monto" class="form-control">
                        </div>

                        <div class="modal-footer">
                            <button type="submit" class="btn btn-success">Guardar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
include_once '../componentes/footer.php';
?>

<script src="js/Datatable-Cuentas-De-Cobro.js"></script>
