<div class="d-flex justify-content-between align-items-center mb-4">
    <h1><?php echo htmlspecialchars($pageTitle ?? 'Gestión'); ?></h1>
</div>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span>Inventario de Partes</span>
        <a href="<?php echo BASE_URL; ?>/public/index.php?/admin/inventario/create" class="btn btn-primary">Añadir Nueva Parte</a>
    </div>
    <div class="card-body">
        <div class="mb-3">
            <form action="<?php echo BASE_URL; ?>/public/index.php?/admin/inventario" method="GET" class="d-flex">
                <input type="text" name="search" class="form-control me-2" placeholder="Buscar por nombre, tipo, marca..." value="<?php echo htmlspecialchars($searchTerm); ?>">
                <button type="submit" class="btn btn-info">Buscar</button>
                <?php if(!empty($searchTerm)): ?>
                     <a href="<?php echo BASE_URL; ?>/public/index.php?/admin/inventario" class="btn btn-outline-secondary ms-2">Limpiar</a>
                <?php endif; ?>
            </form>
        </div>
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead class="table-dark">
                    <tr>
                        <th scope="col">Imagen</th>
                        <th scope="col">Nombre</th>
                        <th scope="col">Tipo</th>
                        <th scope="col">Auto</th>
                        <th scope="col">Año</th>
                        <th scope="col">Precio</th>
                        <th scope="col">Cantidad</th>
                        <th scope="col">Sección</th>
                        <th scope="col">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($parts)): ?>
                        <tr>
                            <td colspan="9" class="text-center">No hay partes que coincidan con la búsqueda o el inventario está vacío.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($parts as $part): ?>
                            <tr>
                                <td>
                                    <img src="<?php echo BASE_URL; ?>/<?php echo htmlspecialchars($part->getThumbnailUrl() ?? 'assets/img/placeholder.png'); ?>" alt="Thumbnail" style="width: 60px; height: 60px; object-fit: cover;">
                                </td>
                                <td><?php echo htmlspecialchars($part->getNombre()); ?></td>
                                <td><?php echo htmlspecialchars($part->getTipoParte() ?? 'N/A'); ?></td>
                                <td><?php echo htmlspecialchars($part->getMarcaAuto() . ' ' . $part->getModeloAuto()); ?></td>
                                <td><?php echo htmlspecialchars($part->getAnioAuto() ?? 'N/A'); ?></td>
                                <td>$<?php echo number_format($part->getPrecio(), 2); ?></td>
                                <td><?php echo htmlspecialchars($part->getCantidadDisponible()); ?></td>
                                <td><?php echo htmlspecialchars($sectionMap[$part->getSeccionId()] ?? 'N/A'); ?></td>
                                <td>
                                    <a href="<?php echo BASE_URL; ?>/public/index.php?/admin/venta/<?php echo $part->getId(); ?>" class="btn btn-sm btn-success <?php echo $part->getCantidadDisponible() > 0 ? '' : 'disabled'; ?>">Vender</a>
                                    <a href="<?php echo BASE_URL; ?>/public/index.php?/admin/inventario/edit/<?php echo $part->getId(); ?>" class="btn btn-sm btn-warning">Editar</a>
                                    <form action="<?php echo BASE_URL; ?>/public/index.php?/admin/inventario/delete" method="POST" onsubmit="return confirm('¿Está seguro de que desea eliminar esta parte?');" style="display: inline;">
                                        <input type="hidden" name="id" value="<?php echo $part->getId(); ?>">
                                        <button type="submit" class="btn btn-sm btn-danger">Borrar</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
