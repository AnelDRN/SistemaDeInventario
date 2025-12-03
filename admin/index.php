<?php
// Incluir el archivo de arranque que carga todo el sistema
require_once dirname(__DIR__) . '/includes/bootstrap.php';

use App\Models\User;
use App\Models\Part;
use App\Models\Sale;

// Proteger esta página: solo para usuarios autenticados
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit();
}

$pageTitle = 'Dashboard';

// Obtener estadísticas para el dashboard
$userCount = User::count();
$partsCount = Part::count();
$totalRevenue = Sale::totalRevenue();
$recentSales = Sale::findRecent(5);


// Incluir el encabezado del layout
require_once ROOT_PATH . '/views/admin/layouts/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1><?php echo htmlspecialchars($pageTitle); ?></h1>
</div>

<!-- Stat Cards -->
<div class="row">
    <div class="col-md-4 mb-4">
        <div class="card text-white bg-primary">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="card-title">Usuarios Registrados</div>
                        <div class="h2"><?php echo $userCount; ?></div>
                    </div>
                    <i class="bi bi-people-fill" style="font-size: 3rem;"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4 mb-4">
        <div class="card text-white bg-warning">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="card-title">Partes en Inventario</div>
                        <div class="h2"><?php echo $partsCount; ?></div>
                    </div>
                    <i class="bi bi-tools" style="font-size: 3rem;"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4 mb-4">
        <div class="card text-white bg-success">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="card-title">Ingresos Totales</div>
                        <div class="h2">$<?php echo number_format($totalRevenue, 2); ?></div>
                    </div>
                    <i class="bi bi-cash-stack" style="font-size: 3rem;"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Recent Sales -->
<div class="card">
    <div class="card-header">
        <i class="bi bi-clock-history"></i> Ventas Recientes
    </div>
    <div class="card-body">
        <table class="table table-striped table-hover">
            <thead>
                <tr>
                    <th>Fecha</th>
                    <th>Parte Vendida</th>
                    <th>Precio</th>
                    <th>Vendedor</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($recentSales)): ?>
                    <tr>
                        <td colspan="4" class="text-center">No hay ventas registradas.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($recentSales as $sale): ?>
                        <tr>
                            <td><?php echo date('d/m/Y H:i', strtotime($sale['fecha_venta'])); ?></td>
                            <td><?php echo htmlspecialchars($sale['nombre_parte']); ?></td>
                            <td>$<?php echo number_format($sale['precio_venta'], 2); ?></td>
                            <td><?php echo htmlspecialchars($sale['vendedor_nombre']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>


<?php
// Incluir el pie de página del layout
require_once ROOT_PATH . '/views/admin/layouts/footer.php';
