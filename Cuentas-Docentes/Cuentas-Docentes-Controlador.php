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

                if (isset($_POST['id_programador'])) {
                    $id_programador = $_POST['id_programador'];
                    echo "ID recibido: " . htmlspecialchars($id_programador, ENT_QUOTES, 'UTF-8');
                    
                    $sql = "UPDATE programador 
                            SET fecha = ?, hora_inicio = ?, hora_salida = ?, estado = 'Pendiente' 
                            WHERE id_programador = ?";
                            
                    $stmt = $this->conexion->prepare($sql);
                    $stmt->bind_param("sssi", $nuevaFecha, $nuevaHoraInicio, $nuevaHoraSalida, $id_programador);
                    return $stmt->execute();

                } else {
                    echo "No se recibió ningún ID.";
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
