<?php
include '../conexion.php';

$accion = isset($_GET['accion']) ? $_GET['accion'] : 'default';

switch ($accion) {
    case 'crear':
        $tipo = $_POST['tipo'] ?? '';
        $nombre = $_POST['nombre'] ?? '';
        $duracion_mes = $_POST['duracion_mes'] ?? '';
        $cant_modulos = $_POST['cant_modulos'] ?? '';
        $descripcion = $_POST['descripcion'] ?? '';

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

            $tipo = $_POST['tipo'] ?? $programa['tipo'];
            $nombre = $_POST['nombre'] ?? $programa['nombre'];
            $duracion_mes = $_POST['duracion_mes'] ?? $programa['duracion_mes'];
            $cant_modulos = $_POST['cant_modulos'] ?? $programa['cant_modulos'];
            $descripcion = $_POST['descripcion'] ?? $programa['descripcion'];
            $estado = $_POST['estado'] ?? $programa['estado'];
            
            $sql_update = "UPDATE programas SET 
                            tipo='$tipo', 
                            nombre='$nombre', 
                            duracion_mes='$duracion_mes', 
                            cant_modulos='$cant_modulos', 
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

    case 'cambiarEstado':
        $id_programa = $_POST['id_programa'];
        $estado = $_POST['estado'];

        $sql = "UPDATE programas SET estado=$estado WHERE id_programa='$id_programa'";

        if ($conn->query($sql) === TRUE) {
            echo "Estado cambiado exitosamente a " . ($estado == 1 ? "Activo" : "Inactivo") . ".";
        } else {
            echo "Error al cambiar el estado: " . $conn->error;
        }
        break;

    case 'BusquedaPorId':
        $id_programa = $_POST['id_programa'];

        $sql = "SELECT * FROM programas WHERE id_programa='$id_programa'";
        $result = $conn->query($sql);

        if ($result === false) {
            die("Error en la consulta SQL: " . $conn->error);
        }
    
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
        $search = isset($_GET['search']['value']) ? $_GET['search']['value'] : '';
        $start = isset($_GET['start']) ? $_GET['start'] : 0;
        $length = isset($_GET['length']) ? $_GET['length'] : 10;

        $sql_count = "SELECT COUNT(*) AS total FROM programas WHERE nombre LIKE '%$search%' OR tipo LIKE '%$search%'";
        $result_count = $conn->query($sql_count);
        $total_records = $result_count->fetch_assoc()['total'];

        $sql = "SELECT * FROM programas WHERE nombre LIKE '%$search%' OR tipo LIKE '%$search%' 
                LIMIT $start, $length";
        $result = $conn->query($sql);

        $data = [];

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $row['estado'] = ($row['estado'] == 1) ? "Activo" : "Inactivo";
                $data[] = $row;
            }
        }

        header('Content-Type: application/json');
        echo json_encode([
            'draw' => isset($_GET['draw']) ? $_GET['draw'] : 1,
            'recordsTotal' => $total_records,
            'recordsFiltered' => $total_records,
            'data' => $data
        ]);
        break;
}

$conn->close();
?>
