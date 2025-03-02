<?php
include '../conexion.php';

$accion = isset($_GET['accion']) ? $_GET['accion'] : 'default';

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
            $fecha = $_POST['fecha'];
            $valor_hora = $_POST['valor_hora'];
            $horas_trabajadas = $_POST['horas_trabajadas'];
            $monto = $_POST['monto'];
            $numero_documento = $_POST['numero_documento'];
        
            $sql = "UPDATE cuentas_cobro 
                    SET fecha = ?, valor_hora = ?, horas_trabajadas = ?, monto = ?, numero_documento = ? 
                    WHERE id_cuenta = ?";
        
            if ($stmt = $conn->prepare($sql)) {
                $stmt->bind_param(
                    "siiisi",  
                    $fecha, 
                    $valor_hora, 
                    $horas_trabajadas, 
                    $monto, 
                    $numero_documento, 
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
                $sql = "SELECT * FROM cuentas_cobro WHERE id_cuenta=?";
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
        
        

    default:
        $sql = "SELECT c.id_cuenta, c.fecha, c.valor_hora, c.horas_trabajadas, c.monto, 
                       d.nombres, d.apellidos, c.estado 
                FROM cuentas_cobro c
                JOIN docentes d ON d.numero_documento = c.numero_documento";
        $result = $conn->query($sql);

        $data = [];
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $row['valor_hora'] = '$' . number_format($row['valor_hora'], 2, '.', ',');
                $row['monto'] = '$' . number_format($row['monto'], 2, '.', ',');
                $data[] = $row;
            }
        }

        header('Content-Type: application/json');
        echo json_encode(['data' => $data]);
        break;
}

$conn->close();
