<?php
include '../conexion.php';

$accion = isset($_GET['accion']) ? $_GET['accion'] : 'default';

switch ($accion) {
    case 'crear':
        $tipo = $_POST['tipo'] ?? '';
        $nombre = $_POST['nombre'] ?? '';
        $duracion_meses = $_POST['duracion_meses'] ?? '';
        $valor_total_programa = $_POST['valor_total_programa'] ?? '';
        $descripcion = $_POST['descripcion'] ?? '';

        $sql = "INSERT INTO programas (tipo, nombre, duracion_meses, valor_total_programa,descripcion) 
                VALUES ('$tipo', '$nombre', '$duracion_meses', '$valor_total_programa','$descripcion')";
        
        echo ($conn->query($sql) === TRUE) 
            ? "Nuevo registro creado exitosamente."
            : "Error al crear el registro: " . $conn->error;
        break;

    case 'editar':
        if (!isset($_POST['id_programa']) || empty($_POST['id_programa'])) {
            echo json_encode(["success" => false, "message" => "El ID del programa es obligatorio."]);
            break;
        }

        $id_programa = $_POST['id_programa'];
        $tipo = $_POST['tipo'] ?? null;
        $nombre = $_POST['nombre'] ?? null;
        $duracion_meses = $_POST['duracion_meses'] ?? null;
        $valor_total_programa = $_POST['valor_total_programa'] ?? null;
        $descripcion = isset($_POST['descripcion']) && $_POST['descripcion'] !== '' ? $_POST['descripcion'] : null;
        $estado = $_POST['estado'] ?? null;

        if (is_null($tipo) && is_null($nombre) && is_null($duracion_meses) && is_null($valor_total_programa) && is_null($descripcion) && is_null($estado)) {
            echo json_encode(["success" => false, "message" => "No se han enviado datos para actualizar."]);
            break;
        }

        if (!is_null($duracion_mes) && !is_numeric($duracion_mes)) {
            echo json_encode(["success" => false, "message" => "La duración debe ser un número válido."]);
            break;
        }

        $sql_select = "SELECT * FROM programas WHERE id_programa = ?";
        $stmt = $conn->prepare($sql_select);
        $stmt->bind_param('i', $id_programa);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $sql_update = "UPDATE programas SET 
                            tipo = IFNULL(?, tipo), 
                            nombre = IFNULL(?, nombre), 
                            duracion_meses = IFNULL(?, duracion_meses), 
                            valor_total_programa = IFNULL(?, valor_total_programa),
                            descripcion = IFNULL(?, descripcion), 
                            estado = IFNULL(?, estado) 
                            WHERE id_programa = ?";

            $stmt_update = $conn->prepare($sql_update);
            $stmt_update->bind_param('ssissii', $tipo, $nombre, $duracion_mes, $valor_total_programa, $descripcion, $estado, $id_programa);
            
            echo ($stmt_update->execute())
                ? json_encode(["success" => true, "message" => "Registro actualizado exitosamente."])
                : json_encode(["success" => false, "message" => "Error al actualizar el registro: " . $stmt_update->error]);
        } else {
            echo json_encode(["success" => false, "message" => "No se encontró el registro con el ID proporcionado."]);
        }
        break;

    case 'cambiarEstado':
        $id_programa = $_POST['id_programa'];
        $estado = $_POST['estado'];

        $sql = "UPDATE programas SET estado=$estado WHERE id_programa='$id_programa'";
        echo ($conn->query($sql) === TRUE) 
            ? "Estado cambiado exitosamente a " . ($estado == 1 ? "Activo" : "Inactivo") . "."
            : "Error al cambiar el estado: " . $conn->error;
        break;

    case 'BusquedaPorId':
        $id_programa = $_POST['id_programa'];
        $sql = "SELECT * FROM programas WHERE id_programa='$id_programa'";
        $result = $conn->query($sql);
        
        if ($result === false) {
            die("Error en la consulta SQL: " . $conn->error);
        }
    
        $data = [];
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
        echo json_encode(['data' => $data]);
        break;

    default:
    header('Content-Type: application/json');

    $draw = isset($_POST['draw']) ? intval($_POST['draw']) : 1;
    $start = isset($_POST['start']) ? intval($_POST['start']) : 0;
    $length = isset($_POST['length']) ? intval($_POST['length']) : 10;
    $searchValue = $_POST['search']['value'] ?? '';
    
    $sql = "SELECT *, CONCAT('$', FORMAT(valor_total_programa, 2, 'de_DE')) AS valor_total_formateado 
            FROM programas WHERE 1=1"; // WHERE 1=1 permite concatenar condiciones sin errores
    
    if (!empty($searchValue)) {
        $sql .= " AND (tipo LIKE '%$searchValue%' 
                    OR nombre LIKE '%$searchValue%'
                    OR duracion_meses LIKE '%$searchValue%'  /* Comillas invertidas en duración */
                    OR descripcion LIKE '%$searchValue%' 
                    OR estado LIKE '%$searchValue%')";
    }
    
    // Agregamos ORDER BY correctamente
    $sql .= " ORDER BY estado DESC";
    
    // Aplicamos paginación
    $sql .= " LIMIT $start, $length";
    
    // Ejecutamos la consulta principal
    $result = $conn->query($sql);
    if (!$result) {
        echo json_encode(['error' => 'Error en la consulta: ' . $conn->error]);
        exit;
    }
    
    // Contamos el total de registros sin filtro
    $totalQuery = "SELECT COUNT(*) as total FROM programas";
    $totalResult = $conn->query($totalQuery);
    $totalData = $totalResult ? $totalResult->fetch_assoc()['total'] : 0;
    
    // Contamos el total de registros filtrados
    $filteredQuery = "SELECT COUNT(*) as total FROM programas WHERE 1=1";
    if (!empty($searchValue)) {
        $filteredQuery .= " AND (tipo LIKE '%$searchValue%' 
                            OR nombre LIKE '%$searchValue%'
                            OR duracion_meses LIKE '%$searchValue%'
                            OR descripcion LIKE '%$searchValue%' 
                            OR estado LIKE '%$searchValue%')";
    }
    
    $filteredResult = $conn->query($filteredQuery);
    $totalFiltered = $filteredResult ? $filteredResult->fetch_assoc()['total'] : 0;
    
    // Formateamos los datos para la respuesta JSON
    $data = [];
    while ($row = $result->fetch_assoc()) {
        $row['estado'] = ($row['estado'] == 1) ? "Activo" : "Inactivo";
        $data[] = $row;
    }
    
    // Respuesta JSON para DataTables
    echo json_encode([
        'draw' => $draw,
        'recordsTotal' => $totalData,
        'recordsFiltered' => $totalFiltered,
        'data' => $data
    ]);
    
        break;
}

$conn->close();
?>