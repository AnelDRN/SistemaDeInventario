<div class="d-flex justify-content-between align-items-center mb-4">
    <h1><?php echo htmlspecialchars($pageTitle); ?></h1>
    <a href="<?php echo BASE_URL; ?>/public/index.php?/admin/reports/exportPartInventoryExcel" class="btn btn-success">
        <i class="bi bi-file-earmark-excel"></i> Exportar Inventario a Excel
    </a>
</div>

<div class="card mb-4">
    <div class="card-header">
        Seleccionar Periodo del Reporte
    </div>
    <div class="card-body">
        <form action="<?php echo BASE_URL; ?>/public/index.php?/admin/reports/monthly" method="POST" class="row g-3 align-items-end">
            <div class="col-md-5">
                <label for="month" class="form-label">Mes</label>
                <select id="month" name="month" class="form-select">
                    <?php for ($i = 1; $i <= 12; $i++): ?>
                        <option value="<?php echo $i; ?>" <?php echo ($i == $month) ? 'selected' : ''; ?>>
                            <?php echo date('F', mktime(0, 0, 0, $i, 10)); ?>
                        </option>
                    <?php endfor; ?>
                </select>
            </div>
            <div class="col-md-5">
                <label for="year" class="form-label">Año</label>
                <input type="number" id="year" name="year" class="form-control" value="<?php echo htmlspecialchars($year); ?>" min="2020" max="2030">
            </div>
            <div class="col-md-2 d-grid">
                <button type="submit" class="btn btn-primary">Generar Reporte</button>
            </div>
        </form>
    </div>
</div>

<?php if ($sales !== null): ?>
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span>Mostrando Reporte para: <strong><?php echo htmlspecialchars($reportDate); ?></strong></span>
        <button class="btn btn-secondary" onclick="window.print();"><i class="bi bi-printer"></i> Imprimir</button>
    </div>
    <div class="card-body">
        <?php if (empty($sales)): ?>
            <div class="alert alert-info">No se encontraron ventas para el periodo seleccionado.</div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>Fecha</th>
                            <th>ID Venta</th>
                            <th>Parte Vendida</th>
                            <th>Vendedor</th>
                            <th class="text-end">Precio Venta</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($sales as $sale): ?>
                            <tr>
                                <td><?php echo date('d/m/Y H:i', strtotime($sale['fecha_venta'])); ?></td>
                                <td><?php echo htmlspecialchars($sale['id']); ?></td>
                                <td><?php echo htmlspecialchars($sale['nombre_parte']); ?></td>
                                <td><?php echo htmlspecialchars($sale['vendedor_nombre']); ?></td>
                                <td class="text-end">$<?php echo number_format($sale['precio_venta'], 2); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr class="table-group-divider">
                            <td colspan="4" class="text-end fw-bold">Total de Ingresos:</td>
                            <td class="text-end fw-bold fs-5">$<?php echo number_format($totalRevenue, 2); ?></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Total por Categoría -->
<?php if (!empty($revenueByCategory)): ?>
<div class="card mt-4">
    <div class="card-header">
        <i class="bi bi-tags"></i> Total de Ingresos por Categoría
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>Categoría</th>
                        <th class="text-end">Ingresos Totales</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($revenueByCategory as $item): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($item['category_name']); ?></td>
                            <td class="text-end">$<?php echo number_format($item['total_revenue'], 2); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- Partes más Vendidas -->
<?php if (!empty($mostSoldParts)): ?>
<div class="card mt-4">
    <div class="card-header">
        <i class="bi bi-graph-up"></i> Partes Más Vendidas
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>Parte</th>
                        <th class="text-end">Cantidad Vendida</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($mostSoldParts as $item): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($item['nombre_parte']); ?></td>
                            <td class="text-end"><?php echo htmlspecialchars($item['quantity_sold']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php endif; ?>

<style>
    @media print {
        body * {
            visibility: hidden;
        }
        .card, .card * {
            visibility: visible;
        }
        .card {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
        }
        .btn {
            display: none;
        }
    }
</style>
<?php endif; ?>
