<div class="p-5 mb-4 bg-light rounded-3">
    <div class="container-fluid py-5">
        <h1 class="display-5 fw-bold">Catálogo de Partes de Auto</h1>
        <p class="col-md-8 fs-4">Encuentre la parte que necesita para su vehículo. Explore nuestro inventario a continuación.</p>
    </div>
</div>

<div class="mb-4 d-flex justify-content-between align-items-center">
    <!-- Search Form -->
    <form action="<?php echo BASE_URL; ?>/public/index.php" method="GET" class="d-flex me-3 flex-grow-1">
        <input type="text" name="search" class="form-control me-2" placeholder="Buscar por nombre, tipo, marca..." value="<?php echo htmlspecialchars($searchTerm ?? ''); ?>">
        <?php if (!empty($selectedPartType)): ?>
            <input type="hidden" name="part_type" value="<?php echo htmlspecialchars($selectedPartType); ?>">
        <?php endif; ?>
        <button type="submit" class="btn btn-info">Buscar</button>
        <?php if(!empty($searchTerm) || !empty($selectedPartType)): ?>
             <a href="<?php echo BASE_URL; ?>/public/index.php" class="btn btn-outline-secondary ms-2">Limpiar</a>
        <?php endif; ?>
    </form>

    <!-- Part Type Filter -->
    <form action="<?php echo BASE_URL; ?>/public/index.php" method="GET" class="d-flex">
        <?php if(!empty($searchTerm)): ?>
            <input type="hidden" name="search" value="<?php echo htmlspecialchars($searchTerm); ?>">
        <?php endif; ?>
        <select name="part_type" class="form-select me-2" onchange="this.form.submit()">
            <option value="">Todos los Tipos</option>
            <?php foreach ($partTypes as $type): ?>
                <option value="<?php echo htmlspecialchars($type); ?>" <?php echo ($selectedPartType == $type) ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($type); ?>
                </option>
            <?php endforeach; ?>
        </select>
    </form>
</div>

<div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-4">
    <?php if (empty($parts)): ?>
        <div class="col">
            <p class="text-center">No hay partes disponibles en este momento.</p>
        </div>
    <?php else: ?>
        <?php foreach ($parts as $part): ?>
            <div class="col">
                <div class="card h-100">
                    <img src="<?php echo BASE_URL; ?>/<?php echo htmlspecialchars($part->getThumbnailUrl() ?? 'assets/img/placeholder.png'); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($part->getNombre()); ?>" style="height: 200px; object-fit: cover;">
                    <div class="card-body">
                        <h5 class="card-title"><?php echo htmlspecialchars($part->getNombre()); ?></h5>
                        <p class="card-text text-muted"><?php echo htmlspecialchars($part->getMarcaAuto() . ' ' . $part->getModeloAuto()); ?></p>
                        <h6 class="card-subtitle mb-2 text-success">$<?php echo number_format($part->getPrecio(), 2); ?></h6>
                    </div>
                    <div class="card-footer text-center">
                        <a href="<?php echo BASE_URL; ?>/public/index.php?/part/<?php echo $part->getId(); ?>" class="btn btn-primary">Ver Detalles</a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>