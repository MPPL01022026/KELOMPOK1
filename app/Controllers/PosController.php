<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\Sale;
use Exception;

class PosController extends Controller
{
    private Product $productModel;
    private Category $categoryModel;
    private Sale $saleModel;

    public function __construct()
    {
        parent::__construct();
        $this->productModel = new Product();
        $this->categoryModel = new Category();
        $this->saleModel = new Sale();
    }

    public function index(): void
    {
        $categories = $this->categoryModel->getAll();
        $products = $this->productModel->getProductsForPos();

        $this->render('pos.index', [
            'categories' => $categories,
            'products' => $products,
            'pageTitle' => 'Kasir POS - Yunda Fashion'
        ]);
    }

    /**
     * Endpoint API untuk memproses Checkout Transaksi Penjualan
     */
    public function checkout(): void
    {
        // Terima data JSON atau POST form
        $input = json_decode(file_get_contents('php://input'), true);
        if (!$input) {
            $input = $_POST;
        }

        $currentUser = $this->currentUser();
        if (!$currentUser) {
            $this->json(['success' => false, 'message' => 'Sesi login telah berakhir.'], 401);
            return;
        }

        $userId = (int)$currentUser['id'];
        $totalAmount = (float)($input['total_amount'] ?? 0);
        $paidAmount = (float)($input['paid_amount'] ?? 0);
        $changeAmount = (float)($input['change_amount'] ?? 0);
        $items = $input['items'] ?? [];

        if (empty($items)) {
            $this->json(['success' => false, 'message' => 'Keranjang transaksi masih kosong.'], 400);
            return;
        }

        if ($paidAmount < $totalAmount) {
            $this->json(['success' => false, 'message' => 'Uang tunai yang dibayarkan kurang dari total belanja.'], 400);
            return;
        }

        try {
            $result = $this->saleModel->processSale($userId, $totalAmount, $paidAmount, $changeAmount, $items);
            $this->json([
                'success' => true,
                'sale_id' => $result['sale_id'],
                'invoice_number' => $result['invoice_number'],
                'total_amount' => $result['total_amount'],
                'paid_amount' => $result['paid_amount'],
                'change_amount' => $result['change_amount'],
                'message' => 'Transaksi berhasil diselesaikan!',
                'redirect_receipt' => "/transactions/receipt/{$result['sale_id']}"
            ]);
        } catch (Exception $e) {
            $this->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }
}
