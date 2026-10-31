<?php
/**
 * Route Definitions for Yunda Fashion POS & Inventory System
 */

use App\Controllers\AuthController;
use App\Controllers\DashboardController;
use App\Controllers\CategoryController;
use App\Controllers\ProductController;
use App\Controllers\PosController;
use App\Controllers\StockController;
use App\Controllers\DigitalTwinController;
use App\Controllers\TransactionController;
use App\Controllers\ReportController;
use App\Controllers\UserController;
use App\Config\App;

// Halaman Utama: Redirect cerdas berdasarkan status autentikasi
$router->get('/', [AuthController::class, 'rootRedirect']);

// Autentikasi
$router->get('/login', [AuthController::class, 'showLogin']);
$router->post('/login', [AuthController::class, 'login']);
$router->get('/logout', [AuthController::class, 'logout']);

// Modul POS (Kasir & Admin)
$router->get('/pos', [PosController::class, 'index'], ['auth']);
$router->post('/pos/checkout', [PosController::class, 'checkout'], ['auth']);

// Modul Etalase Digital Twin (Kasir & Admin)
$router->get('/digital-twin', [DigitalTwinController::class, 'index'], ['auth']);
$router->get('/digital-twin/api', [DigitalTwinController::class, 'apiData'], ['auth']);

// Riwayat Transaksi & Struk (Kasir & Admin)
$router->get('/transactions', [TransactionController::class, 'index'], ['auth']);
$router->get('/transactions/detail/{id}', [TransactionController::class, 'detail'], ['auth']);
$router->get('/transactions/receipt/{id}', [TransactionController::class, 'receipt'], ['auth']);

// Modul Khusus Administrator Toko
$router->get('/dashboard', [DashboardController::class, 'index'], ['auth', 'admin']);

// Manajemen Kategori
$router->get('/categories', [CategoryController::class, 'index'], ['auth', 'admin']);
$router->post('/categories/store', [CategoryController::class, 'store'], ['auth', 'admin']);
$router->post('/categories/update/{id}', [CategoryController::class, 'update'], ['auth', 'admin']);
$router->post('/categories/delete/{id}', [CategoryController::class, 'delete'], ['auth', 'admin']);

// Manajemen Produk & Varian
$router->get('/products', [ProductController::class, 'index'], ['auth', 'admin']);
$router->get('/products/create', [ProductController::class, 'create'], ['auth', 'admin']);
$router->post('/products/store', [ProductController::class, 'store'], ['auth', 'admin']);
$router->get('/products/edit/{id}', [ProductController::class, 'edit'], ['auth', 'admin']);
$router->post('/products/update/{id}', [ProductController::class, 'update'], ['auth', 'admin']);
$router->post('/products/delete/{id}', [ProductController::class, 'delete'], ['auth', 'admin']);
$router->post('/products/{id}/variant/add', [ProductController::class, 'addVariant'], ['auth', 'admin']);
$router->post('/products/variant/delete/{id}', [ProductController::class, 'deleteVariant'], ['auth', 'admin']);

// Manajemen Stok & Pergerakan
$router->get('/stock/restock', [StockController::class, 'restock'], ['auth', 'admin']);
$router->post('/stock/restock', [StockController::class, 'restock'], ['auth', 'admin']);
$router->get('/stock/adjustment', [StockController::class, 'adjustment'], ['auth', 'admin']);
$router->post('/stock/adjustment', [StockController::class, 'adjustment'], ['auth', 'admin']);
$router->get('/stock/movements', [StockController::class, 'movements'], ['auth', 'admin']);

// Laporan Penjualan & Analitik
$router->get('/reports', [ReportController::class, 'index'], ['auth', 'admin']);
$router->get('/reports/export', [ReportController::class, 'exportCsv'], ['auth', 'admin']);
$router->get('/reports/print', [ReportController::class, 'printView'], ['auth', 'admin']);

// Manajemen Pengguna & Akun Kasir
$router->get('/users', [UserController::class, 'index'], ['auth', 'admin']);
$router->post('/users/store', [UserController::class, 'store'], ['auth', 'admin']);
$router->post('/users/toggle/{id}', [UserController::class, 'toggle'], ['auth', 'admin']);
