<?php
include '../conexion.php';

$accion = isset($_GET['accion']) ? $_GET['accion'] : 'default';

switch ($accion) {
    default:
        $columns = ['nombre', 'descripcion'];  // Asegúrate de que estos sean los nombres correctos de tus columnas en la tabla "materias"

        // Recibir parámetros de DataTables
        $search = isset($_POST['search']['value']) ? $conn->real_escape_string($_POST['search']['value']) : '';
        $start = isset($_POST['start']) ? (int)$_POST['start'] : 0;
        $length = isset($_POST['length']) ? (int)$_POST['length'] : 5;
        $order_column = isset($_POST['order'][0]['column']) ? (int)$_POST['order'][0]['column'] : 0;
        $order_dir = isset($_POST['order'][0]['dir']) ? $_POST['order'][0]['dir'] : 'ASC';

        // Definir columna para ordenar
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
        $total_query = "SELECT COUNT(*) as total FROM materias";
        $total_result = $conn->query($total_query);
        $total_data = $total_result->fetch_assoc();
        $total_records = $total_data['total'];

        // Obtener el total de registros filtrados
        $filtered_query = "SELECT COUNT(*) as total FROM materias $where";
        $filtered_result = $conn->query($filtered_query);
        $filtered_data = $filtered_result->fetch_assoc();
        $filtered_records = $filtered_data['total'];

        // Consulta principal con paginación y ordenamiento
        $sql = "SELECT * FROM materias $where ORDER BY $order_by $order_dir LIMIT $start, $length";
        $result = $conn->query($sql);

        $data = [];

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $data[] = [
                    'id_materia'    => $row['id_materia'],
                    'nombre'        => $row['nombre'],
                    'descripcion'   => $row['descripcion'],
                    'radio_button'  => '<input type="radio" name="materia" value="' . $row['id_materia'] . '" required>'
                ];
            }
        }

        // Preparar la respuesta en el formato que espera DataTables
        $response = [
            'draw' => isset($_POST['draw']) ? intval($_POST['draw']) : 0,
            'recordsTotal' => $total_records,
            'recordsFiltered' => $filtered_records,
            'data' => $data
        ];

        header('Content-Type: application/json');
        echo json_encode($response);

        $conn->close();
        break;
}
?>
