<?php
include '../Conexion.php';

$sql = "SELECT numero_documento, nombres, apellidos FROM docentes";
$result = $conn->query($sql);

include_once '../Componentes/header.php';
?>

<div class="container">
    <h1 class="text-center">Cuentas de cobro</h1>
    <br>
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Información Cuentas de Cobro</h5>
            <div>
                <span id="badge-aceptada_docente" 
                    class="badge text-bg-success me-1 text-white filtro-estado" 
                    style="cursor: pointer;" 
                    data-estado="aceptada_docente">
                    Aceptada por el docente: 0
                </span>

                <span id="badge-rechazada_por_docente" 
                    class="badge text-bg-danger me-1 text-white filtro-estado" 
                    style="cursor: pointer;" 
                    data-estado="rechazada_por_docente">
                    Rechazada por el docente: 0
                </span>

                <span id="badge-pendiente_firma" 
                    class="badge text-bg-warning me-1 text-white filtro-estado" 
                    style="cursor: pointer;" 
                    data-estado="pendiente_firma">
                    Pendiente de firma: 0
                </span>

                <span id="badge-proceso_pago" 
                    class="badge text-bg-info me-1 text-white filtro-estado" 
                    style="cursor: pointer;" 
                    data-estado="proceso_pago">
                    En proceso de pago: 0
                </span>

                <span id="badge-pagada" 
                    class="badge text-bg-success me-1 text-white filtro-estado" 
                    style="cursor: pointer;" 
                    data-estado="pagada">
                    Pagada: 0
                </span>
            </div>
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
                            <th>Saldo</th>
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
                        <input type="hidden" name="id_cuenta" id="id_cuenta">

                        <div class="mb-3">
                            <label for="horas_trabajadas" class="form-label" id="label_cant_horas">Horas Trabajadas: </label>
                            <h6 name="cant_horas" id="cant_horas"></h6>
                            <input type="number" name="horas_trabajadas" id="horas_trabajadas" class="form-control">
                        </div>

                        <div class="mb-3">
                            <label for="valor_hora" class="form-label" id="label_valor">Valor Hora: </label>
                            <h6 name="valor" id="valor"></h6>
                            <input type="text" name="valor_hora" id="valor_hora" class="form-control">
                        </div>

                        <div class="mb-3">
                            <label for="monto" class="form-label" id="label_monto_mostrado">Monto total:</label>
                            <h6 id="monto_mostrado" name="monto"></h6>
                            <input type="number" name="monto" id="monto" class="form-control d-none">
                        </div>

                        <div class="mb-3">
                            <label for="saldo" class="form-label" id="label_saldo_mostrado">Saldo restante:</label>
                            <h6 id="saldo_mostrado" name="saldo"></h6>
                            <input type="number" name="saldo" id="saldo" class="form-control d-none">
                        </div>

                        <div class="mb-3">
                            <label for="abono" class="form-label" id="label_abonar">Abonar: </label>
                            <input type="number" name="valor_abonado" id="valor_abonado" class="form-control">
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-success" id="btnModificar" onclick="modificarCuenta()">Modificar</button>
                            <button type="button" class="btn btn-primary" id="btnExportar" data-id="">Exportar</button>
                            <button type="button" class="btn btn-warning" id="btnFirmado" data-id="" onclick="Firmar()">Firmado</button>
                            <button type="button" class="btn btn-danger" id="btnDevolver" data-id="" onclick="Devolver()">Devolver</button>
                            <button type="button" class="btn btn-success" id="btnAbonar" data-id="" onclick="Abonar()">Abonar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
include_once '../Componentes/footer.php';
?>

<script src=js/Validation-Cuentas-De-Cobro.js></script>
<script src="js/Datatable-Cuentas-De-Cobro.js" defer></script>

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
                location.reload();
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
<script>
    function cargarCuentasEstado() {
        $.ajax({
            url: 'Cuentas-De-Cobro-Controlador.php?accion=contarCuentasEstado',
            type: 'GET',
            dataType: 'json',
            success: function(data) {
                if (data) {
                    $('#badge-aceptada_docente').text(`Aceptada por el docente: ${data.aceptada_docente}`);
                    $('#badge-rechazada_por_docente').text(`Rechazada por el docente: ${data.rechazada_por_docente}`);
                    $('#badge-pendiente_firma').text(`Pendiente de firma: ${data.pendiente_firma}`);
                    $('#badge-proceso_pago').text(`En proceso de pago: ${data.proceso_pago}`);
                    $('#badge-pagada').text(`Pagada: ${data.pagada}`);
                } else {
                    console.error('Respuesta vacía o inválida del servidor');
                }
            },
            error: function(xhr, status, error) {
                console.error('Error en la solicitud AJAX:', error);
            }
        });
    }

    $(document).ready(function () {
        cargarCuentasEstado();
    });
</script>
