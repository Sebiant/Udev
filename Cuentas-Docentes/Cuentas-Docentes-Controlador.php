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
        // Recibir datos del formulario
        $fecha = $_POST['nueva_fecha'] ?? null;
        $nueva_hora_inicio = $_POST['nueva_hora_inicio'] ?? null;
        $nueva_hora_salida = $_POST['nueva_hora_salida'] ?? null;
        $id_salon = $_POST['id_salon'] ?? null;
        $numero_documento = $_POST['numero_documento'] ?? null;
        $id_modulo = $_POST['id_modulo'] ?? null;
        $id_periodo = $_POST['id_periodo'] ?? null;
        $modalidad = $_POST['modalidad'] ?? null;
        $estado = "Pendiente";
        $clase_original_id = $_POST['id_programador'] ?? null;
        
        // Validación de datos obligatorios
        if (!$clase_original_id || !$fecha || !$nueva_hora_inicio || !$nueva_hora_salida || !$id_salon || !$numero_documento || !$id_modulo || !$id_periodo || !$modalidad) {
            echo json_encode(["error" => "Todos los campos son obligatorios."]);
            exit;
        }
        
        // Iniciar transacción para evitar inconsistencias
        $conn->begin_transaction();
        
        try {
            // Insertar nueva clase reprogramada
            $sql_insert = "INSERT INTO programador (fecha, hora_inicio, hora_salida, id_salon, numero_documento, id_modulo, id_periodo, modalidad, estado, clase_original_id) 
                           VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            
            $stmt = $conn->prepare($sql_insert);
            if (!$stmt) {
                throw new Exception("Error en la preparación de la consulta: " . $conn->error);
            }
            
            // Enlazar parámetros
            $stmt->bind_param("ssssiiisss", $fecha, $nueva_hora_inicio, $nueva_hora_salida, $id_salon, $numero_documento, $id_modulo, $id_periodo, $modalidad, $estado, $clase_original_id);
            
            if (!$stmt->execute()) {
                throw new Exception("Error al reprogramar la clase: " . $stmt->error);
            }
        
            // Actualizar estado de la clase original a "Reprogramada"
            $sql_update = "UPDATE programador SET estado = 'Reprogramada' WHERE id_programador = ?";
            $stmt_update = $conn->prepare($sql_update);
            
            if (!$stmt_update) {
                throw new Exception("Error en la preparación del UPDATE: " . $conn->error);
            }
        
            $stmt_update->bind_param("i", $clase_original_id);
            
            if (!$stmt_update->execute()) {
                throw new Exception("Error al actualizar el estado de la clase original: " . $stmt_update->error);
            }
        
            // Confirmar transacción
            $conn->commit();
            
            echo json_encode(["success" => "Clase reprogramada con éxito"]);
        
        } catch (Exception $e) {
            $conn->rollback(); // Revertir cambios si hay un error
            echo json_encode(["error" => $e->getMessage()]);
        }
    
        $stmt->close();
        $stmt_update->close();
        break;

case 'contarClasesEstado':
    $sql = "SELECT 
            SUM(estado = 'Pendiente') AS pendiente,
            SUM(estado = 'Reprogramada') AS reprogramada,
            SUM(estado = 'Perdida') AS perdida,
            SUM(estado = 'Vista') AS vista
        FROM programador
        WHERE numero_documento = $docente";

    $result = $conn->query($sql);
    
    if ($result) {
        $data = $result->fetch_assoc();
        foreach ($data as $key => $value) {
            if (is_null($value)) {
                $data[$key] = 0;
            }
        }
        echo json_encode([
            "pendiente" => $data['pendiente'],
            "reprogramada" => $data['reprogramada'],
            "perdida" => $data['perdida'],
            "vista" => $data['vista']  // Añadido el estado "Vista"
        ]);
    } else {
        echo json_encode([
            "error" => "Error al contar clases"
        ]);
    }
    break;

    case 'contarCuentasEstado':
        $sql = "SELECT 
                    SUM(estado = 'creada') AS creada,
                    SUM(estado = 'aceptada_docente') AS aceptada_docente,
                    SUM(estado = 'pendiente_firma') AS pendiente_firma,
                    SUM(estado = 'proceso_pago') AS proceso_pago,
                    SUM(estado = 'pagada') AS pagada,
                    SUM(estado = 'rechazada_por_docente') AS rechazada_por_docente,
                    SUM(estado = 'rechazada_por_institucion') AS rechazada_por_institucion
                FROM cuentas_cobro
                WHERE numero_documento = '$docente'";
    
        $result = $conn->query($sql);
    
        if ($result) {
            $data = $result->fetch_assoc();
            foreach ($data as $key => $value) {
                if (is_null($value)) {
                    $data[$key] = 0;
                }
            }
            echo json_encode([
                "creada" => $data['creada'],
                "aceptada_docente" => $data['aceptada_docente'],
                "pendiente_firma" => $data['pendiente_firma'],
                "proceso_pago" => $data['proceso_pago'],
                "pagada" => $data['pagada'],
                "rechazada_por_docente" => $data['rechazada_por_docente'],
                "rechazada_por_institucion" => $data['rechazada_por_institucion']
            ]);
        } else {
            echo json_encode([
                "error" => "Error al contar estados de cuentas de cobro"
            ]);
        }
        break;
        case 'listarClases':
            $conn->query("SET lc_time_names = 'es_ES'");
        
            $sql = "SELECT *,
                p.id_programador,
                p.estado,
                DATE_FORMAT(p.fecha, '%W %d de %M de %Y') AS fecha, 
                CONCAT(DATE_FORMAT(p.hora_inicio, '%h:%i %p'), ' - ', DATE_FORMAT(p.hora_salida, '%h:%i %p')) AS hora,
                m.nombre,
                s.nombre_salon 
                FROM programador p
                JOIN modulos m ON p.id_modulo = m.id_modulo
                JOIN salones s ON p.id_salon = s.id_salon
                WHERE numero_documento = ?
                ORDER BY 
                    CASE 
                        WHEN p.estado = 'Perdida' THEN 1 
                        WHEN p.estado = 'Pendiente' THEN 2 
                        ELSE 3 
                    END, 
                p.fecha ASC";
        
            if ($stmt = $conn->prepare($sql)) {
                $stmt->bind_param("s", $docente);
                $stmt->execute();
                $resultado = $stmt->get_result();
        
                $clases = [];
                while ($fila = $resultado->fetch_assoc()) {
                    // Establecemos los estados correctos
                    if (isset($fila['estado'])) {
                        if ($fila['estado'] === 'Reprogramada') {
                            $fila['estado'] = 'Reagendada';
                        } else if ($fila['estado'] === 'Pendiente') {
                            $fila['estado'] = 'Agendada';
                        }
                    }
                
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
