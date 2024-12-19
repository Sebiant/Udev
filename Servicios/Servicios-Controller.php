<?php

include("../Conexion.php");

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

function borrar($conn)
{
    if (isset($_POST["codigo_servicio"])) {
        $stmt = $conn->prepare("DELETE FROM servicios WHERE codigo_servicio = :codigo_servicio");

        $resultado = $stmt->execute(
            array(
                ':codigo_servicio'  => $_POST["codigo_servicio"]
            )
        );
        if (!empty($resultado)) {
            echo 'Registro borrado';
        }
    }
}

function crear($conn)
{
    $stmt = $conn->prepare("INSERT INTO programas(id_programa, nombre, cant_modulos, estado) VALUES(?, ?, ?, ?)");

    /*$resultado = $stmt->execute(
        array(
            ':codigo_servicio'  => $_POST["codigo_servicio"],
            ':descripcion_servicio'  => $_POST["descripcion_servicio"],
            ':valor_total_servicio'  => $_POST["valor_total_servicio"],
            ':estado'  => $_POST["estado"],
        )
    );*/
    
        $stmt->bind_param(
            "ssis",
            $_POST["id_programa"] ,
            $_POST["descripcion_servicio"],
            $_POST["valor_total_servicio"],
            $_POST["estado"],
        );
        

    if ($stmt->execute()){
        echo 'Registro creado';
    } else{
        
            echo "Error en la consulta: " . $conn->getMessage();

    }
}

function editar($conn)
{
    $stmt = $conn->prepare("UPDATE servicios SET descripcion_servicio=?, valor_total_servicio=?, estado=? WHERE codigo_servicio = ?");

    $resultado = $stmt->execute(
        array(
            ':descripcion_servicio' => $_POST["descripcion_servicio"],
            ':valor_total_servicio' => $_POST["valor_total_servicio"],
            ':estado' => $_POST["estado"],
            ':codigo_servicio' => $_POST["codigo_servicio"]
        )
    );

    if (!empty($resultado)) {
        echo 'Registro actualizado';
    } else {
        echo "No se pudo actualizar el registro";
    }
}

function obtener_registro($conn)
{
    $salida = array();
    

    try {
        $stmt = $conn->prepare("SELECT * FROM servicios WHERE codigo_servicio = ? LIMIT 1");
        $stmt->bindParam(':codigo_servicio', $_POST['codigo_servicio'], PDO::PARAM_INT);
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

function obtener_registros($conn)
{

$query = "";
$salida = array();
$query = "SELECT * FROM programas  WHERE estado = 'activo'";

if (!empty($_POST["search"]["value"])) {
    $query .= 'WHERE id_programa LIKE ? . $_POST["search"]["value"] . ? ';
    $query .= 'OR nombre LIKE ? . $_POST["search"]["value"] . ? ';
}

if (!empty($_POST["order"])) {
    $query .= 'ORDER BY ' . $_POST['order']['0']['column'] . ' ' . 
    $_POST["order"][0]['dir'] . ' ';
} else {
    $query .= 'ORDER BY id_programa DESC ';
}


if (!empty($_POST["length"]) && $_POST["length"] != -1) {
    $query .= 'LIMIT ?, ?' ;
}

$stmt = $conn->prepare($query);



/*
try {
    $stmt->execute();
    $resultado = $stmt->fetchAll();
    $datos = array();
    $filtered_rows = $stmt->rowCount();

    $draw = isset($_POST['draw']) ? intval($_POST['draw']) : 0;

    foreach ($resultado as $fila) {

        

        $sub_array = array();
        $sub_array[] = $fila["codigo_servicio"];
        $sub_array[] = $fila["descripcion_servicio"];
        $sub_array[] = $fila["valor_total_servicio"];
        $sub_array[] = $fila["estado"];
        $sub_array[] = '<div class="text-center"><button type="button" name="editar" id="' . $fila["codigo_servicio"] . '" class="btn btn-success btn-xs editar"><i class="bi bi-pencil-fill"></i></button></div>';
        //$sub_array[] = '<div class="text-center"><button type="button" name="borrar" id="' . $fila["codigo_servicio"] . '" class="btn btn-danger btn-xs borrar"><i class="bi bi-trash-fill"></i></button></div>';
        
        $datos[] = $sub_array;
    }*/

    $param_types='';
    $params=[];

    if(!empty($_POST["search"]["value"])){
        $search="%" . $_POST["search"]["value"] . "%";
        $param_types .='ss';
        $params[] = &$search;
        $params[] = &$search;

    }

    if(!empty($_POST["length"]) && $_POST["length"] != -1){
        $start = intval($_POST["start"]);
        $length = intval($_POST["length"]);
        $param_types .= 'ii';
        $params[] = &$start;
        $params[] = &$length;
    }

    if($param_types){
        $stmt->bind_param($param_types, ...$params);

    }

    $stmt->execute();
    $resultado = $stmt->get_result();
    $datos = [];

    while($fila=$resultado->fetch_assoc()){
        $sub_array=[
            $fila["id_programa"],
            $fila["nombre"],//nombre clave para la relacion de conde va a ir dirigida la informacion la tabla sql

            $fila["cant_modulos"],
            $fila["estado"],
            '<button type="button" data-bs-toggle="modal" data-bs-target="#modalServicio" name="editar" id="' . $fila["id_programa"] . '" class="btn btn-success bi bi-pencil-square editar"></button>'
        ];

        $datos[]=$sub_array;

    }

    /*$salida = array(
        "draw"              => $draw,
        "recordsTotal"      => $filtered_rows,
        "recordsFiltered"   => obtener_todos_registros(),
        'data'              => $datos
    );*/
    $salida=[
        "draw"=>intval($_POST["draw"] ?? 0),
        "recordsTotal" => obtener_todos_registros($conn),
        "recordsFiltered" => $resultado->num_rows,
        "data" => $datos
    ];

    echo json_encode($salida);


}

function obtener_todos_registros (){
    include("../Conexion.php");
    $stmt = $conn->prepare("SELECT COUNT(*) AS total FROM programas  WHERE estado = 'activo'");
    try{
    $stmt ->execute();


    $stmt->bind_result($total);

    $stmt ->fetch();

    return $total ?? 0;
    } catch(mysqli_sql_exception $e){
        error_log("Error en la consulta: " . $e->getMessage());
        return 0;
    } finally{
        $stmt->close();
    }

}

?>
