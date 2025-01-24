<?php
include_once '../conexion.php';

$accion = isset($_GET['accion']) ? $_GET['accion'] : 'default';

switch ($accion) {
    case 'crear':
        $nombre_salon = $_POST['nombre_salon'];
        $capacidad = $_POST['capacidad'];
        $descripcion = $_POST['descripcion'];
        $id_institucion = $_POST['id_institucion'];
        $estado = isset($_POST['estado']) ? 1 : 0;

        $sql = "INSERT INTO Salones (nombre_salon, capacidad, descripcion, id_institucion, estado) 
                VALUES ('$nombre_salon','$capacidad','$descripcion','$id_institucion','$estado')";
        echo ($conn->query($sql) === TRUE) ? "Salón creado con éxito" : "Error al crear el salón: " . $conn->error;
        break;

    case 'modificar':
        $id_salon = $_POST['id_salon'] ?? null;
        if (empty($id_salon)) {
            echo json_encode(["error" => "ID de salón no proporcionado"]);
            exit;
        }
        $sql = "SELECT * FROM Salones WHERE id_salon = '$id_salon'";
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

    case 'editar':
        $id_salon = $_POST['id_salon'];
        $sql_select = "SELECT * FROM Salones WHERE id_salon='$id_salon'";
        $result = $conn->query($sql_select);
        if ($result->num_rows > 0) {
            $salon = $result->fetch_assoc();
            $nombre_salon = $_POST['nombre_salon'] ?? $salon['nombre_salon'];
            $capacidad = $_POST['capacidad'] ?? $salon['capacidad'];
            $descripcion = $_POST['descripcion'] ?? $salon['descripcion'];
            $id_institucion = $_POST['id_institucion'] ?? $salon['id_institucion'];
            $estado = isset($_POST['estado']) ? ($_POST['estado'] == 'Sí' ? 1 : 0) : $salon['estado'];

            $sql_update = "UPDATE Salones 
                           SET nombre_salon='$nombre_salon', capacidad='$capacidad', 
                               descripcion='$descripcion', id_institucion='$id_institucion', estado='$estado' 
                           WHERE id_salon='$id_salon'";
            echo ($conn->query($sql_update) === TRUE) ? "Registro actualizado exitosamente." : "Error al actualizar el registro: " . $conn->error;
        } else {
            echo "No se encontró el registro del salón.";
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
                ORDER BY $order_by $order_dir 
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
