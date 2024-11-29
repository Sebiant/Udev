<?php

include("../conexion.php");



// Manejo de la petición Ajax
$action = isset($_GET['action']) ? $_GET['action'] : '';
var_dump($action);

main($action, $conexion);

function main($action, $conexion) {
    switch ($action) {
        case 'obtener_datos_tabla': // Corregido el nombre de la acción
            obtener_datos_tabla($conexion);
            break;
        case 'info':
            obtener_estudiante($conexion);
            break;
        default:
        obtener_datos_tabla($conexion);
        
       // info_estudiante($conexion);
            //echo json_encode(array('error' => 'Acción no válida'));
    }
}

//obtiene los datos para la tabla por ajax, no esta en uso ahora
function obtener_datos_tabla($conexion)
{
    $query = "";
    $salida = array();
    $query = "SELECT codigo_movimiento, fecha_movimiento, valor_movimiento FROM movimientos";

   

    $stmt = $conexion->prepare($query);

    try {

        $stmt->execute();
        $resultado = $stmt->fetchAll();
        $datos = array();
        $filtered_rows = $stmt->rowCount();

        $draw = isset($_POST['draw']) ? intval($_POST['draw']) : 0;
        foreach ($resultado as $fila) {
            $sub_array = array();
            $sub_array[] = $fila["codigo_movimiento"];
            $sub_array[] = $fila["fecha_movimiento"];
            $sub_array[] = $fila["valor_movimiento"];
           $sub_array[] = '<button type="button"  data-bs-toggle="modal" data-bs-target="#modalInfoEstudiante" name="info" id="' . $fila["codigo_movimiento"] . '" class="btn btn-info bi bi-person-square info"></button>';

            $datos[] = $sub_array;
        }

        $salida = array(
            "draw" => $draw,
            "recordsTotal" => $filtered_rows,
            "recordsFiltered" => obtener_registros_estudiantes(),
            "data" => $datos,
           /* "carreras"=>$carreras,
            "estudiantes"=>$estudiantes*/
        );

        echo json_encode($salida);
    } catch (Exception $e) {
        echo "Error en la consulta: " . $e->getMessage();
    }
}

//prepara la conexion con la consulta general
function obtener_registros_estudiantes(){
    include('../conexion.php');
    $stmt = $conexion->prepare('SELECT * FROM movimientos');
    $stmt->execute();
    $resultado = $stmt->fetch();
    return $stmt->rowCount();


}
//obtencion de un estidoante por medio del registro de convenio, creo que no esta funcional
function obtener_estudiante($conexion)
{

    $salida = array();

    try {
        $stmt = $conexion->prepare("SELECT codigo_estudiante, nombre_estudiante, apellidos_estudiante, fecha_nacimiento_estudiante FROM estudiantes WHERE codigo_estudiante = :codigo_estudiant LIMIT 1");
        $stmt->bindParam(':codigo_estudiant', $_POST['codigo_estudiante'], PDO::PARAM_INT);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            $salida = $resultado;
        } else {
            $salida["error"] = "No se encontraron resultados";
        }
    } catch (PDOException $e) {
        $salida["error"] = "Error en la ejecución de la consulta: " . $e->getMessage();
    }
    echo json_encode($salida);
}



?>


    