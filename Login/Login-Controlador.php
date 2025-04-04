<?php
session_start();
include '../Conexion.php';


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validar que los campos no estén vacíos
    if (empty($_POST["correo"]) || empty($_POST["clave"])) {
        die("Error: Todos los campos son obligatorios.");
    }

    $correo = trim($_POST["correo"]);
    $clave = trim($_POST["clave"]);

    // Verificar conexión con la base de datos
    if (!$conn) {
        die("Error de conexión: " . $conn->connect_error);
    }

    // Preparar la consulta para evitar inyección SQL
    $sql = "SELECT * FROM usuarios WHERE correo = ?";
    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        die("Error en la consulta SQL: " . $conn->error);
    }

    $stmt->bind_param("s", $correo);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows > 0) {
        $usuario = $resultado->fetch_assoc();

        // Depuración: Imprimir credenciales (eliminar en producción)
        echo "Clave ingresada: " . htmlspecialchars($clave) . "<br>";
        echo "Hash almacenado: " . htmlspecialchars($usuario["clave"]) . "<br>";

        // Verificar la contraseña
        if (password_verify($clave, $usuario["clave"])) {
            $_SESSION["id"] = $usuario["id"];  // Definición de la sesión
            $_SESSION["correo"] = $usuario["correo"];
            header("Location: ../Dashboard/Dashboard.php");
            exit();        
        } else {
            die("Contraseña incorrecta.");
        }
    } else {
        die("Correo no encontrado.");
    }

    // Cerrar conexión
    $stmt->close();
    $conn->close();

}
?>
