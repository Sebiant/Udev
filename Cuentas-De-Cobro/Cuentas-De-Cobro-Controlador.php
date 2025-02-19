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
