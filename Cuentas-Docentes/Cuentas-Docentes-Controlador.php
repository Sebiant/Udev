<?php

include '../conexion.php';
include 'idDocente.php';

$accion = isset($_GET['accion']) ? $_GET['accion'] : 'default';

switch ($accion) {
    case 'Aceptar':
        $id_cuenta = $_POST['id_cuenta'];
        if ($id_cuenta > 0) {
            $sql_update = "UPDATE cuentas_cobro SET estado = 'aceptada_docente' WHERE id_cuenta = ?";
            $stmt_update = $conn->prepare($sql_update);
            $stmt_update->bind_param("i", $id_cuenta);

            if ($stmt_update->execute()) {
                echo json_encode(["success" => true, "message" => "Estado actualizado a 'aceptada_docente'."]);
            } else {
                echo json_encode(["success" => false, "message" => "Error al actualizar el estado: " . $conn->error]);
            }
            $stmt_update->close();
        } else {
            echo json_encode(["success" => false, "message" => "ID de cuenta inválido."]);
        }
        break;

    case 'Rechazar':
        $id_cuenta = $_POST['id_cuenta'];
        if ($id_cuenta > 0) {
            $sql_update = "UPDATE cuentas_cobro SET estado = 'rechazada_por_docente' WHERE id_cuenta = ?";
            $stmt_update = $conn->prepare($sql_update);
            $stmt_update->bind_param("i", $id_cuenta);

            if ($stmt_update->execute()) {
                echo json_encode(["success" => true, "message" => "Estado actualizado a 'rechazada_por_docente'."]);
            } else {
                echo json_encode(["success" => false, "message" => "Error al actualizar el estado: " . $conn->error]);
            }
            $stmt_update->close();
        } else {
            echo json_encode(["success" => false, "message" => "ID de cuenta inválido."]);
        }
        break;

    case 'reprogramar':
        $id_programador = $_POST['id_programador'] ?? null;
        $nueva_fecha = $_POST['nueva_fecha'] ?? null;
        $nueva_hora_inicio = $_POST['nueva_hora_inicio'] ?? null;
        $nueva_hora_salida = $_POST['nueva_hora_salida'] ?? null;
        $estado = "Pendiente";

        if (!$id_programador || !$nueva_fecha || !$nueva_hora_inicio || !$nueva_hora_salida) {
            echo "Error: Todos los campos son obligatorios.";
            exit;
        }

        $sql = "UPDATE programador SET fecha = ?, hora_inicio = ?, hora_salida = ?, estado = ? WHERE id_programador = ?";
        $stmt = $conn->prepare($sql);
        
        if ($stmt) {
            $stmt->bind_param("sssss", $nueva_fecha, $nueva_hora_inicio, $nueva_hora_salida, $estado, $id_programador);
            
            if ($stmt->execute()) {
                echo "Reprogramación exitosa.";
            } else {
                echo "Error al actualizar: " . $stmt->error;
            }
        
            $stmt->close();
        } else {
            echo "Error en la preparación de la consulta: " . $conn->error;
        } 
        break;
        
        case 'listarClases':
            $conn->query("SET lc_time_names = 'es_ES'");

            $sql = "SELECT
                p.id_programador,
                p.estado,
                DATE_FORMAT(p.fecha, '%W %d de %M de %Y') AS fecha, 
                CONCAT(DATE_FORMAT(p.hora_inicio, '%h:%i %p'), ' - ', DATE_FORMAT(p.hora_salida, '%h:%i %p')) AS hora,
                m.nombre,
                s.nombre_salon
            FROM programador p
            JOIN modulos m ON p.id_modulo = m.id_modulo
            JOIN salones s ON p.id_salon = s.id_salon
            WHERE p.numero_documento = ?
            AND WEEK(p.fecha, 1) = WEEK(CURDATE(), 1)  -- Filtra la semana actual
            AND YEAR(p.fecha) = YEAR(CURDATE())        -- Asegura que sea del mismo año
            AND p.estado IN ('Pendiente', 'Perdida')
            ORDER BY FIELD(p.estado, 'Perdida', 'Pendiente'), p.fecha ASC, p.hora_inicio ASC";

            if ($stmt = $conn->prepare($sql)) {
                $stmt->bind_param("s", $docente);
                $stmt->execute();
                $resultado = $stmt->get_result();
                    
                $clases = [];
                while ($fila = $resultado->fetch_assoc()) {
                    $clases[] = $fila;
                }
            
                    echo json_encode(["data" => $clases]);
                } else {
                    echo json_encode(["error" => "Error en la consulta: " . $conn->error]);
                }
            break;
        
        default:
        
        header('Content-Type: application/json');

        $conn->query("SET lc_time_names = 'es_ES'");

        $draw = isset($_POST['draw']) ? intval($_POST['draw']) : 1;
        $start = isset($_POST['start']) ? intval($_POST['start']) : 0;
        $length = isset($_POST['length']) ? intval($_POST['length']) : 10;
        $searchValue = isset($_POST['search']['value']) ? $_POST['search']['value'] : '';

        $columns = ['fecha', 'nombres', 'valor_hora', 'horas_trabajadas', 'monto', 'estado'];
        $orderColumnIndex = isset($_POST['order'][0]['column']) ? intval($_POST['order'][0]['column']) : 0;
        $orderColumn = $columns[$orderColumnIndex];
        $orderDir = isset($_POST['order'][0]['dir']) ? $_POST['order'][0]['dir'] : 'asc';
        
        $searchQuery = "";
        if (!empty($searchValue)) {
            $searchQuery = " AND (DATE_FORMAT(c.fecha, '%M %Y') LIKE '%$searchValue%'
                                OR d.nombres LIKE '%$searchValue%'
                                OR d.apellidos LIKE '%$searchValue%'
                                OR c.estado LIKE '%$searchValue%')";
        }
        
        $sql = "SELECT c.id_cuenta, DATE_FORMAT(c.fecha, '%M %Y') AS fecha, c.valor_hora, c.horas_trabajadas, 
                       (c.valor_hora * c.horas_trabajadas) AS monto, d.nombres, d.apellidos, c.estado
                FROM cuentas_cobro c
                JOIN docentes d ON c.numero_documento = d.numero_documento
                WHERE d.numero_documento = $docente
                AND c.estado <> 'creada'
                $searchQuery
                ORDER BY c.fecha DESC, $orderColumn $orderDir
                LIMIT $start, $length";
        
        $result = $conn->query($sql);

        $countFilteredQuery = "SELECT COUNT(*) as total FROM cuentas_cobro c
                               JOIN docentes d ON c.numero_documento = d.numero_documento
                               WHERE d.numero_documento = $docente AND c.estado <> 'creada' $searchQuery";
        
        $countFilteredResult = $conn->query($countFilteredQuery);
        $countFiltered = $countFilteredResult->fetch_assoc()['total'];

        $countTotalQuery = "SELECT COUNT(*) as total FROM cuentas_cobro WHERE estado <> 'creada'";
        $countTotalResult = $conn->query($countTotalQuery);
        $countTotal = $countTotalResult->fetch_assoc()['total'];
        
        $data = [];
        $estados_legibles = [
            'creada' => 'Creada',
            'aceptada_docente' => 'Aceptada por el docente',
            'pendiente_firma' => 'Pendiente de firma',
            'proceso_pago' => 'En proceso de pago',
            'pagada' => 'Pagada',
            'rechazada_por_docente' => 'Rechazada por el docente',
            'rechazada_por_institucion' => 'Rechazada por la institución'
        ];
        
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $row['valor_hora'] = '$' . number_format($row['valor_hora'], 0, ',', '.');
                $row['monto'] = '$' . number_format($row['monto'], 0, ',', '.');
                $row['estado'] = $estados_legibles[$row['estado']] ?? $row['estado'];
                $data[] = $row;
            }
        }
        
        echo json_encode([
            "draw" => $draw,
            "recordsTotal" => $countTotal,
            "recordsFiltered" => $countFiltered,
            "data" => $data
        ]);
        break;
}
$conn->close();
?>
