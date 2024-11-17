<?php

include '../conexion.php';
include 'idDocente.php';

$accion = isset($_GET['accion']) ? $_GET['accion'] : 'default';

switch ($accion) {
    case 'crear' :
        $fecha = $_POST['fecha'];
        $pago_excepcional = $_POST['pago_excepcional'];
        $valor_hora = $_POST['valor_hora'];
        $horas_trabajadas = $_POST['horas_trabajadas'];
        $monto = $_POST['monto'];
        $id_docente = $_POST['id_docente'];
        $estado = $_POST['estado'];
        $nota = $_POST['notas'];
        $tipo_pago = $_POST['tipo_de_pago'];
        $metodo_pago = $_POST['metodo_pago'];
        
       

        $sql = "INSERT INTO CuentaDeCobroDocente (fecha, pago_excepcional, valor_hora, horas_trabajadas, monto, id_docente, estado, notas, tipo_de_pago, metodo_pago) 
        VALUES ('$fecha','$pago_excepcional','$valor_hora','$horas_trabajadas','$monto','$id_docente','$estado','$nota','$tipo_pago','$metodo_pago')";
        if ($conn ->query($sql)===TRUE) {
            echo "Registro exitoso";
        } else {
                echo "Error al registrar: " . $sql . "<br>" . $conn->error;
        
        }
        break;
    case 'editar' :
        $id_cuenta = $_POST['id_cuenta'];

        $sql_select = "SELECT * FROM CuentaDeCobroDocente WHERE id_cuenta = '$id_cuenta'";
        $result = $conn->query($sql_select);

        if ($result -> num_rows > 0) {
            $cuenta = $result -> fetch_assoc();

            $fecha = isset($_POST['fecha']) ? $_POST['fecha'] : $cuenta['fecha'];
            $pago_excepcional = isset($_POST['pago_excepcional']) ? $_POST['pago_excepcional'] : $cuenta['pago_excepcional'];
            $valor_hora = isset($_POST['valor_hora']) ? $_POST['valor_hora'] : $cuenta['valor_hora'];
            $horas_trabajadas = isset($_POST['horas_trabajadas']) ? $_POST['horas_trabajadas'] : $cuenta['horas_trabajadas'];
            $monto = isset($_POST['monto']) ? $_POST['monto'] : $cuenta['monto'];
            $id_docente = isset($_POST['id_docente']) ? $_POST['id_docente'] : $cuenta['id_docente'];
            $estado = isset($_POST['estado']) ? $_POST['estado'] : $cuenta['estado'];
            $nota = isset($_POST['notas']) ? $_POST['notas'] : $cuenta['notas'];
            $tipo_pago = isset($_POST['tipo_de_pago']) ? $_POST['tipo_de_pago'] : $cuenta['tipo_de_pago'];
            $metodo_pago = isset($_POST['metodo_pago']) ? $_POST['metodo_pago'] : $cuenta['metodo_pago'];


            $sql_update = "UPDATE CuentaDeCobroDocente SET
                            fecha='$fecha',
                            pago_excepcional='$pago_excepcional',
                            valor_hora='$valor_hora',
                            horas_trabajadas='$horas_trabajadas',
                            monto='$monto',
                            id_docente='$id_docente',
                            estado= '$estado' 
                            notas='$nota',
                            tipo_de_pago='$tipo_pago',
                            metodo_pago='$metodo_pago'

                            WHERE id_cuenta = '$id_cuenta'";
        if ($conn ->query($sql_update)===TRUE) {
        echo "Registro actualizado exitosamente.";
            } else {
                echo "Error al actualizar el registro: " . $conn->error;
            }
        } else {
            echo "No hay registros para actualizar.";
        }
        break;

    default:

        $sql = "SELECT c.id_cuenta, c.fecha, c.pago_excepcional, c.valor_hora, c.horas_trabajadas, c.monto, d.nombres, d.apellidos, c.estado
        FROM cuentas_cobro c
        JOIN docentes d ON c.id_docente = d.id_docente
        WHERE d.id_docente = $docente";

        $result = $conn->query($sql);

        if ($result === false) {
            // Error en la consulta
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Error en la consulta SQL']);
            break;
        }

        $data = [];

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
            $row['pago_excepcional'] = $row['pago_excepcional'] ?"Si":"No";
                $data[] = $row;
            }
        }

        header('Content-Type: application/json');
        echo json_encode(['data' => $data]);
        break;

    
    }
    $conn->close();
?>




