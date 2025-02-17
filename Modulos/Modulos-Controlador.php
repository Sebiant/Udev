<?php
include '../conexion.php';

$accion = isset($_GET['accion']) ? $_GET['accion'] : 'default';

switch ($accion) {
    case 'crear':
        $tipo = $_POST['tipo'];
        $nombre = $_POST['nombre'];
        $id_programa = $_POST['id_programa'];
        $descripcion = $_POST['descripcion'];
    
        $sql = "INSERT INTO modulos (id_programa, tipo, nombre, descripcion) VALUES ('$id_programa','$tipo', '$nombre', '$descripcion')";
    
        if ($conn->query($sql) === TRUE) {
        } else {
            echo "Error al crear el registro: " . $conn->error;
        }
        break;
    
        case 'editar':
            $id_modulo = $_POST['id_modulo'];
            $tipo = $_POST['tipo'];
            $nombre = $_POST['nombre'];
            $id_programa = $_POST['id_programa'];
            $descripcion = $_POST['descripcion'];

            $sql = "UPDATE modulos 
                    SET tipo = ?, nombre = ?, id_programa = ?, descripcion = ? 
                    WHERE id_modulo = ?";
    
            if ($stmt = $conn->prepare($sql)) {
                $stmt->bind_param(
                    "ssisi", 
                    $tipo, 
                    $nombre, 
                    $id_programa, 
                    $descripcion,
                    $id_modulo
                );
    
                if ($stmt->execute()) {
                    echo "Registro actualizado correctamente.";
                } else {
                    echo "Error al actualizar el registro: " . $stmt->error;
                }
    
                $stmt->close();
            } else {
                echo "Error al preparar la consulta: " . $conn->error;
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

    $search = "%$search%";

    $sql = "SELECT m.id_modulo, p.nombre AS programa, m.tipo, m.nombre, m.descripcion, m.estado 
            FROM modulos m
            JOIN programas p ON m.id_programa = p.id_programa
            WHERE m.tipo LIKE ? OR m.nombre LIKE ? OR p.nombre LIKE ? OR m.descripcion LIKE ?
            ORDER BY m.estado DESC
            LIMIT ?, ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssii", $search, $search, $search, $search, $start, $length);
    $stmt->execute();
    $result = $stmt->get_result();

    $data = [];
    while ($row = $result->fetch_assoc()) {
        $row['estado'] = ($row['estado'] == 1) ? "Activo" : "Inactivo";
        $data[] = $row;
    }

    $sql_count = "SELECT COUNT(*) AS total 
                  FROM modulos m
                  JOIN programas p ON m.id_programa = p.id_programa
                  WHERE m.nombre LIKE ? OR m.descripcion LIKE ?";
    $stmt_count = $conn->prepare($sql_count);
    $stmt_count->bind_param("ss", $search, $search);
    $stmt_count->execute();
    $result_count = $stmt_count->get_result();
    $totalData = $result_count->fetch_assoc()['total'];

    header('Content-Type: application/json');
    echo json_encode([
        'draw' => isset($_POST['draw']) ? intval($_POST['draw']) : 1,
        'recordsTotal' => $totalData,
        'recordsFiltered' => $totalData,
        'data' => $data
    ]);

    $stmt->close();
    $stmt_count->close();
    break;
}
$conn->close();
?>
