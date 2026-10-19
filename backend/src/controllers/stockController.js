/**
 * stockController.js — Logika Mutasi Stok & Integrasi QR Code Scanner
 * Pengembang: Rana Shofa (Backend & Database Developer)
 *
 * Mengatur:
 * - Pencatatan barang masuk (Restok)
 * - Pencatatan barang keluar (Penjualan/Pengeluaran)
 * - Mencegah stok menjadi negatif (Validasi transaksi database atomik)
 * - Riwayat transaksi mutasi stok
 * - Lookup varian via payload QR Code (untuk integrasi Salsabila)
 * - Emit real-time WebSocket update via Socket.IO
 */

const { pool } = require('../config/database');
const { emitStockUpdate } = require('../config/socket');
const { getStockStatus } = require('./productController');

/**
 * Helper untuk membuat kode transaksi unik
 * Format: TRX-[IN/OUT]-YYYYMMDD-XXXX
 */
function generateTrxCode(type) {
    const prefix = type.toUpperCase() === 'MASUK' ? 'TRX-IN' : 'TRX-OUT';
    const dateStr = new Date().toISOString().slice(0, 10).replace(/-/g, '');
    const randomHex = Math.floor(Math.random() * 9000 + 1000);
    return `${prefix}-${dateStr}-${randomHex}`;
}

/**
 * 1. PENCATATAN BARANG MASUK (RESTOK)
 * Endpoint: POST /api/stock/in
 * Body: { varian_id (atau sku/qr_payload), jumlah, keterangan }
 */
async function stockIn(req, res) {
    const connection = await pool.getConnection();
    try {
        await connection.beginTransaction();

        const { varian_id, sku, qr_payload, jumlah, keterangan } = req.body;
        const qty = parseInt(jumlah, 10);

        if (!qty || qty <= 0) {
            await connection.rollback();
            return res.status(400).json({
                status: 'error',
                message: 'Jumlah barang masuk harus berupa angka positif lebih dari 0!'
            });
        }

        // Cari varian berdasarkan varian_id atau sku atau qr_payload dengan Row-Level Lock (FOR UPDATE)
        let queryVariant = 'SELECT v.*, p.nama_produk, p.kode_produk, p.lokasi_rak FROM product_variants v JOIN products p ON v.produk_id = p.id WHERE ';
        let param;

        if (varian_id) {
            queryVariant += 'v.id = ? FOR UPDATE';
            param = varian_id;
        } else if (sku) {
            queryVariant += 'v.sku = ? FOR UPDATE';
            param = sku;
        } else if (qr_payload) {
            queryVariant += 'v.qr_code_payload = ? FOR UPDATE';
            param = qr_payload;
        } else {
            await connection.rollback();
            return res.status(400).json({
                status: 'error',
                message: 'varian_id, sku, atau qr_payload wajib dikirim!'
            });
        }

        const [variants] = await connection.query(queryVariant, [param]);
        if (variants.length === 0) {
            await connection.rollback();
            return res.status(404).json({
                status: 'error',
                message: 'Varian produk tidak ditemukan dalam sistem!'
            });
        }

        const variant = variants[0];
        const stokSebelum = variant.stok;
        const stokSesudah = stokSebelum + qty;
        const userId = req.user ? req.user.id : null;
        const kodeTrx = generateTrxCode('MASUK');

        // 1. Update stok varian
        await connection.query(
            'UPDATE product_variants SET stok = ? WHERE id = ?',
            [stokSesudah, variant.id]
        );

        // 2. Catat ke tabel riwayat transaksi
        await connection.query(`
            INSERT INTO stock_transactions 
            (kode_transaksi, varian_id, user_id, tipe, jumlah, stok_sebelum, stok_sesudah, keterangan)
            VALUES (?, ?, ?, 'masuk', ?, ?, ?, ?)
        `, [kodeTrx, variant.id, userId, qty, stokSebelum, stokSesudah, keterangan || 'Penerimaan barang masuk']);

        await connection.commit();

        // 3. Pancarkan update stok real-time ke Socket.IO
        const updateData = {
            tipe: 'masuk',
            kode_transaksi: kodeTrx,
            produk_id: variant.produk_id,
            varian_id: variant.id,
            nama_produk: variant.nama_produk,
            sku: variant.sku,
            ukuran: variant.ukuran,
            warna: variant.warna,
            lokasi_rak: variant.lokasi_rak,
            jumlah_mutasi: qty,
            stok_sebelum: stokSebelum,
            stok_sesudah: stokSesudah,
            status_stok: getStockStatus(stokSesudah, variant.stok_minimum)
        };
        emitStockUpdate(updateData);

        return res.status(200).json({
            status: 'success',
            message: `Berhasil mencatat stok masuk (+${qty}) untuk ${variant.nama_produk} [${variant.ukuran}/${variant.warna}]`,
            data: updateData
        });

    } catch (error) {
        await connection.rollback();
        console.error('Error transaksi stok masuk:', error);
        return res.status(500).json({
            status: 'error',
            message: 'Terjadi kesalahan sistem saat memproses stok masuk',
            error: error.message
        });
    } finally {
        connection.release();
    }
}

/**
 * 2. PENCATATAN BARANG KELUAR (PENJUALAN/PENGELUARAN)
 * Endpoint: POST /api/stock/out
 * Body: { varian_id (atau sku/qr_payload), jumlah, keterangan }
 *
 * ⚠️ VALIDASI UTAMA: Memastikan stok tidak menjadi negatif!
 */
async function stockOut(req, res) {
    const connection = await pool.getConnection();
    try {
        await connection.beginTransaction();

        const { varian_id, sku, qr_payload, jumlah, keterangan } = req.body;
        const qty = parseInt(jumlah, 10);

        if (!qty || qty <= 0) {
            await connection.rollback();
            return res.status(400).json({
                status: 'error',
                message: 'Jumlah barang keluar harus berupa angka positif lebih dari 0!'
            });
        }

        // Cari varian dengan Row-Level Lock (FOR UPDATE)
        let queryVariant = 'SELECT v.*, p.nama_produk, p.kode_produk, p.lokasi_rak FROM product_variants v JOIN products p ON v.produk_id = p.id WHERE ';
        let param;

        if (varian_id) {
            queryVariant += 'v.id = ? FOR UPDATE';
            param = varian_id;
        } else if (sku) {
            queryVariant += 'v.sku = ? FOR UPDATE';
            param = sku;
        } else if (qr_payload) {
            queryVariant += 'v.qr_code_payload = ? FOR UPDATE';
            param = qr_payload;
        } else {
            await connection.rollback();
            return res.status(400).json({
                status: 'error',
                message: 'varian_id, sku, atau qr_payload wajib dikirim!'
            });
        }

        const [variants] = await connection.query(queryVariant, [param]);
        if (variants.length === 0) {
            await connection.rollback();
            return res.status(404).json({
                status: 'error',
                message: 'Varian produk tidak ditemukan dalam sistem!'
            });
        }

        const variant = variants[0];
        const stokSebelum = variant.stok;

        // =================================================================
        // ATURAN KRUSIAL: CEK APAKAH STOK MENCUKUPI (MENCEGAH STOK NEGATIF)
        // =================================================================
        if (stokSebelum < qty) {
            await connection.rollback();
            return res.status(400).json({
                status: 'error',
                error_code: 'INSUFFICIENT_STOCK',
                message: `Transaksi dibatalkan! Stok tidak mencukupi untuk ${variant.nama_produk} [${variant.ukuran}/${variant.warna}]. Sisa stok saat ini hanya ${stokSebelum}, permintaan pengeluaran: ${qty}. Stok tidak boleh negatif.`
            });
        }

        const stokSesudah = stokSebelum - qty;
        const userId = req.user ? req.user.id : null;
        const kodeTrx = generateTrxCode('KELUAR');

        // 1. Update stok varian
        await connection.query(
            'UPDATE product_variants SET stok = ? WHERE id = ?',
            [stokSesudah, variant.id]
        );

        // 2. Catat ke tabel riwayat transaksi
        await connection.query(`
            INSERT INTO stock_transactions 
            (kode_transaksi, varian_id, user_id, tipe, jumlah, stok_sebelum, stok_sesudah, keterangan)
            VALUES (?, ?, ?, 'keluar', ?, ?, ?, ?)
        `, [kodeTrx, variant.id, userId, qty, stokSebelum, stokSesudah, keterangan || 'Pengeluaran/penjualan barang']);

        await connection.commit();

        // 3. Status peringatan kritis jika stok sesudah <= 3
        const statusStok = getStockStatus(stokSesudah, variant.stok_minimum);
        const isCritical = statusStok === 'kritis';

        // 4. Pancarkan update stok real-time ke Socket.IO
        const updateData = {
            tipe: 'keluar',
            kode_transaksi: kodeTrx,
            produk_id: variant.produk_id,
            varian_id: variant.id,
            nama_produk: variant.nama_produk,
            sku: variant.sku,
            ukuran: variant.ukuran,
            warna: variant.warna,
            lokasi_rak: variant.lokasi_rak,
            jumlah_mutasi: -qty,
            stok_sebelum: stokSebelum,
            stok_sesudah: stokSesudah,
            status_stok: statusStok,
            is_critical: isCritical
        };
        emitStockUpdate(updateData);

        return res.status(200).json({
            status: 'success',
            message: `Berhasil mencatat stok keluar (-${qty}) untuk ${variant.nama_produk} [${variant.ukuran}/${variant.warna}]`,
            warning: isCritical ? '⚠️ PERINGATAN: Stok varian ini telah mencapai batas KRITIS (<= 3)!' : null,
            data: updateData
        });

    } catch (error) {
        await connection.rollback();
        console.error('Error transaksi stok keluar:', error);
        return res.status(500).json({
            status: 'error',
            message: 'Terjadi kesalahan sistem saat memproses stok keluar',
            error: error.message
        });
    } finally {
        connection.release();
    }
}

/**
 * 3. RIWAYAT TRANSAKSI STOK
 * Endpoint: GET /api/stock/transactions
 * Query: ?tipe= &limit= &search=
 */
async function getTransactions(req, res) {
    try {
        const { tipe, limit, search } = req.query;
        const maxLimit = parseInt(limit || 50, 10);

        let query = `
            SELECT t.id, t.kode_transaksi, t.tipe, t.jumlah, t.stok_sebelum, t.stok_sesudah,
                   t.keterangan, t.created_at,
                   v.id as varian_id, v.sku, v.ukuran, v.warna,
                   p.id as produk_id, p.kode_produk, p.nama_produk, p.lokasi_rak,
                   c.nama_kategori,
                   COALESCE(u.nama, 'Admin') as petugas_nama
            FROM stock_transactions t
            JOIN product_variants v ON t.varian_id = v.id
            JOIN products p ON v.produk_id = p.id
            JOIN categories c ON p.kategori_id = c.id
            LEFT JOIN users u ON t.user_id = u.id
            WHERE 1=1
        `;
        const params = [];

        if (tipe && (tipe === 'masuk' || tipe === 'keluar')) {
            query += ` AND t.tipe = ?`;
            params.push(tipe);
        }

        if (search) {
            query += ` AND (p.nama_produk LIKE ? OR p.kode_produk LIKE ? OR v.sku LIKE ? OR t.kode_transaksi LIKE ?)`;
            params.push(`%${search}%`, `%${search}%`, `%${search}%`, `%${search}%`);
        }

        query += ` ORDER BY t.created_at DESC LIMIT ?`;
        params.push(maxLimit);

        const [transactions] = await pool.query(query, params);

        return res.status(200).json({
            status: 'success',
            total: transactions.length,
            data: transactions
        });

    } catch (error) {
        console.error('Error saat mengambil riwayat transaksi:', error);
        return res.status(500).json({
            status: 'error',
            message: 'Gagal mengambil riwayat transaksi stok',
            error: error.message
        });
    }
}

/**
 * 4. ENDPOINT LOOKUP SCAN QR CODE (UNTUK SALSABILA)
 * Endpoint: GET /api/variants/scan/:code
 * Menerima payload teks hasil pemindaian scanner QR Code html5-qrcode
 */
async function scanVariantQR(req, res) {
    try {
        const { code } = req.params;

        if (!code) {
            return res.status(400).json({
                status: 'error',
                message: 'Kode QR tidak boleh kosong!'
            });
        }

        const [rows] = await pool.query(`
            SELECT v.id as varian_id, v.sku, v.ukuran, v.warna, v.stok, v.stok_minimum, v.qr_code_payload,
                   p.id as produk_id, p.kode_produk, p.nama_produk, p.deskripsi, p.lokasi_rak,
                   c.id as kategori_id, c.nama_kategori, c.warna_label as kategori_warna
            FROM product_variants v
            JOIN products p ON v.produk_id = p.id
            JOIN categories c ON p.kategori_id = c.id
            WHERE v.qr_code_payload = ? OR v.sku = ?
            LIMIT 1
        `, [code.trim(), code.trim()]);

        if (rows.length === 0) {
            return res.status(404).json({
                status: 'error',
                message: `QR Code '${code}' tidak valid atau produk belum terdaftar dalam sistem toko!`
            });
        }

        const data = rows[0];
        data.status_stok = getStockStatus(data.stok, data.stok_minimum);

        return res.status(200).json({
            status: 'success',
            message: 'Data produk berhasil dikenali dari QR Code',
            data
        });

    } catch (error) {
        console.error('Error saat scan QR varian:', error);
        return res.status(500).json({
            status: 'error',
            message: 'Gagal mengenali QR Code produk',
            error: error.message
        });
    }
}

module.exports = {
    stockIn,
    stockOut,
    getTransactions,
    scanVariantQR
};
