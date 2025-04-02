<?php include("../Conexion.php");


//obtencion de la tabla movimientos para mostrar sin DATATABLES por medio de listas y variables

if($conn){
    try{
    $consulta = "SELECT movimientos.id_movimiento, movimientos.fecha_movimiento, movimientos.descripcion, movimientos.valor_movimiento, convenio.codigo_estudiante
    FROM movimientos
    LEFT JOIN convenio
    ON movimientos.codigo_estudiante = convenio.codigo_estudiante
    WHERE movimientos.codigo_estudiante = ? ";
    $stmt = $conn->prepare($consulta);

    $id_recibido=intval($_POST['codigo_convenios']);
    //echo $id_recibido;

    
    $stmt->bind_param(
        'i', $id_recibido
    );
    if(!$stmt){
        throw new Exception("Error al preparar la consulta: " . $conn->error);
        
    }   
        if($stmt->execute()){

            $resultados=$stmt->get_result();
        
            if($resultados){
                while($row = $resultados->fetch_assoc())   {
                    $codigo = $row['id_movimiento'];
                    $fecha = $row['fecha_movimiento'];
                    $descripcion = $row['descripcion'];
                    $cuotas = $row['valor_movimiento'];

                    echo "<tr>";
                    echo "<td>$codigo</td>";
                    echo "<td>$fecha</td>";
                    echo "<td>$descripcion</td>";
                    echo "<td>$cuotas</td>";
                    echo '<td class="text-center"><input type="checkbox" class="form-check-input"></td>';
                    echo "</tr>";

                }
            } else {
                echo "<tr><td colspan='5'>No se encontraron resultados</td></tr>";
            }
        }else {
            throw new Exception("Error al ejecutar la consulta: " . $stmt->error);
        }
    }catch(Exception $e){
        echo "<tr><td colspan='5'>Error: " . $e->getMessage() . "</td></tr>";
    }

}else{
    echo "<tr><td colspan='5'>Error de conexión a la base de datos</td></tr>";
}

  



