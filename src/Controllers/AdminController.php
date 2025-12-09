<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\BaseController;
use App\Models\User;
use App\Models\Part;
use App\Models\Sale;

class AdminController extends BaseController
{
    public function index(): void
    {
        // Proteger esta página
        $this->authorizeAdmin();

        // Obtener estadísticas para el dashboard (misma lógica que en el antiguo admin/index.php)
        $userCount = User::count();
        $partsCount = Part::count();
        $totalRevenue = Sale::totalRevenue();
        $recentSales = Sale::findRecent(5);
        
        $pageTitle = 'Dashboard';

        // Renderizar la nueva vista del dashboard
        $this->view('admin/dashboard/index', [
            'pageTitle' => $pageTitle,
            'userCount' => $userCount,
            'partsCount' => $partsCount,
            'totalRevenue' => $totalRevenue,
            'recentSales' => $recentSales
        ]);
    }
}
