<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Udev</title>
    <link rel="stylesheet" href="../css/bootstrap.rtl.css">
    <link rel="stylesheet" href="../css/bootstrap.css">
</head>
<body>
    
<div class="container d-flex justify-content-center mt-5">
    <div class="card p-4 shadow-lg w-50 w-md-25">
        <h2 class="text-center text-dark">Iniciar Sesión</h2>
        
        <form id="loginForm" enctype="multipart/form-data" method="post" action="Login-Controlador.php">
            <div class="mb-3">
                <label for="correo" class="form-label">Correo electrónico:</label>
                <input type="email" class="form-control" id="correo" name="correo" required>
            </div>

            <div class="mb-3">
                <label for="contraseña" class="form-label">Contraseña:</label>
                <input type="password" class="form-control" id="clave" name="clave" required>
            </div>

            <button type="submit" class="btn btn-primary w-100 mb-2" id="login_button" name="login_button">
                Iniciar Sesión
            </button>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>