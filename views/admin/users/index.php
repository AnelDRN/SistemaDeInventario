<div class="d-flex justify-content-between align-items-center mb-4">
    <h1><?php echo htmlspecialchars($pageTitle ?? 'Gestión'); ?></h1>
</div>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span>Lista de Usuarios</span>
        <a href="index.php?/admin/users/create" class="btn btn-primary">Crear Nuevo Usuario</a>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead class="table-dark">
                    <tr>
                        <th scope="col">ID</th>
                        <th scope="col">Usuario</th>
                        <th scope="col">Email</th>
                        <th scope="col">Rol</th>
                        <th scope="col">Estado</th>
                        <th scope="col">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($users)): ?>
                        <tr>
                            <td colspan="6" class="text-center">No hay usuarios para mostrar.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($users as $user): ?>
                            <tr>
                                <th scope="row"><?php echo htmlspecialchars($user->getId()); ?></th>
                                <td><?php echo htmlspecialchars($user->getNombreUsuario()); ?></td>
                                <td><?php echo htmlspecialchars($user->getEmail()); ?></td>
                                <td><?php echo htmlspecialchars($roleMap[$user->getRolId()] ?? 'N/A'); ?></td>
                                <td>
                                    <span class="<?php echo $user->isActivo() ? 'text-success' : 'text-danger'; ?> fw-bold">
                                        <?php echo $user->isActivo() ? 'Activo' : 'Inactivo'; ?>
                                    </span>
                                </td>
                                <td>
                                    <a href="index.php?/admin/users/edit/<?php echo $user->getId(); ?>" class="btn btn-sm btn-warning">Editar</a>
                                    <?php if ($user->isActivo()): ?>
                                        <a href="index.php?/admin/users/deactivate/<?php echo $user->getId(); ?>" class="btn btn-sm btn-danger">Desactivar</a>
                                    <?php else: ?>
                                        <a href="index.php?/admin/users/activate/<?php echo $user->getId(); ?>" class="btn btn-sm btn-success">Activar</a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
