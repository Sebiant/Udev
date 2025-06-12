<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Udev</title>
  <link rel="stylesheet" href="../css/bootstrap.css">
  <style>
    .login-card {
      max-width: 400px;
      width: 100%;
    }
  </style>
</head>
<body>

<div class="container d-flex justify-content-center align-items-center min-vh-100">
  <div class="card p-4 shadow login-card">
    <h2 class="text-center text-dark mb-4">Iniciar Sesión</h2>

    <!-- Mensajes de error -->
    <?php if (isset($_GET['error'])): ?>
      <div class="alert alert-danger">
        <?php
          switch ($_GET['error']) {
              case 'empty_fields':
                  echo "Todos los campos son obligatorios.";
                  break;
              case 'db_connection':
                  echo "Error de conexión con la base de datos.";
                  break;
              case 'sql_error':
                  echo "Error en la consulta de la base de datos.";
                  break;
              case 'incorrect_password':
                  echo "Contraseña incorrecta.";
                  break;
              case 'email_not_found':
                  echo "Correo no encontrado.";
                  break;
              case 'session_expired':
                  echo "Tu sesión ha expirado. Por favor, vuelve a iniciar sesión.";
                  break;
              case 'no_session':
                  echo "Debes iniciar sesión para acceder a esta página.";
                  break;
              default:
                  echo "Error desconocido.";
                  break;
          }
        ?>
      </div>
    <?php endif; ?>

    <form method="post" action="Login-Controlador.php?accion=login">
      <div class="mb-3">
        <label for="correo" class="form-label">Correo electrónico:</label>
        <input type="email" class="form-control" id="correo" name="correo" required>
      </div>

      <div class="mb-3">
        <label for="clave" class="form-label">Contraseña:</label>
        <input type="password" class="form-control" id="clave" name="clave" required>
      </div>

      <button type="submit" class="btn btn-primary w-100">Iniciar Sesión</button>
    </form>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
