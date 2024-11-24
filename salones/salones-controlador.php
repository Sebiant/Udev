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
        if ($conn->query($sql) === TRUE) {
            echo "Salón creado con éxito";
        } else {
            echo "Error al crear el salón: " . $conn->error;
        }
        break;

    case 'modificar':
        $id_salon = $_POST['id_salon'];

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
            // Cambiar a "Sí" o "No"
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

            // Obtener valores del formulario o mantener los existentes
            $nombre_salon = isset($_POST['nombre_salon']) ? $_POST['nombre_salon'] : $salon['nombre_salon'];
            $capacidad = isset($_POST['capacidad']) ? $_POST['capacidad'] : $salon['capacidad'];
            $descripcion = isset($_POST['descripcion']) ? $_POST['descripcion'] : $salon['descripcion'];
            $id_institucion = isset($_POST['id_institucion']) ? $_POST['id_institucion'] : $salon['id_institucion'];
            // Asegúrate de convertir el estado a 1 o 0
            $estado = isset($_POST['estado']) ? ($_POST['estado'] == 'Sí' ? 1 : 0) : $salon['estado'];

            // Actualizar el salón
            $sql_update = "UPDATE Salones SET  
                            nombre_salon='$nombre_salon', 
                            capacidad='$capacidad',
                            descripcion='$descripcion',
                            id_institucion='$id_institucion',
                            estado='$estado'
                            WHERE id_salon='$id_salon'";

            if ($conn->query($sql_update) === TRUE) {
                echo "Registro actualizado exitosamente.";
            } else {
                echo "Error al actualizar el registro: " . $conn->error;
            }
        } else {
            echo "No se encontró el registro del salón.";
        }
        break;

    case 'activar':
        activarSalon($conn);
        break;

    case 'desactivar':
        desactivarSalon($conn);
        break;

    default:
        // Consulta para obtener todos los salones
        $sql = "SELECT S.id_salon, S.nombre_salon, S.capacidad, S.descripcion, i.nombre, S.estado 
                FROM salones S
                JOIN instituciones i ON i.id_institucion = S.id_institucion";
        
        // Ejecutar consulta
        if ($result = $conn->query($sql)) {
            // Manejar resultados
            while ($row = $result->fetch_assoc()) {
                // Convertir estado a "Sí" o "No"
                $row['estado'] = ($row['estado'] == 1) ? "Activo" : "Inactivo";
                $data[] = $row;
            }
            
            header('Content-Type: application/json');
            echo json_encode(['data' => $data]);
        } else {
            die("Error en la consulta SQL: " . $conn->error);
        }
}

function activarSalon($conn) {
    // Obtener ID del salón
    if (!isset($_POST['id_salon'])) {
        echo json_encode(['success' => false, 'message' => 'ID del salón no proporcionado.']);
        return;
    }

    // Preparar consulta
    $id_salon = $_POST['id_salon'];
    // Cambiar a 1 para activo
    $sql = "UPDATE salones SET estado = 1 WHERE id_salon = ?";
    
    // Preparar y ejecutar consulta
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param('i', $id_salon);
        
        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Salón activado exitosamente.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error al activar el salón.']);
        }
        
        // Cerrar declaración
        $stmt->close();
    } else {
       echo json_encode(['success' => false, 'message' => 'Error al preparar la consulta.']);
    }
}

function desactivarSalon($conn) {
    // Obtener ID del salón
    if (!isset($_POST['id_salon'])) {
       echo json_encode(['success' => false, 'message' => 'ID del salón no proporcionado.']);
       return;
    }

    // Preparar consulta
    $id_salon = $_POST['id_salon'];
    // Cambiar a 0 para inactivo
    $sql = "UPDATE salones SET estado = 0 WHERE id_salon = ?";
    
    // Preparar y ejecutar consulta
    if ($stmt = $conn->prepare($sql)) {
       $stmt->bind_param('i', $id_salon);
       
       if ($stmt->execute()) {
           echo json_encode(['success' => true, 'message' => 'Salón desactivado exitosamente.']);
       } else {
           echo json_encode(['success' => false, 'message' => 'Error al desactivar el salón.']);
       }
       
       // Cerrar declaración
       $stmt->close();
   } else {
      echo json_encode(['success' => false, 'message' => 'Error al preparar la consulta.']);
   }
}

// Cerrar conexión a la base de datos
$conn->close();
?>