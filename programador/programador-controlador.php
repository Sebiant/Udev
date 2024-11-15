<?php
include '../conexion.php';

$accion = isset($_GET['accion']) ? $_GET['accion'] : 'default';

switch ($accion) {
    case 'crear':
        $fecha = $_POST['fecha'];
        $hora_inicio = $_POST['hora_inicio'];
        $hora_salida = $_POST['hora_salida'];
        $salon = $_POST['salon'];
        $docente = $_POST['docente'];
        $materia = $_POST['materia'];

        $sql = "INSERT INTO programadores (fecha, hora_inicio, hora_salida, salon, docente, materia, estado) 
                VALUES ('$fecha', '$hora_inicio', '$hora_salida', '$salon', '$docente', '$materia', 1)";

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
        $salon = $_POST['salon'];
        $docente = $_POST['docente'];
        $materia = $_POST['materia'];

        $sql = "UPDATE programadores 
                SET fecha='$fecha', hora_inicio='$hora_inicio', hora_salida='$hora_salida', salon='$salon', docente='$docente', materia='$materia' 
                WHERE id_programador='$id_programador'";

        if ($conn->query($sql) === TRUE) {
            echo json_encode(['status' => 'success', 'message' => 'Programador actualizado con éxito.']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Error al actualizar el programador: ' . $conn->error]);
        }
        break;

    case 'eliminar':
        $id_programador = $_POST['id_programador'];

        $sql = "DELETE FROM programadores WHERE id_programador='$id_programador'";

        if ($conn->query($sql) === TRUE) {
            echo json_encode(['status' => 'success', 'message' => 'Programador eliminado con éxito.']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Error al eliminar el programador: ' . $conn->error]);
        }
        break;

    default:
        $conn->query("SET lc_time_names = 'es_ES'");

        $sql = "SELECT
                    p.id_programador, 
                    DATE_FORMAT(p.fecha, '%d/%M/%Y') AS fecha, 
                    DATE_FORMAT(p.hora_inicio, '%h:%i %p') AS hora_inicio, 
                    DATE_FORMAT(p.hora_salida, '%h:%i %p') AS hora_salida, 
                    d.nombres,
                    d.apellidos,
                    s.nombre_salon, 
                    m.nombre 
                FROM programador p
                JOIN docentes d ON p.id_docente = d.id_docente
                JOIN salones s ON p.id_salon = s.id_salon
                JOIN materias m ON p.id_materia = m.id_materia";

        $result = $conn->query($sql);

        $data = [];

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $data[] = $row;
            }
        }

        header('Content-Type: application/json');
        echo json_encode(['data' => $data]);
        break;
}

$conn->close();
?>
