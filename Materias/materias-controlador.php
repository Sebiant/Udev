<?php
include '../conexion.php';

// Obtener la acción de la solicitud (puede ser 'crear', 'editar', 'eliminar', 'consultar' o 'default')
$accion = isset($_GET['accion']) ? $_GET['accion'] : 'default';

switch ($accion) {
    case 'crear':
        $nombre= $_POST['nombre'];
        $descripcion = $_POST['descripcion'];
        $frecuencia = $_POST['frecuencia'];
        $modalidad = $_POST['modalidad'];

        $sql = "INSERT INTO materias (nombre, descripcion, frecuencia, modalidad) 
                VALUES ('$nombre', '$descripcion', '$frecuencia', '$modalidad')";
        
        if ($conn->query($sql) === TRUE) {
            echo "Nuevo registro creado exitosamente.";
        } else {
            echo "Error al crear el registro: " . $conn->error;
        }
        break;

    case 'editar':
        $id_materia = $_POST['id_materia'];

        $sql_select = "SELECT * FROM materias WHERE id_materia='$id_materia'";
        $result = $conn->query($sql_select);

        if ($result->num_rows > 0) {
            $materia = $result->fetch_assoc();
            // Otros campos
            $nombre = isset($_POST['nombre']) ? $_POST['nombre'] : $materia['nombre'];
            $descripcion = isset($_POST['descripcion']) ? $_POST['descripcion'] : $materia['descripcion'];
            $frecuencia = isset($_POST['frecuencia']) ? $_POST['frecuencia'] : $materia['frecuencia'];
            $modalidad = isset($_POST['modalidad']) ? $_POST['modalidad'] : $docente['modalidad'];
            $estado = isset($_POST['estado']) ? $_POST['estado'] : $materia['estado'];

            $sql_update = "UPDATE materias SET 
                            nombre='$nombre', 
                            descripcion='$descripcion', 
                            frecuencia='$frecuencia', 
                            modalidad='$modalidad', 
                            estado='$estado'
                            WHERE id_materia='$id_materia'";

            if ($conn->query($sql_update) === TRUE) {
                echo "Registro actualizado exitosamente.";
            } else {
                echo "Error al actualizar el registro: " . $conn->error;
            }
        } else {
            echo "No se encontró el registro de la materia.";
        }
    break;


    case 'eliminar':
        $id_materia = $_POST['id_materia'];

        $sql = "UPDATE materias SET estado=0 WHERE id_materia='$id_materia'";

        if ($conn->query($sql) === TRUE) {
            echo "Registro desactivado exitosamente.";
        } else {
            echo "Error al desactivar el registro: " . $conn->error;
        }
        break;

        case 'activar':
            $id_materia = $_POST['id_materia'];
    
            $sql = "UPDATE materias SET estado=1 WHERE id_materia='$id_materia'";
    
            if ($conn->query($sql) === TRUE) {
                echo "Registro activado exitosamente.";
            } else {
                echo "Error al activar el registro: " . $conn->error;
            }
            break;

            default:
            $sql = "SELECT * FROM materias";
            $result = $conn->query($sql);
        
            $data = [];
        
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $data[] = $row;
                }
            }
        
            header('Content-Type: application/json');
            echo json_encode(['data' => $data]);
            break;
        
    }
    $conn->close();
?>