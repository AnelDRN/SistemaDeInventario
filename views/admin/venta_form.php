<div class="d-flex justify-content-between align-items-center mb-4">
    <h1><?php echo htmlspecialchars($pageTitle); ?></h1>
    <a href="inventario.php" class="btn btn-secondary">Cancelar</a>
</div>

<?php if (!empty($errors)): ?>
    <div class="alert alert-danger">
        <?php foreach ($errors as $error): ?>
            <p class="mb-0"><?php echo htmlspecialchars($error); ?></p>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<div class="card">
    <div class="card-header">
        Confirmar Venta de la Parte
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-4">
                <img src="../<?php echo htmlspecialchars($part->getThumbnailUrl() ?? 'assets/img/placeholder.png'); ?>" class="img-fluid rounded" alt="Thumbnail">
            </div>
            <div class="col-md-8">
                <h4 class="card-title"><?php echo htmlspecialchars($part->getNombre()); ?></h4>
                <p><strong>Marca/Modelo:</strong> <?php echo htmlspecialchars($part->getMarcaAuto() . ' ' . $part->getModeloAuto()); ?></p>
                <p><strong>Precio Sugerido:</strong> $<?php echo number_format($part->getPrecio(), 2); ?></p>
                <p><strong>Cantidad Disponible:</strong> <?php echo htmlspecialchars($part->getCantidadDisponible()); ?></p>
            </div>
        </div>

        <hr>

        <form action="venta_form.php?part_id=<?php echo $part->getId(); ?>" method="POST">
            <div class="mb-3">
                <label for="precio_venta" class="form-label"><strong>Precio de Venta Final</strong></label>
                <div class="input-group">
                    <span class="input-group-text">$</span>
                    <input type="number" class="form-control" id="precio_venta" name="precio_venta" value="<?php echo htmlspecialchars($part->getPrecio()); ?>" step="0.01" required>
                </div>
            </div>
            <div class="d-grid">
                <button type="submit" class="btn btn-success btn-lg">Confirmar y Registrar Venta</button>
            </div>
        </form>
    </div>
</div>
