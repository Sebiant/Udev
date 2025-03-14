<?php include_once '../componentes/header.php'; ?>

<div class="container">
    <br>
    <div class="card">
        <div class="card-header text-center">
            <h5>Gestion de Asistencias</h5>
        </div>
        <div class="card-body">
            
            <div class="card">
                <div class="card-header">
                    <h5>Asistencias de la semana</h5>
                </div>
                <div class="table-responsive" style="max-height: 400px; overflow-y: auto; overflow-x: hidden;">
                    <table id="datos_asistencia" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Fecha</th>
                                <th>Docentes</th>
                                <th>Horas trabajadas</th>
                                <th>Editar</th>
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

<?php include_once '../componentes/footer.php'; ?>
<script src="js/Datatable-Programador.js"></script>
<script src="js/Datatable-Asistencias.js"></script>

