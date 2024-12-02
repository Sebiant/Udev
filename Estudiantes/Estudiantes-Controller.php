<?php

include ("../Conexion.php");
//include("funciones.php");

@$action = $_POST["operacion"];
main($action, $conn);

function main($action, $conn)
{
    switch ($action) {
        case 'crear':
            crear($conn);
            break;

        case 'editar':
            editar($conn);
            break;

        case 'borrar':
            borrar($conn);
            break;

        case 'obtener_registro':
            obtener_registro($conn);
            break;

        default:
            obtener_registros($conn);
            break;
    }
}

function crear($conn)
{

    if ($_POST["operacion"] == "crear") {


        $imagen = '';
        if (!empty($_FILES["imagen_estudiante"]["name"])) {
            $imagen = subir_imagen();
        }

        //se cambia los valores por placeholders al ser mysqli

        $stmt = $conn->prepare("INSERT INTO estudiantes(nombre_estudiante, apellidos_estudiante, fecha_nacimiento_estudiante, imagen, estado)VALUES(?,?,?,?,?)");
/*
        $resultado = $stmt->execute(
            array(
                ':nombre' => $_POST["nombre"],
                ':apellidos' => $_POST["apellidos"],
                ':fecha_nacimiento_estudiante' => $_POST["fecha_nacimiento_estudiante"],
                ':imagen_estudiante' => $imagen,
                ':estado' => $_POST["estado"],
            )
        );
        */

        $stmt->bind_param(
            "sssss",
            
            $_POST["nombre"],
            $_POST["apellidos"],
            $_POST["fecha_nacimiento_estudiante"],
            $imagen,
            $_POST["estado"]
        );



        /* if (!empty($resultado) ) {
            echo 'Registro creado';
        }*/
        if ($stmt->execute()){
            echo 'Registro creado';
        }else{
            echo 'Error al crear el registro: ' . $conn->errror;
        }
    }
}

function editar($conn)
{


    $codigo = $_POST["codigo_estudiante"];


    if ($_POST["operacion"] == "editar") {
        $imagen = '';

        if ($_FILES["imagen_estudiante"]["name"] != '') {
            $imagen = subir_imagen();
        } else {
            $imagen = $_POST["imagen_estudiante_oculta"];
        }


        $stmt = $conn->prepare("UPDATE estudiantes SET nombre_estudiante=:nombre, apellidos_estudiante=:apellidos,fecha_nacimiento_estudiante=:fecha_nacimiento_estudiante, 
        imagen=:imagen_estudiante,estado=:estado WHERE codigo_estudiante = :codigo_estudiante");

        $stmt->bindParam(':nombre', $_POST["nombre"]);
        $stmt->bindParam(':apellidos', $_POST["apellidos"]);
        $stmt->bindParam(':fecha_nacimiento_estudiante', $_POST["fecha_nacimiento_estudiante"]);
        $stmt->bindParam(':imagen_estudiante', $imagen);
        $stmt->bindParam(':estado', $_POST["estado"]);
        $stmt->bindParam(':codigo_estudiante', $codigo);

        $resultado = $stmt->execute();

        if ($resultado) {
            echo 'Registro actualizado';
        } else {
            echo 'Error al actualizar el registro';
            print_r($stmt->errorInfo());
        }
    }


}

function borrar($conn)
{
    if (isset($_POST["codigo_estudiante"])) {
        $stmt = $conn->prepare("DELETE FROM estudiantes WHERE codigo_estudiante = :codigo_estudiante");

        $resultado = $stmt->execute(
            array(
                ':codigo_estudiante' => $_POST["codigo_estudiante"]
            )
        );
        if (!empty($resultado)) {
            echo 'Registro borrado';
        }
    }
}


function obtener_registros($conn) //Se realizo revision de filtro para mostrar solo estudiantes activos
{
    $query = "SELECT * FROM estudiantes WHERE estado = 'activo' ";//visualizacion de error
//filtro de busqueda en la tabla
    if (!empty($_POST["search"]["value"])) {
        $query .= 'AND (nombre_estudiante LIKE ? OR apellidos_estudiante LIKE ?) ';
    }//cambio serch por ?

    //ORDENAMIENTO por columna designada
    if (!empty($_POST["order"])) {
        $query .= 'ORDER BY ' . intval($_POST['order']['0']['column']) . ' ' . $_POST["order"][0]['dir'] . ' ';
    } else {
        $query .= 'ORDER BY codigo_estudiante DESC ';
    }

    //PAGINACION

    if (!empty($_POST["length"]) && $_POST["length"] != -1) {
        $query .= 'LIMIT ?, ?'; //cambios de star y length por ?
    }

    //preparacion de la consulta

    $stmt = $conn->prepare($query);
    $param_types = '';
    $params = [];

    //correccion realiada para simplificacion del codigo y manejo de busqueda, orden y paginacion

    if (!empty($_POST["search"]["value"])) {
        $search = "%" . $_POST["search"]["value"] . "%";
        $param_types .= 'ss';
        $params[] = &$search;
        $params[] = &$search;
    }

    if (!empty($_POST["length"]) && $_POST["length"] != -1) {
        $start = intval($_POST["start"]);
        $length = intval($_POST["length"]);
        $param_types .= 'ii';
        $params[] = &$start;
        $params[] = &$length;
    }

    if ($param_types) {
        $stmt->bind_param($param_types, ...$params);
    }


    try {
        $stmt->execute();
        //$resultado = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $resultado = $stmt->get_result();
        $datos = array();
        //$filtered_rows = $stmt->rowCount();
        //$draw = isset($_POST['draw']) ? intval($_POST['draw']) : 0;

        while ($fila = $resultado->fetch_assoc()) {
            $imagen = $fila["imagen"] ? '<img src="../img/' . $fila["imagen"] . '" class="img-thumbnail" width="50" height="35" />' : '';

            $datos[] = [
                $fila["codigo_estudiante"],
                $fila["nombre_estudiante"],
                $fila["apellidos_estudiante"],
                $fila["fecha_nacimiento_estudiante"],
                $imagen,
                $fila["estado"],
                '<button type="button" data-bs-toggle="modal" data-bs-target="#modalUsuario" name="editar" id="' . $fila["codigo_estudiante"] . '" class="btn btn-success bi bi-pencil-square editar"></button>'
            ];
        }

        $salida = array(
            //"draw" => $draw,
           // "recordsTotal" => $filtered_rows,
            //"draw" => isset($_POST['draw']) ? intval($_POST['draw']) : 0,
            //"recordsTotal" => $stmt->num_rows,
            //"recordsFiltered" => obtener_todos_registros($conn),
            "draw" => intval($_POST["draw"] ?? 0),
            "recordsTotal" => obtener_todos_registros($conn),
            "recordsFiltered" => $resultado->num_rows,
            "data" => $datos
        );

        echo json_encode($salida);


    } /*catch (PDOException $e)*/catch (mysqli_sql_exception $e) {
        echo "Error en la consulta: " . $e->getMessage();
    }
}

function obtener_registro($conn)
{
    if (isset($_POST["codigo_estudiante"])) {
        $stmt = $conn->prepare("SELECT * FROM estudiantes WHERE codigo_estudiante = :codigo_estudiante LIMIT 1");
        $stmt->bindParam(':codigo_estudiante', $_POST["codigo_estudiante"], PDO::PARAM_INT);

        try {
            $stmt->execute();
            $resultado = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $salida = array();

            foreach ($resultado as $fila) {
                $salida["nombre_estudiante"] = $fila["nombre_estudiante"];
                $salida["apellidos_estudiante"] = $fila["apellidos_estudiante"];
                $salida["fecha_nacimiento_estudiante"] = $fila["fecha_nacimiento_estudiante"];
                $salida["imagen_estudiante"] = $fila["imagen"] != "" ? '<img src="../img/' . $fila["imagen"] . '" class="img-thumbnail" width="100" height="" /><input type="hidden" name="imagen_estudiante_oculta" value="' . $fila["imagen"] . '"/>' : '<input type="hidden" name="imagen_estudiante_oculta" value=""/>';
                $salida["estado"] = $fila["estado"];
            }

            echo json_encode($salida);
        } catch (mysqli_sql_exception $e) {
            echo "Error en la consulta: " . $e->getMessage();
        }
    }
}


//correcion de forma y parametros de conexion nueva funcion
/*function obtener_todos_registros($conn)
{
    $stmt = $conn->prepare("SELECT COUNT(*) AS total FROM estudiantes WHERE estado = 'activo'");
    try {
        $stmt->execute();
        return $stmt->fetch_assoc()["total"] ?? 0;
    } catch (mysqli_sql_exception $e) {
        echo "Error en la consulta: " . $e->getMessage();
        return 0;
    }
}*/

function obtener_todos_registros($conn)
{
    $query = "SELECT COUNT(*) AS total FROM estudiantes WHERE estado = 'activo'";
    $result = $conn->query($query);
    return $result->fetch_assoc()["total"] ?? 0;
}



function subir_imagen()
{
    if (isset($_FILES["imagen_estudiante"])) {

        $extensiones = explode('.', $_FILES["imagen_estudiante"]['name']);
        $nuevo_nombre = rand() . '.' . $extensiones[1];
        $ubicacion = '../img/' . $nuevo_nombre;
        move_uploaded_file($_FILES["imagen_estudiante"]['tmp_name'], $ubicacion);
        return $nuevo_nombre;


    }
}

function obtener_nombre_imagen($codigo_estudiante)
{
    include ('../Conexion.php');
    $stmt = $conexion->prepare("SELECT imagen From estudiantes WHERE codigo_estudiante= '$codigo_estudiante'");
    $stmt->execute();
    $resultado = $stmt->fetchAll();
    foreach ($resultado as $fila) {
        return $fila["imagen"];
    }

}

function obtener_estado($conn)
{

    include ('../Conexion.php');
    $stmt = $conexion->prepare("SELECT estado FROM estudiantes ");
    $stmt->execute();
    $resutlado = $stmt->fetchAll();
    return $stmt->rowCount();

}