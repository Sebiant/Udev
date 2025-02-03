<?php
include '../conexion.php';

$accion = isset($_GET['accion']) ? $_GET['accion'] : 'default';

switch ($accion) {
    case 'exportar':
        $id_cuenta = isset($_GET['id_cuenta']) ? intval($_GET['id_cuenta']) : 0;
        $formato = isset($_GET['formato']) ? $_GET['formato'] : 'pdf';

        if ($id_cuenta > 0) {
            $sql = "SELECT c.*, d.nombres, d.apellidos 
                    FROM cuentas_cobro c 
                    JOIN docentes d ON c.numero_documento = d.numero_documento
                    WHERE c.id_cuenta = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $id_cuenta);
            $stmt->execute();
            $result = $stmt->get_result()->fetch_assoc();

            if ($formato === 'pdf') {
                exportarPDF($result);
            } elseif ($formato === 'csv') {
                exportarCSV($result);
            } else {
                echo "Formato no válido.";
            }
        } else {
            echo "ID de cuenta no válido.";
        }
        break;

    case 'exportar_todos':
        $formato = isset($_GET['formato']) ? $_GET['formato'] : 'pdf';
        $sql = "SELECT c.*, d.nombres, d.apellidos 
                FROM cuentas_cobro c 
                JOIN docentes d ON c.id_docente = d.id_docente";
        $result = $conn->query($sql);

        $data = [];
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $data[] = $row;
            }
        }

        if ($formato === 'pdf') {
            exportarTodosPDF($data);
        } elseif ($formato === 'csv') {
            exportarTodosCSV($data);
        } else {
            echo "Formato no válido.";
        }
        break;

    default:
        $sql = "SELECT c.id_cuenta, c.fecha, c.valor_hora, c.horas_trabajadas, c.monto, d.nombres, d.apellidos, c.estado 
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

function exportarPDF($data) {
    require('../fpdf/fpdf.php');

    $pdf = new FPDF();
    $pdf->AddPage();
    $pdf->SetFont('Arial', 'B', 16);
    $pdf->Cell(40, 10, 'Cuenta de Cobro');
    $pdf->Ln(10);
    $pdf->SetFont('Arial', '', 12);
    $pdf->Cell(0, 10, 'Docente: ' . $data['nombres'] . ' ' . $data['apellidos']);
    $pdf->Ln(8);
    $pdf->Cell(0, 10, 'Fecha: ' . $data['fecha']);
    $pdf->Ln(8);
    $pdf->Cell(0, 10, 'Horas trabajadas: ' . $data['horas_trabajadas']);
    $pdf->Ln(8);
    $pdf->Cell(0, 10, 'Monto: ' . $data['monto']);
    $pdf->Output('D', 'Cuenta_' . $data['nombres'] . '_' . $data['apellidos'] . '.pdf');
}

function exportarCSV($data) {
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="Cuenta_' . $data['nombres'] . '_' . $data['apellidos'] . '.csv"');

    $output = fopen('php://output', 'w');
    fputcsv($output, ['Docente', 'Fecha', 'Horas Trabajadas', 'Monto']);
    fputcsv($output, [$data['nombres'] . ' ' . $data['apellidos'], $data['fecha'], $data['horas_trabajadas'], $data['monto']]);
    fclose($output);
}

function exportarTodosPDF($data) {
    require('../fpdf/fpdf.php');

    $pdf = new FPDF();
    $pdf->AddPage();
    $pdf->SetFont('Arial', 'B', 16);
    $pdf->Cell(40, 10, 'Cuentas de Cobro');
    $pdf->Ln(10);
    $pdf->SetFont('Arial', '', 12);

    foreach ($data as $cuenta) {
        $pdf->Cell(0, 10, 'Docente: ' . $cuenta['nombres'] . ' ' . $cuenta['apellidos']);
        $pdf->Ln(8);
        $pdf->Cell(0, 10, 'Fecha: ' . $cuenta['fecha']);
        $pdf->Ln(8);
        $pdf->Cell(0, 10, 'Horas trabajadas: ' . $cuenta['horas_trabajadas']);
        $pdf->Ln(8);
        $pdf->Cell(0, 10, 'Monto: ' . $cuenta['monto']);
        $pdf->Ln(12);
    }
    $pdf->Output('D', 'Cuentas_Todos_Docentes.pdf');
}

function exportarTodosCSV($data) {
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="Cuentas_Todos_Docentes.csv"');

    $output = fopen('php://output', 'w');
    fputcsv($output, ['Docente', 'Fecha', 'Horas Trabajadas', 'Monto']);
    
    foreach ($data as $cuenta) {
        fputcsv($output, [$cuenta['nombres'] . ' ' . $cuenta['apellidos'], $cuenta['fecha'], $cuenta['horas_trabajadas'], $cuenta['monto']]);
    }
    fclose($output);
}
