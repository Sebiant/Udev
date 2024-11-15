<?php
$servername = "mysql-hugosdd.alwaysdata.net";
$db_username = "hugosdd_udev2";
$db_password = "udev2024*&";
$dbname = "hugosdd_pruebas";

$conn = new mysqli($servername, $db_username, $db_password, $dbname);


if ($conn->connect_error) {
    die("Error de conexión a la base de datos: " . $conn->connect_error);
} else {
    // echo "Conexión exitosa a la base de datos";
}
?>


