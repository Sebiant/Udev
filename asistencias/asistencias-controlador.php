<?php
include '../conexion.php';

$accion = isset($_GET['accion']) ? $_GET['accion'] : 'default';

switch ($accion) {

    case 'Historial':
        $conn->query("SET lc_time_names = 'es_ES'");

        $docente = $_POST['numero_documento'];
        $mes = $_POST['mes'];
        $año = $_POST['año'];
    
        // Verifica que los datos no estén vacíos
        if (!empty($docente) && !empty($mes) && !empty($año)) {
            
            $sql = "SELECT DATE_FORMAT(a.fecha, '%W %d de %M de %Y') AS fecha, 
                DATE_FORMAT(a.hora_entrada, '%h:%i %p') AS hora_entrada, 
                DATE_FORMAT(a.hora_salida, '%h:%i %p') AS hora_salida, 
                        d.nombres,
                        d.apellidos,
                        a.estado
                FROM asistencias a
                JOIN programador p ON a.id_programador = p.id_programador
                JOIN docentes d ON p.numero_documento = d.numero_documento
                WHERE p.numero_documento = ? 
                AND MONTH(a.fecha) = ? 
                AND YEAR(a.fecha) = ?
                ORDER BY a.fecha ASC";

            $stmt = $conn->prepare($sql);
            if (!$stmt) {
                die("Error en prepare(): " . $conn->error);
            }
            $stmt->bind_param("iii", $docente, $mes, $año);
            $stmt->execute();
            $result = $stmt->get_result();
            
            
            $data = [];
            while ($row = $result->fetch_assoc()) {
                $data[] = $row;
            }
    
            header('Content-Type: application/json');
            echo json_encode(['data' => $data]);
        } else {
            echo json_encode(['error' => 'Faltan datos en la solicitud']);
        }
    
        break;    
    
    case 'ClasePerdida':
        // Depurar el valor de $_POST['id']
        die("Valor de ID recibido en el servidor: " . (isset($_POST['id']) ? $_POST['id'] : 'No recibido'));
    
        if (isset($_POST['id']) && !empty($_POST['id'])) {
            $id_programador = $_POST['id'];
    
            $sql = "UPDATE programador SET estado = 'Perdida' WHERE id_programador = ?";
    
            $stmt = $conn->prepare($sql);
    
            if ($stmt === false) {
                die("Error al preparar la consulta: " . $conn->error);  // Detener ejecución si hay error
            }
    
            $stmt->bind_param("s", $id_programador);  // Usamos la variable correcta
    
            if ($stmt->execute()) {
                echo json_encode(['success' => true, 'message' => 'La operación se completó con éxito.']);
            } else {
                echo json_encode(['success' => false, 'error' => $stmt->error]);
            }
            $stmt->close();
        } else {
            echo json_encode(['success' => false, 'error' => 'Datos incompletos']);
        }
        break;
    
    case 'BusquedaPorId':
        if (empty($_POST['id_programador'])) {
            echo json_encode(["error" => "Número de clase no proporcionado"]);
            exit;
        }
    
        $sql = "SELECT
                    p.id_programador,
                    p.estado,
                    DATE_FORMAT(p.fecha, '%W %d de %M de %Y') AS fecha, 
                    DATE_FORMAT(p.hora_inicio, '%h:%i %p') AS hora_inicio, 
                    DATE_FORMAT(p.hora_salida, '%h:%i %p') AS hora_salida, 
                    m.nombre,
                    d.nombres, 
                    d.apellidos, 
                    s.nombre_salon
                FROM programador p
                JOIN docentes d ON p.numero_documento = d.numero_documento 
                JOIN modulos m ON p.id_modulo = m.id_modulo
                JOIN salones s ON p.id_salon = s.id_salon";
    
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('s', $_POST['id_programador']);
        $stmt->execute();
        $result = $stmt->get_result();
    
        echo json_encode(['data' => $result->num_rows > 0 ? $result->fetch_all(MYSQLI_ASSOC) : ['error' => 'Registro no encontrado']]);
        $stmt->close();
        break;
    
    default:
        $sql = "SELECT 
                    DATE_FORMAT(a.fecha, '%M') AS fecha, 
                    a.hora_entrada,
                    a.hora_salida, 
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
    

