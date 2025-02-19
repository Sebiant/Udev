<?php

include '../conexion.php';
include 'idDocente.php';

$accion = isset($_GET['accion']) ? $_GET['accion'] : 'default';

switch ($accion) {
    case 'Aceptar':
        if (!isset($_POST['id_cuenta']) || empty($_POST['id_cuenta'])) {
            echo json_encode(["success" => false, "message" => "ID de cuenta no proporcionado."]);
            break;
        }
        $id_cuenta = $_POST['id_cuenta'];

        $sql_update = "UPDATE cuentas_cobro SET estado = 'aceptada_docente' WHERE id_cuenta = '$id_cuenta'";

        if ($conn->query($sql_update) === TRUE) {
            echo json_encode(["success" => true, "message" => "Estado actualizado a 'aceptada_docente'."]);
        } else {
            echo json_encode(["success" => false, "message" => "Error al actualizar el estado: " . $conn->error]);
        }
        break;

    case 'Rechazar':
        if (!isset($_POST['id_cuenta']) || empty($_POST['id_cuenta'])) {
            echo json_encode(["success" => false, "message" => "ID de cuenta no proporcionado."]);
            break;
        }
        $id_cuenta = $_POST['id_cuenta'];

        $sql_update = "UPDATE cuentas_cobro SET estado = 'rechazada_por_docente' WHERE id_cuenta = '$id_cuenta'";

        if ($conn->query($sql_update) === TRUE) {
            echo json_encode(["success" => true, "message" => "Estado actualizado a 'rechazada_por_docente'."]);
        } else {
            echo json_encode(["success" => false, "message" => "Error al actualizar el estado: " . $conn->error]);
        }
        break;

    default:
        $sql = "SELECT c.id_cuenta, c.fecha, c.valor_hora, c.horas_trabajadas, c.monto, d.nombres, d.apellidos, c.estado
                FROM cuentas_cobro c
                JOIN docentes d ON c.numero_documento = d.numero_documento
                WHERE d.numero_documento = $docente";

        $result = $conn->query($sql);
    header('Content-Type: application/json');
    if ($result) {
        $data = [];
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
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
?>
