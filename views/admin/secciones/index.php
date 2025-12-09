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
        <?php echo $sectionToEdit->getId() ? 'Editar Sección' : 'Crear Nueva Sección'; ?>
    </div>
    <div class="card-body">
        <form action="<?php echo BASE_URL; ?>/public/index.php?/admin/secciones/save" method="POST">
            <input type="hidden" name="id" value="<?php echo htmlspecialchars($sectionToEdit->getId() ?? ''); ?>">
            
            <div class="mb-3">
                <label for="nombre" class="form-label">Nombre de la Sección</label>
                <input type="text" class="form-control" id="nombre" name="nombre" value="<?php echo htmlspecialchars($sectionToEdit->getNombre() ?? ''); ?>" required>
            </div>
            <div class="mb-3">
                <label for="descripcion" class="form-label">Descripción (Opcional)</label>
                <textarea class="form-control" id="descripcion" name="descripcion" rows="3"><?php echo htmlspecialchars($sectionToEdit->getDescripcion() ?? ''); ?></textarea>
            </div>
            <div class="d-flex justify-content-end">
                <button type="submit" class="btn btn-primary"><?php echo $sectionToEdit->getId() ? 'Actualizar' : 'Crear'; ?></button>
            </div>
        </form>
    </div>
</div>

<!-- Lista de Secciones -->
<div class="card">
    <div class="card-header">
        <span>Lista de Secciones</span>
    </div>
    <div class="card-body">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Descripción</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($sections as $section): ?>
                <tr>
                    <td><?php echo htmlspecialchars($section->getId()); ?></td>
                    <td><?php echo htmlspecialchars($section->getNombre()); ?></td>
                    <td><?php echo htmlspecialchars($section->getDescripcion() ?? 'N/A'); ?></td>
                    <td>
                        <a href="<?php echo BASE_URL; ?>/public/index.php?/admin/secciones/edit/<?php echo $section->getId(); ?>" class="btn btn-sm btn-warning">Editar</a>
                        <form action="<?php echo BASE_URL; ?>/public/index.php?/admin/secciones/delete" method="POST" onsubmit="return confirm('¿Está seguro de que desea eliminar esta sección?');" style="display: inline;">
                            <input type="hidden" name="id" value="<?php echo $section->getId(); ?>">
                            <button type="submit" class="btn btn-sm btn-danger">Borrar</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>