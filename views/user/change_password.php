<div class="row">
    <div class="col-md-6 offset-md-3">
        <div class="card mt-5">
            <div class="card-header text-center">
                <h2>Cambiar Contraseña</h2>
            </div>
            <div class="card-body">
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

                <?php if ($successMessage): ?>
                    <div class="alert alert-success">
                        <?php echo htmlspecialchars($successMessage); ?>
                    </div>
                <?php endif; ?>

                <form action="<?php echo BASE_URL; ?>/public/index.php?/profile/change-password/process" method="POST">
                    <div class="mb-3">
                        <label for="current_password" class="form-label">Contraseña Actual:</label>
                        <input type="password" class="form-control" id="current_password" name="current_password" required>
                    </div>
                    <div class="mb-3">
                        <label for="new_password" class="form-label">Nueva Contraseña:</label>
                        <input type="password" class="form-control" id="new_password" name="new_password" required>
                    </div>
                    <div class="mb-3">
                        <label for="confirm_new_password" class="form-label">Confirmar Nueva Contraseña:</label>
                        <input type="password" class="form-control" id="confirm_new_password" name="confirm_new_password" required>
                    </div>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>