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

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('public/index.php?/admin/inventario');
            return;
        }

        $partId = (int)($_POST['part_id'] ?? 0);
        $part = Part::findById($partId);

        if (!$part || $part->getCantidadDisponible() <= 0) {
            FlashMessage::setMessage('La parte ya no está disponible para la venta.', 'danger');
            $this->redirect('public/index.php?/admin/inventario');
            return;
        }
        
        $precioVenta = isset($_POST['precio_venta']) ? (float)$_POST['precio_venta'] : 0;
        $errors = [];

        if ($precioVenta <= 0) {
            $errors[] = 'El precio de venta final debe ser mayor que cero.';
        } else {
            $pdo = Database::getInstance()->getConnection();
            try {
                $pdo->beginTransaction();

                $sale = new Sale();
                $sale->setParteOriginalId($part->getId());
                $sale->setNombreParte($part->getNombre());
                $sale->setPrecioVenta($precioVenta);
                $sale->setUsuarioVendedorId((int)$_SESSION['user_id']);
                
                if (!$sale->save()) {
                    throw new \Exception('No se pudo guardar el registro de la venta.');
                }

                if (!$part->decrementStock(1)) {
                     throw new \Exception('No se pudo actualizar el stock de la parte.');
                }

                $pdo->commit();
                FlashMessage::setMessage('Venta registrada con éxito.', 'success');
                $this->redirect('public/index.php?/admin/inventario');
                return;

            } catch (\Exception $e) {
                $pdo->rollBack();
                $errors[] = "Error al procesar la venta: " . $e->getMessage();
            }
        }

        // Si hay errores, volver a mostrar el formulario
        $pageTitle = 'Registrar Venta';
        $this->view('admin/venta_form', [
            'pageTitle' => $pageTitle,
            'part' => $part,
            'errors' => $errors
        ]);
    }
}
