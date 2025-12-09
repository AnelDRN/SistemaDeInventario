<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\BaseController;
use App\Helpers\FlashMessage;
use App\Models\Part;
use App\Models\Sale;
use App\Config\Database;
use App\Helpers\InvoiceGenerator;

class CartController extends BaseController
{
    public function __construct(array $params = [])
    {
        parent::__construct($params);
        // Initialize the cart in session if it doesn't exist
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }
    }

    /**
     * Display the shopping cart page.
     */
    public function index(): void
    {
        $cart = $_SESSION['cart'] ?? [];
        $cartItems = [];
        $total = 0;

        if (!empty($cart)) {
            foreach ($cart as $part_id => $item) {
                $part = Part::findById($part_id);
                if ($part) {
                    $subtotal = $item['price'] * $item['quantity'];
                    $cartItems[$part_id] = [
                        'part_id' => $part_id,
                        'name' => $item['name'],
                        'price' => $item['price'],
                        'quantity' => $item['quantity'],
                        'image' => $item['image'],
                        'stock' => $part->getCantidadDisponible(), // Get current stock
                        'subtotal' => $subtotal,
                    ];
                    $total += $subtotal;
                } else {
                    // If a part in the cart doesn't exist anymore, remove it
                    unset($_SESSION['cart'][$part_id]);
                }
            }
        }
        
        $this->view('public/cart/index', [
            'cartItems' => $cartItems,
            'total' => $total,
            'pageTitle' => 'Carrito de Compras'
        ]);
    }

    /**
     * Add an item to the shopping cart.
     */
    public function add(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/cart');
            return;
        }

        $part_id = filter_input(INPUT_POST, 'part_id', FILTER_VALIDATE_INT);
        $quantity = filter_input(INPUT_POST, 'quantity', FILTER_VALIDATE_INT);

        if (!$part_id || !$quantity || $quantity <= 0) {
            FlashMessage::setMessage('danger', 'Datos inválidos.');
            $this->redirect('/cart');
            return;
        }

        $part = Part::findById($part_id);

        if (!$part) {
            FlashMessage::setMessage('danger', 'El producto no fue encontrado.');
            $this->redirect('/cart');
            return;
        }

        if ($part->getCantidadDisponible() < $quantity) {
            FlashMessage::setMessage('warning', 'No hay suficiente stock para la cantidad solicitada.');
            $this->redirect('/part/' . $part_id);
            return;
        }

        // Add or update the item in the cart
        if (isset($_SESSION['cart'][$part_id])) {
            // If item already in cart, check stock before updating quantity
            $new_quantity_in_cart = $_SESSION['cart'][$part_id]['quantity'] + $quantity;
            if ($part->getCantidadDisponible() < $new_quantity_in_cart) {
                FlashMessage::setMessage('warning', 'No puedes agregar esa cantidad. El total en el carrito excedería el stock disponible (' . $part->getCantidadDisponible() . ' unidades).');
                $this->redirect('/part/' . $part_id);
                return;
            }
            $_SESSION['cart'][$part_id]['quantity'] = $new_quantity_in_cart;
        } else {
            // Add new item to cart
            $_SESSION['cart'][$part_id] = [
                'part_id' => $part_id,
                'quantity' => $quantity,
                'price' => $part->getPrecio(),
                'name' => $part->getNombre(),
                'image' => $part->getThumbnailUrl()
            ];
        }
        
        FlashMessage::setMessage('success', '¡"' . htmlspecialchars($part->getNombre()) . '" fue agregado al carrito!');
        $this->redirect('public/index.php?/cart');
    }

    /**
     * Update an item's quantity in the cart.
     */
    public function update(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/cart');
            return;
        }

        $part_id = filter_input(INPUT_POST, 'part_id', FILTER_VALIDATE_INT);
        $quantity = filter_input(INPUT_POST, 'quantity', FILTER_VALIDATE_INT);

        if (!$part_id || !isset($quantity) || $quantity < 0) {
            FlashMessage::setMessage('danger', 'Datos inválidos.');
            $this->redirect('public/index.php?/cart');
            return;
        }

        if (!isset($_SESSION['cart'][$part_id])) {
            $this->redirect('public/index.php?/cart');
            return;
        }

        if ($quantity == 0) {
            $itemName = $_SESSION['cart'][$part_id]['name'];
            unset($_SESSION['cart'][$part_id]);
            FlashMessage::setMessage('success', 'Producto "' . htmlspecialchars($itemName) . '" eliminado del carrito.');
            $this->redirect('public/index.php?/cart');
            return;
        }

        $part = Part::findById($part_id);
        if ($part && $part->getCantidadDisponible() < $quantity) {
            FlashMessage::setMessage('warning', 'No hay suficiente stock para la cantidad solicitada. Solo hay ' . $part->getCantidadDisponible() . ' unidades disponibles.');
        } else {
            $_SESSION['cart'][$part_id]['quantity'] = $quantity;
            FlashMessage::setMessage('success', 'Cantidad actualizada correctamente.');
        }
        
        $this->redirect('public/index.php?/cart');
    }

    /**
     * Remove an item from the cart.
     */
    public function remove(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/cart');
            return;
        }

        $part_id = filter_input(INPUT_POST, 'part_id', FILTER_VALIDATE_INT);
        
        if ($part_id && isset($_SESSION['cart'][$part_id])) {
            $itemName = $_SESSION['cart'][$part_id]['name'];
            unset($_SESSION['cart'][$part_id]);
            FlashMessage::setMessage('success', 'Producto "' . htmlspecialchars($itemName) . '" eliminado del carrito.');
        } else {
            FlashMessage::setMessage('danger', 'No se pudo eliminar el producto.');
        }
        
        $this->redirect('public/index.php?/cart');
    }

    /**
     * Handle the checkout process.
     */
    public function checkout(): void
    {
        // 1. Guards
        if (empty($_SESSION['cart'])) {
            FlashMessage::setMessage('info', 'Tu carrito está vacío.');
            $this->redirect('public/index.php?/cart');
            return;
        }

        if (!isset($_SESSION['user_id'])) {
            FlashMessage::setMessage('warning', 'Debes iniciar sesión para finalizar la compra.');
            $_SESSION['redirect_to'] = '/cart/checkout';
            $this->redirect('public/index.php?/login');
            return;
        }

        $cart = $_SESSION['cart'];
        $pdo = Database::getInstance()->getConnection();

        try {
            $pdo->beginTransaction();

            // Generate a unique order ID for this transaction (simple timestamp for now)
            $orderId = time(); 
            $orderDate = date('Y-m-d H:i:s');
            $customerName = $_SESSION['username'] ?? 'Invitado'; // Should be logged in here

            foreach ($cart as $part_id => $item) {
                $part = Part::findById($part_id);

                // Re-validate stock
                if (!$part || $part->getCantidadDisponible() < $item['quantity']) {
                    throw new \Exception('La parte "' . htmlspecialchars($item['name']) . '" ya no tiene stock suficiente.');
                }
                
                // Create a sale record for each unit (as per existing structure)
                for ($i = 0; $i < $item['quantity']; $i++) {
                    $sale = new Sale();
                    $sale->setParteOriginalId($part->getId());
                    $sale->setNombreParte($part->getNombre());
                    $sale->setPrecioVenta($part->getPrecio()); // Use current price from DB
                    $sale->setUsuarioVendedorId((int)$_SESSION['user_id']); // Customer is the 'seller'
                    
                    if (!$sale->save()) {
                        throw new \Exception('No se pudo guardar el registro de la venta para "' . htmlspecialchars($item['name']) . '".');
                    }
                }

                // Decrement stock once for the total quantity
                if (!$part->decrementStock($item['quantity'])) {
                    throw new \Exception('No se pudo actualizar el stock para "' . htmlspecialchars($item['name']) . '".');
                }
            }

            $pdo->commit();

            // Store order details and metadata for success/invoice page and clear cart
            $_SESSION['latest_order'] = $cart;
            $_SESSION['latest_order_metadata'] = [
                'order_id' => $orderId,
                'order_date' => $orderDate,
                'customer_name' => $customerName
            ];
            unset($_SESSION['cart']);

            // Redirect to order summary/invoice display
            $this->redirect('public/index.php?/cart/order-summary');

        } catch (\Exception $e) {
            $pdo->rollBack();
            FlashMessage::setMessage('danger', "Error al procesar la compra: " . $e->getMessage());
            $this->redirect('public/index.php?/cart');
        }
    }


    /**
     * Displays the order summary and invoice after checkout.
     */
    public function orderSummary(): void
    {
        if (!isset($_SESSION['latest_order']) || !isset($_SESSION['latest_order_metadata'])) {
            // Redirect to home or cart if no order data is found
            $this->redirect('public/index.php?');
            return;
        }

        $order_details = $_SESSION['latest_order'];
        $order_metadata = $_SESSION['latest_order_metadata'];

        $invoiceGenerator = new InvoiceGenerator();
        $invoiceHtml = $invoiceGenerator->getMultiItemHtml(
            $order_details,
            $order_metadata['order_id'],
            $order_metadata['customer_name'],
            $order_metadata['order_date']
        );



        $this->view('public/cart/order_summary', [
            'pageTitle' => 'Resumen de tu Pedido y Factura',
            'invoice_html' => $invoiceHtml,
            'download_pdf_url' => BASE_URL . '/public/index.php?/cart/download-invoice'
        ]);
    }

    /**
     * Handles the download of the order invoice as PDF.
     */
    public function downloadInvoice(): void
    {
        error_log("DEBUG: CartController::downloadInvoice - Method called.");
        if (!isset($_SESSION['latest_order']) || !isset($_SESSION['latest_order_metadata'])) {
            error_log("DEBUG: CartController::downloadInvoice - Session data for order not found. Redirecting.");
            // Redirect to home or cart if no order data is found
            $this->redirect('public/index.php?');
            return;
        }

        $order_details = $_SESSION['latest_order'];
        $order_metadata = $_SESSION['latest_order_metadata'];
        error_log("DEBUG: CartController::downloadInvoice - Order data retrieved from session. Order ID: " . $order_metadata['order_id']);

        $invoiceGenerator = new InvoiceGenerator();
        error_log("DEBUG: CartController::downloadInvoice - InvoiceGenerator instantiated. Calling generateMultiItemPdf.");
        $invoiceGenerator->generateMultiItemPdf(
            $order_details,
            $order_metadata['order_id'],
            $order_metadata['customer_name'],
            $order_metadata['order_date']
        );
        error_log("DEBUG: CartController::downloadInvoice - generateMultiItemPdf returned (should not happen due to exit())."); // This line should ideally not be logged
    }
}
