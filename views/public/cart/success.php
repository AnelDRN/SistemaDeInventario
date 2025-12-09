<?php
$pageTitle = '¡Compra Exitosa!';
$total = 0;
?>

<div class="container my-5 text-center">
    <div class="py-5">
        <i class="bi bi-check-circle-fill text-success" style="font-size: 5rem;"></i>
        <h1 class="display-5 mt-3"><?php echo $pageTitle; ?></h1>
        <p class="lead">Gracias por tu compra. Tu pedido ha sido procesado con éxito.</p>
    </div>

    <?php if (!empty($order)): ?>
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <h3 class="mb-4">Resumen de tu Pedido</h3>
                <div class="card">
                    <div class="card-body">
                        <ul class="list-group list-group-flush">
                            <?php foreach ($order as $item): ?>
                                <?php
                                $subtotal = $item['price'] * $item['quantity'];
                                $total += $subtotal;
                                ?>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <div>
                                        <img src="<?php echo BASE_URL; ?>/<?php echo htmlspecialchars($item['image'] ?? 'assets/img/placeholder_thumb.png'); ?>" class="img-fluid rounded me-3" style="width: 50px;" alt="<?php echo htmlspecialchars($item['name']); ?>">
                                        <span class="fw-bold"><?php echo htmlspecialchars($item['name']); ?></span>
                                        <span class="text-muted">(x<?php echo htmlspecialchars($item['quantity']); ?>)</span>
                                    </div>
                                    <span class="badge bg-secondary rounded-pill">$<?php echo number_format($subtotal, 2); ?></span>
                                </li>
                            <?php endforeach; ?>
                            <li class="list-group-item d-flex justify-content-between align-items-center fw-bold fs-5">
                                <span>Total Pagado:</span>
                                <span>$<?php echo number_format($total, 2); ?></span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <div class="mt-5">
        <a href="<?php echo BASE_URL; ?>/public/index.php" class="btn btn-primary btn-lg">
            <i class="bi bi-arrow-left"></i> Volver al Catálogo
        </a>
    </div>
</div>
