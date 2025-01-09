<?php
include '../conexion.php';

// Obtener la acción de la solicitud (puede ser 'crear', 'editar', 'eliminar', 'consultar' o 'default')
$accion = isset($_GET['accion']) ? $_GET['accion'] : 'default';

switch ($accion) {
    case 'crear':
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

        $sql = "INSERT INTO docentes (tipo_documento, numero_documento, nombres, apellidos, especialidad, descripcion_especialidad, telefono, direccion, email, declara_renta, retenedor_iva, estado) 
                VALUES ('$tipo_documento', '$numero_documento', '$nombres', '$apellidos', '$especialidad', '$descripcion_especialidad', '$telefono', '$direccion', '$email', '$declara_renta', '$retenedor_iva', '$estado')";
        
        if ($conn->query($sql) === TRUE) {
            echo "Nuevo registro creado exitosamente.";
        } else {
            echo "Error al crear el registro: " . $conn->error;
        }
        break;

    case 'Modificar':
        $id_docente = $_POST['id_docente'];

        if (empty($id_docente)) {
            echo json_encode(["error" => "ID de docente no proporcionado"]);
            exit;
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

        $sql_update = "UPDATE docentes SET 
        tipo_documento='$tipo_documento', 
        numero_documento='$numero_documento', 
        nombres='$nombres', 
        apellidos='$apellidos', 
        especialidad='$especialidad', 
        descripcion_especialidad='$descripcion_especialidad', 
        telefono='$telefono', 
        direccion='$direccion', 
        email='$email', 
        declara_renta='$declara_renta', 
        retenedor_iva='$retenedor_iva',
        estado='$estado'
        WHERE id_docente='$id_docente'";

        if ($conn->query($sql_update) === TRUE) {
            echo "Registro actualizado exitosamente.";
        } else {
            echo "Error al actualizar el registro: " . $conn->error;
        }
        break;

    case 'eliminar':
        $id_docente = $_POST['id_docente'];

        $sql = "UPDATE docentes SET estado=0 WHERE id_docente='$id_docente'";

        if ($conn->query($sql) === TRUE) {
            echo "Registro desactivado exitosamente.";
        } else {
            echo "Error al desactivar el registro: " . $conn->error;
        }
        break;

    case 'activar':
        $id_docente = $_POST['id_docente'];

        $sql = "UPDATE docentes SET estado=1 WHERE id_docente='$id_docente'";

        if ($conn->query($sql) === TRUE) {
            echo "Registro activado exitosamente.";
        } else {
            echo "Error al activar el registro: " . $conn->error;
        }
        break;

    case 'seleccionarId':
        $id_docente = $_POST['id_docente'];

        // Consulta SQL para seleccionar los datos del docente
        $sql = "SELECT * FROM docentes WHERE id_docente = $id_docente";

        $result = $conn->query($sql);
        if (!$result) {
            die("Error en la consulta: " . $conn->error);
        }
        $data = [];

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $row['estado'] = $row['estado'] ? "activo" : "inactivo";
                $data[] = $row;
            }
        }
        header('Content-Type: application/json');
        echo json_encode(['data' => $data]);

        break;

    default:
        $sql = "SELECT  id_docente,
                        tipo_documento, 
                        numero_documento, 
                        nombres,
                        apellidos,
                        CONCAT(nombres, ' ', apellidos) AS nombre_completo,
                        especialidad,
                        descripcion_especialidad, 
                        telefono, 
                        direccion, 
                        email, 
                        declara_renta, 
                        retenedor_iva, 
                        estado 
        FROM docentes";
        $result = $conn->query($sql);
        if (!$result) {
            die("Error en la consulta: " . $conn->error);
        }
        $data = [];

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $row['estado'] = $row['estado'] ? "activo" : "inactivo";
                $data[] = $row;
            }
        }

        header('Content-Type: application/json');
        echo json_encode(['data' => $data]);
        break;
}

$conn->close();
?>
