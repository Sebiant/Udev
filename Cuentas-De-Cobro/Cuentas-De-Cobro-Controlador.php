<?php
include '../conexion.php';

$accion = isset($_GET['accion']) ? $_GET['accion'] : 'default';
$conn->query("SET lc_time_names = 'es_ES'");
switch ($accion) {
    case 'exportar':
        $id_cuenta = isset($_GET['id_cuenta']) ? intval($_GET['id_cuenta']) : 0;

        if ($id_cuenta > 0) {
            $sql = "SELECT c.*, d.nombres, d.apellidos 
                    FROM cuentas_cobro c 
                    JOIN docentes d ON c.numero_documento = d.numero_documento
                    WHERE c.id_cuenta = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $id_cuenta);
            $stmt->execute();
            $result = $stmt->get_result()->fetch_assoc();
        }
        break;

    case 'exportar_todos':
        $sql = "SELECT c.*, d.nombres, d.apellidos 
                FROM cuentas_cobro c 
                JOIN docentes d ON c.numero_documento = d.numero_documento";
        $result = $conn->query($sql);

        // Nombre del archivo CSV
        $filename = "export_" . date("Y-m-d_H-i-s") . ".csv";

        // Cabecera para descarga
        header("Content-Type: text/csv; charset=UTF-8");
        header("Content-Disposition: attachment; filename=$filename");

        // Abrir salida de buffer
        $output = fopen("php://output", "w");

        // Verificar si hay resultados
        if ($result->num_rows > 0) {
            // Obtener y escribir los nombres de las columnas
            $firstRow = $result->fetch_assoc();
            fputcsv($output, array_keys($firstRow)); // Encabezados
            fputcsv($output, $firstRow); // Primera fila

            // Escribir el resto de las filas
            while ($row = $result->fetch_assoc()) {
                fputcsv($output, $row);
            }
        }

        fclose($output);
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
