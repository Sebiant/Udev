<?php
include '../conexion.php';

$accion = isset($_GET['accion']) ? $_GET['accion'] : 'default';

switch ($accion) {
    case 'crear':
        $fecha_inicio = $_POST['fecha_inicio'];
        $fecha_fin = $_POST['fecha_fin'];
        $id_programa = $_POST['id_programa'];
        $estado = isset($_POST['estado']) ? 1 : 0;

        $sql = "INSERT INTO modulos (fecha_inicio, fecha_fin, estado, id_programa) 
                VALUES ('$fecha_inicio', '$fecha_fin', '$estado', '$id_programa')";
        
        if ($conn->query($sql) === TRUE) {
            echo "Nuevo módulo creado exitosamente.";
        } else {
            echo "Error al crear el módulo: " . $conn->error;
        }
        break;

    case 'editar':
        $id_modulo = $_POST['id_modulo'];

        $sql_select = "SELECT * FROM modulos WHERE id_modulo='$id_modulo'";
        $result = $conn->query($sql_select);

        if ($result->num_rows > 0) {
            $modulo = $result->fetch_assoc();

            $fecha_inicio = isset($_POST['fecha_inicio']) ? $_POST['fecha_inicio'] : $modulo['fecha_inicio'];
            $fecha_fin = isset($_POST['fecha_fin']) ? $_POST['fecha_fin'] : $modulo['fecha_fin'];
            $id_programa = isset($_POST['id_programa']) ? $_POST['id_programa'] : $modulo['id_programa'];
            $estado = isset($_POST['estado']) ? 1 : $modulo['estado'];

            $sql_update = "UPDATE modulos SET 
                            fecha_inicio='$fecha_inicio', 
                            fecha_fin='$fecha_fin', 
                            estado='$estado',
                            id_programa='$id_programa'
                            WHERE id_modulo='$id_modulo'";

            if ($conn->query($sql_update) === TRUE) {
                echo "Módulo actualizado exitosamente.";
            } else {
                echo "Error al actualizar el módulo: " . $conn->error;
            }
        } else {
            echo "No se encontró el módulo.";
        }
        break;

    case 'eliminar':
        $id_modulo = $_POST['id_modulo'];

        $sql = "UPDATE modulos SET estado=0 WHERE id_modulo='$id_modulo'";

        if ($conn->query($sql) === TRUE) {
            echo "Módulo desactivado exitosamente.";
        } else {
            echo "Error al desactivar el módulo: " . $conn->error;
        }
        break;

    case 'activar':
        $id_modulo = $_POST['id_modulo'];

        $sql = "UPDATE modulos SET estado=1 WHERE id_modulo='$id_modulo'";

        if ($conn->query($sql) === TRUE) {
            echo "Módulo activado exitosamente.";
        } else {
            echo "Error al activar el módulo: " . $conn->error;
        }
        break;

    default:
        $sql = "SELECT modulos.*, programas.nombre AS nombre_programa 
                FROM modulos 
                JOIN programas ON modulos.id_programa = programas.id_programa";
        $result = $conn->query($sql);
    
        $data = [];
    
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $row['estado'] = $row['estado'] ? "activo" : "inactivo";
                $row['id_programa'] = $row['nombre_programa'];
                $data[] = $row;
            }
        }
    
        header('Content-Type: application/json');
        echo json_encode(['data' => $data]);
    break;
}

$conn->close();
?>
