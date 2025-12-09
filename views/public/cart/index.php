<?php
// Set Page Title
$pageTitle = 'Carrito de Compras';
?>

<div class="container my-5">
    <h1 class="mb-4"><?php echo $pageTitle; ?></h1>

    <?php if (empty($cartItems)): ?>
        <div class="alert alert-info text-center">
            <p class="h4">Tu carrito de compras está vacío.</p>
            <a href="<?php echo BASE_URL; ?>/public/index.php" class="btn btn-primary mt-3">Volver al Catálogo</a>
        </div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-dark">
                    <tr>
                        <th scope="col" colspan="2">Producto</th>
                        <th scope="col" class="text-center">Cantidad</th>
                        <th scope="col" class="text-end">Precio Unitario</th>
                        <th scope="col" class="text-end">Subtotal</th>
                        <th scope="col" class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($cartItems as $item): ?>
                        <tr>
                            <td style="width: 100px;">
                                <img src="<?php echo BASE_URL; ?>/<?php echo htmlspecialchars($item['image'] ?? 'assets/img/placeholder_thumb.png'); ?>" class="img-fluid rounded" alt="<?php echo htmlspecialchars($item['name']); ?>">
                            </td>
                            <td>
                                <a href="<?php echo BASE_URL; ?>/public/index.php?/part/<?php echo $item['part_id']; ?>">
                                    <?php echo htmlspecialchars($item['name']); ?>
                                </a>
                            </td>
                            <td class="text-center" style="width: 150px;">
                                <!-- Update Quantity Form -->
                                <form action="<?php echo BASE_URL; ?>/public/index.php?/cart/update" method="POST" class="d-flex justify-content-center">
                                    <input type="hidden" name="part_id" value="<?php echo $item['part_id']; ?>">
                                    <input type="number" name="quantity" class="form-control form-control-sm" value="<?php echo $item['quantity']; ?>" min="1" max="<?php echo $item['stock']; ?>" style="width: 70px;">
                                    <button type="submit" class="btn btn-outline-secondary btn-sm ms-2" title="Actualizar Cantidad"><i class="bi bi-arrow-repeat"></i></button>
                                </form>
                            </td>
                            <td class="text-end">$<?php echo number_format($item['price'], 2); ?></td>
                            <td class="text-end">$<?php echo number_format($item['subtotal'], 2); ?></td>
                            <td class="text-center">
                                <!-- Remove Item Form -->
                                <form action="<?php echo BASE_URL; ?>/public/index.php?/cart/remove" method="POST" onsubmit="return confirm('¿Estás seguro de que quieres quitar este producto del carrito?');">
                                    <input type="hidden" name="part_id" value="<?php echo $item['part_id']; ?>">
                                    <button type="submit" class="btn btn-danger btn-sm" title="Quitar del Carrito"><i class="bi bi-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr class="table-group-divider">
                        <td colspan="4" class="text-end h4 fw-bold">Total:</td>
                        <td class="text-end h4 fw-bold">$<?php echo number_format($total, 2); ?></td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <div class="d-flex justify-content-between mt-4">
            <a href="<?php echo BASE_URL; ?>/public/index.php" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Seguir Comprando
            </a>
            <a href="<?php echo BASE_URL; ?>/public/index.php?/cart/checkout" class="btn btn-success btn-lg">
                Finalizar Compra <i class="bi bi-arrow-right"></i>
            </a>
        </div>

    <?php endif; ?>
</div>
