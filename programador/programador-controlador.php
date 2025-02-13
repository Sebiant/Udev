<?php
include '../conexion.php';

$accion = $_GET['accion'] ?? 'default';

switch ($accion) {
    case 'crear':
        $fecha = $_POST['fecha'];
        $hora_inicio = $_POST['hora_inicio'];
        $hora_salida = $_POST['hora_salida'];
        $salon = $_POST['id_salon'];
        $docente = $_POST['numero_documento'];
        $modulo = $_POST['id_asignacion_periodo'];
        $estado = $_POST['estado'];
        $modalidad = $_POST['modalidad'];

        $sql = "INSERT INTO programador (fecha, hora_inicio, hora_salida, id_salon, numero_documento, id_asignacion_periodo, estado, modalidad) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('sssssiss', $fecha, $hora_inicio, $hora_salida, $salon, $docente, $modulo, $estado, $modalidad);

        if ($stmt->execute()) {
            echo json_encode(['status' => 'success', 'message' => 'Programador creado con éxito.']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Error al crear el programador: ' . $stmt->error]);
        }

        $stmt->close();
        break;

    case 'editar':
        $id_programador = $_POST['id_programador'];
        $fecha = $_POST['fecha'];
        $hora_inicio = $_POST['hora_inicio'];
        $hora_salida = $_POST['hora_salida'];
        $salon = $_POST['id_salon'];
        $docente = $_POST['numero_documento'];
        $modulo = $_POST['id_asignacion_periodo'];
        $modalidad = $_POST['modalidad'];
        $estado = $_POST['estado'] ?? null;

        $sql = "UPDATE programador 
                SET fecha=?, hora_inicio=?, hora_salida=?, id_salon=?, numero_documento=?, id_asignacion_periodo=?, modalidad=?, estado=?  
                WHERE id_programador=?";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param('ssssssssi', $fecha, $hora_inicio, $hora_salida, $salon, $docente, $modulo, $modalidad, $estado, $id_programador);

        if ($stmt->execute()) {
            echo json_encode(['status' => 'success', 'message' => 'Programador actualizado con éxito.']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Error al actualizar el programador: ' . $stmt->error]);
        }

        $stmt->close();
        break;

    case 'BusquedaPorId':
        $id_programador = $_POST['id_programador'] ?? null;

        if (!$id_programador) {
            echo json_encode(['error' => 'ID no proporcionado']);
            exit;
        }

        $sql = "SELECT * FROM programador WHERE id_programador = ?";
        $stmt = $conn->prepare($sql);

        if (!$stmt) {
            die(json_encode(['error' => 'Error en la preparación de la consulta: ' . $conn->error]));
        }

        $stmt->bind_param('i', $id_programador);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $data = $result->fetch_all(MYSQLI_ASSOC);
            echo json_encode(['data' => $data]);
        } else {
            echo json_encode(['error' => 'Registro no encontrado']);
        }

        $stmt->close();
        break;

    case 'default':
        $conn->query("SET lc_time_names = 'es_ES'");

        $sql = "SELECT 
                    p.id_programador, 
                    DATE_FORMAT(p.fecha, '%d/%M/%Y') AS fecha, 
                    DATE_FORMAT(p.hora_inicio, '%h:%i %p') AS hora_inicio, 
                    DATE_FORMAT(p.hora_salida, '%h:%i %p') AS hora_salida, 
                    d.nombres,
                    d.apellidos,
                    s.nombre_salon, 
                    m.nombre AS nombre_modulo,
                    p.estado,
                    p.modalidad
                FROM programador p
                JOIN docentes d ON p.numero_documento = d.numero_documento
                JOIN salones s ON p.id_salon = s.id_salon
                LEFT JOIN asignacion_a_modulo am ON p.id_asignacion_periodo = am.id_asignacion
                LEFT JOIN modulos m ON am.id_modulo = m.id_modulo";

        $result = $conn->query($sql);

        $data = [];

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $data[] = $row; // Corrección de índice incorrecto
            }
        }

        header('Content-Type: application/json');
        echo json_encode(['data' => $data]);
        break;
}

$conn->close();
?>
