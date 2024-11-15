<?php
include '../conexion.php';

$sql = "SELECT * FROM materias";
$result = $conn->query($sql);

$data = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $data[] = [
            'id_materia' => $row['id_materia'],
            'nombre' => $row['nombre'],
            'descripcion' => $row['descripcion'],
            'frecuencia' => $row['frecuencia'],
            'modalidad' => $row['modalidad'],
            'radio_button' => '<input type="radio" name="materia" value="' . $row['id_materia'] . '" required>'
        ];
    }
}

header('Content-Type: application/json');
echo json_encode(['data' => $data]);

$conn->close();
?>
