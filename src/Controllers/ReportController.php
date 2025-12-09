<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\BaseController;
use App\Models\Sale;

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

        $pageTitle = 'Reporte de Ventas Mensual';
        $this->view('admin/reports/index', [
            'pageTitle' => $pageTitle,
            'sales' => $sales,
            'totalRevenue' => $totalRevenue,
            'month' => sprintf('%02d', $month),
            'year' => $year,
            'reportDate' => date('F Y', mktime(0, 0, 0, $month, 1, $year))
        ]);
    }
}
