<?php 
include '../conexion.php';

$accion = isset($_GET['accion']) ? $_GET['accion'] : 'default';

switch ($accion) {
    case 'obtenerFechas':
        $sql = "SELECT fecha, hora_inicio FROM programador WHERE estado = 'pendiente'";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            $fechaHora = [];
            while ($row = $result->fetch_assoc()) {
                $fechaHora[] = [
                    'fecha' => $row['fecha'],
                    'hora_inicio' => $row['hora_inicio']
                ];
            }
        }
        break;

    case 'cambiarEstado':
        $id_programador = isset($_POST['id_programador']) ? intval($_POST['id_programador']) : null;

        if ($id_programador) {
            $stmt_select = $conn->prepare("SELECT * FROM programador WHERE id_programador = ?");
            $stmt_select->bind_param("i", $id_programador);
            $stmt_select->execute();
            $result = $stmt_select->get_result();

            if ($result->num_rows > 0) {
                $programador = $result->fetch_assoc();
                $estado_actual = $programador['estado'];
                $nuevo_estado = ($estado_actual === 'pendiente') ? 'vista' : 'pendiente';

                $stmt_update = $conn->prepare("UPDATE programador SET estado = ? WHERE id_programador = ?");
                $stmt_update->bind_param("si", $nuevo_estado, $id_programador);
                
                if ($stmt_update->execute()) {
                    echo json_encode(["success" => true, "mensaje" => "Programador actualizado exitosamente."]);
                } else {
                    echo json_encode(["success" => false, "error" => "Error al actualizar el programador: " . $stmt_update->error]);
                }
                $stmt_update->close();
            } else {
                echo json_encode(["success" => false, "error" => "No se encontró el programador."]);
            }
            $stmt_select->close();
        } else {
            echo json_encode(["success" => false, "error" => "ID de programador no proporcionado."]);
        }
        break;

    case 'crearAsistencia':
        $id_programador = isset($_POST['id_programador']) ? intval($_POST['id_programador']) : null;

        if ($id_programador) {
            $sql = "SELECT fecha, numero_documento, TIMEDIFF(hora_salida, hora_inicio) AS horas_trabajadas FROM programador WHERE id_programador = ?";
            $stmt_select = $conn->prepare($sql);
            $stmt_select->bind_param("i", $id_programador);
            $stmt_select->execute();
            $result = $stmt_select->get_result();

            if ($result->num_rows > 0) {
                $insert_sql = "INSERT INTO asistencias (fecha, numero_documento, horas_trabajadas) VALUES (?, ?, ?)";
                $stmt_insert = $conn->prepare($insert_sql);

                while ($row = $result->fetch_assoc()) {
                    $fecha = $row['fecha'];
                    $horas_trabajadas = $row['horas_trabajadas'];
                    $id_docente = $row['numero_documento'];

                    $stmt_insert->bind_param("sss", $fecha, $id_docente, $horas_trabajadas);
                    
                    if ($stmt_insert->execute()) {
                        echo json_encode(["success" => true, "mensaje" => "Asistencia creada exitosamente."]);
                    } else {
                        echo json_encode(["success" => false, "error" => "Error al insertar la asistencia: " . $stmt_insert->error]);
                    }
                }
                $stmt_insert->close();
            } else {
                echo json_encode(["success" => false, "error" => "No se encontró el programador."]);
            }
            $stmt_select->close();
        } else {
            echo json_encode(["success" => false, "error" => "ID de programador no proporcionado."]);
        }
        break;

    default:
        $sql = "SELECT p.fecha, DATE_FORMAT(p.hora_inicio, '%h:%i:%p') AS hora_inicio, DATE_FORMAT(p.hora_salida, '%h:%i:%p') AS hora_salida, s.nombre_salon, CONCAT(d.nombres, ' ', d.apellidos) AS nombre_completo, m.nombre, p.estado, p.id_programador FROM programador p JOIN docentes d ON p.numero_documento = d.numero_documento JOIN salones s ON p.id_salon = s.id_salon JOIN materias m ON p.id_materia = m.id_materia WHERE p.estado = 'pendiente'";

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
