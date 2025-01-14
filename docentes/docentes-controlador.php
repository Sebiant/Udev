<?php
include '../conexion.php';

// Obtener la acción de la solicitud
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
            echo "Nuevo registro creado exitosamente.";
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
                die("Error en la preparación de la consulta: " . $conn->error);
            }
        
            // Asignación de valores desde el formulario
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
        
            // Enlazar parámetros
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
        
            // Ejecutar consulta
            if ($stmt->execute()) {
                echo json_encode(["success" => "Registro actualizado exitosamente"]);
            } else {
                echo json_encode(["error" => "Error al actualizar el registro: " . $stmt->error]);
            }
        
            $stmt->close();
            break;
        
    case 'activar':
        if (empty($_POST['id_docente'])) {
            echo json_encode(["error" => "ID de docente no proporcionado"]);
            exit;
        }

        $estado = ($accion === 'eliminar') ? 0 : 1;

        $sql = "UPDATE docentes SET estado=? WHERE id_docente=?";
        $stmt = $conn->prepare($sql);

        if (!$stmt) {
            die("Error en la preparación de la consulta: " . $conn->error);
        }

        $stmt->bind_param('ii', $estado, $_POST['id_docente']);

        if ($stmt->execute()) {
            echo $accion === 'eliminar' ? "Registro desactivado exitosamente." : "Registro activado exitosamente.";
        } else {
            echo "Error al cambiar el estado del registro: " . $stmt->error;
        }
        $stmt->close();
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
        $sql = "SELECT id_docente, tipo_documento, numero_documento, nombres, apellidos,
                        CONCAT(nombres, ' ', apellidos) AS nombre_completo, especialidad,
                        descripcion_especialidad, telefono, direccion, email, declara_renta, 
                        retenedor_iva, estado 
                FROM docentes";
        $result = $conn->query($sql);

        if (!$result) {
            die("Error en la consulta: " . $conn->error);
        }

        $data = [];
        while ($row = $result->fetch_assoc()) {
            $row['estado'] = $row['estado'] ? "activo" : "inactivo";
            $data[] = $row;
        }

        header('Content-Type: application/json');
        echo json_encode(['data' => $data]);
        break;
}

$conn->close();
