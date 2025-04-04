<?php
include '../conexion.php';

$sql = "SELECT numero_documento, nombres, apellidos FROM docentes";
$result = $conn->query($sql);

include_once '../componentes/header.php';
?>

<div class="container">
    <h1 class="text-center">Cuentas de cobro</h1>
    <br>
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
                    <div class="contenedor-titulos">
                        <h5 class="modal-title" name="fecha">Fecha</h5>
                        <h6 class="modal-title" name="modalCuentasCobroLabel">Docente</h6>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <form id="formCuentaCobro">
                        <input type="hidden" name="accion" value="editar">
                        <input type="hidden" name="id_cuenta" id="id_cuenta">
                        <div class="mb-3">
                            <label for="horas_trabajadas" class="form-label">Horas Trabajadas</label>
                            <input type="number" name="horas_trabajadas" id="horas_trabajadas" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label for="valor_hora" class="form-label">Valor Hora</label>
                            <input type="text" name="valor_hora" id="valor_hora" class="form-control">
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-success" id="btnModificar" onclick="modificarCuenta()">Modificar</button>
                            <button type="button" class="btn btn-primary" id="btnExportar" data-id="">Exportar</button>
                            <button type="button" class="btn btn-warning" id="btnFirmado" data-id="" onclick="Firmar()">Firmado</button>
                            <button  type="button" class="btn btn-danger" id="btnDevolver" data-id="" onclick="Devolver()">Devolver</button>
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

<script src=js/Validation-Cuentas-De-Cobro.js></script>
<script src="js/Datatable-Cuentas-De-Cobro.js"></script>

<script>
    function modificarCuenta() {
        if (!$("#formCuentaCobro").valid()) {
            console.log("El formulario no es válido.");
            return;
        }

        const formData = new FormData(document.getElementById('formCuentaCobro'));
        console.log('Datos del formulario:', ...formData.entries());

        $.ajax({
            url: 'Cuentas-De-Cobro-Controlador.php?accion=modificar',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                console.log('Respuesta del servidor:', response);
                //location.reload();
            },
            error: function(xhr, status, error) {
                console.error('Error:', error);
            }
        });
    }

    document.getElementById("btnExportar").addEventListener("click", function () {
    let id_cuenta = this.getAttribute("data-id");
    exportar(id_cuenta);
});

function exportar(id_cuenta) {
    window.location.href = 'Cuentas-De-Cobro-Controlador.php?accion=exportar&id_cuenta=' + id_cuenta;
}


</script>
