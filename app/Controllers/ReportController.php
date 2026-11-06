<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\Sale;
use App\Config\App;

class ReportController extends Controller
{
    private Sale $saleModel;

    public function __construct()
    {
        parent::__construct();
        $this->saleModel = new Sale();
    }

    public function index(): void
    {
        $startDate = !empty($_GET['start_date']) ? $_GET['start_date'] : date('Y-m-01');
        $endDate = !empty($_GET['end_date']) ? $_GET['end_date'] : date('Y-m-d');

        $sales = $this->saleModel->getSalesHistory($startDate, $endDate, null, 500);

        $totalRevenue = 0;
        $totalTransactions = count($sales);
        $totalQtySold = 0;

        foreach ($sales as $s) {
            $totalRevenue += (float)$s['total_amount'];
            $totalQtySold += (int)$s['total_qty_sold'];
        }

        $topProducts = $this->saleModel->getTopSellingProducts(5);
        $chartData = $this->saleModel->getWeeklySalesChartData();

        $this->render('reports.index', [
            'sales' => $sales,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'totalRevenue' => $totalRevenue,
            'totalTransactions' => $totalTransactions,
            'totalQtySold' => $totalQtySold,
            'topProducts' => $topProducts,
            'chartData' => $chartData,
            'pageTitle' => 'Laporan Penjualan & Analitik'
        ]);
    }

    public function exportCsv(): void
    {
        $startDate = !empty($_GET['start_date']) ? $_GET['start_date'] : date('Y-m-01');
        $endDate = !empty($_GET['end_date']) ? $_GET['end_date'] : date('Y-m-d');

        $sales = $this->saleModel->getSalesHistory($startDate, $endDate, null, 1000);

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=Laporan_Penjualan_YundaFashion_' . date('Ymd_His') . '.csv');

        $output = fopen('php://output', 'w');
        fputcsv($output, ['No. Invoice', 'Tanggal', 'Kasir', 'Total Belanja (Rp)', 'Dibayar (Rp)', 'Kembalian (Rp)', 'Total Pcs']);

        foreach ($sales as $s) {
            fputcsv($output, [
                $s['invoice_number'],
                $s['created_at'],
                $s['cashier_name'],
                $s['total_amount'],
                $s['paid_amount'],
                $s['change_amount'],
                $s['total_qty_sold']
            ]);
        }

        fclose($output);
        exit;
    }

    public function printView(): void
    {
        $startDate = !empty($_GET['start_date']) ? $_GET['start_date'] : date('Y-m-01');
        $endDate = !empty($_GET['end_date']) ? $_GET['end_date'] : date('Y-m-d');

        $sales = $this->saleModel->getSalesHistory($startDate, $endDate, null, 500);

        $totalRevenue = 0;
        $totalTransactions = count($sales);
        $totalQtySold = 0;

        foreach ($sales as $s) {
            $totalRevenue += (float)$s['total_amount'];
            $totalQtySold += (int)$s['total_qty_sold'];
        }

        $topProducts = $this->saleModel->getTopSellingProducts(5);

        $this->render('reports.print', [
            'sales' => $sales,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'totalRevenue' => $totalRevenue,
            'totalTransactions' => $totalTransactions,
            'totalQtySold' => $totalQtySold,
            'topProducts' => $topProducts
        ], null);
    }
}
