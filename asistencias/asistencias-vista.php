<?php include_once '../componentes/header.php'; ?>

<div class="container">
    <h1 class="text-center">Gestión de Asistencias</h1>

    <div class="row">
        <div class="col-sm-7 mb-3 mb-sm-0">
    <div class="card">
        <div class="card-body">
            <h5 class="card-title">Clases programadas</h5>
            <div class="table-responsive" style="max-height: 400px; overflow-y: auto; overflow-x: hidden;">  <!-- Se añade overflow-x: hidden -->
                <table id="datos_programador" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Fecha</th>
                            <th>Inicio</th>
                            <th>Salida</th>
                            <th>Salón</th>
                            <th>Docente</th>
                            <th>Materia</th>
                            <th>Verificación</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>


        <div class="col-sm-5 mb-3 mb-sm-0">
    <div class="card">
        <div class="card-body">
            <h5 class="card-title">Asistencias</h5>
            <div class="table-responsive" style="max-height: 400px; overflow-y: auto; overflow-x: hidden;"> <!-- Scroll vertical añadido -->
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

<script src="js/Datatable-Asistencias.js"></script>
<script src="js/Datatable-Programador.js"></script>
