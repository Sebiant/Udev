<?php
include '../conexion.php';

$accion = isset($_GET['accion']) ? $_GET['accion'] : 'default';

switch ($accion) {
    case 'crear':
        $tipo = $_POST['tipo'];
        $nombre = $_POST['nombre'];
        $descripcion = $_POST['descripcion'];
    
        $sql = "INSERT INTO modulos (tipo, nombre, descripcion) VALUES ('$tipo', '$nombre', '$descripcion')";
    
        if ($conn->query($sql) === TRUE) {
        } else {
            echo "Error al crear el registro: " . $conn->error;
        }
        break;
    
    case 'editar':
        $id_modulo = $_POST['id_modulo'];
        $sql_select = "SELECT * FROM modulos WHERE id_modulo='$id_modulo'";
        $result = $conn->query($sql_select);
    
        if ($result->num_rows > 0) {
            $modulo = $result->fetch_assoc();
            $tipo = isset($_POST['tipo']) ? $_POST['tipo'] : $modulo['tipo'];
            $nombre = isset($_POST['nombre']) ? $_POST['nombre'] : $modulo['nombre'];
            $descripcion = isset($_POST['descripcion']) ? $_POST['descripcion'] : $modulo['descripcion'];
    
            $sql_update = "UPDATE modulos SET tipo='$tipo', nombre='$nombre', descripcion='$descripcion' WHERE id_modulo='$id_modulo'";
    
            if ($conn->query($sql_update) === TRUE) {
            } else {
                echo "Error al actualizar el registro: " . $conn->error;
            }
        } else {
        }
        break;
    
        case 'cambiarEstado':
            $id_modulo = $_POST['id_modulo'];
            $estado = $_POST['estado'];
        
            $sql = "UPDATE modulos SET estado=$estado WHERE id_modulo='$id_modulo'";
        
            if ($conn->query($sql) === TRUE) {
            } else {
                echo "Error al cambiar el estado: " . $conn->error;
            }
            break;
        
        
        case 'busquedaPorId':
            $id_modulo = $_POST['id_modulo'];
            $sql = "SELECT * FROM modulos WHERE id_modulo='$id_modulo'";
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
    
    $sql = "SELECT * FROM modulos 
    WHERE nombre LIKE '%$search%' OR descripcion LIKE '%$search%' 
    ORDER BY estado DESC
    LIMIT $start, $length";
    $result = $conn->query($sql);
    
    $data = [];
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $row['estado'] = ($row['estado'] == 1) ? "Activo" : "Inactivo";
            $data[] = $row;
        }
    }
    
    $sql_count = "SELECT COUNT(*) AS total FROM modulos WHERE nombre LIKE '%$search%' OR descripcion LIKE '%$search%'";
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
