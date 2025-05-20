<?php
include '../conexion.php';

$accion = isset($_GET['accion']) ? $_GET['accion'] : 'default';

switch ($accion) {
    case 'crear':
        header('Content-Type: application/json; charset=utf-8');
        ob_clean(); // Limpia cualquier salida previa para evitar errores en JSON
        error_reporting(E_ALL);
        ini_set('display_errors', 0); // No mostrar HTML de errores
    
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
    
            // Registro en tabla docentes
            $sql = "INSERT INTO docentes 
                    (tipo_documento, numero_documento, nombres, apellidos, perfil_profesional, telefono, direccion, declara_renta, retenedor_iva, estado) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    
            $stmt = $conn->prepare($sql);
            if (!$stmt) {
                throw new Exception("Error en la preparación de la consulta para docentes: " . $conn->error);
            }
    
            $declara_renta = isset($_POST['declara_renta']) ? 1 : 0;
            $retenedor_iva = isset($_POST['retenedor_iva']) ? 1 : 0;
            $estado = 1;
    
            $stmt->bind_param(
                'sssssssiii',
                $_POST['tipo_documento'],
                $_POST['numero_documento'],
                $_POST['nombres'],
                $_POST['apellidos'],
                $_POST['perfil_profesional'],
                $_POST['telefono'],
                $_POST['direccion'],
                $declara_renta,
                $retenedor_iva,
                $estado
            );
    
            if (!$stmt->execute()) {
                throw new Exception("Error al insertar docente: " . $stmt->error);
            }
            $stmt->close();
    
            // Registro en tabla usuarios
            $sql_usuario = "INSERT INTO usuarios (correo, clave, rol, numero_documento) VALUES (?, ?, ?, ?)";
            $stmt_user = $conn->prepare($sql_usuario);
            if (!$stmt_user) {
                throw new Exception("Error preparando inserción en usuarios: " . $conn->error);
            }
    
            $clave_hashed = password_hash($_POST['numero_documento'], PASSWORD_DEFAULT);
            $rol = "docente";
    
            $stmt_user->bind_param(
                'ssss',
                $_POST['email'],
                $clave_hashed,
                $rol,
                $_POST['numero_documento']
            );
    
            if (!$stmt_user->execute()) {
                throw new Exception("Error al insertar usuario: " . $stmt_user->error);
            }
            $stmt_user->close();
    
            // Registro en docente_modulo con bucle for
            if (!empty($_POST['id_modulo']) && is_array($_POST['id_modulo'])) {
                $sql_materias = "INSERT INTO docente_modulo (numero_documento, id_modulo) VALUES (?, ?)";
                $stmt_materias = $conn->prepare($sql_materias);
                if (!$stmt_materias) {
                    throw new Exception("Error preparando inserción en docente_modulo: " . $conn->error);
                }
    
                foreach ($_POST['id_modulo'] as $modulo_id) {
                    $modulo_id = intval($modulo_id);
                    $stmt_materias->bind_param('si', $_POST['numero_documento'], $modulo_id);
                    if (!$stmt_materias->execute()) {
                        throw new Exception("Error al insertar módulo del docente: " . $stmt_materias->error);
                    }
                }
    
                $stmt_materias->close();
            } else {
                throw new Exception("No se recibieron módulos válidos para asignar.");
            }
    
            // Todo correcto
            echo json_encode(["status" => "success", "message" => "Docente y módulo registrados correctamente."]);
            exit;
    
        } catch (Exception $e) {
            ob_clean();
            echo json_encode(["status" => "error", "message" => "Error al crear el registro: " . $e->getMessage()]);
            exit;
        }
    
        break;
    
    case 'traerMaterias':
        $sql_materias = "SELECT id_modulo, nombre FROM modulos";
        $resultado = $conn->query($sql_materias);
        $materias = [];

        if ($resultado && $resultado->num_rows > 0) {
            while ($fila = $resultado->fetch_assoc()) {
                $materias[] = $fila;
            }
        }

        echo json_encode($materias);
        break;

    case 'traerMateriasDocente':
            // Usamos $_POST['numero_documento'] en lugar de $_GET['id']
            $id = $_POST['numero_documento'];
            
            // Traer todas las materias
            $sqlTodas = "SELECT id_modulo, nombre FROM modulos";
            $resultadoTodas = mysqli_query($conn, $sqlTodas);
        
            $materias = [];
            while ($fila = mysqli_fetch_assoc($resultadoTodas)) {
                $materias[] = $fila;
            }
        
            // Traer las asignadas al docente
            $sqlAsignadas = "SELECT id_modulo FROM docente_modulo WHERE numero_documento = ?";
            $stmt = $conn->prepare($sqlAsignadas);
            $stmt->bind_param("i", $id); // 'i' para integer
            $stmt->execute();
            $resultadoAsignadas = $stmt->get_result();
        
            $asignadas = [];
            while ($fila = $resultadoAsignadas->fetch_assoc()) {
                $asignadas[] = (int)$fila['id_modulo'];
            }
        
            echo json_encode([
                'materias' => $materias,
                'asignadas' => $asignadas
            ]);
            break;       

    case 'Modificar':
        header('Content-Type: application/json; charset=utf-8');
        ob_clean();
        error_reporting(E_ALL);
        ini_set('display_errors', 0);
    
        try {
            $numero_documento = $_POST['numero_documento'];
            $tipo_documento = $_POST['tipo_documento'];
            $nombres = $_POST['nombres'];
            $apellidos = $_POST['apellidos'];
            $perfil_profesional = $_POST['perfil_profesional'];
            $telefono = $_POST['telefono'];
            $direccion = $_POST['direccion'];
            $email = $_POST['correo'];
            $documento_anterior = $_POST['documento_anterior'];

            $retenedor_iva = isset($_POST['retenedor_iva']) && $_POST['retenedor_iva'] === 'on' ? 1 : 0;
            $declara_renta = isset($_POST['declara_renta']) && $_POST['declara_renta'] === 'on' ? 1 : 0;
    
            $documento_anterior = $_POST['documento_anterior'];

            if (!validarCedulaEditar($numero_documento, $documento_anterior)) {
                echo json_encode(["status" => "error", "message" => "El número de documento ya está registrado por otro docente."]);
                exit;
            }
            
            if (!validarTelefonoEditar($telefono, $numero_documento)) {
                echo json_encode(["status" => "error", "message" => "El número de teléfono ya está registrado por otro docente."]);
                exit;
            }
            
            if (!validarCorreoEditar($email, $numero_documento)) {
                echo json_encode(["status" => "error", "message" => "El correo electrónico ya está registrado por otro usuario."]);
                exit;
            }            
    
            // Actualizar datos del docente
            $sql = "UPDATE docentes 
                    SET tipo_documento = ?, nombres = ?, apellidos = ?, perfil_profesional = ?, telefono = ?, direccion = ?, declara_renta = ?, retenedor_iva = ? 
                    WHERE numero_documento = ?";
    
            if ($stmt = $conn->prepare($sql)) {
                $stmt->bind_param(
                    "ssssssiis", 
                    $tipo_documento, 
                    $nombres, 
                    $apellidos, 
                    $perfil_profesional, 
                    $telefono, 
                    $direccion, 
                    $declara_renta,
                    $retenedor_iva,
                    $numero_documento
                );
    
                if (!$stmt->execute()) {
                    throw new Exception("Error al actualizar el docente: " . $stmt->error);
                }
    
                $stmt->close();
            } else {
                throw new Exception("Error al preparar consulta de docente: " . $conn->error);
            }
    
            // Actualizar correo del usuario
            $sql_usuario = "UPDATE usuarios SET correo = ? WHERE numero_documento = ?";
            $stmt = $conn->prepare($sql_usuario);
    
            if ($stmt) {
                $stmt->bind_param("ss", $email, $numero_documento);
    
                if (!$stmt->execute()) {
                    throw new Exception("Error al actualizar el correo del usuario: " . $stmt->error);
                }
    
                $stmt->close();
            } else {
                throw new Exception("Error al preparar consulta de usuario: " . $conn->error);
            }
    
            // Sincronizar módulos
            $modulos_formulario = isset($_POST['id_modulo']) ? array_map('intval', $_POST['id_modulo']) : [];
    
            // Obtener módulos actuales de la BD
            $modulos_bd = [];
            $sql_actuales = "SELECT id_modulo FROM docente_modulo WHERE numero_documento = ?";
            $stmt_actuales = $conn->prepare($sql_actuales);
            $stmt_actuales->bind_param('s', $numero_documento);
            $stmt_actuales->execute();
            $result = $stmt_actuales->get_result();
    
            while ($row = $result->fetch_assoc()) {
                $modulos_bd[] = (int)$row['id_modulo'];
            }
    
            $stmt_actuales->close();
    
            // Calcular diferencias
            $modulos_a_insertar = array_diff($modulos_formulario, $modulos_bd);
            $modulos_a_eliminar = array_diff($modulos_bd, $modulos_formulario);
    
            // Insertar nuevos módulos
            if (!empty($modulos_a_insertar)) {
                $sql_insert = "INSERT INTO docente_modulo (numero_documento, id_modulo) VALUES (?, ?)";
                $stmt_insert = $conn->prepare($sql_insert);
                foreach ($modulos_a_insertar as $nuevo_modulo) {
                    $stmt_insert->bind_param('si', $numero_documento, $nuevo_modulo);
                    $stmt_insert->execute();
                }
                $stmt_insert->close();
            }
    
            // Eliminar módulos no deseados
            if (!empty($modulos_a_eliminar)) {
                $sql_delete = "DELETE FROM docente_modulo WHERE numero_documento = ? AND id_modulo = ?";
                $stmt_delete = $conn->prepare($sql_delete);
                foreach ($modulos_a_eliminar as $modulo_fuera) {
                    $stmt_delete->bind_param('si', $numero_documento, $modulo_fuera);
                    $stmt_delete->execute();
                }
                $stmt_delete->close();
            }
    
            echo json_encode(["status" => "success", "message" => "Docente actualizado y módulos sincronizados correctamente."]);
            exit;
    
        } catch (Exception $e) {
            ob_clean();
            echo json_encode(["status" => "error", "message" => "Error al editar: " . $e->getMessage()]);
            exit;
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

        $sql = "SELECT d.*, u.correo 
                FROM docentes d 
                LEFT JOIN usuarios u ON d.numero_documento = u.numero_documento
                WHERE d.numero_documento = ?";

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
        header('Content-Type: application/json; charset=utf-8');
        error_reporting(E_ALL);
        ini_set('display_errors', 1);

        $search = isset($_POST['search']['value']) ? $_POST['search']['value'] : '';
        $page = isset($_POST['page']) ? (int)$_POST['page'] : 1;
        $pageSize = isset($_POST['pageSize']) ? (int)$_POST['pageSize'] : 10;
        $offset = ($page - 1) * $pageSize;

        $totalRecordsSql = "SELECT COUNT(*) AS total FROM docentes WHERE nombres LIKE ? OR apellidos LIKE ? OR tipo_documento LIKE ? OR numero_documento LIKE ?";
        $stmt = $conn->prepare($totalRecordsSql);

        if (!$stmt) {
            ob_clean();
            echo json_encode(["status" => "error", "message" => "Error al contar registros: " . $conn->error]);
            exit;
        }

        $searchTerm = "%$search%";
        $stmt->bind_param('ssss', $searchTerm, $searchTerm, $searchTerm, $searchTerm);
        $stmt->execute();
        $totalResult = $stmt->get_result();
        $totalRecords = $totalResult->fetch_assoc()['total'];
        $stmt->close();

        $sql = "SELECT d.tipo_documento, d.numero_documento, d.nombres, d.apellidos,
                       CONCAT(d.nombres, ' ', d.apellidos) AS nombre_completo, d.perfil_profesional,
                       d.telefono, d.direccion, u.correo AS email, d.declara_renta, 
                       d.retenedor_iva, d.estado
                FROM docentes d
                LEFT JOIN usuarios u ON d.numero_documento = u.numero_documento
                WHERE d.nombres LIKE ? OR d.apellidos LIKE ? OR d.tipo_documento LIKE ? OR d.numero_documento LIKE ?
                ORDER BY d.estado DESC
                LIMIT ?, ?";

        $stmt = $conn->prepare($sql);

        if (!$stmt) {
            ob_clean();
            echo json_encode(["status" => "error", "message" => "Error al preparar consulta principal: " . $conn->error]);
            exit;
        }

        $stmt->bind_param('ssssii', $searchTerm, $searchTerm, $searchTerm, $searchTerm, $offset, $pageSize);
        $stmt->execute();
        $result = $stmt->get_result();

        $data = [];
        while ($row = $result->fetch_assoc()) {
            $row['estado'] = ($row['estado'] == 1) ? "Activo" : "Inactivo";
            $data[] = $row;
        }

        ob_clean();
        echo json_encode([
            'data' => $data,
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $totalRecords,
        ]);
        break;
}

$conn->close();

function validarTelefonoEditar($telefono, $numero_documento) {
    include '../conexion.php';
    $sql = "SELECT telefono FROM docentes WHERE telefono = ? AND numero_documento != ?";
    $stmt = $conn->prepare($sql);
    if (!$stmt) return false;
    $stmt->bind_param("ss", $telefono, $numero_documento);
    $stmt->execute();
    $stmt->store_result();
    $telefono_valido = ($stmt->num_rows === 0);
    $stmt->close();
    $conn->close();
    return $telefono_valido;
}

function validarTelefono($telefono) {
    include '../conexion.php';
    $sql = "SELECT telefono FROM docentes WHERE telefono = ?";
    $stmt = $conn->prepare($sql);
    if (!$stmt) return false;
    $stmt->bind_param("s", $telefono);
    $stmt->execute();
    $stmt->store_result();
    $telefono_valido = ($stmt->num_rows === 0);
    $stmt->close();
    $conn->close();
    return $telefono_valido;
}

function validarCedulaEditar($cedula, $numero_documento) {
    include '../conexion.php';
    $sql = "SELECT numero_documento FROM docentes WHERE numero_documento = ? AND numero_documento != ?";
    $stmt = $conn->prepare($sql);
    if (!$stmt) return false;
    $stmt->bind_param("ss", $cedula, $numero_documento);
    $stmt->execute();
    $stmt->store_result();
    $cedula_valida = ($stmt->num_rows === 0);
    $stmt->close();
    $conn->close();
    return $cedula_valida;
}

function validarCedula($cedula) {
    include '../conexion.php';
    $sql = "SELECT numero_documento FROM docentes WHERE numero_documento = ?";
    $stmt = $conn->prepare($sql);
    if (!$stmt) return false;
    $stmt->bind_param("s", $cedula);
    $stmt->execute();
    $stmt->store_result();
    $cedula_valida = ($stmt->num_rows === 0);
    $stmt->close();
    $conn->close();
    return $cedula_valida;
}

function validarCorreoEditar($correo, $numero_documento) {
    include '../conexion.php';
    $sql = "SELECT correo FROM usuarios WHERE correo = ? AND numero_documento != ?";
    $stmt = $conn->prepare($sql);
    if (!$stmt) return false;
    $stmt->bind_param("ss", $correo, $numero_documento);
    $stmt->execute();
    $stmt->store_result();
    $correo_valido = ($stmt->num_rows === 0);
    $stmt->close();
    $conn->close();
    return $correo_valido;
}

function validarCorreo($correo) {
    include '../conexion.php';
    $sql = "SELECT correo FROM usuarios WHERE correo = ?";
    $stmt = $conn->prepare($sql);
    if (!$stmt) return false;
    $stmt->bind_param("s", $correo);
    $stmt->execute();
    $stmt->store_result();
    $correo_valido = ($stmt->num_rows === 0);
    $stmt->close();
    $conn->close();
    return $correo_valido;
}
