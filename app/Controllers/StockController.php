<?php
/**
 * Controller Manajemen Stok (Barang Masuk & Penyesuaian Fisik)
 * Modul: Inventory System - Yunda Fashion
 * Developer: Salsabila (230504097)
 */
namespace App\Controllers;

use App\Core\Controller;
use App\Models\ProductVariant;
use App\Models\StockMovement;
use Exception;

class StockController extends Controller
{
    private ProductVariant $variantModel;
    private StockMovement $movementModel;

    public function __construct()
    {
        parent::__construct();
        $this->variantModel = new ProductVariant();
        $this->movementModel = new StockMovement();
    }

    /**
     * Halaman & Proses Barang Masuk (Restock) - FR-08 A
     */
    public function restock(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $variantId = (int)($_POST['variant_id'] ?? 0);
            $quantity = (int)($_POST['quantity'] ?? 0);
            $notes = trim($_POST['notes'] ?? '');

            $user = $this->currentUser();

            if ($variantId <= 0 || $quantity <= 0) {
                $this->setFlash('error', 'Pilih varian produk dan masukkan jumlah restock minimal 1 pcs.');
                $this->redirect('/stock/restock');
                return;
            }

            try {
                $result = $this->movementModel->restock($variantId, (int)$user['id'], $quantity, $notes);
                $this->setFlash('success', "Barang masuk berhasil dicatat! Stok bertambah dari {$result['stock_before']} menjadi {$result['stock_after']} pcs.");
            } catch (Exception $e) {
                $this->setFlash('error', 'Gagal memproses restock: ' . $e->getMessage());
            }

            $this->redirect('/stock/restock');
            return;
        }

        $variants = $this->variantModel->getAllFlatWithProduct();
        $recentRestocks = $this->movementModel->getRecentMovements(20, null, 'restock');

        $this->render('stock.restock', [
            'variants' => $variants,
            'recentRestocks' => $recentRestocks,
            'pageTitle' => 'Barang Masuk (Restock)'
        ]);
    }

    /**
     * Halaman & Proses Penyesuaian Stok Fisik (Adjustment) - FR-08 B
     */
    public function adjustment(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $variantId = (int)($_POST['variant_id'] ?? 0);
            $physicalStock = (int)($_POST['physical_stock'] ?? 0);
            $reason = trim($_POST['reason'] ?? '');

            $user = $this->currentUser();

            if ($variantId <= 0 || empty($reason)) {
                $this->setFlash('error', 'Pilih varian produk dan cantumkan alasan penyesuaian stok.');
                $this->redirect('/stock/adjustment');
                return;
            }

            try {
                $result = $this->movementModel->adjustStock($variantId, (int)$user['id'], $physicalStock, $reason);
                $diffText = ($result['diff'] >= 0) ? "+{$result['diff']}" : "{$result['diff']}";
                $this->setFlash('success', "Penyesuaian stok berhasil! Stok fisik disinkronkan ke {$result['stock_after']} pcs (Selisih: {$diffText} pcs).");
            } catch (Exception $e) {
                $this->setFlash('error', 'Gagal penyesuaian stok: ' . $e->getMessage());
            }

            $this->redirect('/stock/adjustment');
            return;
        }

        $variants = $this->variantModel->getAllFlatWithProduct();
        $recentAdjustments = $this->movementModel->getRecentMovements(20, null, 'adjustment');

        $this->render('stock.adjustment', [
            'variants' => $variants,
            'recentAdjustments' => $recentAdjustments,
            'pageTitle' => 'Penyesuaian Stok Fisik (Stock Adjustment)'
        ]);
    }

    /**
     * Log Semua Pergerakan Stok (Stock Movements)
     */
    public function movements(): void
    {
        $movementType = !empty($_GET['type']) ? trim($_GET['type']) : null;
        $movements = $this->movementModel->getRecentMovements(150, null, $movementType);

        $this->render('stock.movements', [
            'movements' => $movements,
            'selectedType' => $movementType,
            'pageTitle' => 'Riwayat Pergerakan Stok (Stock Movements)'
        ]);
    }
}
