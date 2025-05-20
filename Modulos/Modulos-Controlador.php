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
            
                // Primero consultamos si el programa del módulo está activo
                $sql_validacion = "
                    SELECT p.estado AS estado_programa 
                    FROM modulos m 
                    INNER JOIN programas p ON m.id_programa = p.id_programa 
                    WHERE m.id_modulo = '$id_modulo'
                ";
            
                $resultado = $conn->query($sql_validacion);
            
                if ($resultado && $resultado->num_rows > 0) {
                    $fila = $resultado->fetch_assoc();
                    $estado_programa = $fila['estado_programa'];
            
                    if ($estado_programa != 1 && $estado == 1) {
                        echo "Error: No se puede activar el módulo porque su programa está inactivo.";
                        break;
                    }
            
                    // Todo OK, actualizamos el módulo
                    $sql_update = "UPDATE modulos SET estado = $estado WHERE id_modulo = '$id_modulo'";
                    if ($conn->query($sql_update) === TRUE) {
                        echo "Estado del módulo actualizado correctamente a " . ($estado == 1 ? "Activo" : "Inactivo") . ".";
                    } else {
                        echo "Error al cambiar el estado del módulo: " . $conn->error;
                    }
                } else {
                    echo "Error: No se encontró el módulo o su programa relacionado.";
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

    $sql = "SELECT m.id_modulo, p.nombre AS programa, p.estado AS estado_programa, m.tipo, m.nombre, m.descripcion, m.estado 
        FROM modulos m
        JOIN programas p ON m.id_programa = p.id_programa
        WHERE m.tipo LIKE ? OR m.nombre LIKE ? OR p.nombre LIKE ? OR m.descripcion LIKE ?
        ORDER BY 
            (m.estado = 0),       -- primero módulos activos (m.estado = 1 → falso = 0)
            (p.estado = 0),       -- luego programas activos (p.estado = 1 → falso = 0)
            p.nombre
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
