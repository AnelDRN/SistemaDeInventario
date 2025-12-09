<div class="d-flex justify-content-between align-items-center mb-4">
    <h1><?php echo htmlspecialchars($pageTitle ?? 'Gestión'); ?></h1>
</div>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span>Inventario de Partes</span>
        <div>
            <?php
                $exportQueryParams = [];
                if (!empty($searchTerm)) {
                    $exportQueryParams['search'] = $searchTerm;
                }
                if (!empty($selectedSectionId)) {
                    $exportQueryParams['section_id'] = $selectedSectionId;
                }
                $exportQueryString = http_build_query($exportQueryParams);
                $exportUrl = BASE_URL . '/public/index.php?/admin/inventario/export-csv';
                if (!empty($exportQueryString)) {
                    $exportUrl .= '?' . $exportQueryString; // Corrected to use '?'
                }
            ?>
            <a href="<?php echo $exportUrl; ?>" class="btn btn-success me-2">Exportar a CSV</a>
            <a href="<?php echo BASE_URL; ?>/public/index.php?/admin/inventario/create" class="btn btn-primary">Añadir Nueva Parte</a>
        </div>
    </div>
    <div class="card-body">
        <div class="mb-3 d-flex justify-content-between align-items-center">
            <!-- Search Form -->
            <form onsubmit="event.preventDefault(); applyFilters(); return false;" class="d-flex me-3 flex-grow-1">
                <input type="text" id="search_input" name="search" class="form-control me-2" placeholder="Buscar por nombre, tipo, marca..." value="<?php echo htmlspecialchars($searchTerm); ?>">
                <button type="submit" class="btn btn-info">Buscar</button>
                <?php if(!empty($searchTerm) || !empty($selectedSectionId)): ?>
                     <a href="<?php echo BASE_URL; ?>/public/index.php?/admin/inventario" class="btn btn-outline-secondary ms-2">Limpiar</a>
                <?php endif; ?>
            </form>

            <!-- Section Filter -->
            <div class="d-flex">
                <select name="section_id" id="section_filter" class="form-select me-2" onchange="applyFilters()">
                    <option value="">Todas las Secciones</option>
                    <?php foreach ($sections as $section): ?>
                        <option value="<?php echo $section->getId(); ?>" <?php echo ($selectedSectionId == $section->getId()) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($section->getNombre()); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
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

<script>
function applyFilters() {
    const searchTerm = document.getElementById('search_input').value;
    const sectionId = document.getElementById('section_filter').value;
    
    let url = "<?php echo BASE_URL; ?>/public/index.php?/admin/inventario";
    const queryParts = [];

    if (sectionId) {
        queryParts.push('section_id=' + encodeURIComponent(sectionId));
    }
    if (searchTerm) {
        queryParts.push('search=' + encodeURIComponent(searchTerm));
    }

    if (queryParts.length > 0) {
        url += '?' + queryParts.join('&');
    }

    window.location.href = url;
}
</script>
