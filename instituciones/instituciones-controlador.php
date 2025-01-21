<?php
include '../conexion.php';

$accion = isset($_GET['accion']) ? $_GET['accion'] : 'default';

switch ($accion) {
    case 'crear':
        $Nombre = $_POST['nombre'];
        $Direccion = $_POST['direccion'];
        $Estado = isset($_POST['estado']) ? 1 : 0;
        
        $sql = "INSERT INTO instituciones (nombre, direccion, estado) 
                VALUES ('$Nombre', '$Direccion','$Estado')";
        
        if ($conn->query($sql) === TRUE) {
        } else {
            echo "Error al crear el registro: " . $conn->error;
        }
        break;

    case 'editar':
        $id_institucion = $_POST['id_institucion'];

        $sql_select = "SELECT * FROM instituciones WHERE id_institucion='$id_institucion'";
        $result = $conn->query($sql_select);

        if ($result->num_rows > 0) {
            $instituto = $result->fetch_assoc();

            $Nombre = isset($_POST['nombre']) ? $_POST['nombre'] : $instituto['nombre'];
            $Direccion = isset($_POST['direccion']) ? $_POST['direccion'] : $instituto['direccion'];
            $Estado = isset($_POST['estado']) ? $_POST['estado'] : $instituto['estado'];
            
            $sql_update = "UPDATE instituciones SET  
                            nombre='$Nombre', 
                            direccion='$Direccion', 
                            estado='$Estado'
                            WHERE id_institucion='$id_institucion'";

            if ($conn->query($sql_update) === TRUE) {
            } else {
                echo "Error al actualizar el registro: " . $conn->error;
            }
        } else {
            echo "No se encontró el registro de la institución.";
        }
    break;

    case 'cambiarEstado':
        $id_institucion = $_POST['id_institucion'];
        $estado = $_POST['estado'];
    
        $sql = "UPDATE instituciones SET estado=$estado WHERE id_institucion='$id_institucion'";
    
        if ($conn->query($sql) === TRUE) {
        } else {
            echo "Error al cambiar el estado: " . $conn->error;
        }
        break;

     case 'buscarPorId':     
        
        if (empty($_POST['id_institucion'])) {
            echo json_encode(["error" => "ID de institución no proporcionado"]);
            exit;
        }

        $sql = "SELECT * FROM instituciones WHERE id_institucion=?";
        $stmt = $conn->prepare($sql);

        if (!$stmt) {
            die("Error en la preparación de la consulta: " . $conn->error);
        }

        $stmt->bind_param('i', $_POST['id_institucion']);
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
        $sql = "SELECT * FROM instituciones";
        $result = $conn->query($sql);

        $data = [];

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $row['estado'] = ($row['estado'] == 1) ? "Activo" : "Inactivo";
                $data[] = $row;
            }
        }

        header('Content-Type: application/json');
        echo json_encode(['data' => $data]);
        break;
}

$conn->close();
?>
