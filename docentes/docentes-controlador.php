<?php
include '../conexion.php';

$accion = isset($_GET['accion']) ? $_GET['accion'] : 'default';

switch ($accion) {
    case 'crear':
        $sql = "INSERT INTO docentes 
                (tipo_documento, numero_documento, nombres, apellidos, especialidad, descripcion_especialidad, telefono, direccion, email, declara_renta, retenedor_iva, estado) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);

        if (!$stmt) {
            die("Error en la preparación de la consulta: " . $conn->error);
        }

        $declara_renta = isset($_POST['declara_renta']) ? 1 : 0;
        $retenedor_iva = isset($_POST['retenedor_iva']) ? 1 : 0;
        $estado = isset($_POST['estado']) ? 1 : 0;

        $stmt->bind_param(
            'ssssssssssii',
            $_POST['tipo_documento'],
            $_POST['numero_documento'],
            $_POST['nombres'],
            $_POST['apellidos'],
            $_POST['especialidad'],
            $_POST['descripcion_especialidad'],
            $_POST['telefono'],
            $_POST['direccion'],
            $_POST['email'],
            $declara_renta,
            $retenedor_iva,
            $estado
        );

        if (!$stmt->execute()) {
            echo "Error al crear el registro: " . $stmt->error;
        }
        $stmt->close();
        break;

    case 'Modificar':
        if (empty($_POST['id_docente'])) {
            echo json_encode(["error" => "ID de docente no proporcionado"]);
            exit;
        }

        $sql = "UPDATE docentes SET 
                tipo_documento=?, numero_documento=?, nombres=?, apellidos=?,
                especialidad=?, descripcion_especialidad=?, telefono=?, direccion=?,
                email=?, declara_renta=?, retenedor_iva=?, estado=?
                WHERE id_docente=?";

        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            die("Error en la preparación de la consulta: " . $conn->error);
        }

        $stmt->bind_param(
            'sssssssssiisi',
            $_POST['tipo_documento'],
            $_POST['numero_documento'],
            $_POST['nombres'],
            $_POST['apellidos'],
            $_POST['especialidad'],
            $_POST['descripcion_especialidad'],
            $_POST['telefono'],
            $_POST['direccion'],
            $_POST['email'],
            isset($_POST['declara_renta']) ? 1 : 0,
            isset($_POST['retenedor_iva']) ? 1 : 0,
            isset($_POST['estado']) ? 1 : 0,
            $_POST['id_docente']
        );

        if (!$stmt->execute()) {
            echo json_encode(["error" => "Error al actualizar el registro: " . $stmt->error]);
        }
        $stmt->close();
        break;

    case 'cambiarEstado':
        $id_docente = $_POST['id_docente'];
        $estado = $_POST['estado'];

        $sql = "UPDATE docentes SET estado=$estado WHERE id_docente='$id_docente'";

        if (!$conn->query($sql)) {
            echo "Error al cambiar el estado: " . $conn->error;
        }
        break;

    case 'buscarPorId':
        if (empty($_POST['id_docente'])) {
            echo json_encode(["error" => "ID de docente no proporcionado"]);
            exit;
        }

        $sql = "SELECT * FROM docentes WHERE id_docente=?";
        $stmt = $conn->prepare($sql);

        if (!$stmt) {
            die("Error en la preparación de la consulta: " . $conn->error);
        }

        $stmt->bind_param('i', $_POST['id_docente']);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            echo json_encode(['data' => $result->fetch_all(MYSQLI_ASSOC)]);
        } else {
            echo json_encode(['error' => 'Registro no encontrado']);
        }
        $stmt->close();
        break;

    default:
        $page = isset($_POST['page']) ? (int)$_POST['page'] : 1;
        $pageSize = isset($_POST['pageSize']) ? (int)$_POST['pageSize'] : 10;
        $offset = ($page - 1) * $pageSize;

        $totalRecordsSql = "SELECT COUNT(*) AS total FROM docentes";
        $totalResult = $conn->query($totalRecordsSql);
        $totalRecords = $totalResult->fetch_assoc()['total'];

        $sql = "SELECT id_docente, tipo_documento, numero_documento, nombres, apellidos,
                       CONCAT(nombres, ' ', apellidos) AS nombre_completo, especialidad,
                       descripcion_especialidad, telefono, direccion, email, declara_renta, 
                       retenedor_iva, estado
                FROM docentes
                LIMIT ?, ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('ii', $offset, $pageSize);
        $stmt->execute();
        $result = $stmt->get_result();

        $data = [];
        while ($row = $result->fetch_assoc()) {
            $row['estado'] = ($row['estado'] == 1) ? "Activo" : "Inactivo";
            $data[] = $row;
        }

        echo json_encode([
            'data' => $data,
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $totalRecords,
        ]);
        break;
}

$conn->close();
