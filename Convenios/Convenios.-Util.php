<?php include("../conexion.php");


//obtencion de la tabla movimientos para mostrar sin DATATABLES por medio de listas y variables

if($conexion){
    try{
    $consulta = "SELECT movimientos.codigo_movimiento, movimientos.fecha_movimiento, movimientos.descripcion_movimiento, movimientos.valor_movimiento, convenio.codigo_estudiante
    FROM movimientos
    LEFT JOIN convenio
    ON movimientos.codigo_fk_estudiante = convenio.codigo_estudiante
    WHERE movimientos.codigo_fk_estudiante = 10 ";
    $resultado = $conexion->query($consulta);
    

        while($row = $resultado->fetch(PDO::FETCH_ASSOC))   {
            $codigo = $row['codigo_movimiento'];
            $fecha = $row['fecha_movimiento'];
            $descripcion = $row['descripcion_movimiento'];
            $cuotas = $row['valor_movimiento'];

            echo "<tr>";
            echo "<td>$codigo</td>";
            echo "<td>$fecha</td>";
            echo "<td>$descripcion</td>";
            echo "<td>$cuotas</td>";
            echo '<td class="text-center"><input type="checkbox" class="form-check-input"></td>';
            echo "</tr>";

        }
    }catch(PDOException $e){
        echo "error al ejecutar " . $e->getMessage();
    }

   
}


