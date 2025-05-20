<?php 
include '../conexion.php';

$accion = isset($_GET['accion']) ? $_GET['accion'] : 'default';

switch ($accion) {

    case 'cambiarEstado':
        $id_programador = $_POST['id_programador'];
        $estado = $_POST['estado'];

        $sql = "UPDATE programador SET estado=? WHERE id_programador=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('ss', $estado, $id_programador);


        if (!$stmt->execute()) {
            echo "Error al cambiar el estado: " . $stmt->error;
        }
        $stmt->close();
        break;
    default:

        $sql = "SELECT 
                    p.fecha, 
                    DATE_FORMAT(p.hora_inicio, '%h:%i %p') AS hora_inicio, 
                    DATE_FORMAT(p.hora_salida, '%h:%i %p') AS hora_salida, 
                    CONCAT(DATE_FORMAT(p.hora_inicio, '%h:%i %p'), ' - ', DATE_FORMAT(p.hora_salida, '%h:%i %p')) AS horario,
                    s.nombre_salon, 
                    CONCAT(d.nombres, ' ', d.apellidos) AS nombre_completo, 
                    m.nombre, 
                    p.estado, 
                    p.id_programador 
                FROM programador p 
                JOIN docentes d ON p.numero_documento = d.numero_documento 
                JOIN salones s ON p.id_salon = s.id_salon 
                JOIN modulos m ON p.id_modulo = m.id_modulo
                WHERE p.fecha = CURDATE();
                ";



        $result = $conn->query($sql);
        if (!$result) {
            die(json_encode(["error" => "Error en la consulta SQL: " . $conn->error]));
        }
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
