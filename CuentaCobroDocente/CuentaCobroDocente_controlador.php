<?php

include '../conexion.php';
include 'idDocente.php';

$accion = isset($_GET['accion']) ? $_GET['accion'] : 'default';

switch ($accion) {
    case 'crear':
        $fecha = $_POST['fecha'];
        $pago_excepcional = $_POST['pago_excepcional'];
        $valor_hora = $_POST['valor_hora'];
        $horas_trabajadas = $_POST['horas_trabajadas'];
        $monto = $_POST['monto'];
        $id_docente = $_POST['id_docente'];
        $estado = $_POST['estado'];

        $sql = "INSERT INTO CuentaDeCobroDocente (fecha, pago_excepcional, valor_hora, horas_trabajadas, monto, id_docente, estado) 
                VALUES ('$fecha','$pago_excepcional','$valor_hora','$horas_trabajadas','$monto','$id_docente','$estado')";
        
        if ($conn->query($sql) === TRUE) {
            echo json_encode(["success" => true, "message" => "Registro exitoso"]);
        } else {
            echo json_encode(["success" => false, "message" => "Error al registrar: " . $conn->error]);
        }
        break;

    case 'editar':
        $id_cuenta = $_POST['id_cuenta'];

        $sql_select = "SELECT * FROM CuentaDeCobroDocente WHERE id_cuenta = '$id_cuenta'";
        $result = $conn->query($sql_select);

        if ($result->num_rows > 0) {
            $cuenta = $result->fetch_assoc();

            $fecha = isset($_POST['fecha']) ? $_POST['fecha'] : $cuenta['fecha'];
            $pago_excepcional = isset($_POST['pago_excepcional']) ? $_POST['pago_excepcional'] : $cuenta['pago_excepcional'];
            $valor_hora = isset($_POST['valor_hora']) ? $_POST['valor_hora'] : $cuenta['valor_hora'];
            $horas_trabajadas = isset($_POST['horas_trabajadas']) ? $_POST['horas_trabajadas'] : $cuenta['horas_trabajadas'];
            $monto = isset($_POST['monto']) ? $_POST['monto'] : $cuenta['monto'];
            $id_docente = isset($_POST['id_docente']) ? $_POST['id_docente'] : $cuenta['id_docente'];
            $estado = isset($_POST['estado']) ? $_POST['estado'] : $cuenta['estado'];
            
            $sql_update = "UPDATE CuentaDeCobroDocente SET
                            fecha='$fecha',
                            pago_excepcional='$pago_excepcional',
                            valor_hora='$valor_hora',
                            horas_trabajadas='$horas_trabajadas',
                            monto='$monto',
                            id_docente='$id_docente',
                            estado='$estado'
                            WHERE id_cuenta = '$id_cuenta'";
            
            if ($conn->query($sql_update) === TRUE) {
                echo json_encode(["success" => true, "message" => "Registro actualizado exitosamente."]);
            } else {
                echo json_encode(["success" => false, "message" => "Error al actualizar el registro: " . $conn->error]);
            }
        } else {
            echo json_encode(["success" => false, "message" => "No hay registros para actualizar."]);
        }
        break;

    case 'Aceptar':
        if (!isset($_POST['id_cuenta']) || empty($_POST['id_cuenta'])) {
            echo json_encode(["success" => false, "message" => "ID de cuenta no proporcionado."]);
            break;
        }
        $id_cuenta = $_POST['id_cuenta'];
            aceptarFuncion($conn, $id_cuenta);
        break;

    default:
        $sql = "SELECT c.id_cuenta, c.fecha, c.pago_excepcional, c.valor_hora, c.horas_trabajadas, c.monto, d.nombres, d.apellidos, c.estado
                FROM cuentas_cobro c
                JOIN docentes d ON c.id_docente = d.id_docente
                WHERE d.id_docente = $docente
                AND c.estado = 'en_proceso'";

        $result = $conn->query($sql);
    header('Content-Type: application/json');
    if ($result) {
        $data = [];
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $row['pago_excepcional'] = $row['pago_excepcional'] ? "Si" : "No";
                $data[] = $row;
            }
        }
        echo json_encode(['success' => true, 'data' => $data]);
    } else {
        echo json_encode(['success' => false, 'message' => "Error en la consulta SQL: " . $conn->error]);
    }
    break;

}

function aceptarFuncion($conn, $id_cuenta) {
    $sql_update = "UPDATE CuentaDeCobroDocente SET estado = 'en_proceso' WHERE id_cuenta = '$id_cuenta'";
    if ($conn->query($sql_update) === TRUE) {
        echo json_encode(["success" => true, "message" => "Estado actualizado a 'en_proceso'."]);
    } else {
        echo json_encode(["success" => false, "message" => "Error al actualizar el estado: " . $conn->error]);
    }
}

$conn->close();
?>
