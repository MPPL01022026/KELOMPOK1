/**
 * dashboardController.js — Ringkasan Statistik untuk Dashboard & Rak Digital
 * Pengembang: Rana Shofa (Backend & Database Developer)
 *
 * Menghasilkan data yang 100% selaras dengan kebutuhan Frontend Arya (dashboard.js)
 */

const { pool } = require('../config/database');
const { getStockStatus } = require('./productController');

/**
 * GET DASHBOARD STATS
 * Endpoint: GET /api/dashboard/stats
 */
async function getDashboardStats(req, res) {
    try {
        // 1. Ringkasan Kartu Statistik Utama
        const [counts] = await pool.query(`
            SELECT 
                COUNT(DISTINCT p.id) as total_products,
                COUNT(DISTINCT v.id) as total_variants,
                COALESCE(SUM(v.stok), 0) as total_stock,
                COALESCE(SUM(CASE WHEN v.stok <= v.stok_minimum THEN 1 ELSE 0 END), 0) as critical_stock
            FROM products p
            LEFT JOIN product_variants v ON p.id = v.produk_id
        `);

        const stats = {
            totalProducts: counts[0].total_products || 0,
            totalVariants: counts[0].total_variants || 0,
            totalStock: Number(counts[0].total_stock || 0),
            criticalStock: Number(counts[0].critical_stock || 0)
        };

        // 2. Stok per Kategori (Untuk Bar Chart Frontend)
        const [categories] = await pool.query(`
            SELECT 
                c.nama_kategori as name,
                COALESCE(SUM(v.stok), 0) as stock,
                c.warna_label as color
            FROM categories c
            LEFT JOIN products p ON c.id = p.kategori_id
            LEFT JOIN product_variants v ON p.id = v.produk_id
            GROUP BY c.id, c.nama_kategori, c.warna_label
            ORDER BY c.id ASC
        `);

        // 3. Distribusi Status Stok (Untuk Donut Chart & Legend)
        const [variantsStatus] = await pool.query(`
            SELECT 
                SUM(CASE WHEN stok >= 10 THEN 1 ELSE 0 END) as aman_count,
                SUM(CASE WHEN stok >= 4 AND stok <= 9 THEN 1 ELSE 0 END) as waspada_count,
                SUM(CASE WHEN stok <= 3 THEN 1 ELSE 0 END) as kritis_count
            FROM product_variants
        `);

        const stockStatus = {
            aman: {
                label: 'Aman (≥ 10)',
                count: Number(variantsStatus[0].aman_count || 0),
                color: '#10B981'
            },
            waspada: {
                label: 'Waspada (4 – 9)',
                count: Number(variantsStatus[0].waspada_count || 0),
                color: '#F59E0B'
            },
            kritis: {
                label: 'Kritis (≤ 3)',
                count: Number(variantsStatus[0].kritis_count || 0),
                color: '#EF4444'
            }
        };

        // 4. Produk Stok Terendah (Untuk Tabel Peringatan Restok)
        const [lowStockRows] = await pool.query(`
            SELECT 
                p.kode_produk as code,
                p.nama_produk as name,
                c.nama_kategori as category,
                CONCAT(v.ukuran, ' / ', v.warna) as variant,
                v.stok as stock,
                v.stok_minimum
            FROM product_variants v
            JOIN products p ON v.produk_id = p.id
            JOIN categories c ON p.kategori_id = c.id
            WHERE v.stok <= 9
            ORDER BY v.stok ASC
            LIMIT 10
        `);

        const lowStockProducts = lowStockRows.map(row => ({
            code: row.code,
            name: row.name,
            category: row.category,
            variant: row.variant,
            stock: row.stock,
            status: getStockStatus(row.stock, row.stok_minimum)
        }));

        // 5. Transaksi Stok Terbaru
        const [txRows] = await pool.query(`
            SELECT 
                p.kode_produk as code,
                p.nama_produk as product,
                t.tipe as type,
                CASE WHEN t.tipe = 'masuk' THEN t.jumlah ELSE -t.jumlah END as amount,
                DATE_FORMAT(t.created_at, '%d/%m/%Y %H:%i') as time,
                COALESCE(u.nama, 'Admin') as officer
            FROM stock_transactions t
            JOIN product_variants v ON t.varian_id = v.id
            JOIN products p ON v.produk_id = p.id
            LEFT JOIN users u ON t.user_id = u.id
            ORDER BY t.created_at DESC
            LIMIT 5
        `);

        return res.status(200).json({
            status: 'success',
            data: {
                stats,
                categories,
                stockStatus,
                lowStockProducts,
                recentTransactions: txRows
            }
        });

    } catch (error) {
        console.error('Error saat mengambil statistik dashboard:', error);
        return res.status(500).json({
            status: 'error',
            message: 'Gagal mengambil data statistik dashboard',
            error: error.message
        });
    }
}

module.exports = {
    getDashboardStats
};
