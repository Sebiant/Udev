<?php
include_once '../conexion.php';

$accion = isset($_GET['accion']) ? $_GET['accion'] : 'default';

switch ($accion){
    case 'crear':
        $nombre_salon = $_POST['nombre_salon'];
        $capacidad = $_POST['capacidad'];
        $descripcion = $_POST['descripcion'];
        $id_institucion = $_POST['id_institucion'];
        $estado = isset($_POST['estado']) ? 1 : 0;

        $sql = "INSERT INTO salones (nombre_salon, capacidad, descripcion, id_institucion, estado)
                VALUES ('$nombre_salon','$capacidad','$descripcion','$id_institucion','$estado')";
        if ($conn -> query($sql)=== TRUE){
            echo "Salón creado con éxito";
            } else {
                echo "Error al crear el salón: " . $conn->error;
        }
        break;
    case 'editar':
        $id_salon = $_POST['id_salon'];

        $sql_select = "SELECT * FROM salones WHERE id_salones='$id_salon'";
        $result = $conn->query($sql_select);

        if ($result->num_rows > 0) {
            $salon = $result->fetch_assoc();
  
            // Otros campos         
            $nombre_salon = isset($_POST['nombre_salon']) ? $_POST['nombre_salon'] : $salon['nombre_salon'];
            $capacidad = isset($_POST['capacidad']) ? $_POST['capacidad'] : $salon['capacidad'];
            $descripcion = isset($_POST['descripcion']) ? $_POST['descripcion'] : $salon ['descripcion'];
            $id_institucion = isset($_POST['id_institucion']) ? $_POST['id_institucion'] : $salon['id_institucion'];
            $estado = isset($_POST['estado']) ? $_POST['estado'] : $salon['estado'];
            
            $sql_update = "UPDATE salones SET  
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
            echo "No se encontró el registro del salon.";
        }
    break;


    case 'eliminar':
        $id_salon = $_POST['id_salon'];

        $sql = "UPDATE salones SET estado=0 WHERE id_salon='$id_salon'";

        if ($conn->query($sql) === TRUE) {
            echo "Registro desactivado exitosamente.";
        } else {
            echo "Error al desactivar el registro: " . $conn->error;
        }
        break;

        case 'activar':
            $id_salon = $_POST['id_salon'];
    
            $sql = "UPDATE salones SET estado=1 WHERE id_salon='$id_salon'";
    
            if ($conn->query($sql) === TRUE) {
                echo "Registro activado exitosamente.";
            } else {
                echo "Error al activar el registro: " . $conn->error;
            }
            break;

    default:
        $sql = "SELECT * FROM salones";
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
    $conn ->close();
?>





