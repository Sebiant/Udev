<?php
include '../conexion.php';

$accion = isset($_GET['accion']) ? $_GET['accion'] : 'default';

switch ($accion) {
    default: // Cambiado de 'crear' a 'visualizar'
        $sql = "SELECT c.id_cuenta, c.fecha, c.valor_hora, c.horas_trabajadas, c.monto, d.nombres, d.apellidos, c.estado 
        FROM cuentas_cobro c
        JOIN docentes d ON d.id_docente = c.id_docente";
        $result = $conn->query($sql);

        $data = [];

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $row['estado'] = ($row['estado'] == 1) ? "Activo" : "Inactivo";
                $data[] = $row;
            }
        }
        
        header('Content-Type: application/json');
        echo json_encode(['data' => $data]);
        break;
}

$conn->close();
?>
