<?php
include '../conexion.php';

$accion = isset($_GET['accion']) ? $_GET['accion'] : 'default';

switch ($accion) {
    case 'crear':
        $nombre = $_POST['nombre'];
        $descripcion = $_POST['descripcion'];

        $sql = "INSERT INTO materias (nombre, descripcion) VALUES ('$nombre', '$descripcion')";
        
        if ($conn->query($sql) === TRUE) {
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
            $nombre = isset($_POST['nombre']) ? $_POST['nombre'] : $materia['nombre'];
            $descripcion = isset($_POST['descripcion']) ? $_POST['descripcion'] : $materia['descripcion'];
            
            $sql_update = "UPDATE materias SET nombre='$nombre', descripcion='$descripcion' WHERE id_materia='$id_materia'";

            if ($conn->query($sql_update) === TRUE) {
            } else {
                echo "Error al actualizar el registro: " . $conn->error;
            }
        } else {
        }
        break;

    case 'eliminar':
        $id_materia = $_POST['id_materia'];
        $sql = "UPDATE materias SET estado=0 WHERE id_materia='$id_materia'";

        if ($conn->query($sql) === TRUE) {
        } else {
            echo "Error al desactivar el registro: " . $conn->error;
        }
        break;

    case 'activar':
        $id_materia = $_POST['id_materia'];
        $sql = "UPDATE materias SET estado=1 WHERE id_materia='$id_materia'";
    
        if ($conn->query($sql) === TRUE) {
        } else {
            echo "Error al activar el registro: " . $conn->error;
        }
        break;
        
    case 'cambiarEstado':
        $id_materia = $_POST['id_materia'];
        $estado = $_POST['estado'];
    
        $sql = "UPDATE materias SET estado=$estado WHERE id_materia='$id_materia'";
    
        if ($conn->query($sql) === TRUE) {
        } else {
            echo "Error al cambiar el estado: " . $conn->error;
        }
        break;
        
    case 'busquedaPorId':
        $id_materia = $_POST['id_materia'];
        $sql = "SELECT * FROM materias WHERE id_materia='$id_materia'";
        $result = $conn->query($sql);
    
        $data = [];
    
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $data[] = $row;
            }
            echo json_encode(['data' => $data]);
        } else {
            echo json_encode(['error' => 'Registro no encontrado']);
        }
        break;

    default:
        $start = isset($_POST['start']) ? intval($_POST['start']) : 0;
        $length = isset($_POST['length']) ? intval($_POST['length']) : 10;
        $search = isset($_POST['search']['value']) ? $_POST['search']['value'] : '';
        
        $sql = "SELECT * FROM materias WHERE nombre LIKE '%$search%' OR descripcion LIKE '%$search%' LIMIT $start, $length";
        $result = $conn->query($sql);
    
        $data = [];
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $row['estado'] = ($row['estado'] == 1) ? "Activo" : "Inactivo";
                $data[] = $row;
            }
        }
        
        $sql_count = "SELECT COUNT(*) AS total FROM materias WHERE nombre LIKE '%$search%' OR descripcion LIKE '%$search%'";
        $result_count = $conn->query($sql_count);
        $totalData = $result_count->fetch_assoc()['total'];
    
        header('Content-Type: application/json');
        echo json_encode([
            'draw' => isset($_POST['draw']) ? intval($_POST['draw']) : 1,
            'recordsTotal' => $totalData,
            'recordsFiltered' => $totalData,
            'data' => $data
        ]);
        break;
}

$conn->close();
?>
