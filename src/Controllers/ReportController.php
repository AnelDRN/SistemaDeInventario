<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\BaseController;
use App\Models\Sale;
use App\Models\Part;     // Added for inventory report
use App\Models\Section;  // Added for inventory report
use App\Helpers\ExcelGenerator; // Added for Excel export

class ReportController extends BaseController
{
    /**
     * Displays the form to select a month and year for the sales report.
     */
    public function index(): void
    {
        $this->authorizeAdmin();
        $pageTitle = 'Reportes de Ventas';
        $this->view('admin/reports/index', [
            'pageTitle' => $pageTitle,
            'sales' => null, // No sales data on initial view
            'month' => date('m'),
            'year' => date('Y')
        ]);
    }

    /**
     * Generates and displays the monthly sales report.
     */
    public function monthly(): void
    {
        $this->authorizeAdmin();

        $year = isset($_POST['year']) ? (int)$_POST['year'] : date('Y');
        $month = isset($_POST['month']) ? (int)$_POST['month'] : date('m');

        $sales = Sale::findSalesByMonth($year, $month);
        $totalRevenue = array_reduce($sales, function($carry, $sale) {
            return $carry + $sale['precio_venta'];
        }, 0);

        $revenueByCategory = Sale::getTotalRevenueByCategory($year, $month);
        $mostSoldParts = Sale::getMostSoldParts($year, $month);

        $pageTitle = 'Reporte de Ventas Mensual';
        $this->view('admin/reports/index', [
            'pageTitle' => $pageTitle,
            'sales' => $sales,
            'totalRevenue' => $totalRevenue,
            'revenueByCategory' => $revenueByCategory, // Pass to view
            'mostSoldParts' => $mostSoldParts, // Pass to view
            'month' => sprintf('%02d', $month),
            'year' => $year,
            'reportDate' => date('F Y', mktime(0, 0, 0, $month, 1, $year))
        ]);
    }

    /**
     * Exports the current inventory to an Excel file.
     */
    public function exportPartInventoryExcel(): void
    {
        $this->authorizeAdmin();

        // Fetch all parts
        $parts = Part::findAll();
        $partsData = [];
        foreach ($parts as $part) {
            $partsData[] = [
                'id_parte' => $part->getId(),
                'nombre_parte' => $part->getNombre(),
                'descripcion_parte' => $part->getDescripcion(),
                'tipo_parte' => $part->getTipoParte(),
                'marca_auto' => $part->getMarcaAuto(),
                'modelo_auto' => $part->getModeloAuto(),
                'ano_auto' => $part->getAnioAuto(),
                'precio_parte' => $part->getPrecio(),
                'cantidad_disponible' => $part->getCantidadDisponible(),
                'id_seccion' => $part->getSeccionId(),
            ];
        }

        // Fetch all sections
        $sections = Section::findAll();
        $sectionsData = [];
        foreach ($sections as $section) {
            $sectionsData[] = [
                'id' => $section->getId(),
                'nombre_seccion' => $section->getNombre()
            ];
        }

        $excelGenerator = new ExcelGenerator();
        $excelGenerator->generatePartInventoryReport($partsData, $sectionsData);
    }
}
