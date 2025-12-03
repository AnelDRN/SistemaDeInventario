<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($pageTitle ?? 'Rastro'); ?> - Sistema de Inventario</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="index.php">Rastro de Partes</a>
            <div class="collapse navbar-collapse">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="login.php">Admin Login</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-5">
        <div class="p-5 mb-4 bg-light rounded-3">
            <div class="container-fluid py-5">
                <h1 class="display-5 fw-bold">Catálogo de Partes de Auto</h1>
                <p class="col-md-8 fs-4">Encuentre la parte que necesita para su vehículo. Explore nuestro inventario a continuación.</p>
            </div>
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
                            <img src="<?php echo htmlspecialchars($part->getThumbnailUrl() ?? 'assets/img/placeholder.png'); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($part->getNombre()); ?>" style="height: 200px; object-fit: cover;">
                            <div class="card-body">
                                <h5 class="card-title"><?php echo htmlspecialchars($part->getNombre()); ?></h5>
                                <p class="card-text text-muted"><?php echo htmlspecialchars($part->getMarcaAuto() . ' ' . $part->getModeloAuto()); ?></p>
                                <h6 class="card-subtitle mb-2 text-success">$<?php echo number_format($part->getPrecio(), 2); ?></h6>
                            </div>
                            <div class="card-footer text-center">
                                <a href="detalle.php?id=<?php echo $part->getId(); ?>" class="btn btn-primary">Ver Detalles</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <footer class="py-4 mt-5 bg-light">
        <div class="container text-center">
            <small>Sistema de Inventario &copy; 2025</small>
        </div>
    </footer>

</body>
</html>
