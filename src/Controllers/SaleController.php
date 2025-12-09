<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\BaseController;
use App\Models\Part;
use App\Models\Sale;
use App\Config\Database;
use App\Helpers\FlashMessage;

class SaleController extends BaseController
{
    /**
     * Muestra el formulario para registrar la venta de una parte.
     */
    public function showForm(): void
    {
        $this->authorizeAdmin();

        $partId = (int)($this->params['id'] ?? 0);
        $part = Part::findById($partId);

        // Si la parte no existe o no hay stock, redirigir al inventario
        if (!$part || $part->getCantidadDisponible() <= 0) {
            FlashMessage::setMessage('La parte no está disponible para la venta.', 'warning');
            $this->redirect('public/index.php?/admin/inventario');
            return;
        }

        $pageTitle = 'Registrar Venta';
        $this->view('admin/venta_form', [
            'pageTitle' => $pageTitle,
            'part' => $part,
            'errors' => []
        ]);
    }

    /**
     * Procesa la venta de una parte.
     */
    public function process(): void
    {
        $this->authorizeAdmin();
        error_log("DEBUG: SaleController::process - Method called.");

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            error_log("DEBUG: SaleController::process - Invalid request method. Redirecting.");
            $this->redirect('public/index.php?/admin/inventario');
            return;
        }

        $partId = (int)($_POST['part_id'] ?? 0);
        $part = Part::findById($partId);
        error_log("DEBUG: SaleController::process - Part ID: " . $partId . ", Part found: " . ($part ? 'true' : 'false'));

        if (!$part || $part->getCantidadDisponible() <= 0) {
            error_log("DEBUG: SaleController::process - Part not found or out of stock. Redirecting.");
            FlashMessage::setMessage('La parte ya no está disponible para la venta.', 'danger');
            $this->redirect('public/index.php?/admin/inventario');
            return;
        }
        
        $precioVenta = isset($_POST['precio_venta']) ? (float)$_POST['precio_venta'] : 0;
        $errors = [];
        error_log("DEBUG: SaleController::process - Precio Venta: " . $precioVenta);

        if ($precioVenta <= 0) {
            $errors[] = 'El precio de venta final debe ser mayor que cero.';
            error_log("DEBUG: SaleController::process - Validation error: Precio Venta <= 0.");
        } else {
            $pdo = Database::getInstance()->getConnection();
            try {
                error_log("DEBUG: SaleController::process - Starting transaction.");
                $pdo->beginTransaction();

                $sale = new Sale();
                $sale->setParteOriginalId($part->getId());
                $sale->setNombreParte($part->getNombre());
                $sale->setPrecioVenta($precioVenta);
                $sale->setUsuarioVendedorId((int)$_SESSION['user_id']);
                error_log("DEBUG: SaleController::process - Attempting to save sale record.");
                
                if (!$sale->save()) {
                    throw new \Exception('No se pudo guardar el registro de la venta.');
                }
                error_log("DEBUG: SaleController::process - Sale saved successfully. Sale ID: " . $sale->getId());

                error_log("DEBUG: SaleController::process - Attempting to decrement stock for part ID: " . $part->getId() . " from " . $part->getCantidadDisponible() . " to " . ($part->getCantidadDisponible() - 1));
                if (!$part->decrementStock(1)) {
                     throw new \Exception('No se pudo actualizar el stock de la parte.');
                }
                error_log("DEBUG: SaleController::process - Stock decremented successfully.");

                error_log("DEBUG: SaleController::process - Committing transaction.");
                $pdo->commit();
                error_log("DEBUG: SaleController::process - Transaction committed.");

                // Generate Invoice
                $saleData = [
                    'sale_id' => $sale->getId(),
                    'fecha_venta' => date('Y-m-d H:i:s'), // Current timestamp
                    'vendedor_nombre' => $_SESSION['username'],
                    'nombre_parte' => $part->getNombre(),
                    'precio_venta' => $sale->getPrecioVenta()
                ];
                error_log("DEBUG: SaleController::process - Generating single item invoice.");

                // Use the generateSingleItemPdf method as generate is renamed
                $invoiceGenerator = new \App\Helpers\InvoiceGenerator();
                $invoiceGenerator->generateSingleItemPdf($saleData);
                error_log("DEBUG: SaleController::process - generateSingleItemPdf called (should exit).");
                // The generate method handles exit()

            } catch (\Exception $e) {
                error_log("DEBUG: SaleController::process - Exception caught: " . $e->getMessage());
                $pdo->rollBack();
                $errors[] = "Error al procesar la venta: " . $e->getMessage();
            }
        }

        // Si hay errores, volver a mostrar el formulario
        $pageTitle = 'Registrar Venta';
        error_log("DEBUG: SaleController::process - Displaying form with errors: " . print_r($errors, true));
        $this->view('admin/venta_form', [
            'pageTitle' => $pageTitle,
            'part' => $part,
            'errors' => $errors
        ]);
    }
}
