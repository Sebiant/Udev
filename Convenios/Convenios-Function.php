<?php

include("../conexion.php");
/*Este archivo hace la consulta de la info de estudiante para el modal
info_estudiante Estado:FUNCIONAL activo*/

@$action = $_POST["operacion"];

main($action, $conexion);

function main($action, $conexion)
{
    switch ($action) {
        case 'registro_individual':
            Registro_individual($conexion);
            break;
       
            
      
        default:
            
            break;


    }
}

function Registro_individual($conexion){
    
    $query = "SELECT estudiantes.codigo_estudiante, estudiantes.nombre_estudiante, estudiantes.apellidos_estudiante, estudiantes.fecha_nacimiento_estudiante, estudiantes.imagen, servicios.codigo_servicio, servicios.descripcion_servicio
            FROM convenio 
            INNER JOIN estudiantes 
            ON convenio.codigo_estudiante = estudiantes.codigo_estudiante 
            INNER JOIN servicios
            ON convenio.codigo_servicio = servicios.codigo_servicio 
            WHERE convenio.codigo_convenio = :codigo_convenio LIMIT 1 "; 
            /*Consulta que compara con el codigo convenio recibido de el view y limit la busqueda a 1 rgistro*/

        try {
            $stmt=$conexion->prepare($query);
            $stmt->bindParam(':codigo_convenio',$_POST['codigo_convenio'], PDO::PARAM_INT);
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
                $salida = $resultado;
            } else {
                $salida["error"] = "No se encontraron resultados";
            }

    
          
            
    
            //$resultadoDato = $conexion->query($query);
            
    
    
            /*while ($row = $resultadoDato->fetch(PDO::FETCH_ASSOC)) {
                $codigo_estudi = $row['codigo_estudiante'];
                $nombre_est = $row['nombre_estudiante'];
                $apellidos_est = $row['apellidos_estudiante'];
                $fecha_naci_est = $row['fecha_nacimiento_estudiante'];
                $carrera_est= $row['descripcion_servicio'];
                $imagen_est= $row['imagen'];
               
            }*/
        } catch (PDOException $e) {
            echo "error al ejecutar " . $e->getMessage();
        }
        echo json_encode($salida);
    }

