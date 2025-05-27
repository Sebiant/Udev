<?php

$servername = "mysql-udev.alwaysdata.net";
$db_username = "udev_app";
$db_password = "8BD9zlYixj1dey";
$dbname = "udev_db";

$conn = new mysqli($servername, $db_username, $db_password, $dbname);


if ($conn->connect_error) {
    die("Error de conexión a la base de datos: " . $conn->connect_error);
} else {
    // echo "Conexión exitosa a la base de datos";
}
?>


