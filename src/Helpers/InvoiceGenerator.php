<?php
declare(strict_types=1);

namespace App\Helpers;

require_once(ROOT_PATH . '/libs/fpdf/fpdf.php');

class InvoiceGenerator extends \FPDF
{
    private const ITBMS_RATE = 0.07; // 7%

    // Page header
    function Header()
    {
        // Logo or title
        $this->SetFont('Arial','B',15);
        $this->Cell(80); // Center
        $this->Cell(30,10,'Factura de Venta',1,0,'C');
        $this->Ln(20); // Line break
    }

    // Page footer
    function Footer()
    {
        $this->SetY(-15);
        $this->SetFont('Arial','I',8);
        $this->Cell(0,10,'Pagina ' . $this->PageNo() . '/{nb}',0,0,'C');
    }

    /**
     * Generates a PDF invoice for a single item sale and outputs it to the browser.
     * This is the refactored original generate() method.
     *
     * @param array $saleData Associative array with single sale details.
     */
    public function generateSingleItemPdf(array $saleData): void
    {
        $this->AliasNbPages();
        $this->AddPage();
        $this->SetFont('Times','',12);

        // Sale Details
        $this->Cell(0,10,'Detalles de la Venta:',0,1);
        $this->Cell(40,10,'ID de Venta:',0,0);
        $this->Cell(0,10,$saleData['sale_id'],0,1);

        $this->Cell(40,10,'Fecha de Venta:',0,0);
        $this->Cell(0,10,date('d/m/Y H:i', strtotime($saleData['fecha_venta'])),0,1);

        $this->Cell(40,10,'Vendedor:',0,0);
        $this->Cell(0,10,htmlspecialchars($saleData['vendedor_nombre']),0,1);

        $this->Ln(10); // Line break

        // Calculate amounts (assuming precio_venta is the net price)
        $netPrice = $saleData['precio_venta'];
        $itbmsAmount = $netPrice * self::ITBMS_RATE;
        $totalPrice = $netPrice + $itbmsAmount;

        // Table Header
        $this->SetFont('Times','B',12);
        $this->Cell(130,10,'Parte Vendida',1,0,'C');
        $this->Cell(60,10,'Precio Neto',1,1,'C');

        // Table Body - Item details
        $this->SetFont('Times','',12);
        $this->Cell(130,10,htmlspecialchars($saleData['nombre_parte']),1,0);
        $this->Cell(60,10,'$' . number_format($netPrice, 2),1,1,'R'); // This is Net Price

        $this->Ln(5); // Small line break

        // Summary
        $this->Cell(130,10,'Subtotal:',0,0,'R');
        $this->Cell(60,10,'$' . number_format($netPrice, 2),0,1,'R');

        $this->Cell(130,10,'ITBMS ('. (self::ITBMS_RATE * 100) .'%),:',0,0,'R');
        $this->Cell(60,10,'$' . number_format($itbmsAmount, 2),0,1,'R');

        $this->SetFont('Times','B',12);
        $this->Cell(130,10,'TOTAL:',0,0,'R');
        $this->Cell(60,10,'$' . number_format($totalPrice, 2),0,1,'R');


        // Output PDF
        $this->Output('D', 'Factura_' . $saleData['sale_id'] . '.pdf');
        exit; // Stop script execution after PDF generation
    }

    /**
     * Generates a PDF invoice for a multi-item order and outputs it to the browser.
     *
     * @param array $orderDetails Array of items, each with 'name', 'price', 'quantity'.
     */
    public function generateMultiItemPdf(array $orderDetails, int $orderId, string $customerName, string $orderDate): void
    {
        $this->AliasNbPages();
        $this->AddPage();
        $this->SetFont('Times','',12);

        // Order Details
        $this->Cell(0,10,'Detalles del Pedido:',0,1);
        $this->Cell(40,10,'ID de Pedido:',0,0);
        $this->Cell(0,10,$orderId,0,1);

        $this->Cell(40,10,'Fecha de Pedido:',0,0);
        $this->Cell(0,10,date('d/m/Y H:i', strtotime($orderDate)),0,1);

        $this->Cell(40,10,'Cliente:',0,0);
        $this->Cell(0,10,htmlspecialchars($customerName),0,1);

        $this->Ln(10); // Line break

        // Table Header
        $this->SetFont('Times','B',12);
        $this->Cell(90,10,'Producto',1,0,'C');
        $this->Cell(30,10,'Cant.',1,0,'C');
        $this->Cell(40,10,'Precio Unit.',1,0,'C');
        $this->Cell(30,10,'Subtotal',1,1,'C');

        $netTotal = 0;
        $this->SetFont('Times','',12);
        foreach ($orderDetails as $item) {
            $itemSubtotal = $item['price'] * $item['quantity'];
            $netTotal += $itemSubtotal;
            $this->Cell(90,10,htmlspecialchars($item['name']),1,0);
            $this->Cell(30,10,$item['quantity'],1,0,'C');
            $this->Cell(40,10,'$' . number_format($item['price'], 2),1,0,'R');
            $this->Cell(30,10,'$' . number_format($itemSubtotal, 2),1,1,'R');
        }

        $itbmsAmount = $netTotal * self::ITBMS_RATE;
        $grandTotal = $netTotal + $itbmsAmount;

        $this->Ln(5); // Small line break

        // Summary
        $this->Cell(160,10,'Subtotal:',0,0,'R');
        $this->Cell(30,10,'$' . number_format($netTotal, 2),0,1,'R');

        $this->Cell(160,10,'ITBMS ('. (self::ITBMS_RATE * 100) .'%):',0,0,'R');
        $this->Cell(30,10,'$' . number_format($itbmsAmount, 2),0,1,'R');

        $this->SetFont('Times','B',12);
        $this->Cell(160,10,'TOTAL:',0,0,'R');
        $this->Cell(30,10,'$' . number_format($grandTotal, 2),0,1,'R');

        // Output PDF
        $this->Output('D', 'Factura_Pedido_' . $orderId . '.pdf');
        exit;
    }

    /**
     * Generates an HTML invoice for a multi-item order.
     *
     * @param array $orderDetails Array of items, each with 'name', 'price', 'quantity'.
     * @return string HTML string of the invoice.
     */
    public function getMultiItemHtml(array $orderDetails, int $orderId, string $customerName, string $orderDate): string
    {
        $html = '<div style="font-family: Arial, sans-serif; font-size: 14px; line-height: 1.5;">';
        $html .= '<h2 style="text-align: center; color: #333;">Factura de Pedido</h2>';
        $html .= '<p><strong>ID de Pedido:</strong> ' . htmlspecialchars((string)$orderId) . '</p>';
        $html .= '<p><strong>Fecha:</strong> ' . htmlspecialchars(date('d/m/Y H:i', strtotime($orderDate))) . '</p>';
        $html .= '<p><strong>Cliente:</strong> ' . htmlspecialchars($customerName) . '</p>';
        $html .= '<hr style="border: 0; border-top: 1px solid #eee; margin: 20px 0;">';

        $html .= '<table width="100%" cellpadding="5" cellspacing="0" border="0" style="border-collapse: collapse;">';
        $html .= '<thead>';
        $html .= '<tr style="background-color: #f2f2f2;">';
        $html .= '<th style="border: 1px solid #ddd; padding: 8px; text-align: left;">Producto</th>';
        $html .= '<th style="border: 1px solid #ddd; padding: 8px; text-align: center;">Cantidad</th>';
        $html .= '<th style="border: 1px solid #ddd; padding: 8px; text-align: right;">Precio Unit.</th>';
        $html .= '<th style="border: 1px solid #ddd; padding: 8px; text-align: right;">Subtotal</th>';
        $html .= '</tr>';
        $html .= '</thead>';
        $html .= '<tbody>';

        $netTotal = 0;
        foreach ($orderDetails as $item) {
            $itemSubtotal = $item['price'] * $item['quantity'];
            $netTotal += $itemSubtotal;
            $html .= '<tr>';
            $html .= '<td style="border: 1px solid #ddd; padding: 8px; text-align: left;">' . htmlspecialchars($item['name']) . '</td>';
            $html .= '<td style="border: 1px solid #ddd; padding: 8px; text-align: center;">' . htmlspecialchars((string)$item['quantity']) . '</td>';
            $html .= '<td style="border: 1px solid #ddd; padding: 8px; text-align: right;">$' . number_format($item['price'], 2) . '</td>';
            $html .= '<td style="border: 1px solid #ddd; padding: 8px; text-align: right;">$' . number_format($itemSubtotal, 2) . '</td>';
            $html .= '</tr>';
        }
        $html .= '</tbody>';
        $html .= '</table>';

        $itbmsAmount = $netTotal * self::ITBMS_RATE;
        $grandTotal = $netTotal + $itbmsAmount;

        $html .= '<div style="text-align: right; margin-top: 20px;">';
        $html .= '<p><strong>Subtotal:</strong> $' . number_format($netTotal, 2) . '</p>';
        $html .= '<p><strong>ITBMS (' . (self::ITBMS_RATE * 100) . '%):</strong> $' . number_format($itbmsAmount, 2) . '</p>';
        $html .= '<p style="font-size: 16px; font-weight: bold;">TOTAL: $' . number_format($grandTotal, 2) . '</p>';
        $html .= '</div>';
        $html .= '</div>';

        return $html;
    }
}
