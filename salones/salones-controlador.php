<?php
include_once '../conexion.php';

$accion = isset($_GET['accion']) ? $_GET['accion'] : 'default';

switch ($accion) {
    case 'crear':
        $nombre_salon = isset($_POST['nombre_salon']) ? $_POST['nombre_salon'] : '';
        $capacidad = isset($_POST['capacidad']) ? $_POST['capacidad'] : '';
        $descripcion = isset($_POST['descripcion']) ? $_POST['descripcion'] : '';
        $id_institucion = isset($_POST['id_institucion']) ? $_POST['id_institucion'] : '';
        $estado = 1;

        $sql = "INSERT INTO salones (nombre_salon, capacidad, descripcion, id_institucion, estado) 
                VALUES ('$nombre_salon','$capacidad','$descripcion','$id_institucion','$estado')";
        echo ($conn->query($sql) === TRUE) ? "Salón creado con éxito" : "Error al crear el salón: " . $conn->error;
        break;

    case 'buscarPorId':
        $id_salon = $_POST['id_salon'];

        $sql = "SELECT * FROM salones WHERE id_salon = '$id_salon'";
        $result = $conn->query($sql);
        if ($result === false) {
            echo json_encode(["error" => "Error en la consulta SQL: " . $conn->error]);
            exit;
        }
        $data = [];
        if ($result->num_rows > 0) {
            $salon = $result->fetch_assoc();
            $salon['estado'] = $salon['estado'] == 1 ? "Activo" : "Inactivo";
            $data[] = $salon;
        }
        header('Content-Type: application/json');
        echo json_encode(["data" => $data]);
        break;

    case 'modificar':
        // Obtener los datos enviados por POST
        $id_salon = $_POST['id_salon'] ?? null;
        $nombre_salon = $_POST['nombre_salon'] ?? '';
        $capacidad = $_POST['capacidad'] ?? '';
        $descripcion = $_POST['descripcion'] ?? '';
        $id_institucion = $_POST['id_institucion'] ?? '';
        $estado = isset($_POST['estado']) ? 1 : 0; // Si el checkbox está marcado, estado es activo

        // Validar si el ID de salón existe
        if (empty($id_salon)) {
            echo json_encode(["error" => "ID de salón no proporcionado"]);
            exit;
        }

        // Validar que los campos requeridos no estén vacíos
        if (empty($nombre_salon) || empty($capacidad) || empty($descripcion) || empty($id_institucion)) {
            echo json_encode(["error" => "Faltan datos para actualizar el salón"]);
            exit;
        }

        // Realizar la consulta de actualización
        $sql = "UPDATE salones SET nombre_salon = ?, capacidad = ?, descripcion = ?, id_institucion = ?, estado = ? WHERE id_salon = ?";

        // Preparar y ejecutar la consulta
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("ssssii", $nombre_salon, $capacidad, $descripcion, $id_institucion, $estado, $id_salon);

            if ($stmt->execute()) {
                echo json_encode(["success" => "Salón actualizado correctamente"]);
            } else {
                echo json_encode(["error" => "Error al actualizar el salón: " . $stmt->error]);
            }

            $stmt->close();
        } else {
            echo json_encode(["error" => "Error en la preparación de la consulta SQL: " . $conn->error]);
        }
        break;

    case 'cambiarEstado':
        $id_salon = $_POST['id_salon'];
        $estado = $_POST['estado'];

        $sql = "UPDATE salones SET estado=$estado WHERE id_salon='$id_salon'";

        if ($conn->query($sql) === TRUE) {
            echo "Estado cambiado exitosamente a " . ($estado == 1 ? "Activo" : "Inactivo") . ".";
        } else {
            echo "Error al cambiar el estado: " . $conn->error;
        }
        break;

    default:
        $columns = ['S.nombre_salon', 'S.capacidad', 'S.descripcion', 'i.nombre', 'S.estado'];

        $search = isset($_POST['search']['value']) ? $conn->real_escape_string($_POST['search']['value']) : '';
        $start = isset($_POST['start']) ? (int)$_POST['start'] : 0;
        $length = isset($_POST['length']) ? (int)$_POST['length'] : 10;
        $order_column = isset($_POST['order'][0]['column']) ? (int)$_POST['order'][0]['column'] : 0;
        $order_dir = isset($_POST['order'][0]['dir']) ? $_POST['order'][0]['dir'] : 'ASC';

        $order_by = $columns[$order_column];

        $where = "";
        if (!empty($search)) {
            $where .= " WHERE (";
            foreach ($columns as $column) {
                $where .= "$column LIKE '%$search%' OR ";
            }
            $where = rtrim($where, " OR ") . ")";
        }

        $sql = "SELECT S.id_salon, S.nombre_salon, S.capacidad, S.descripcion, i.nombre, S.estado 
                FROM salones S 
                JOIN instituciones i ON i.id_institucion = S.id_institucion
                $where 
                ORDER BY estado DESC, $order_by $order_dir 
                LIMIT $start, $length";

        $result = $conn->query($sql);

        $data = [];
        while ($row = $result->fetch_assoc()) {
            $row['estado'] = ($row['estado'] == 1) ? "Activo" : "Inactivo";
            $data[] = $row;
        }

        $sql_count = "SELECT COUNT(*) as total FROM salones S JOIN instituciones i ON i.id_institucion = S.id_institucion";
        $totalRecords = $conn->query($sql_count)->fetch_assoc()['total'];

        $sql_count_filtered = "SELECT COUNT(*) as total FROM salones S JOIN instituciones i ON i.id_institucion = S.id_institucion $where";
        $totalFiltered = $conn->query($sql_count_filtered)->fetch_assoc()['total'];

        $response = [
            "draw" => isset($_POST['draw']) ? (int)$_POST['draw'] : 0,
            "recordsTotal" => $totalRecords,
            "recordsFiltered" => $totalFiltered,
            "data" => $data
        ];

        header('Content-Type: application/json');
        echo json_encode($response);
        break;
}

$conn->close();
?>
