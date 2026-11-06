<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\Product;
use App\Models\Category;

class DigitalTwinController extends Controller
{
    private Product $productModel;
    private Category $categoryModel;

    public function __construct()
    {
        parent::__construct();
        $this->productModel = new Product();
        $this->categoryModel = new Category();
    }

    public function index(): void
    {
        $categories = $this->categoryModel->getAll();
        $products = $this->productModel->getDigitalTwinData();

        $this->render('digital_twin.index', [
            'categories' => $categories,
            'products' => $products,
            'pageTitle' => 'Etalase Digital Twin - Monitoring Stok Fisik'
        ]);
    }

    /**
     * API Endpoint untuk Real-time Polling / Auto-refresh Digital Twin
     */
    public function apiData(): void
    {
        $categoryId = !empty($_GET['category_id']) ? (int)$_GET['category_id'] : null;
        $keyword = !empty($_GET['keyword']) ? trim($_GET['keyword']) : null;
        $statusFilter = !empty($_GET['status']) ? trim($_GET['status']) : null;

        $data = $this->productModel->getDigitalTwinData($categoryId, $keyword, $statusFilter);

        $totalStockAll = 0;
        $criticalCount = 0;
        $menipisCount = 0;
        $amanCount = 0;

        foreach ($data as $p) {
            $totalStockAll += (int)$p['total_stock'];
            if ($p['overall_status'] === 'kritis') $criticalCount++;
            elseif ($p['overall_status'] === 'menipis') $menipisCount++;
            else $amanCount++;
        }

        $this->json([
            'success' => true,
            'timestamp' => date('H:i:s'),
            'summary' => [
                'total_products' => count($data),
                'total_stock' => $totalStockAll,
                'critical_count' => $criticalCount,
                'menipis_count' => $menipisCount,
                'aman_count' => $amanCount
            ],
            'products' => $data
        ]);
    }
}
