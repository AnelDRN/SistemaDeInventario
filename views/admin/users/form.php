<div class="d-flex justify-content-between align-items-center mb-4">
    <h1><?php echo htmlspecialchars($pageTitle); ?></h1>
</div>


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

<div class="card">
    <div class="card-body">
        <form action="usuario_form.php<?php echo $isEditMode ? '?id=' . htmlspecialchars($user->getId()) : ''; ?>" method="POST">
            
            <?php if ($isEditMode): ?>
                <input type="hidden" name="id" value="<?php echo htmlspecialchars($user->getId()); ?>">
            <?php endif; ?>

            <div class="mb-3">
                <label for="nombre_usuario" class="form-label">Nombre de Usuario:</label>
                <input type="text" class="form-control" id="nombre_usuario" name="nombre_usuario" value="<?php echo htmlspecialchars($user->getNombreUsuario()); ?>" required>
            </div>

            <div class="mb-3">
                <label for="email" class="form-label">Email:</label>
                <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($user->getEmail()); ?>" required>
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">Contraseña:</label>
                <input type="password" class="form-control" id="password" name="password" <?php echo !$isEditMode ? 'required' : ''; ?>>
                <?php if ($isEditMode): ?>
                    <div class="form-text">Dejar en blanco para no cambiar la contraseña.</div>
                <?php endif; ?>
            </div>

            <div class="mb-3">
                <label for="rol_id" class="form-label">Rol:</label>
                <select class="form-select" id="rol_id" name="rol_id" required>
                    <!-- Idealmente, estos roles vendrían de la tabla `roles` de la BD -->
                    <option value="1" <?php echo $user->getRolId() === 1 ? 'selected' : ''; ?>>Administrador</option>
                    <option value="2" <?php echo $user->getRolId() === 2 ? 'selected' : ''; ?>>Vendedor</option>
                    <option value="3" <?php echo $user->getRolId() === 3 ? 'selected' : ''; ?>>Cliente</option>
                </select>
            </div>
            
            <div class="mb-3 form-check">
                <input type="checkbox" class="form-check-input" id="activo" name="activo" <?php echo $user->isActivo() ? 'checked' : ''; ?>>
                <label class="form-check-label" for="activo">Activo</label>
            </div>

            <div class="d-flex justify-content-end gap-2">
                <a href="usuarios.php" class="btn btn-secondary">Cancelar</a>
                <button type="submit" class="btn btn-primary">Guardar Cambios</button>
            </div>
        </form>
    </div>
</div>
