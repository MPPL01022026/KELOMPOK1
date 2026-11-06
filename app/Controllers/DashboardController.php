<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\Sale;
use App\Models\ProductVariant;
use App\Models\StockMovement;
use App\Models\Product;

class DashboardController extends Controller
{
    private Sale $saleModel;
    private ProductVariant $variantModel;
    private StockMovement $movementModel;
    private Product $productModel;

    public function __construct()
    {
        parent::__construct();
        $this->saleModel = new Sale();
        $this->variantModel = new ProductVariant();
        $this->movementModel = new StockMovement();
        $this->productModel = new Product();
    }

    public function index(): void
    {
        $todaySummary = $this->saleModel->getTodaySummary();
        $criticalVariants = $this->variantModel->getCriticalVariants(8);
        $recentSales = $this->saleModel->getSalesHistory(null, null, null, 5);
        $chartData = $this->saleModel->getWeeklySalesChartData();
        $topProducts = $this->saleModel->getTopSellingProducts(5);
        $recentMovements = $this->movementModel->getRecentMovements(6);

        $this->render('dashboard.index', [
            'todaySummary' => $todaySummary,
            'criticalVariants' => $criticalVariants,
            'criticalCount' => count($criticalVariants),
            'recentSales' => $recentSales,
            'chartData' => $chartData,
            'topProducts' => $topProducts,
            'recentMovements' => $recentMovements,
            'pageTitle' => 'Dashboard Monitoring Toko'
        ]);
    }
}
