<?php
include '../conexion.php';

$accion = isset($_GET['accion']) ? $_GET['accion'] : 'default';

switch ($accion) {
    case 'visualizar': // Cambiado de 'crear' a 'visualizar'
        $sql = "SELECT * FROM cuentas_cobro";
        $result = $conn->query($sql);

        $data = [];

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $data[] = $row;
            }
        }
        
        header('Content-Type: application/json');
        echo json_encode(['data' => $data]);
        break;
}

$conn->close();
?>
