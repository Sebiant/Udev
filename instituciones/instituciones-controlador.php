<?php
include '../conexion.php';

// Obtener la acción de la solicitud (puede ser 'crear', 'editar', 'eliminar', 'consultar' o 'default')
$accion = isset($_GET['accion']) ? $_GET['accion'] : 'default';

switch ($accion) {
    case 'crear':
        $Nombre = $_POST['nombre'];
        $Direccion = $_POST['direccion'];
        $Estado = isset($_POST['estado']) ? 1 : 0;
        
        $sql = "INSERT INTO instituciones (nombre, direccion, estado) 
                VALUES ('$Nombre', '$Direccion','$Estado')";
        
        if ($conn->query($sql) === TRUE) {
            echo "Nuevo registro creado exitosamente.";
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

            
             // Si no está seleccionado, valor es 0   
            // Otros campos         
            $Nombre = isset($_POST['nombre']) ? $_POST['nombre'] : $instituto['nombre'];
            $Direccion = isset($_POST['direccion']) ? $_POST['direccion'] : $instituto['direccion'];
            $Estado = isset($_POST['estado']) ? $_POST['estado'] : $instituto['estado'];
            
            $sql_update = "UPDATE instituciones SET  
                            nombre='$Nombre', 
                            direccion='$Direccion', 
                            estado='$Estado'
                            
                            WHERE id_institucion='$id_institucion'";

            if ($conn->query($sql_update) === TRUE) {
                echo "Registro actualizado exitosamente.";
            } else {
                echo "Error al actualizar el registro: " . $conn->error;
            }
        } else {
            echo "No se encontró el registro de la institucion.";
        }
    break;


    case 'eliminar':
        $id_institucion = $_POST['id_institucion'];

        $sql = "UPDATE instituciones SET estado=0 WHERE id_institucion='$id_institucion'";

        if ($conn->query($sql) === TRUE) {
            echo "Registro desactivado exitosamente.";
        } else {
            echo "Error al desactivar el registro: " . $conn->error;
        }
        break;

        case 'activar':
            $id_institucion = $_POST['id_institucion'];
    
            $sql = "UPDATE instituciones SET estado=1 WHERE id_institucion='$id_institucion'";
    
            if ($conn->query($sql) === TRUE) {
                echo "Registro activado exitosamente.";
            } else {
                echo "Error al activar el registro: " . $conn->error;
            }
            break;

    default:
        $sql = "SELECT * FROM instituciones";
        $result = $conn->query($sql);

            $data = [];

            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $row['estado'] = $row['estado'] ? "activo" : "innactivo";
                    $data[] = $row;
                }
            }

            header('Content-Type: application/json');
            echo json_encode(['data' => $data]);
            break;
    }
    $conn->close();
?>
