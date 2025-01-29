<?php
include '../conexion.php';

$accion = isset($_GET['accion']) ? $_GET['accion'] : 'default';

switch ($accion) {
    case 'crear':
        $tipo = $_POST['tipo'] ?? '';
        $nombre = $_POST['nombre'] ?? '';
        $duracion_mes = $_POST['duracion_mes'] ?? '';
        $cant_modulos = $_POST['cant_modulos'] ?? '';
        $descripcion = $_POST['descripcion'] ?? '';

        $sql = "INSERT INTO programas (tipo, nombre, duracion_mes, cant_modulos, descripcion) 
                VALUES ('$tipo', '$nombre', '$duracion_mes', '$cant_modulos', '$descripcion')";
        
        if ($conn->query($sql) === TRUE) {
            echo "Nuevo registro creado exitosamente.";
        } else {
            echo "Error al crear el registro: " . $conn->error;
        }
        break;

        case 'editar':
            // Validar que se haya enviado el ID del programa
            if (!isset($_POST['id_programa']) || empty($_POST['id_programa'])) {
                echo json_encode(["success" => false, "message" => "El ID del programa es obligatorio."]);
                break;
            }
        
            // Recibir los datos del formulario
            $id_programa = $_POST['id_programa'];
            $tipo = isset($_POST['tipo']) ? $_POST['tipo'] : null;
            $nombre = isset($_POST['nombre']) ? $_POST['nombre'] : null;
            $duracion_mes = isset($_POST['duracion_mes']) ? $_POST['duracion_mes'] : null;
            $cant_modulos = isset($_POST['cant_modulos']) ? $_POST['cant_modulos'] : null;
            $descripcion = isset($_POST['descripcion']) ? $_POST['descripcion'] : null;
            $estado = isset($_POST['estado']) ? $_POST['estado'] : null;
        
            // Validar que al menos un campo adicional esté presente
            if (is_null($tipo) && is_null($nombre) && is_null($duracion_mes) && is_null($cant_modulos) && is_null($descripcion) && is_null($estado)) {
                echo json_encode(["success" => false, "message" => "No se han enviado datos para actualizar."]);
                break;
            }
        
            // Validar que los campos duracion_mes y cant_modulos sean números válidos
            if (!is_null($duracion_mes) && !is_numeric($duracion_mes)) {
                echo json_encode(["success" => false, "message" => "La duración debe ser un número válido."]);
                break;
            }
        
            if (!is_null($cant_modulos) && !is_numeric($cant_modulos)) {
                echo json_encode(["success" => false, "message" => "La cantidad de módulos debe ser un número válido."]);
                break;
            }
        
            // Preparar la consulta para seleccionar el programa
            $sql_select = "SELECT * FROM programas WHERE id_programa = ?";
            $stmt = $conn->prepare($sql_select);
            $stmt->bind_param('i', $id_programa);
            $stmt->execute();
            $result = $stmt->get_result();
        
            // Verificar si el programa existe
            if ($result->num_rows > 0) {
                // Preparar la consulta para actualizar los datos
                $sql_update = "UPDATE programas SET 
                                tipo = IFNULL(?, tipo), 
                                nombre = IFNULL(?, nombre), 
                                duracion_mes = IFNULL(?, duracion_mes), 
                                cant_modulos = IFNULL(?, cant_modulos), 
                                descripcion = IFNULL(?, descripcion), 
                                estado = IFNULL(?, estado) 
                                WHERE id_programa = ?";
        
                $stmt_update = $conn->prepare($sql_update);
                $stmt_update->bind_param('ssiiisi', $tipo, $nombre, $duracion_mes, $cant_modulos, $descripcion, $estado, $id_programa);
        
                // Ejecutar la actualización
                if ($stmt_update->execute()) {
                    echo json_encode(["success" => true, "message" => "Registro actualizado exitosamente."]);
                } else {
                    echo json_encode(["success" => false, "message" => "Error al actualizar el registro: " . $stmt_update->error]);
                }
            } else {
                echo json_encode(["success" => false, "message" => "No se encontró el registro con el ID proporcionado."]);
            }
            break;
        

    case 'cambiarEstado':
        $id_programa = $_POST['id_programa'];
        $estado = $_POST['estado'];

        $sql = "UPDATE programas SET estado=$estado WHERE id_programa='$id_programa'";

        if ($conn->query($sql) === TRUE) {
            echo "Estado cambiado exitosamente a " . ($estado == 1 ? "Activo" : "Inactivo") . ".";
        } else {
            echo "Error al cambiar el estado: " . $conn->error;
        }
        break;

    case 'BusquedaPorId':
        $id_programa = $_POST['id_programa'];

        $sql = "SELECT * FROM programas WHERE id_programa='$id_programa'";
        $result = $conn->query($sql);

        if ($result === false) {
            die("Error en la consulta SQL: " . $conn->error);
        }
    
        $data = [];

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $data[] = $row;
            }
            echo json_encode(['data' => $data]);
        } else {
            echo json_encode(['error' => 'Registro no encontrado']);
        }
        break;

    default:
        $search = isset($_GET['search']['value']) ? $_GET['search']['value'] : '';
        $start = isset($_GET['start']) ? $_GET['start'] : 0;
        $length = isset($_GET['length']) ? $_GET['length'] : 10;

        $sql_count = "SELECT COUNT(*) AS total FROM programas WHERE nombre LIKE '%$search%' OR tipo LIKE '%$search%'";
        $result_count = $conn->query($sql_count);
        $total_records = $result_count->fetch_assoc()['total'];

        $sql = "SELECT * FROM programas WHERE nombre LIKE '%$search%' OR tipo LIKE '%$search%' 
                LIMIT $start, $length";
        $result = $conn->query($sql);

        $data = [];

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $row['estado'] = ($row['estado'] == 1) ? "Activo" : "Inactivo";
                $data[] = $row;
            }
        }

        header('Content-Type: application/json');
        echo json_encode([
            'draw' => isset($_GET['draw']) ? $_GET['draw'] : 1,
            'recordsTotal' => $total_records,
            'recordsFiltered' => $total_records,
            'data' => $data
        ]);
        break;
}

$conn->close();
?>
