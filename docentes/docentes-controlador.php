<?php
include '../conexion.php';

$accion = isset($_GET['accion']) ? $_GET['accion'] : 'default';

switch ($accion) {
    case 'crear':
        header('Content-Type: application/json; charset=utf-8');
        error_reporting(E_ALL);
        ini_set('display_errors', 1);
        
        try {
            if (!$conn) {
                throw new Exception("Error en la conexión con la base de datos.");
            }

            if (!validarCedula($_POST['numero_documento'])) {
                echo json_encode(["status" => "error", "message" => "El número de documento ya está registrado."]);
                exit;
            }

            if (!validarTelefono($_POST['telefono'])) {
                echo json_encode(["status" => "error", "message" => "El número de teléfono ya está registrado."]);
                exit;
            }

            if (!validarCorreo($_POST['email'])) {
                echo json_encode(["status" => "error", "message" => "El correo electrónico ya está registrado."]);
                exit;
            }

            $sql = "INSERT INTO docentes 
                    (tipo_documento, numero_documento, nombres, apellidos, perfil_profesional, telefono, direccion, email, declara_renta, retenedor_iva, estado) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
            $stmt = $conn->prepare($sql);
        
            if (!$stmt) {
                throw new Exception("Error en la preparación de la consulta: " . $conn->error);
            }
        
            $declara_renta = isset($_POST['declara_renta']) ? 1 : 0;
            $retenedor_iva = isset($_POST['retenedor_iva']) ? 1 : 0;
            $estado = 1;
        
            $stmt->bind_param(
                'ssssssssiii',
                $_POST['tipo_documento'],
                $_POST['numero_documento'],
                $_POST['nombres'],
                $_POST['apellidos'],
                $_POST['perfil_profesional'],
                $_POST['telefono'],
                $_POST['direccion'],
                $_POST['email'],
                $declara_renta,
                $retenedor_iva,
                $estado
            );
        
            if (!$stmt->execute()) {
                throw new Exception($stmt->error, $stmt->errno);
            }

            echo json_encode(["status" => "success", "message" => "Docente registrado correctamente."]);
            $stmt->close();
        
        } catch (Exception $e) {
            ob_clean(); 
            echo json_encode(["status" => "error", "message" => "Error al crear el registro: " . $e->getMessage()]);
        }
        break;
    
    case 'Modificar':
        $numero_documento = $_POST['numero_documento'];
        $tipo_documento = $_POST['tipo_documento'];
        $nombres = $_POST['nombres'];
        $apellidos = $_POST['apellidos'];
        $perfil_profesional = $_POST['perfil_profesional'];
        $telefono = $_POST['telefono'];
        $direccion = $_POST['direccion'];
        $email = $_POST['email'];

        $retenedor_iva = isset($_POST['retenedor_iva']) && $_POST['retenedor_iva'] === 'on' ? 1 : 0;
        $declara_renta = isset($_POST['declara_renta']) && $_POST['declara_renta'] === 'on' ? 1 : 0;
    
        $sql = "UPDATE docentes 
                SET tipo_documento = ?, nombres = ?, apellidos = ?, perfil_profesional = ?, telefono = ?, direccion = ?, email = ?, declara_renta = ?, retenedor_iva = ? 
                WHERE numero_documento = ?";

        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param(
                "sssssssiis", 
                $tipo_documento, 
                $nombres, 
                $apellidos, 
                $perfil_profesional, 
                $telefono, 
                $direccion, 
                $email, 
                $declara_renta,
                $retenedor_iva,
                $numero_documento
            );

            if ($stmt->execute()) {
                echo "Registro actualizado correctamente.";
            } else {
                echo "Error al actualizar el registro: " . $stmt->error;
            }

            $stmt->close();
        } else {
            echo "Error al preparar la consulta: " . $conn->error;
        }
        break;

    case 'cambiarEstado':
        $numero_documento = $_POST['numero_documento'];
        $estado = $_POST['estado'];

        $sql = "UPDATE docentes SET estado=? WHERE numero_documento=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('is', $estado, $numero_documento);

        if (!$stmt->execute()) {
            echo "Error al cambiar el estado: " . $stmt->error;
        }
        $stmt->close();
        break;

        case 'buscarPorId':
            if (empty($_POST['numero_documento'])) {
                echo json_encode(["error" => "Número de documento no proporcionado"]);
                exit;
            }
            $sql = "SELECT * FROM docentes WHERE numero_documento=?";
            $stmt = $conn->prepare($sql);
    
            if (!$stmt) {
                die("Error en la preparación de la consulta: " . $conn->error);
            }
    
            $stmt->bind_param('s', $_POST['numero_documento']);
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
        $search = isset($_POST['search']['value']) ? $_POST['search']['value'] : '';
        $page = isset($_POST['page']) ? (int)$_POST['page'] : 1;
        $pageSize = isset($_POST['pageSize']) ? (int)$_POST['pageSize'] : 10;
        $offset = ($page - 1) * $pageSize;

        $totalRecordsSql = "SELECT COUNT(*) AS total FROM docentes WHERE nombres LIKE ? OR apellidos LIKE ? OR tipo_documento LIKE ? OR numero_documento LIKE ?";
        $stmt = $conn->prepare($totalRecordsSql);
        $searchTerm = "%$search%";
        $stmt->bind_param('ssss', $searchTerm, $searchTerm, $searchTerm, $searchTerm);
        $stmt->execute();
        $totalResult = $stmt->get_result();
        $totalRecords = $totalResult->fetch_assoc()['total'];

        $sql ="SELECT tipo_documento, numero_documento, nombres, apellidos,
            CONCAT(nombres, ' ', apellidos) AS nombre_completo, perfil_profesional,
            telefono, direccion, email, declara_renta, 
            retenedor_iva, estado
            FROM docentes
            WHERE nombres LIKE ? OR apellidos LIKE ? OR tipo_documento LIKE ? OR numero_documento LIKE ?
            ORDER BY estado DESC
            LIMIT ?, ?
            ";
 
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('ssssii', $searchTerm, $searchTerm, $searchTerm, $searchTerm, $offset, $pageSize);
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

function validarTelefono($telefono) {
    include '../conexion.php';

    $sql = "SELECT telefono FROM docentes WHERE telefono = ?";
    $stmt = $conn->prepare($sql);
    
    if (!$stmt) {
        return false;
    }

    $stmt->bind_param("s", $telefono);
    $stmt->execute();
    $stmt->store_result();

    $telefono_valido = ($stmt->num_rows === 0);

    $stmt->close();
    $conn->close();

    return $telefono_valido;
}

function validarCedula($cedula) {
    include '../conexion.php';

    $sql = "SELECT numero_documento FROM docentes WHERE numero_documento = ?";
    $stmt = $conn->prepare($sql);
    
    if (!$stmt) {
        return false;
    }

    $stmt->bind_param("s", $cedula);
    $stmt->execute();
    $stmt->store_result();

    $cedula_valida = ($stmt->num_rows === 0);

    $stmt->close();
    $conn->close();

    return $cedula_valida;
}

function validarCorreo($correo) {
    include '../conexion.php';

    $sql = "SELECT email FROM docentes WHERE email = ?";
    $stmt = $conn->prepare($sql);
    
    if (!$stmt) {
        return false;
    }

    $stmt->bind_param("s", $correo);
    $stmt->execute();
    $stmt->store_result();

    $correo_valido = ($stmt->num_rows === 0);

    $stmt->close();
    $conn->close();

    return $correo_valido;
}






