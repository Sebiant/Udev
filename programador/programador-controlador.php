<?php
include '../conexion.php';

$accion = isset($_GET['accion']) ? $_GET['accion'] : 'default';

switch ($accion) {
    case 'crear':
        $fecha = $_POST['fecha'];
        $hora_inicio = $_POST['hora_inicio'];
        $hora_salida = $_POST['hora_salida'];
        $salon = $_POST['id_salon'];
        $docente = $_POST['numero_documento'];
        $materia = $_POST['id_materia'];
        $estado = $_POST['estado'];
        $modalidad = $_POST['modalidad'];

        $sql = "INSERT INTO programador (fecha, hora_inicio, hora_salida, id_salon, numero_documento, id_materia, estado, modalidad) 
                VALUES ('$fecha', '$hora_inicio', '$hora_salida', '$salon', '$docente', '$materia', $estado, $modalidad)";

        if ($conn->query($sql) === TRUE) {
            echo json_encode(['status' => 'success', 'message' => 'Programador creado con éxito.']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Error al crear el programador: ' . $conn->error]);
        }
        break;

    case 'editar':
        $id_programador = $_POST['id_programador'];
        $fecha = $_POST['fecha'];
        $hora_inicio = $_POST['hora_inicio'];
        $hora_salida = $_POST['hora_salida'];
        $salon = $_POST['id_salon'];
        $docente = $_POST['numero_documento'];
        $materia = $_POST['materia'];
        $estado = $_POST['estado'];
        $modalidad = $_POST['modalidad'];

        $sql = "UPDATE programador 
                SET fecha='$fecha', hora_inicio='$hora_inicio', hora_salida='$hora_salida', salon='$salon', docente='$docente', materia='$materia', estado='$estado', modalidad='$modalidad' 
                WHERE id_programador='$id_programador'";

        if ($conn->query($sql) === TRUE) {
            echo json_encode(['status' => 'success', 'message' => 'Programador actualizado con éxito.']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Error al actualizar el programador: ' . $conn->error]);
        }
        break;
    
    case 'BusquedaPorId':
        $id_programador = $_POST['id_programador'];

        $sql = "SELECT * FROM programador WHERE id_programador = '$id_programador'";
        $result = $conn->query($sql);
        if ($result === false) {
            echo json_encode(["error" => "Error en la consulta SQL: " . $conn->error]);
            exit;
        }
        $data = [];
        if ($result->num_rows > 0) {
            $programador = $result->fetch_assoc();
            $salon['estado'] = $programador['estado'] == 1 ? "Vista" : "Perdida";
            $data[] = $programador;
        }
        header('Content-Type: application/json');
        echo json_encode(["data" => $data]);
        break;


    default:
        // Configurar idioma para fechas en español
        $conn->query("SET lc_time_names = 'es_ES'");

        // Definir las columnas que se pueden buscar y ordenar
        $columns = [
            'p.id_programador', 
            'p.fecha', 
            'p.hora_inicio', 
            'p.hora_salida', 
            'd.nombres',
            'd.apellidos',
            's.nombre_salon', 
            'm.nombre'
        ];

        // Recibir parámetros de DataTables
        $search = isset($_POST['search']['value']) ? $conn->real_escape_string($_POST['search']['value']) : '';
        $start = isset($_POST['start']) ? (int)$_POST['start'] : 0;
        $length = isset($_POST['length']) ? (int)$_POST['length'] : 10;
        $order_column = isset($_POST['order'][0]['column']) ? (int)$_POST['order'][0]['column'] : 0;
        $order_dir = isset($_POST['order'][0]['dir']) ? $_POST['order'][0]['dir'] : 'ASC';

        // Definir la columna para ordenar
        $order_by = $columns[$order_column];

        // Construir la cláusula WHERE para la búsqueda
        $where = "";
        if (!empty($search)) {
            $where .= " WHERE ";
            $search_terms = [];
            foreach ($columns as $column) {
                $search_terms[] = "$column LIKE '%$search%'";
            }
            $where .= implode(' OR ', $search_terms);
        }

        // Obtener el total de registros sin filtrar
        $total_query = "SELECT COUNT(*) as total 
                        FROM programador p
                        JOIN docentes d ON p.numero_documento = d.numero_documento
                        JOIN salones s ON p.id_salon = s.id_salon
                        JOIN materias m ON p.id_materia = m.id_materia";
        $total_result = $conn->query($total_query);
        $total_data = $total_result->fetch_assoc();
        $total_records = $total_data['total'];

        // Obtener el total de registros filtrados
        $filtered_query = "SELECT COUNT(*) as total 
                           FROM programador p
                           JOIN docentes d ON p.numero_documento = d.numero_documento
                           JOIN salones s ON p.id_salon = s.id_salon
                           JOIN materias m ON p.id_materia = m.id_materia
                           $where";
        $filtered_result = $conn->query($filtered_query);
        $filtered_data = $filtered_result->fetch_assoc();
        $filtered_records = $filtered_data['total'];

        // Consulta principal con paginación y ordenamiento
        $sql = "SELECT 
                    p.id_programador, 
                    DATE_FORMAT(p.fecha, '%d/%M/%Y') AS fecha, 
                    DATE_FORMAT(p.hora_inicio, '%h:%i %p') AS hora_inicio, 
                    DATE_FORMAT(p.hora_salida, '%h:%i %p') AS hora_salida, 
                    d.nombres,
                    d.apellidos,
                    s.nombre_salon, 
                    m.nombre,
                    p.estado,
                    p.modalidad
                FROM programador p
                JOIN docentes d ON p.numero_documento = d.numero_documento
                JOIN salones s ON p.id_salon = s.id_salon
                JOIN materias m ON p.id_materia = m.id_materia
                $where
                ORDER BY $order_by $order_dir
                LIMIT $start, $length";

        $result = $conn->query($sql);

        $data = [];

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $data[] = $row;
            }
        }

        // Responder en el formato que espera DataTables
        $response = [
            'draw' => isset($_POST['draw']) ? intval($_POST['draw']) : 0,
            'recordsTotal' => $total_records,
            'recordsFiltered' => $filtered_records,
            'data' => $data
        ];

        header('Content-Type: application/json');
        echo json_encode($response);
        break;
}

$conn->close();
?>
