<div class="d-flex justify-content-between align-items-center mb-4">
    <h1><?php echo htmlspecialchars($pageTitle); ?></h1>
</div>

<?php if (!empty($errors)): ?>
    <div class="alert alert-danger">
        <?php foreach ($errors as $error): ?>
            <p class="mb-0"><?php echo htmlspecialchars($error); ?></p>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<!-- Formulario de Creación/Edición -->
<div class="card mb-4">
    <div class="card-header">
        <?php echo $roleToEdit->getId() ? 'Editar Rol' : 'Crear Nuevo Rol'; ?>
    </div>
    <div class="card-body">
        <form action="<?php echo BASE_URL; ?>/public/index.php?/admin/roles/save" method="POST">
            <input type="hidden" name="id" value="<?php echo htmlspecialchars($roleToEdit->getId() ?? ''); ?>">
            
            <div class="row">
                <div class="col-md-9">
                    <input type="text" class="form-control" name="nombre" placeholder="Nombre del rol" value="<?php echo htmlspecialchars($roleToEdit->getNombre() ?? ''); ?>" required>
                </div>
                <div class="col-md-3 d-grid">
                    <button type="submit" class="btn btn-primary"><?php echo $roleToEdit->getId() ? 'Actualizar' : 'Crear'; ?></button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Lista de Roles -->
<div class="card">
    <div class="card-header">
        <span>Lista de Roles</span>
    </div>
    <div class="card-body">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($roles as $role): ?>
                <tr>
                    <td><?php echo htmlspecialchars($role->getId()); ?></td>
                    <td><?php echo htmlspecialchars($role->getNombre()); ?></td>
                    <td>
                        <a href="<?php echo BASE_URL; ?>/public/index.php?/admin/roles/edit/<?php echo $role->getId(); ?>" class="btn btn-sm btn-warning">Editar</a>
                        <?php if ($role->getId() > 3): // No permitir borrar roles básicos ?>
                        <form action="<?php echo BASE_URL; ?>/public/index.php?/admin/roles/delete" method="POST" onsubmit="return confirm('¿Está seguro de que desea eliminar este rol?');" style="display: inline;">
                            <input type="hidden" name="id" value="<?php echo $role->getId(); ?>">
                            <button type="submit" class="btn btn-sm btn-danger">Borrar</button>
                        </form>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
