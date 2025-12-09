<div class="row">
    <div class="col-md-6 offset-md-3">
        <div class="register-container" style="margin-top: 20px;">
            <h2 class="text-center mb-4">Crear Nueva Cuenta</h2>
            
            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger">
                    <strong>Por favor, corrija los siguientes errores:</strong>
                    <ul>
                        <?php foreach ($errors as $error): ?>
                            <li><?php echo htmlspecialchars($error); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <form action="<?php echo BASE_URL; ?>/public/index.php?/register/process" method="POST">
                <div class="mb-3">
                    <label for="nombre_usuario" class="form-label">Nombre de Usuario:</label>
                    <input type="text" class="form-control" id="nombre_usuario" name="nombre_usuario" value="<?php echo htmlspecialchars($nombre_usuario ?? ''); ?>" required>
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label">Email:</label>
                    <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($email ?? ''); ?>" required>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Contraseña:</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                </div>
                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-success">Registrarse</button>
                </div>
            </form>
            <div class="text-center mt-3">
                <p>¿Ya tienes una cuenta? <a href="<?php echo BASE_URL; ?>/public/index.php?/login">Inicia sesión aquí</a></p>
            </div>
        </div>
    </div>
</div>
