<?php

use LDAP\Result;

include '../Conexion.php';

$accion = isset($_GET['accion']) ? $_GET['accion'] : 'default';
$conn->query("SET lc_time_names = 'es_ES'");

switch ($accion) {
    case 'exportar':
        require(__DIR__ . '/pdf/fpdf/fpdf.php');

        if (!isset($_GET['id_cuenta'])) {
            die("Error: No se proporcionó un ID de cuenta.");
        }

        $id_cuenta = $_GET['id_cuenta'];

        $sql = "SELECT 
                    c.id_cuenta, 
                    DATE_FORMAT(c.fecha, '%M %Y') AS fecha, 
                    c.valor_hora, 
                    c.horas_trabajadas,  
                    (c.valor_hora * c.horas_trabajadas) AS monto, 
                    d.nombres, 
                    d.apellidos
                FROM 
                    cuentas_cobro c
                JOIN 
                    docentes d ON c.numero_documento = d.numero_documento
                WHERE 
                    c.id_cuenta = ?";

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

    case 'abonar':
        $valorAbonado = $_POST['valor_abonado'] ?? null;
        $idCuenta = $_POST['id_cuenta'] ?? null;

        if (!$valorAbonado || !$idCuenta) {
            echo json_encode([
                'success' => false,
                'message' => 'Faltan datos: valor_abonado o id_cuenta.'
            ]);
            break;
        }

        // Obtener el monto total y lo abonado hasta ahora
        $sql = "SELECT 
                    (c.valor_hora * c.horas_trabajadas) AS monto, 
                    COALESCE((SELECT SUM(a.valor_abonado) FROM abonos a WHERE a.id_cuenta = c.id_cuenta), 0) AS total_abonado 
                FROM 
                    cuentas_cobro c
                WHERE 
                    c.id_cuenta = ?";

        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            echo json_encode(['success' => false, 'message' => 'Error al preparar consulta.']);
            break;
        }

        $stmt->bind_param("i", $idCuenta);
        $stmt->execute();
        $stmt->bind_result($monto, $totalAbonado);
        $stmt->fetch();
        $stmt->close();

        // Validar si el abono es permitido
        if (!validarLimiteAbono($valorAbonado, $monto, $totalAbonado)) {
            echo json_encode([
                'success' => false,
                'message' => 'El abono no es válido: puede ser menor a $10.000 o excede el monto restante.'
            ]);
            break;
        }

        // Insertar el abono
        $sql_abono = "INSERT INTO abonos (id_cuenta, valor_abonado) VALUES (?, ?)";
        $stmt = $conn->prepare($sql_abono);

        if ($stmt) {
            $stmt->bind_param("ii", $idCuenta, $valorAbonado);
            if ($stmt->execute()) {
                // Verificar si el abono completa el monto y actualizar estado si es necesario
                validarAbonoOpago($conn, $monto, $valorAbonado, $totalAbonado, $idCuenta);

                echo json_encode(['success' => true, 'message' => 'Abono registrado correctamente.']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Error al registrar: ' . $stmt->error]);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Error al preparar la consulta: ' . $conn->error]);
        }

        $conn->close();
        break;
        
    case 'Firmar':
        $id_cuenta = $_POST['id_cuenta'];

            // Prepara la consulta para actualizar el estado
            $sql_firma = "UPDATE cuentas_cobro SET estado = 'proceso_pago' WHERE id_cuenta = ?";
            $stmt = $conn->prepare($sql_firma);

            if ($stmt) {
                $stmt->bind_param("i", $id_cuenta); // "i" significa integer
                if ($stmt->execute()) {
                    echo "Estado actualizado correctamente a proceso_pago.";
                } else {
                    echo "Error al ejecutar la consulta: " . $stmt->error;
                }
                $stmt->close();
            } else {
                echo "Error al preparar la consulta: " . $conn->error;
            }        
        break;

    case 'Devolver':
        $id_cuenta = $_POST['id_cuenta'];

            // Prepara la consulta para actualizar el estado
            $sql_devolver = "UPDATE cuentas_cobro SET estado = 'creada' WHERE id_cuenta = ?";
            $stmt = $conn->prepare($sql_devolver);

            if ($stmt) {
                $stmt->bind_param("i", $id_cuenta); // "i" significa integer
                if ($stmt->execute()) {
                    echo "Estado actualizado correctamente a creada.";
                } else {
                    echo "Error al ejecutar la consulta: " . $stmt->error;
                }
                $stmt->close();
            } else {
                echo "Error al preparar la consulta: " . $conn->error;
            }    
        break;

    case 'modificar':
        $id_cuenta = $_POST['id_cuenta'];
        $valor_hora = $_POST['valor_hora'];
        $horas_trabajadas = $_POST['horas_trabajadas'];

        $sql = "UPDATE cuentas_cobro 
                SET 
                    valor_hora = ?, 
                    horas_trabajadas = ? 
                WHERE 
                    id_cuenta = ?";

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

    case 'contarCuentasEstado':
        $sql = "SELECT 
                    SUM(estado = 'aceptada_docente') AS aceptada_docente,
                    SUM(estado = 'pendiente_firma') AS pendiente_firma,
                    SUM(estado = 'proceso_pago') AS proceso_pago,
                    SUM(estado = 'pagada') AS pagada,
                    SUM(estado = 'rechazada_por_docente') AS rechazada_por_docente
                FROM 
                    cuentas_cobro";
        
        $result = $conn->query($sql);
    
        if ($result) {
            $data = $result->fetch_assoc();
    
            // Asegurar que no hay nulls
            foreach ($data as $key => $value) {
                if (is_null($value)) {
                    $data[$key] = 0;
                }
            }
    
            echo json_encode([
                "aceptada_docente" => $data['aceptada_docente'],
                "pendiente_firma" => $data['pendiente_firma'],
                "proceso_pago" => $data['proceso_pago'],
                "pagada" => $data['pagada'],
                "rechazada_por_docente" => $data['rechazada_por_docente'],
            ]);
        } else {
            echo json_encode([
                "error" => "Error al contar estados de cuentas de cobro"
            ]);
        }
        break;        

    case 'BusquedaPorId':
        if (empty($_POST['id_cuenta'])) {
            echo json_encode(["error" => "Número de cuenta no proporcionado"]);
            exit;
        }

        $sql = "SELECT 
                    c.id_cuenta, 
                    DATE_FORMAT(c.fecha, '%M %Y') AS fecha, 
                    c.valor_hora, 
                    c.horas_trabajadas,  
                    (c.valor_hora * c.horas_trabajadas) AS monto, 
                    d.nombres, 
                    d.apellidos, 
                    c.estado,
                    COALESCE((SELECT SUM(a.valor_abonado) FROM abonos a WHERE a.id_cuenta = c.id_cuenta), 0) AS total_abonado
                FROM 
                    cuentas_cobro c
                JOIN 
                    docentes d ON c.numero_documento = d.numero_documento
                WHERE 
                    id_cuenta = ?";

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

        // 1. Recibir parámetro estado
        $estado = isset($_GET['estado']) && $_GET['estado'] !== '' ? $conn->real_escape_string($_GET['estado']) : null;

        // 2. Preparar filtro si estado existe
        $filtroEstado = '';
        if ($estado !== null) {
            $filtroEstado = " AND c.estado = '$estado'";
        }
    
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
        $sql = "SELECT 
                    c.id_cuenta, 
                    DATE_FORMAT(c.fecha, '%M %Y') AS fecha, 
                    c.valor_hora, 
                    c.horas_trabajadas,  
                    (c.valor_hora * c.horas_trabajadas) AS monto, 
                    d.nombres, 
                    d.apellidos, 
                    c.estado, 
                    COALESCE((SELECT SUM(a.valor_abonado) FROM abonos a WHERE a.id_cuenta = c.id_cuenta), 0) AS total_abonado 
                FROM 
                    cuentas_cobro c
                JOIN 
                    docentes d ON c.numero_documento = d.numero_documento
                WHERE 
                    c.estado <> 'creada' $filtroEstado $searchQuery 
                LIMIT $start, $length";

        $result = $conn->query($sql);
    
        // Consulta para contar el total de registros sin paginación
        $sqlCount = "SELECT 
                        COUNT(*) as total 
                    FROM 
                        cuentas_cobro c 
                    JOIN 
                        docentes d ON c.numero_documento = d.numero_documento 
                    WHERE 
                        c.estado <> 'creada' $filtroEstado $searchQuery";
                    
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
                $row['total_abonado'] = '$' . number_format($row['total_abonado'], 0, ',', '.');
                $row['estado'] = $estados_legibles[$row['estado']] ?? $row['estado'];
                $data[] = $row;
            }
           
            echo json_encode([
                'draw' => isset($_GET['draw']) ? intval($_GET['draw']) : 1,
                'recordsTotal' => $totalRecords,
                'recordsFiltered' => $totalRecords,
                'data' => $data,
                'estado' => $estado
            ]);
        } else {
            echo json_encode([
                'success' => false, 
                'message' => "Error en la consulta SQL: " . $conn->error
            ]);
        }
        break;
}

function validarLimiteAbono($valorAbonado, $monto, $totalAbonado) {
    include '../Conexion.php';

    // Rechazar abonos menores a 10.000
    if ($valorAbonado < 10000) {
        return false;
    }

    // Validar que el nuevo abono no exceda el monto
    if (($totalAbonado + $valorAbonado) > $monto) {
        return false;
    }
    return true;
}

function validarAbonoOpago($conn, $monto, $valorAbonado, $totalAbonado, $idCuenta) {
    if (($totalAbonado + $valorAbonado) === $monto) {
        $sql = "UPDATE cuentas_cobro SET estado = 'pagada' WHERE id_cuenta = ?";
        $stmt = $conn->prepare($sql);
        if ($stmt) {
            $stmt->bind_param("i", $idCuenta);
            if ($stmt->execute()) {
                return true;
            } else {
                error_log("Error al actualizar estado: " . $stmt->error);
            }
        } else {
            error_log("Error al preparar la consulta: " . $conn->error);
        }
    }
    return false;
}