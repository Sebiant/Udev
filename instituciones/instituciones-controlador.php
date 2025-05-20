<?php
include '../conexion.php';

$accion = isset($_GET['accion']) ? $_GET['accion'] : 'default';

switch ($accion) {
    case 'crear':
        $Nombre = $_POST['nombre'];
        $Direccion = $_POST['direccion'];
        
        $sql = "INSERT INTO instituciones (nombre, direccion) 
                VALUES ('$Nombre', '$Direccion')";
        
        if ($conn->query($sql) === TRUE) {
        } else {
            echo "Error al crear el registro: " . $conn->error;
        }
        break;

        case 'editar':
            // Validar que se haya enviado el formulario completo
            if (!isset($_POST['id_institucion']) || empty($_POST['id_institucion'])) {
                echo "El ID de la institución es obligatorio.";
                break;
            }
        
            // Recibir los datos del formulario
            $id_institucion = $_POST['id_institucion'];
            $Nombre = isset($_POST['nombre']) ? $_POST['nombre'] : null;
            $Direccion = isset($_POST['direccion']) ? $_POST['direccion'] : null;
           
            // Validar que al menos un campo adicional esté presente
            if (is_null($Nombre) && is_null($Direccion)) {
                echo "No se han enviado datos para actualizar.";
                break;
            }
        
            // Obtener el registro actual desde la base de datos
            $sql_select = "SELECT * FROM instituciones WHERE id_institucion = '$id_institucion'";
            $result = $conn->query($sql_select);
        
            if ($result->num_rows > 0) {
                // Actualizar solo los campos enviados
                $fieldsToUpdate = [];
                if (!is_null($Nombre)) {
                    $fieldsToUpdate[] = "nombre = '$Nombre'";
                }
                if (!is_null($Direccion)) {
                    $fieldsToUpdate[] = "direccion = '$Direccion'";
                }
        
                // Construir la consulta de actualización dinámica
                $sql_update = "UPDATE instituciones SET " . implode(", ", $fieldsToUpdate) . " WHERE id_institucion = '$id_institucion'";
        
                if ($conn->query($sql_update) === TRUE) {
                    echo "Institución actualizada correctamente.";
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
    
        $sql_institucion = "UPDATE instituciones SET estado=$estado WHERE id_institucion='$id_institucion'";
        $resultado = $conn->query($sql_institucion);
        
        if ($resultado === TRUE) {
            // Cambiar estado del salon asociados con la institucion
            $sql_salones = "UPDATE salones SET estado = $estado WHERE id_institucion = '$id_institucion'";
            $resultado_salones = $conn->query($sql_salones);
    
            if ($resultado_salones === TRUE) {
                echo "Estado cambiado exitosamente a " . ($estado == 1 ? "Activo" : "Inactivo") . " para la institución y sus salónes.";
            } else {
                echo "Institución actualizada, pero error al cambiar estado de salones: " . $conn->error;
            }
        } else {
            echo "Error al cambiar el estado de la institución: " . $conn->error;
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
        $draw = isset($_POST['draw']) ? intval($_POST['draw']) : 1;
        $start = isset($_POST['start']) ? intval($_POST['start']) : 0;
        $length = isset($_POST['length']) ? intval($_POST['length']) : 10;
        $searchValue = isset($_POST['search']['value']) ? $conn->real_escape_string($_POST['search']['value']) : '';
        $orderColumnIndex = isset($_POST['order'][0]['column']) ? intval($_POST['order'][0]['column']) : 0;
        $orderDir = isset($_POST['order'][0]['dir']) && $_POST['order'][0]['dir'] === 'desc' ? 'DESC' : 'ASC';

        $columns = ['nombre', 'direccion', 'estado'];
        $orderColumn = $columns[$orderColumnIndex];

        $where = "";
        if (!empty($searchValue)) {
            $where = "WHERE nombre LIKE '%$searchValue%' OR direccion LIKE '%$searchValue%'";
        }

        $sqlTotal = "SELECT COUNT(*) as total FROM instituciones";
        $totalResult = $conn->query($sqlTotal);
        $totalRecords = $totalResult->fetch_assoc()['total'];

        $sqlFiltered = "SELECT COUNT(*) as total FROM instituciones $where";
        $filteredResult = $conn->query($sqlFiltered);
        $filteredRecords = $filteredResult->fetch_assoc()['total'];

        $sql = "SELECT * FROM instituciones $where 
        ORDER BY estado DESC, $orderColumn $orderDir
        LIMIT $start, $length";
        $result = $conn->query($sql);

        $data = [];

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $row['estado'] = ($row['estado'] == 1) ? "Activo" : "Inactivo";
                $data[] = $row;
            }
        }

        $response = [
            "draw" => $draw,
            "recordsTotal" => $totalRecords,
            "recordsFiltered" => $filteredRecords,
            "data" => $data
        ];

        header('Content-Type: application/json');
        echo json_encode($response);
        break;
}

$conn->close();
?>
