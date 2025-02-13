<?php
include '../conexion.php';

$accion = isset($_GET['accion']) ? $_GET['accion'] : 'default';

switch ($accion) {
    default:
        // Consulta principal con paginación y ordenamiento
        $sql = "SELECT * FROM modulos";
        $result = $conn->query($sql);

        $data = [];

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $data[] = [
                    'id_materia'    => $row['id_modulo'],
                    'tipo'          => $row['tipo'],
                    'nombre'        => $row['nombre'],
                    'descripcion'   => $row['descripcion'],
                    'radio_button'  => '<input type="radio" name="modulo" value="' . $row['id_modulo'] . '" required>'
                ];
            }
        }

        // Preparar la respuesta en el formato que espera DataTables
        $response = [
            'data' => $data
        ];

        header('Content-Type: application/json');
        echo json_encode($response);

        $conn->close();
        break;
}
?>
