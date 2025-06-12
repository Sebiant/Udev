<?php
include_once '../Componentes/header.php';
include_once '../Conexion.php';

// Establecer idioma español para las fechas
$conn->query("SET lc_time_names = 'es_ES'");

// Consulta para obtener la fecha actual formateada
$sql = "SELECT DATE_FORMAT(CURDATE(), '%W %d de %M de %Y') AS fecha_hoy;";
$resultado = $conn->query($sql);

// Obtener resultado
$fila = $resultado->fetch_assoc();
?>

<div class="container mt-4">
    <div class="card">
        <div class="card-header">
            <h5>Clases Programadas: <?php echo $fila['fecha_hoy']; ?></h5>
        </div>
        <div class="card-body">
            <div class="overflow-auto" style="max-height: 300px;">
                <div class="table-responsive">
                    <table id="datos_programador" class="table table-striped table-bordered">
                        <thead class="thead-dark">
                            <tr>
                                <th>Hora</th>
                                <th>Docente</th>
                                <th>Materia</th>
                                <th>Salón</th>
                            </tr>
                        </thead>
                        <!-- Aquí iría el tbody con los datos dinámicos -->
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include_once '../Componentes/footer.php'; ?>
<script src="js/Datatable-Programador.js"></script>
