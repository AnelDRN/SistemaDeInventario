<?php
// This view will receive $invoice_html and $download_pdf_url from CartController@orderSummary
$pageTitle = $pageTitle ?? 'Resumen de Pedido';
?>

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white text-center py-3">
                    <h2 class="mb-0"><?php echo htmlspecialchars($pageTitle); ?></h2>
                </div>
                <div class="card-body p-4">
                    <div class="text-center mb-4">
                        <i class="bi bi-check-circle-fill text-success" style="font-size: 4rem;"></i>
                        <p class="lead mt-2">¡Tu compra se ha realizado con éxito!</p>
                        <p>A continuación, puedes ver el resumen de tu pedido.</p>
                    </div>

                    <div class="invoice-container mb-4" style="border: 1px solid #ddd; padding: 20px; border-radius: 5px;">
                        <?php echo $invoice_html; // HTML content directly echoed ?>
                    </div>

                    <div class="d-flex justify-content-center gap-3">
                        <a href="<?php echo BASE_URL; ?>/public/index.php" class="btn btn-outline-primary btn-lg">
                            <i class="bi bi-arrow-left"></i> Volver al Catálogo
                        </a>
                        <a href="<?php echo htmlspecialchars($download_pdf_url); ?>" class="btn btn-success btn-lg" target="_blank">
                            <i class="bi bi-file-earmark-pdf"></i> Descargar Factura (PDF)
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
