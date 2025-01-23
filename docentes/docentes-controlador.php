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

        if ($stmt->execute()) {
        } else {
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
                    tipo_documento=?, 
                    numero_documento=?, 
                    nombres=?, 
                    apellidos=?, 
                    especialidad=?, 
                    descripcion_especialidad=?, 
                    telefono=?, 
                    direccion=?, 
                    email=?, 
                    declara_renta=?, 
                    retenedor_iva=?, 
                    estado=? 
                    WHERE id_docente=?";
        
            $stmt = $conn->prepare($sql);
        
            if (!$stmt) {
            }
        
            $tipo_documento = $_POST['tipo_documento'];
            $numero_documento = $_POST['numero_documento'];
            $nombres = $_POST['nombres'];
            $apellidos = $_POST['apellidos'];
            $especialidad = $_POST['especialidad'];
            $descripcion_especialidad = $_POST['descripcion_especialidad'];
            $telefono = $_POST['telefono'];
            $direccion = $_POST['direccion'];
            $email = $_POST['email'];
            $declara_renta = isset($_POST['declara_renta']) ? 1 : 0;
            $retenedor_iva = isset($_POST['retenedor_iva']) ? 1 : 0;
            $estado = isset($_POST['estado']) ? 1 : 0;
            $id_docente = $_POST['id_docente'];
        
            $stmt->bind_param(
                'sssssssssiisi', 
                $tipo_documento, 
                $numero_documento, 
                $nombres, 
                $apellidos, 
                $especialidad, 
                $descripcion_especialidad, 
                $telefono, 
                $direccion, 
                $email, 
                $declara_renta, 
                $retenedor_iva, 
                $estado, 
                $id_docente
            );
        
            if ($stmt->execute()) {
            } else {
                echo json_encode(["error" => "Error al actualizar el registro: " . $stmt->error]);
            }
        
            $stmt->close();
            break;
            
            case 'cambiarEstado':
                $id_docente = $_POST['id_docente'];
                $estado = $_POST['estado'];
        
                $sql = "UPDATE docentes SET estado=$estado WHERE id_docente='$id_docente'";
        
                if ($conn->query($sql) === TRUE) {
                } else {
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
            $data = $result->fetch_all(MYSQLI_ASSOC);
            echo json_encode(['data' => $data]);
        } else {
            echo json_encode(['error' => 'Registro no encontrado']);
        }
        $stmt->close();
        break;


    default:
// Obtener los parámetros de la paginación desde la solicitud POST
$page = isset($_POST['page']) ? (int)$_POST['page'] : 1; // Página actual (por defecto 1)
$pageSize = isset($_POST['pageSize']) ? (int)$_POST['pageSize'] : 10; // Número de registros por página (por defecto 10)
$offset = ($page - 1) * $pageSize; // Calcular el desplazamiento para la consulta LIMIT

// Obtener el total de registros
$totalRecordsSql = "SELECT COUNT(*) AS total FROM docentes";
$totalResult = $conn->query($totalRecordsSql);
if (!$totalResult) {
    die("Error en la consulta del total de registros: " . $conn->error);
}
$totalRecords = $totalResult->fetch_assoc()['total'];

// Consulta con LIMIT y OFFSET para la paginación
$sql = "SELECT id_docente, tipo_documento, numero_documento, nombres, apellidos,
               CONCAT(nombres, ' ', apellidos) AS nombre_completo, especialidad,
               descripcion_especialidad, telefono, direccion, email, declara_renta, 
               retenedor_iva, estado
        FROM docentes
        LIMIT ?, ?";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    die("Error en la preparación de la consulta: " . $conn->error);
}

// Vincular los parámetros LIMIT y OFFSET
$stmt->bind_param('ii', $offset, $pageSize);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $data = [];
    while ($row = $result->fetch_assoc()) {
        $row['estado'] = ($row['estado'] == 1) ? "Activo" : "Inactivo";
        $data[] = $row;
    }

    // Responder con los datos y la información de la paginación
    echo json_encode([
        'data' => $data,
        'recordsTotal' => $totalRecords,  // Total de registros en la base de datos
        'recordsFiltered' => $totalRecords,  // Total de registros filtrados (igual a total si no hay filtro)
    ]);
} else {
    echo json_encode(['data' => [], 'recordsTotal' => 0, 'recordsFiltered' => 0]);
}
        break;
}

$conn->close();
