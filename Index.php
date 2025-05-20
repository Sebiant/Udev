<?php
session_start();

// Verificar si el usuario está autenticado
if (isset($_SESSION['id'])) {
    // Si el usuario está autenticado, verificar el rol
    if ($_SESSION['rol'] == "docente") {
        // Si es docente, redirigir al área de cuentas de docentes
        header("Location: Cuentas-Docentes/Cuentas-Docentes-Vista.php");
        exit();
    } else {
        // Si no es docente (probablemente admin o algún otro rol), redirigir al dashboard
        header("Location: Dashboard/Dashboard.php");
        exit();
    }
} else {
    // Si no está autenticado, redirigir al formulario de login
    header("Location: Login/Login-Vista.php");
    exit();
}
?>
