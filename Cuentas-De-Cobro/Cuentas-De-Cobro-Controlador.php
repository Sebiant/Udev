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
        $stmt->close();

        $estado = "pendiente_firma";

        $sql_update = "UPDATE cuentas_cobro SET estado = ? WHERE id_cuenta = ?";
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

        $conn->close();

        class PDF extends FPDF {
            function Header() {
                $this->SetFont('Arial', 'B', 16);
                $this->Cell(0, 10, 'Reporte de Cuenta de Cobro', 0, 1, 'C');
                $this->Ln(10);
            }
            function Footer() {
                $this->SetY(-15);
                $this->SetFont('Arial', 'I', 8);
                $this->Cell(0, 10, 'Página ' . $this->PageNo(), 0, 0, 'C');
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
        $pdf->Cell(100, 10, "Firma del Docente: ____________________", 0, 1);

        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="cuenta_cobro_' . $data['id_cuenta'] . '.pdf"');
        $pdf->Output('D', 'cuenta_cobro_' . $data['id_cuenta'] . '.pdf');

        exit;
        break;

    case 'modificar':
        $id_cuenta = $_POST['id_cuenta'];
        $valor_hora = $_POST['valor_hora'];
        $horas_trabajadas = $_POST['horas_trabajadas'];

        $sql = "UPDATE cuentas_cobro SET valor_hora = ?, horas_trabajadas = ? WHERE id_cuenta = ?";

        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("iii", $valor_hora, $horas_trabajadas, $id_cuenta);
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

        $sql = "SELECT c.id_cuenta, DATE_FORMAT(c.fecha, '%M %Y') AS fecha, c.valor_hora, c.horas_trabajadas,  
                       (c.valor_hora * c.horas_trabajadas) AS monto, d.nombres, d.apellidos, c.estado
                FROM cuentas_cobro c
                JOIN docentes d ON c.numero_documento = d.numero_documento
                WHERE id_cuenta=?";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param('s', $_POST['id_cuenta']);
        $stmt->execute();
        $result = $stmt->get_result();

        echo json_encode(['data' => $result->num_rows > 0 ? $result->fetch_all(MYSQLI_ASSOC) : ['error' => 'Registro no encontrado']]);
        $stmt->close();
        break;

    default:
        // Obtener parámetros de DataTables
        $start = isset($_GET['start']) ? intval($_GET['start']) : 0;
        $length = isset($_GET['length']) ? intval($_GET['length']) : 10;
        $search = isset($_GET['search']['value']) ? $conn->real_escape_string($_GET['search']['value']) : '';
    
        // Filtro de búsqueda
        $searchQuery = "";
        if (!empty($search)) {
            $searchQuery = " AND (d.nombres LIKE '%$search%' 
                                OR d.apellidos LIKE '%$search%' 
                                OR c.horas_trabajadas LIKE '%$search%'
                                OR c.estado LIKE '%$search%' 
                                OR DATE_FORMAT(c.fecha, '%M %Y') LIKE '%$search%')";
        }
    
        // Consulta principal con paginación y búsqueda
        $sql = "SELECT c.id_cuenta, DATE_FORMAT(c.fecha, '%M %Y') AS fecha, c.valor_hora, c.horas_trabajadas,  
                        (c.valor_hora * c.horas_trabajadas) AS monto, d.nombres, d.apellidos, c.estado
                FROM cuentas_cobro c
                JOIN docentes d ON c.numero_documento = d.numero_documento
                WHERE c.estado <> 'creada' $searchQuery
                ORDER BY 
                    FIELD(c.estado, 'rechazada_por_docente', 'aceptada_docente', 'pendiente_firma', 'proceso_pago', 'pagada') ASC, 
                    c.fecha ASC
                LIMIT $start, $length;";
    
        $result = $conn->query($sql);
    
        // Consulta para contar el total de registros sin paginación
        $sqlCount = "SELECT COUNT(*) as total 
                    FROM cuentas_cobro c 
                    JOIN docentes d ON c.numero_documento = d.numero_documento 
                    WHERE c.estado <> 'creada' $searchQuery;";
                    
        $resultCount = $conn->query($sqlCount);
        $totalRecords = $resultCount->fetch_assoc()['total'];
    
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
    
            while ($row = $result->fetch_assoc()) {
                $row['valor_hora'] = '$' . number_format($row['valor_hora'], 0, ',', '.');
                $row['monto'] = '$' . number_format($row['monto'], 0, ',', '.');
                $row['estado'] = $estados_legibles[$row['estado']] ?? $row['estado'];
                $data[] = $row;
            }
    
            echo json_encode([
                'draw' => isset($_GET['draw']) ? intval($_GET['draw']) : 1,
                'recordsTotal' => $totalRecords,
                'recordsFiltered' => $totalRecords,
                'data' => $data
            ]);
        } else {
            echo json_encode([
                'success' => false, 
                'message' => "Error en la consulta SQL: " . $conn->error
            ]);
        }
        break;
    }

$conn->close();
