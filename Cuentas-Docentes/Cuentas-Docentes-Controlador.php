<?php

include '../conexion.php';
include 'idDocente.php';

$accion = isset($_GET['accion']) ? $_GET['accion'] : 'default';

switch ($accion) {
    case 'Aceptar':
        $sql_select = "SELECT id_cuenta 
                       FROM cuentas_cobro 
                       WHERE estado = 'creada' 
                       ORDER BY fecha ASC 
                       LIMIT 1";
    
        $result = $conn->query($sql_select);
    
        if ($row = $result->fetch_assoc()) {
            $id_cuenta = $row['id_cuenta'];
    
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
            echo json_encode(["success" => false, "message" => "No hay cuentas en estado 'creada'."]);
        }
    
        break;

        case 'Rechazar':
            $sql_select = "SELECT id_cuenta 
                           FROM cuentas_cobro 
                           WHERE estado = 'creada' 
                           ORDER BY fecha ASC 
                           LIMIT 1";
        
            $result = $conn->query($sql_select);
        
            if ($row = $result->fetch_assoc()) {
                $id_cuenta = $row['id_cuenta'];
        
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
                echo json_encode(["success" => false, "message" => "No hay cuentas en estado 'creada'."]);
            }
        
            break;
        
        default:
        $conn->query("SET lc_time_names = 'es_ES'");
        
        $sql = "SELECT c.id_cuenta, DATE_FORMAT(c.fecha, '%M %Y') AS fecha, c.valor_hora, c.horas_trabajadas, (c.valor_hora * c.horas_trabajadas) AS monto, d.nombres, d.apellidos, c.estado
                FROM cuentas_cobro c
                JOIN docentes d ON c.numero_documento = d.numero_documento
                WHERE d.numero_documento = $docente
                AND c.estado <> 'creada'
                ORDER BY FIELD(c.estado, 'rechazada_por_docente', 'aceptada_docente', 'pendiente_firma', 'proceso_pago', 'pagada'), c.fecha ASC;";
    
        $result = $conn->query($sql);
        header('Content-Type: application/json');
    
        if ($result) {
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
            echo json_encode(['success' => true, 'data' => $data]);
        } else {
            echo json_encode(['success' => false, 'message' => "Error en la consulta SQL: " . $conn->error]);
        }
        break;
}
$conn->close();
?>
