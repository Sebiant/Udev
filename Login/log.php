<?php include_once '../componentes/header.php'; ?>

<div class="container d-flex justify-content-center mt-5">
    <div class="card p-4 shadow-lg w-50 w-md-25">
        <h2 class="text-center text-dark">Iniciar Sesión</h2>
        
        <form id="loginForm" enctype="multipart/form-data" method="post" action="DB/DB_AUTH/login.php">
            <div class="mb-3">
                <label for="correo" class="form-label">Correo electrónico:</label>
                <input type="email" class="form-control" id="correo" name="correo" required>
            </div>

            <div class="mb-3">
                <label for="contraseña" class="form-label">Contraseña:</label>
                <input type="password" class="form-control" id="contraseña" name="contraseña" required>
            </div>

            <button type="submit" class="btn btn-primary w-100 mb-2" id="login_button" name="login_button">
                Iniciar Sesión
            </button>

            <button type="button" class="btn btn-outline-primary w-100" id="register_button" data-bs-toggle="modal" data-bs-target="#registroModal">
                Registro
            </button>
        </form>
    </div>
</div>

<!-- Modal de Registro -->
<div class="modal fade" id="registroModal" tabindex="-1" aria-labelledby="registroModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="registroModalLabel">Registro</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <!-- Formulario de Registro -->
            <form id="registerForm" enctype="multipart/form-data" method="post" action="DB/DB_AUTH/registro.php">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="registro-nombre" class="form-label">Nombre de usuario:</label>
                        <input type="text" class="form-control" id="registro-nombre" name="nombre_usuario" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="registro-email" class="form-label">Correo electrónico:</label>
                        <input type="email" class="form-control" id="registro-email" name="correo" required>
                    </div>

                    <div class="mb-3">
                        <label for="registro-password" class="form-label">Contraseña:</label>
                        <input type="password" class="form-control" id="registro-password" name="contraseña" required>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-success w-100" id="register_button_modal">Registrar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include_once '../componentes/footer.php'; ?>
