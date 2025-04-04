<?php

include("../Conexion.php");
/*Este archivo hace la consulta de la info de estudiante para el modal
info_estudiante Estado:FUNCIONAL activo*/

@$action = $_POST["operacion"];

main($action, $conn);

function main($action, $conn)
{
    switch ($action) {
        case 'registro_individual':
            Registro_individual($conn);
            break;
       
            
      
        default:
            
            break;


    }
}

function Registro_individual($conn){


    try {
    $query = "SELECT estudiantes.codigo_estudiante, estudiantes.nombre_estudiante, estudiantes.apellidos_estudiante, estudiantes.fecha_nacimiento_estudiante, estudiantes.imagen, programas.id_programa, programas.nombre
            FROM convenio 
            INNER JOIN estudiantes 
            ON convenio.codigo_estudiante = estudiantes.codigo_estudiante 
            INNER JOIN programas
            ON convenio.codigo_convenio = programas.id_programa 
            WHERE convenio.codigo_convenio = ?  "; 
            /*Consulta que compara con el codigo convenio recibido de el view y limit la busqueda a 1 rgistro*/


    $codigo_conveni=intval($_POST['codigo_convenio']);

    
    //echo $codigo_conveni;

       
            $stmt=$conn->prepare($query);
            $stmt->bind_param('i', $codigo_conveni);
            $stmt->execute();
            $resultado = $stmt->get_result();

            

            if ($resultado->num_rows > 0) {

                $fila=$resultado->fetch_assoc();


                $salida = $fila;

                //$salida = $resultado->fetch_assoc();
                
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

