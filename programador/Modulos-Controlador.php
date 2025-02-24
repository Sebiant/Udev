<?php
include '../conexion.php';

$accion = isset($_GET['accion']) ? $_GET['accion'] : 'default';

switch ($accion) {
    default:
        $draw = isset($_POST['draw']) ? intval($_POST['draw']) : 0;
        $start = isset($_POST['start']) ? intval($_POST['start']) : 0;
        $length = isset($_POST['length']) ? intval($_POST['length']) : 10;
        $searchValue = isset($_POST['search']['value']) ? $_POST['search']['value'] : '';
        $orderColumn = isset($_POST['order'][0]['column']) ? intval($_POST['order'][0]['column']) : 0;
        $orderDir = isset($_POST['order'][0]['dir']) ? $_POST['order'][0]['dir'] : 'asc';

        $columns = ['m.tipo', 'm.nombre', 'p.nombre', 'm.descripcion'];

        $orderBy = $columns[$orderColumn] ?? 'm.id_modulo';

        $sql = "SELECT m.id_modulo, m.tipo, p.nombre AS programa, m.nombre, m.descripcion
        FROM modulos m
        LEFT JOIN programas p ON m.id_programa = p.id_programa
        WHERE m.estado = 1";


        if (!empty($searchValue)) {
            $sql .= " WHERE m.tipo LIKE '%$searchValue%' 
                      OR m.nombre LIKE '%$searchValue%' 
                      OR p.nombre LIKE '%$searchValue%' 
                      OR m.descripcion LIKE '%$searchValue%'";
        }

        $totalQuery = $sql;

        $sql .= " ORDER BY $orderBy $orderDir LIMIT $start, $length";

        $result = $conn->query($sql);

        $data = [];
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $data[] = [
                    'id_materia'  => $row['id_modulo'],
                    'tipo'        => $row['tipo'],
                    'programa'    => $row['programa'],
                    'nombre'      => $row['nombre'],
                    'descripcion' => $row['descripcion'],
                    'radio_button'=> '<input type="radio" name="modulo" value="' . $row['id_modulo'] . '" required>'
                ];
            }
        }

        $totalRecords = $conn->query("SELECT COUNT(*) as total FROM modulos")->fetch_assoc()['total'];

        $filteredRecords = $conn->query("SELECT COUNT(*) as total FROM ($totalQuery) as sub")->fetch_assoc()['total'];

        $response = [
            'draw' => $draw,
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $filteredRecords,
            'data' => $data
        ];

        header('Content-Type: application/json');
        echo json_encode($response);

        $conn->close();
        break;
}
?>
