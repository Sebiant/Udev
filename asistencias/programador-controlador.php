<?php 
include '../conexion.php';

$accion = isset($_GET['accion']) ? $_GET['accion'] : 'default';

switch ($accion) {
    default:
        $sql = "SELECT 
            p.fecha, 
            DATE_FORMAT(p.hora_inicio, '%h:%i %p') AS hora_inicio, 
            DATE_FORMAT(p.hora_salida, '%h:%i %p') AS hora_salida, 
            s.nombre_salon, 
            CONCAT(d.nombres, ' ', d.apellidos) AS nombre_completo, 
            m.nombre, 
            p.estado, 
            p.id_programador 
        FROM programador p 
        JOIN docentes d ON p.numero_documento = d.numero_documento 
        JOIN salones s ON p.id_salon = s.id_salon 
        JOIN materias m ON p.id_materia = m.id_materia 
        WHERE p.estado = 'pendiente' 
        AND YEARWEEK(p.fecha, 1) = YEARWEEK(NOW(), 1)";


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
