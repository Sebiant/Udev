
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
        
        case 'obtener_registro':
            obtener_registro($conn);
            break;
        case 'obtener_registrooos':
                obtener_registrooos($conn);
                break; 
        case 'obtener_pagos_estudiantes':
            obtener_pagos_estudiantes($conn);
                    break;
        case 'cambiarEstado':
            cambiar_Estado($conn);
            break;
            
      
        default:
            obtener_registros($conn);
            break;


    }
}



function crear($conn){

    $stmt = $conn->prepare("INSERT INTO convenio(codigo_convenio, descripcion_convenio, valor_total_convenio, saldo_convenio, id_programa, codigo_estudiante, estado, tipo_fk_convenio) VALUES(?, ?, ?, ?, ?, ?, ?, ?)");

    $state = 1;
    


    $stmt->bind_param(
        "ssiissss",
        
            $_POST["codigo_convenio"],
            $_POST["descripcion_convenio"],
            $_POST["valor_total_convenio"],
            $_POST["saldo_convenio"],
            $_POST["codigo_In_servicio"],
            $_POST["codigo_estudiante"],
            $state,
            $_POST["tipo_convenio"],
        
    );

    if ($stmt->execute()) {
        echo
        'Convenio creado';
    } else {
        echo 'Convenio no creado';
    }


}
function editar($conn) {

    if(isset($_POST["codigo_convenio"])){

        $stmt = $conn->prepare("UPDATE convenio 
        SET descripcion_convenio=?, valor_total_convenio=?, saldo_convenio=?, id_programa=?, codigo_estudiante=?, tipo_fk_convenio=? 
        WHERE codigo_convenio=? 
        limit 1");

        

        $stmt->bind_param(
            "siissis",
            $_POST["descripcion_convenio"],
            $_POST["valor_total_convenio"],
            $_POST["saldo_convenio"],
            $_POST["codigo_In_servicio"],
            $_POST["codigo_estudiante"],
            //$_POST["estado"],
            $_POST["tipo_convenio"],

            //identificador para hacer el update
            $_POST['codigo_convenio']
        );


        

        if ($stmt->execute()) {
            echo 'Convenio actualizado';
        } else {
            echo "No se pudo actualizar el convenio";
            echo 'Error al actualizar el registro: ' . $conn->error; // Error de conexión
        echo 'Error del statement: ' . $stmt->error; // Error específico de la consulta
        }
    } else {
        echo "no ha llegado ningun codigo";
    }
    
}
//datos de la tabla con ajax y DATATABLES tabla convenio
function obtener_registros($conn)
{
    $query = "";
    $salida = array();
    $query = "SELECT convenio.codigo_convenio, convenio.codigo_estudiante, estudiantes.nombre_estudiante, estudiantes.apellidos_estudiante, convenio.id_programa, programas.nombre, 
    convenio.descripcion_convenio, tipo_convenio.codigo_tipo_convenio, tipo_convenio.valor_descuento, convenio.valor_total_convenio, convenio.saldo_convenio, convenio.estado 
    FROM convenio 
    INNER JOIN estudiantes 
    ON convenio.codigo_estudiante = estudiantes.codigo_estudiante 
    LEFT JOIN tipo_convenio
    ON convenio.tipo_fk_convenio = tipo_convenio.codigo_tipo_convenio
    INNER JOIN programas 
    ON convenio.id_programa = programas.id_programa;";

   /* if (isset($_POST["search"]["value"])) {
        $query .= ' WHERE descripcion_convenio LIKE "%' . $_POST["search"]["value"] . '%" ';
    }

    if (isset($_POST["order"])) {
        $query .= ' ORDER BY ' . $_POST['order']['0']['column'] . ' ' . $_POST["order"][0]['dir'] . ' ';
    } else {
        $query .= ' ORDER BY codigo_convenio DESC ';
    }

    if (isset($_POST['length']) && isset($_POST['start'])) {
        $query .= 'LIMIT ' . ($_POST["start"]) . ',' . $_POST["length"];
    }*/

    $stmt = $conn->prepare($query);

    try {/*

        $stmt->execute();
        $resultado = $stmt->fetchAll();
        $datos = array();
        $filtered_rows = $stmt->rowCount();

        $draw = isset($_POST['draw']) ? intval($_POST['draw']) : 0;
        foreach ($resultado as $fila) {
            $sub_array = array();
            $sub_array[] = $fila["codigo_convenio"];
            //$sub_array[] = $fila["codigo_estudiante"];
            $sub_array[] = $fila["nombre_estudiante"];
            $sub_array[] = $fila["apellidos_estudiante"];
            //$sub_array[] = $fila["codigo_servicio"];
            $sub_array[] = $fila["nombre"];
            $sub_array[] = $fila["descripcion_convenio"];
            $sub_array[] = $fila["valor_descuento"];
            $sub_array[] = $fila["valor_total_convenio"];
            $sub_array[] = $fila["saldo_convenio"];
            $sub_array[] = $fila["estado"];

           $sub_array[] = '<button type="button" data-bs-toggle="modal" data-bs-target="#modalCrearConvenio" name="editar" id="' . $fila["codigo_convenio"] . '" class="btn btn-success bi bi-pencil-square editar"></button>';
           $sub_array[] = '<button type="button"  data-bs-toggle="modal" data-bs-target="#modalInfoEstudiante" name="info" id="' . $fila["codigo_convenio"] . '" class="btn btn-info bi bi-person-square info"></button>';

            $datos[] = $sub_array;
        }
        /*$stmt_carreras = $conexion->query("SELECT * FROM carreras");
        $carreras = $stmt_carreras->fetchAll(PDO::FETCH_ASSOC);

        $stmt_estudiantes = $conexion->query("SELECT * FROM estudiantes");
        $estudiantes = $stmt_estudiantes->fetchAll(PDO::FETCH_ASSOC);
*/
        $param_types='';
        $params=[];

        $stmt->execute();
        $resultado = $stmt->get_result();
        $datos = [];

        while($fila=$resultado->fetch_assoc()){

            $estado = $fila["estado"];
            if($estado == 1){
                $estado = "Activo";

            }else{
                $estado = "Inactivo";
            }
            $codigo_convenio =$fila["codigo_convenio"];

            $buttonClass = ($estado === "Activo") ? "btn-danger" : "btn-success";
            $buttonText = ($estado === "Activo") ? "Inactivar" : "Activar";

           $porcent= $fila["valor_descuento"];
           $valor_porcent=$fila["valor_total_convenio"];
           $total_cal=(($valor_porcent * $porcent) / 100);
           $TOTAL=$valor_porcent - $total_cal;

            if(!$fila["valor_descuento"]){
                $fila["valor_descuento"] = 0;
            };

            $sub_array=[
                $codigo_convenio,
                //$sub_array[] = $fila["codigo_estudiante"];
                $fila["nombre_estudiante"],
                $fila["apellidos_estudiante"],
                //$sub_array[] = $fila["codigo_servicio"];
                $fila["nombre"],
                $fila["descripcion_convenio"],
                $fila["valor_descuento"] . ' %',
                $fila["valor_total_convenio"],
                $TOTAL,
                $fila["saldo_convenio"],
                $estado,
                //boton modificar
            '<button type="button" data-bs-toggle="modal" data-bs-target="#modalCrearConvenio" name="acciones" id="' . $codigo_convenio . '" class="btn btn-primary w-100 editar">Modificar</button>',
            // boton dinamico
            '<button type="button" class="btn w-100 ' . $buttonClass . ' btn-toggle-state" data-id="' . $codigo_convenio . '"data-estado="' . $estado . '">' . $buttonText . '</button>',
            '<button type="button"  data-bs-toggle="modal" data-bs-target="#modalInfoEstudiante" name="info" id="' . $fila["codigo_convenio"] . '" class="btn btn-info bi bi-person-square info"></button>'
            ];

            $datos[] = $sub_array;
        }



        /*$salida = array(
            "draw" => $draw,
            "recordsTotal" => $filtered_rows,
            "recordsFiltered" => obtener_todos_registros(),
            "data" => $datos,
           /* "carreras"=>$carreras,
            "estudiantes"=>$estudiantes*/

        $salida=[
            "draw"=>intval($_POST["draw"] ?? 0),
            "recordsTotal" => obtener_todos_registros($conn),
            "recordsFiltered" => $resultado->num_rows,
            "data" => $datos
    
        ];

        

        echo json_encode($salida);
    } catch (Exception $e) {
        echo "Error en la consulta: " . $e->getMessage();
    }
}

function cambiar_estado($conn){


    
    if (isset($_POST["codigo_convenio"]) && isset($_POST["estado"])) {
        $nuevoEstado = intval($_POST["estado"]);
        echo $nuevoEstado;
        


    $stmt = $conn->prepare ("UPDATE convenio SET estado=? WHERE codigo_convenio=? LIMIT 1");
    $stmt->bind_param(
        'ii',
        $nuevoEstado,
        $_POST["codigo_convenio"]
    );

    if ($stmt->execute()){
        echo "Estado cambiado exitosamente a " . ($nuevoEstado == 1 ? "Activo" : "Inactivo" . ".");
    }else {
        echo "Error al cambiar el estado: " . $conn->error;
    }
}else{
    echo "DATOS INSUFICIENTES PARA CAMBIAR EL ESTADO";

}
}

function obtener_registro($conn)
{
    if(isset($_POST["codigo_convenio"])){

    $salida = array();


        $stmt = $conn->prepare("SELECT * FROM convenio WHERE codigo_convenio = ? LIMIT 1");
        
        $codigo_conve = intval($_POST["codigo_convenio"]);

        $stmt->bind_param('i', $codigo_conve);

    try {

        $stmt->execute();
        $resultado=$stmt->get_result();

        if ($fila=$resultado->fetch_assoc()) {
    
            $salida = $fila;
        } else {
            $salida["error"] = "No se encontraron resultados";
        }
    } catch (PDOException $e) {
        $salida["error"] = "Error en la ejecución de la consulta: " . $e->getMessage();
    }
    echo json_encode($salida);
}
}





function obtener_todos_registros()
{
    include('../Conexion.php');
    //$stmt = $conn->prepare('SELECT * FROM convenio');
    $stmt = $conn->prepare("SELECT COUNT(*) AS total FROM convenio  WHERE estado = 'activo'");
   /* $stmt->execute();
    $resultado = $stmt->fetch();
    return $stmt->rowCount();*/
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

function obtener_registros_estudiantes(){
    include('../conexion.php');
    $stmt = $conn->prepare('SELECT movimientos.codigo_movimiento, movimientos.fecha_movimiento, movimientos.valor_movimiento
    FROM convenio INNER JOIN movimientos ON convenio.codigo_estudiante=movimientos.codigo_fk_estudiante');
    $stmt->execute();
    $resultado = $stmt->fetch();
    return $stmt->rowCount();


}
function obtener_pagos_estudiantes($conn)
{
    $query = "";
    $salida = array();
    $query = "SELECT * FROM movimientos";

    if (isset($_POST["search"]["value"])) {
        $query .= ' WHERE fecha_movimiento LIKE "%' . $_POST["search"]["value"] . '%" ';
    }

    if (isset($_POST["order"])) {
        $query .= ' ORDER BY ' . $_POST['order']['0']['column'] . ' ' . $_POST["order"][0]['dir'] . ' ';
    } else {
        $query .= ' ORDER BY codigo_movimiento DESC ';
    }

    if (isset($_POST['length']) && isset($_POST['start'])) {
        $query .= 'LIMIT ' . ($_POST["start"]) . ',' . $_POST["length"];
    }

    $stmt = $conn->prepare($query);

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
            $sub_array[] = $fila["valor_moviento"];
            $sub_array[] = '<button type="button" data-bs-toggle="modal" data-bs-target="#modalInfoEstudiante" name="editar" id="' . $fila["codigo_movimiento"] . '" class="btn btn-success bi bi-pencil-square editar"></button>';
            
            $datos[] = $sub_array;
        }
        /*$stmt_carreras = $conexion->query("SELECT * FROM carreras");
        $carreras = $stmt_carreras->fetchAll(PDO::FETCH_ASSOC);

        $stmt_estudiantes = $conexion->query("SELECT * FROM estudiantes");
        $estudiantes = $stmt_estudiantes->fetchAll(PDO::FETCH_ASSOC);
*/
        $salida = array(
            "draw" => $draw,
            "recordsTotal" => $filtered_rows,
            "recordsFiltered" =>obtener_registros_estudiantes(),
            "data" => $datos,
           /* "carreras"=>$carreras,
            "estudiantes"=>$estudiantes*/
        );

        echo json_encode($salida);
    } catch (Exception $e) {
        echo "Error en la consulta: " . $e->getMessage();
    }
}
function obtener_registrooos($conexion)
{
    $salida = array();

    try {
        $stmt = $conn->prepare("SELECT estudiantes.*, movimientos.* FROM convenio
                                    INNER JOIN estudiantes ON convenio.codigo_estudiante = estudiantes.codigo_estudiante
                                    INNER JOIN movimientos ON convenio.codigo_estudiante = movimientos.codigo_fk_estudiante
                                    WHERE convenio.codigo_convenio = :codigo_convenio");
        $stmt->bindParam(':codigo_convenio', $_POST['codigo_convenio'], PDO::PARAM_INT);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $resultado = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $salida = $resultado;
        } else {
            $salida["error"] = "No se encontraron resultados";
        }
    } catch (PDOException $e) {
        $salida["error"] = "Error en la ejecución de la consulta: " . $e->getMessage();
    }
    echo json_encode($salida);
}
