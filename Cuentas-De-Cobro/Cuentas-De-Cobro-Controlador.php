<?php
include '../conexion.php';

$accion = isset($_GET['accion']) ? $_GET['accion'] : 'default';
$conn->query("SET lc_time_names = 'es_ES'");
switch ($accion) {
    case 'exportar':

        require(__DIR__ . '/pdf/fpdf/fpdf.php');
    
        if (!isset($_GET['id_cuenta'])) {
            die("Error: No se proporcionó un ID de cuenta.");
        }
    
        $id_cuenta = $_GET['id_cuenta'];
    
        $sql = "SELECT c.id_cuenta, DATE_FORMAT(c.fecha, '%M %Y') AS fecha, c.valor_hora, c.horas_trabajadas,  
                       (c.valor_hora * c.horas_trabajadas) AS monto, d.nombres, d.apellidos
                FROM cuentas_cobro c
                JOIN docentes d ON c.numero_documento = d.numero_documento
                WHERE c.id_cuenta = ?";
    
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $id_cuenta);
        $stmt->execute();
        $result = $stmt->get_result();
    
        if ($result->num_rows == 0) {
            die("No se encontraron registros.");
        }
    
        $data = $result->fetch_assoc();
        $stmt->close(); // Cerrar consulta de selección
    
        // ✅ Actualizar estado ANTES de cerrar la conexión
        $estado = "pendiente_firma"; 
    
        $sql_update = "UPDATE cuentas_cobro 
                       SET estado = ? 
                       WHERE id_cuenta = ?";
        $stmt_update = $conn->prepare($sql_update);
        
        if ($stmt_update) {
            $stmt_update->bind_param("si", $estado, $id_cuenta);
            
            if (!$stmt_update->execute()) {
                echo json_encode(["error" => "Error al actualizar la cuenta: " . $stmt_update->error]);
                exit;
            }
            $stmt_update->close();
        } else {
            echo json_encode(["error" => "Error al preparar la consulta: " . $conn->error]);
            exit;
        }
    
        $conn->close(); // Cierra la conexión después de actualizar el estado
    
        class PDF extends FPDF {
            function Header() {
                $this->SetFont('Arial', 'B', 16);
                $this->Cell(0, 10, 'Reporte de Cuenta de Cobro', 0, 1, 'C');
                $this->Ln(10);
            }
        }
    
        $pdf = new PDF();
        $pdf->AddPage();
        $pdf->SetFont('Arial', '', 12);
    
        $pdf->Cell(0, 10, "ID Cuenta: " . $data['id_cuenta'], 0, 1);
        $pdf->Cell(0, 10, "Fecha: " . $data['fecha'], 0, 1);
        $pdf->Cell(0, 10, "Docente: " . $data['nombres'] . " " . $data['apellidos'], 0, 1);
        $pdf->Cell(0, 10, "Valor Hora: $" . number_format($data['valor_hora'], 2), 0, 1);
        $pdf->Cell(0, 10, "Horas Trabajadas: " . $data['horas_trabajadas'], 0, 1);
        $pdf->Cell(0, 10, "Monto Total: $" . number_format($data['monto'], 2), 0, 1);
    
        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="cuenta_cobro_' . $data['id_cuenta'] . '.pdf"');
        $pdf->Output('D', 'cuenta_cobro_' . $data['id_cuenta'] . '.pdf');
    
        exit;
        break;
    

    case 'exportar_todos':

        break;

        case 'modificar':
            $id_cuenta = $_POST['id_cuenta'];
            $valor_hora = $_POST['valor_hora'];
            $horas_trabajadas = $_POST['horas_trabajadas'];
            
        
            $sql = "UPDATE cuentas_cobro 
                    SET valor_hora = ?, horas_trabajadas = ?
                    WHERE id_cuenta = ?";
        
            if ($stmt = $conn->prepare($sql)) {
                $stmt->bind_param(
                    "iii",  
                     
                    $valor_hora, 
                    $horas_trabajadas,  
                    $id_cuenta  
                );
        
                if ($stmt->execute()) {
                    echo "Registro actualizado correctamente.";
                } else {
                    echo "Error al actualizar el registro: " . $stmt->error;
                }
        
                $stmt->close();
            } else {
                echo "Error al preparar la consulta: " . $conn->error;
            }
            break;

            case 'BusquedaPorId':
                if (empty($_POST['id_cuenta'])) {
                    echo json_encode(["error" => "Número de cuenta no proporcionado"]);
                    exit;
                }
                $sql = "SELECT c.id_cuenta, DATE_FORMAT(c.fecha, '%M %Y') AS fecha, c.valor_hora, c.horas_trabajadas,  (c.valor_hora * c.horas_trabajadas) AS monto, d.nombres, d.apellidos, c.estado
                FROM cuentas_cobro c
                JOIN docentes d ON c.numero_documento = d.numero_documento
                WHERE id_cuenta=?
                ";
                $stmt = $conn->prepare($sql);
        
                if (!$stmt) {
                    die("Error en la preparación de la consulta: " . $conn->error);
                }
        
                $stmt->bind_param('s', $_POST['id_cuenta']);
                $stmt->execute();
                $result = $stmt->get_result();
        
                if ($result->num_rows > 0) {
                    echo json_encode(['data' => $result->fetch_all(MYSQLI_ASSOC)]);
                } else {
                    echo json_encode(['error' => 'Registro no encontrado']);
                }
                $stmt->close();
                break;

                case 'Firmar':
                    if (empty($_POST['id_cuenta'])) {
                        echo json_encode(["error" => "Número de cuenta no proporcionado"]);
                        exit;
                    }
                
                    $id_cuenta = $_POST['id_cuenta'];
                    $estado = "proceso_pago"; // Estado que se va a actualizar
                    
                    $sql = "UPDATE cuentas_cobro 
                            SET estado = ? 
                            WHERE id_cuenta = ?";
                
                    if ($stmt = $conn->prepare($sql)) {
                        $stmt->bind_param("si", $estado, $id_cuenta); // Se agregan los dos parámetros
                
                        if ($stmt->execute()) {
                            echo json_encode(["success" => true, "message" => "Cuenta firmada correctamente"]);
                        } else {
                            echo json_encode(["error" => "Error al actualizar la cuenta: " . $stmt->error]);
                        }
                
                        $stmt->close();
                    } else {
                        echo json_encode(["error" => "Error al preparar la consulta: " . $conn->error]);
                    }
                    break;
                
                case 'Devolver':
                    if (empty($_POST['id_cuenta'])) {
                        echo json_encode(["error" => "Número de cuenta no proporcionado"]);
                        exit;
                    }
                
                    $id_cuenta = $_POST['id_cuenta'];
                    $estado = "creada"; 
                    
                    $sql = "UPDATE cuentas_cobro 
                            SET estado = ? 
                            WHERE id_cuenta = ?";
                
                    if ($stmt = $conn->prepare($sql)) {
                        $stmt->bind_param("si", $estado, $id_cuenta); // Se agregan los dos parámetros
                
                        if ($stmt->execute()) {
                            echo json_encode(["success" => true, "message" => "Cuenta firmada correctamente"]);
                        } else {
                            echo json_encode(["error" => "Error al actualizar la cuenta: " . $stmt->error]);
                        }
                
                        $stmt->close();
                    } else {
                        echo json_encode(["error" => "Error al preparar la consulta: " . $conn->error]);
                    }
                    break;
                
        
                default:
                
                
                $sql = "SELECT c.id_cuenta, DATE_FORMAT(c.fecha, '%M %Y') AS fecha, c.valor_hora, c.horas_trabajadas,  (c.valor_hora * c.horas_trabajadas) AS monto, d.nombres, d.apellidos, c.estado
                        FROM cuentas_cobro c
                        JOIN docentes d ON c.numero_documento = d.numero_documento
                        AND c.estado <> 'creada'
                        ORDER BY 
                        FIELD(c.estado, 'rechazada_por_docente', 'aceptada_docente', 'pendiente_firma', 'proceso_pago', 'pagada') ASC, 
                        c.fecha ASC;";
            
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
                        'rechazada_por_docente' => 'Rechazada por el docente'
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
