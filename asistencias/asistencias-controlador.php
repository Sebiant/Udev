<?php
include '../conexion.php';

$accion = isset($_GET['accion']) ? $_GET['accion'] : 'default';

switch ($accion) {
 
    default:
        $sql = "SELECT 
            a.fecha, 
            a.horas_trabajadas, 
            d.nombres, 
            d.apellidos 
        FROM asistencias a 
        JOIN docentes d ON a.numero_documento = d.numero_documento
        WHERE YEARWEEK(a.fecha, 1) = YEARWEEK(NOW(), 1)";

        
        $result = $conn->query($sql);
        $data = [];
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $data[] = $row;
            }
        }
        header('Content-Type: application/json');
        echo json_encode(['data' => $data]);
        break;
}
$conn->close();
?>

