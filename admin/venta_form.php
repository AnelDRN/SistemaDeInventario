<?php
require_once dirname(__DIR__) . '/includes/bootstrap.php';

use App\Models\Part;
use App\Models\Sale;
use App\Helpers\Sanitizer;
use App\Config\Database;

// --- Controlador del Formulario de Venta ---

// 1. Proteger la página
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit();
}

$pageTitle = 'Registrar Venta';
$errors = [];
$part = null;

// 2. Validar input y obtener la parte
$partId = isset($_GET['part_id']) ? (int)$_GET['part_id'] : null;
if (!$partId) {
    // Idealmente, guardaríamos un error en la sesión para mostrarlo en la siguiente página
    header('Location: inventario.php');
    exit();
}

$part = Part::findById($partId);
if (!$part || $part->getCantidadDisponible() <= 0) {
    // Si la parte no existe o no hay stock, redirigir
    header('Location: inventario.php');
    exit();
}

// 3. Procesar la venta
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $precioVenta = isset($_POST['precio_venta']) ? (float)$_POST['precio_venta'] : 0;

    if ($precioVenta <= 0) {
        $errors[] = 'El precio de venta final debe ser mayor que cero.';
    } else {
        $pdo = Database::getInstance()->getConnection();
        try {
            $pdo->beginTransaction();

            // 1. Crear y guardar el registro de la venta
            $sale = new Sale();
            $sale->setParteOriginalId($part->getId());
            $sale->setNombreParte($part->getNombre());
            $sale->setPrecioVenta($precioVenta);
            $sale->setUsuarioVendedorId((int)$_SESSION['user_id']);
            
            if (!$sale->save()) {
                throw new Exception('No se pudo guardar el registro de la venta.');
            }

            // 2. Decrementar el stock de la parte
            if (!$part->decrementStock(1)) {
                 throw new Exception('No se pudo actualizar el stock de la parte.');
            }

            // 3. Si todo fue bien, confirmar la transacción
            $pdo->commit();

            // Redirigir a la lista de inventario (con un mensaje de éxito sería ideal)
            header('Location: inventario.php');
            exit();

        } catch (Exception $e) {
            // Si algo falló, revertir la transacción
            $pdo->rollBack();
            $errors[] = "Error al procesar la venta: " . $e->getMessage();
        }
    }
}

// 4. Incluir el layout y la vista
require_once ROOT_PATH . '/views/admin/layouts/header.php';
require_once ROOT_PATH . '/views/admin/venta_form.php';
require_once ROOT_PATH . '/views/admin/layouts/footer.php';
