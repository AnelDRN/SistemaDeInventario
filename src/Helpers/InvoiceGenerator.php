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
     * Generates the PDF invoice and outputs it to the browser.
     *
     * @param array $saleData Associative array with sale details.
     */
    public function generate(array $saleData): void
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
}
