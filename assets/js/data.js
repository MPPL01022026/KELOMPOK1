/**
 * data.js — Data Dummy Yunda Fashion Inventory
 *
 * ⚠️ PERINGATAN: Seluruh data di bawah ini bersifat DUMMY / STATIS.
 * Data belum terhubung ke database MySQL apapun.
 * Pada tahap pengembangan berikutnya, file ini akan digantikan
 * oleh panggilan API ke backend Node.js + Express.js.
 *
 * Aturan Indikator Stok:
 *   - Hijau  (Aman)    : stok >= 10
 *   - Kuning (Waspada) : stok 4 – 9
 *   - Merah  (Kritis)  : stok <= 3
 */

const DUMMY_DATA = {

    /* --- Ringkasan Statistik --- */
    stats: {
        totalProducts: 48,
        totalVariants: 156,
        totalStock: 2340,
        criticalStock: 7
    },

    /* --- Stok per Kategori (untuk Bar Chart) --- */
    categories: [
        { name: 'Atasan',     stock: 540, color: '#7C3AED' },
        { name: 'Bawahan',    stock: 380, color: '#3B82F6' },
        { name: 'Dress',      stock: 420, color: '#EC4899' },
        { name: 'Outerwear',  stock: 310, color: '#F59E0B' },
        { name: 'Aksesoris',  stock: 290, color: '#10B981' }
    ],

    /* --- Status Stok (untuk Donut Chart & Legend) --- */
    stockStatus: {
        aman: {
            label: 'Aman (≥ 10)',
            count: 35,
            color: '#10B981'
        },
        waspada: {
            label: 'Waspada (4 – 9)',
            count: 6,
            color: '#F59E0B'
        },
        kritis: {
            label: 'Kritis (≤ 3)',
            count: 7,
            color: '#EF4444'
        }
    },

    /* --- Produk dengan Stok Terendah --- */
    lowStockProducts: [
        {
            code: 'YF-001',
            name: 'Kemeja Polos Premium',
            category: 'Atasan',
            variant: 'M / Putih',
            stock: 2
        },
        {
            code: 'YF-015',
            name: 'Celana Chino Slim',
            category: 'Bawahan',
            variant: '32 / Khaki',
            stock: 3
        },
        {
            code: 'YF-028',
            name: 'Dress Midi Floral',
            category: 'Dress',
            variant: 'M / Navy',
            stock: 3
        },
        {
            code: 'YF-042',
            name: 'Cardigan Rajut',
            category: 'Outerwear',
            variant: 'L / Cream',
            stock: 5
        },
        {
            code: 'YF-009',
            name: 'Blouse Batik Modern',
            category: 'Atasan',
            variant: 'S / Coklat',
            stock: 6
        },
        {
            code: 'YF-055',
            name: 'Rok Plisket',
            category: 'Bawahan',
            variant: 'S / Hitam',
            stock: 7
        },
        {
            code: 'YF-031',
            name: 'Hoodie Casual',
            category: 'Outerwear',
            variant: 'XL / Abu-abu',
            stock: 8
        }
    ],

    /* --- Transaksi Stok Terbaru --- */
    recentTransactions: [
        {
            code:    'YF-023',
            product: 'T-Shirt Basic Oversize',
            type:    'keluar',
            amount:  -3,
            time:    'Hari ini, 08:30',
            officer: 'Admin'
        },
        {
            code:    'YF-008',
            product: 'Jeans Slim Fit Navy',
            type:    'masuk',
            amount:  +20,
            time:    'Hari ini, 07:15',
            officer: 'Admin'
        },
        {
            code:    'YF-019',
            product: 'Dress Casual Motif',
            type:    'keluar',
            amount:  -2,
            time:    'Kemarin, 16:45',
            officer: 'Admin'
        },
        {
            code:    'YF-041',
            product: 'Jaket Bomber Army',
            type:    'masuk',
            amount:  +15,
            time:    'Kemarin, 14:20',
            officer: 'Admin'
        },
        {
            code:    'YF-003',
            product: 'Kemeja Flannel Kotak',
            type:    'keluar',
            amount:  -5,
            time:    'Kemarin, 11:30',
            officer: 'Admin'
        }
    ]
};
