<?php
include '../conexion.php';

// Obtener la acción de la solicitud (puede ser 'crear', 'editar', 'eliminar', 'consultar' o 'default')
$accion = isset($_GET['accion']) ? $_GET['accion'] : 'default';

switch ($accion) {
    case 'crear':
        $tipo = $_POST['tipo'];
        $nombre = $_POST['nombre'];
        $duracion_mes = $_POST['duracion_mes'];
        $cant_modulos = $_POST['cant_modulos'];
        $descripcion = $_POST['descripcion'];  

        $sql = "INSERT INTO programas (tipo, nombre, duracion_mes, cant_modulos, descripcion) 
                VALUES ('$tipo', '$nombre', '$duracion_mes', '$cant_modulos', '$descripcion')";
        
        if ($conn->query($sql) === TRUE) {
            echo "Nuevo registro creado exitosamente.";
        } else {
            echo "Error al crear el registro: " . $conn->error;
        }
        break;

    case 'editar':
        $id_programa = $_POST['id_programa'];

        $sql_select = "SELECT * FROM programas WHERE id_programa='$id_programa'";
        $result = $conn->query($sql_select);

        if ($result->num_rows > 0) {
            $programa = $result->fetch_assoc();

            // Otros campos
            $tipo = isset($_POST['tipo']) ? $_POST['tipo'] : $programa['tipo'];
            $nombre = isset($_POST['nombre']) ? $_POST['nombre'] : $programa['nombre'];
            $duracion_mes = isset($_POST['duracion_mes']) ? $_POST['duracion_mes'] : $programa['duracion_mes'];
            $cant_modulos = isset($_POST['cant_modulos']) ? $_POST['cant_modulos'] : $programa['cant_modulos'];
            $descripcion = isset($_POST['descripcion']) ? $_POST['descripcion'] : $programa['descripcion'];
           
            $sql_update = "UPDATE programas SET 
                            tipo='$tipo', 
                            nombre='$nombre', 
                            duracion_mes='$duracion_mes', 
                            cant_modulos='cant_modulos', 
                            descripcion='$descripcion', 
                            estado='$estado'
                            WHERE id_programa='$id_programa'";

            if ($conn->query($sql_update) === TRUE) {
                echo "Registro actualizado exitosamente.";
            } else {
                echo "Error al actualizar el registro: " . $conn->error;
            }
        } else {
            echo "No se encontró el registro.";
        }
    break;


    case 'eliminar':
        $id_programa = $_POST['id_programa'];

        $sql = "UPDATE programas SET estado=0 WHERE id_programa='$id_programa'";

        if ($conn->query($sql) === TRUE) {
            echo "Registro desactivado exitosamente.";
        } else {
            echo "Error al desactivar el registro: " . $conn->error;
        }
        break;

        case 'activar':
            $id_programa = $_POST['id_programa'];
    
            $sql = "UPDATE programas SET estado=1 WHERE id_programa='$id_programa'";
    
            if ($conn->query($sql) === TRUE) {
                echo "Registro activado exitosamente.";
            } else {
                echo "Error al activar el registro: " . $conn->error;
            }
            break;

    default:
        $sql = "SELECT * FROM programas";
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
