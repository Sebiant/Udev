<?php
session_start();

if (!isset($_SESSION['id'])) {
  header("Location: ../Login/Login-Vista.php?error=no_session");
  exit();
}

$rol = $_SESSION['rol'] ?? null;
$nombre = $_SESSION['nombre'];

if ($rol === 'admin' && basename($_SERVER['PHP_SELF']) === 'Cuentas-Docentes-Vista.php') {
  header("Location: ../Index.php");
  exit();
}

if ($rol === 'docente' && basename($_SERVER['PHP_SELF']) !== 'Cuentas-Docentes-Vista.php') {
  header("Location: ../Index.php");
  exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Udev</title>
  <link rel="stylesheet" href="../css/bootstrap.rtl.css">
  <link rel="stylesheet" href="../css/bootstrap.css">
  <style>
    .error { color: red; font-size: 0.9em; }
    #contador { font-size: 0.9em; margin-top: 5px; }
    .alerta { color: orange; font-weight: bold; }
  </style>
</head>
<body>
<nav class="navbar navbar-expand-lg bg-body-tertiary">
  <div class="container-fluid">
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarTogglerDemo01">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse justify-content-between" id="navbarTogglerDemo01">
      <a class="navbar-brand" href="../index.php">Udev</a>

      <!-- CENTRADO del menú -->
      <ul class="navbar-nav mx-auto mb-2 mb-lg-0">
        <?php if ($rol === 'admin'): ?>
          <li class="nav-item"><a class="nav-link" href="../Cuentas-De-Cobro/Cuentas-De-Cobro-Vista.php">Cuentas de Cobro</a></li>
          <li class="nav-item"><a class="nav-link" href="../Programador/Programador-Vista.php">Programador</a></li>
          <li class="nav-item"><a class="nav-link" href="../Asistencias/Asistencias-Vista.php">Asistencias</a></li>
          <li class="nav-item"><a class="nav-link" href="../Docentes/Docentes-Vista.php">Docentes</a></li>
          <li class="nav-item"><a class="nav-link" href="../Salones/Salones-Vista.php">Salones</a></li>
          <li class="nav-item"><a class="nav-link" href="../Instituciones/Instituciones-Vista.php">Instituciones</a></li>
          <li class="nav-item"><a class="nav-link" href="../Periodos/Periodos-Vista.php">Periodos</a></li>
          <li class="nav-item"><a class="nav-link" href="../Programas/Programas-Vista.php">Programas</a></li>
          <li class="nav-item"><a class="nav-link" href="../Modulos/Modulos-Vista.php">Modulos</a></li>
        <?php elseif ($rol === 'docente'): ?>
          <?php
          echo '<span class="navbar-text">¡Bienvenido, <strong>' . htmlspecialchars($nombre) . '</strong>!</span>';
          ?>
        <?php endif; ?>
      </ul>
      
      <div class="d-flex">
        <a class="nav-link text-danger fw-bold" href="../Login/Login-Controlador.php?accion=logout">
          <i class="bi bi-box-arrow-right"></i> Cerrar sesión
        </a>
      </div>
    </div>
  </div>
</nav>

</body>
</html>
