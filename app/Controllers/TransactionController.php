<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\Sale;
use App\Models\User;

class TransactionController extends Controller
{
    private Sale $saleModel;
    private User $userModel;

    public function __construct()
    {
        parent::__construct();
        $this->saleModel = new Sale();
        $this->userModel = new User();
    }

    public function index(): void
    {
        $currentUser = $this->currentUser();
        $startDate = !empty($_GET['start_date']) ? $_GET['start_date'] : null;
        $endDate = !empty($_GET['end_date']) ? $_GET['end_date'] : null;

        // Jika kasir, batasi riwayat transaksi pribadinya sesuai FR-10
        $userIdFilter = null;
        if ($this->isKasir()) {
            $userIdFilter = (int)$currentUser['id'];
        } elseif (!empty($_GET['user_id'])) {
            $userIdFilter = (int)$_GET['user_id'];
        }

        $sales = $this->saleModel->getSalesHistory($startDate, $endDate, $userIdFilter, 100);
        $cashiers = $this->userModel->getAllUsers();

        $this->render('transactions.index', [
            'sales' => $sales,
            'cashiers' => $cashiers,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'selectedUserId' => $userIdFilter,
            'pageTitle' => $this->isKasir() ? 'Riwayat Transaksi Saya' : 'Semua Riwayat Transaksi'
        ]);
    }

    public function detail(string $id): void
    {
        $saleId = (int)$id;
        $sale = $this->saleModel->getById($saleId);

        if (!$sale) {
            $this->setFlash('error', 'Transaksi tidak ditemukan.');
            $this->redirect('/transactions');
            return;
        }

        // Jika kasir, pastikan hanya bisa lihat transaksinya sendiri
        if ($this->isKasir() && $sale['user_id'] != $this->currentUser()['id']) {
            $this->setFlash('error', 'Anda tidak memiliki hak akses untuk transaksi ini.');
            $this->redirect('/transactions');
            return;
        }

        $this->render('transactions.detail', [
            'sale' => $sale,
            'pageTitle' => 'Detail Transaksi ' . $sale['invoice_number']
        ]);
    }

    /**
     * Struk Thermal 80mm siap cetak langsung
     */
    public function receipt(string $id): void
    {
        $saleId = (int)$id;
        $sale = $this->saleModel->getById($saleId);

        if (!$sale) {
            die("Transaksi tidak ditemukan.");
        }

        // Render receipt tanpa layout wrapper utama
        $this->render('transactions.receipt', [
            'sale' => $sale
        ], null);
    }
}
