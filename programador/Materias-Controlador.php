<?php
include '../Conexion.php';

// Establecer el header para JSON
header('Content-Type: application/json');

$accion = isset($_GET['accion']) ? $_GET['accion'] : 'default';

switch ($accion) {
    default:
        // Consulta para obtener las materias
        $query = "SELECT * FROM modulos";
        $result = mysqli_query($conn, $query);
        
        $materias = array();
        
        if ($result) {
            while ($row = mysqli_fetch_assoc($result)) {
                $materias[] = $row;
            }
            echo json_encode($materias);
        } else {
            echo json_encode(array("error" => "Error al obtener materias: " . mysqli_error($conexion)));
        }
        break;
}
?>
