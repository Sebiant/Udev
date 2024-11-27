<?php 
include("../conexion.php");

// consulta de la info del estudiante y mostrados en los inputs listas y variables 
//ESTADO:Funcional inactivo
if ($conexion) {
    try {

      
        $query = "SELECT estudiantes.codigo_estudiante, estudiantes.nombre_estudiante, estudiantes.apellidos_estudiante, estudiantes.fecha_nacimiento_estudiante, estudiantes.imagen, servicios.codigo_servicio, servicios.descripcion_servicio
        FROM convenio 
        INNER JOIN estudiantes 
        ON convenio.codigo_estudiante = estudiantes.codigo_estudiante 
        INNER JOIN servicios
        ON convenio.codigo_servicio = servicios.codigo_servicio 
        WHERE convenio.codigo_estudiante = 10";

        $resultadoDato = $conexion->query($query);
        


        while ($row = $resultadoDato->fetch(PDO::FETCH_ASSOC)) {
            $codigo_estudi = $row['codigo_estudiante'];
            $nombre_est = $row['nombre_estudiante'];
            $apellidos_est = $row['apellidos_estudiante'];
            $fecha_naci_est = $row['fecha_nacimiento_estudiante'];
            $carrera_est= $row['descripcion_servicio'];
            $imagen_est= $row['imagen'];
           
        }
    } catch (PDOException $e) {
        echo "error al ejecutar " . $e->getMessage();
    }
}

function obtener_registro($conexion)
{

    $salida = array();

    try {
        $stmt = $conexion->prepare("SELECT * FROM convenio WHERE codigo_convenio = :codigo_convenio LIMIT 1");
        $stmt->bindParam(':codigo_convenio', $_POST['codigo_convenio'], PDO::PARAM_INT);
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
