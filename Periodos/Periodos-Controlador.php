
<?php
include '../conexion.php';

$accion = isset($_GET['accion']) ? $_GET['accion'] : 'default';

switch ($accion) {
    case 'crear':
        $nombre_periodo = $_POST['nombre'];
        $fecha_inicio = $_POST['fecha_inicio'];
        $fecha_fin = $_POST['fecha_fin'];

        if (empty($fecha_inicio) || empty($fecha_fin)) {
            echo "Error: Las fechas son obligatorias.";
            exit;
        }

        $sql = "INSERT INTO periodos (nombre,fecha_inicio, fecha_fin) VALUES ('$nombre_periodo','$fecha_inicio', '$fecha_fin')";
        
        if ($conn->query($sql) === TRUE) {
        } else {
            echo "Error al crear el periódo: " . $conn->error;
        }
        break;

    case 'editar':
        $id_periodo = $_POST['id_periodo']?? null;

        $sql_select = "SELECT * FROM periodos WHERE id_periodo='$id_periodo'";
        $result = $conn->query($sql_select);

        if ($result->num_rows > 0) {
            $periodo = $result->fetch_assoc();
            $nombre_periodo = $_POST['nombre'] ?? $periodo['nombre'];
            $fecha_inicio = $_POST['fecha_inicio'] ?? $periodo['fecha_inicio'];
            $fecha_fin = $_POST['fecha_fin'] ?? $periodo['fecha_fin'];
           
            $sql_update = "UPDATE periodos SET nombre= '$nombre_periodo', fecha_inicio='$fecha_inicio', fecha_fin='$fecha_fin'WHERE id_periodo='$id_periodo'";

            if ($conn->query($sql_update) === TRUE) {
            } else {
                echo "Error al actualizar el periódo: " . $conn->error;
            }
        } else {
            echo "No se encontró el periódo.";
        }
        break;

    case 'cambiarEstado':
        $id_periodo = $_POST['id_periodo'];
        $estado = $_POST['estado'];
        $sql = "UPDATE periodos SET estado=$estado WHERE id_periodo='$id_periodo'";

        if ($conn->query($sql) === TRUE) {
        } else {
            echo "Error al cambiar el estado: " . $conn->error;
        }
        break;

    case 'BusquedaPorId':
        $id_periodo = $_POST['id_periodo'];
        $sql = "SELECT * FROM periodos WHERE id_periodo='$id_periodo'";
        $result = $conn->query($sql);
        
        $data = $result->fetch_all(MYSQLI_ASSOC);
        echo json_encode(['data' => $data]);
        break;

    default:
    $draw = $_GET['draw'] ?? '';  // Si no existe, asigna una cadena vacía
    $start = $_GET['start'] ?? 0; // Si no existe, asigna 0
    $length = $_GET['length'] ?? 10; // Si no existe, asigna un valor por defecto
    $searchValue = $_GET['search']['value'] ?? ''; // Si no existe, asigna cadena vacía
    
    $where = "";
    if (!empty($searchValue)) {
        $where = " WHERE periodos.nombre LIKE '%$searchValue% OR periodos.fecha_inicio LIKE '%$searchValue%' OR periodos.fecha_fin LIKE '%$searchValue%'";
    }
    
    // Obtener el total de registros antes de filtrar
    $sqlTotal = "SELECT COUNT(*) AS total FROM periodos";
    $resultTotal = $conn->query($sqlTotal);
    $rowTotal = $resultTotal->fetch_assoc();
    $totalRecords = $rowTotal['total'];
    
    // Obtener el total de registros después del filtro
    $sqlFiltered = "SELECT COUNT(*) AS total FROM periodos $where";
    $resultFiltered = $conn->query($sqlFiltered);
    $rowFiltered = $resultFiltered->fetch_assoc();
    $totalFiltered = $rowFiltered['total'];
    
    // Consulta principal con paginación
    $sql = "SELECT * FROM periodos $where ORDER BY estado DESC LIMIT $start, $length";
    $result = $conn->query($sql);
    
    $data = [];
    while ($row = $result->fetch_assoc()) {
        $row['estado'] = ($row['estado'] == 1) ? "Activo" : "Inactivo";
        $data[] = $row;
    }
    
    // Enviar respuesta JSON
    echo json_encode([
        "draw" => intval($draw),
        "recordsTotal" => $totalRecords,  // Total de registros sin filtro
        "recordsFiltered" => $totalFiltered,  // Total de registros después del filtro
        "data" => $data
    ]);    
}

$conn->close();
?>
