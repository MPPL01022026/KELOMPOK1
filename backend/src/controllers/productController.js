/**
 * productController.js — Pengelolaan Master Produk & Varian Pakaian
 * Pengembang: Rana Shofa (Backend & Database Developer)
 */

const { pool } = require('../config/database');

/**
 * Helper untuk menentukan status stok berdasarkan jumlah
 * - Hijau  (Aman)    : stok >= 10
 * - Kuning (Waspada) : stok 4 - 9
 * - Merah  (Kritis)  : stok <= 3
 */
function getStockStatus(stok, stokMinimum = 3) {
    if (stok <= stokMinimum) return 'kritis';
    if (stok < 10) return 'waspada';
    return 'aman';
}

/**
 * 1. GET ALL PRODUCTS
 * Endpoint: GET /api/products
 * Query Params: ?search= &kategori_id= &status=
 */
async function getAllProducts(req, res) {
    try {
        const { search, kategori_id, status } = req.query;

        let query = `
            SELECT p.id, p.kode_produk, p.nama_produk, p.deskripsi, p.lokasi_rak, p.created_at,
                   c.id as kategori_id, c.nama_kategori, c.warna_label as kategori_warna,
                   COALESCE(SUM(v.stok), 0) as total_stok,
                   COUNT(v.id) as total_varian
            FROM products p
            JOIN categories c ON p.kategori_id = c.id
            LEFT JOIN product_variants v ON p.id = v.produk_id
            WHERE 1=1
        `;
        const params = [];

        if (search) {
            query += ` AND (p.nama_produk LIKE ? OR p.kode_produk LIKE ? OR p.lokasi_rak LIKE ?)`;
            params.push(`%${search}%`, `%${search}%`, `%${search}%`);
        }

        if (kategori_id) {
            query += ` AND p.kategori_id = ?`;
            params.push(kategori_id);
        }

        query += ` GROUP BY p.id, c.id ORDER BY p.created_at DESC`;

        const [products] = await pool.query(query, params);

        // Ambil semua varian produk untuk melengkapi data produk
        const [variants] = await pool.query(`
            SELECT id, produk_id, sku, ukuran, warna, stok, stok_minimum, qr_code_payload
            FROM product_variants
            ORDER BY id ASC
        `);

        // Petakan varian ke masing-masing produk
        const productsWithVariants = products.map(prod => {
            const prodVariants = variants
                .filter(v => v.produk_id === prod.id)
                .map(v => ({
                    ...v,
                    status: getStockStatus(v.stok, v.stok_minimum)
                }));

            // Tentukan status ringkasan produk (jika salah satu varian kritis -> produk kritis)
            let overallStatus = 'aman';
            if (prodVariants.some(v => v.status === 'kritis')) {
                overallStatus = 'kritis';
            } else if (prodVariants.some(v => v.status === 'waspada')) {
                overallStatus = 'waspada';
            }

            return {
                ...prod,
                overall_status: overallStatus,
                variants: prodVariants
            };
        });

        // Filter berdasarkan status jika diminta
        let filtered = productsWithVariants;
        if (status) {
            filtered = productsWithVariants.filter(p => p.overall_status === status);
        }

        return res.status(200).json({
            status: 'success',
            total: filtered.length,
            data: filtered
        });

    } catch (error) {
        console.error('Error saat mengambil produk:', error);
        return res.status(500).json({
            status: 'error',
            message: 'Gagal mengambil data produk',
            error: error.message
        });
    }
}

/**
 * 2. GET PRODUCT BY ID
 * Endpoint: GET /api/products/:id
 */
async function getProductById(req, res) {
    try {
        const { id } = req.params;

        const [products] = await pool.query(`
            SELECT p.*, c.nama_kategori, c.warna_label as kategori_warna
            FROM products p
            JOIN categories c ON p.kategori_id = c.id
            WHERE p.id = ?
        `, [id]);

        if (products.length === 0) {
            return res.status(404).json({
                status: 'error',
                message: 'Produk tidak ditemukan'
            });
        }

        const product = products[0];

        // Ambil varian produk
        const [variants] = await pool.query(`
            SELECT id, sku, ukuran, warna, stok, stok_minimum, qr_code_payload
            FROM product_variants
            WHERE produk_id = ?
        `, [id]);

        product.variants = variants.map(v => ({
            ...v,
            status: getStockStatus(v.stok, v.stok_minimum)
        }));

        return res.status(200).json({
            status: 'success',
            data: product
        });

    } catch (error) {
        return res.status(500).json({
            status: 'error',
            message: 'Gagal mengambil detail produk',
            error: error.message
        });
    }
}

/**
 * 3. CREATE PRODUCT (Beserta Varian Awal)
 * Endpoint: POST /api/products
 * Body: { kode_produk, nama_produk, kategori_id, deskripsi, lokasi_rak, variants: [...] }
 */
async function createProduct(req, res) {
    const connection = await pool.getConnection();
    try {
        await connection.beginTransaction();

        const { kode_produk, nama_produk, kategori_id, deskripsi, lokasi_rak, variants } = req.body;

        if (!kode_produk || !nama_produk || !kategori_id) {
            await connection.rollback();
            return res.status(400).json({
                status: 'error',
                message: 'Kode produk, nama produk, dan kategori wajib diisi!'
            });
        }

        // Cek keunikan kode_produk
        const [existing] = await connection.query(
            'SELECT id FROM products WHERE kode_produk = ?',
            [kode_produk]
        );
        if (existing.length > 0) {
            await connection.rollback();
            return res.status(409).json({
                status: 'error',
                message: `Kode produk '${kode_produk}' sudah digunakan!`
            });
        }

        // Simpan master produk
        const [prodResult] = await connection.query(`
            INSERT INTO products (kode_produk, nama_produk, kategori_id, deskripsi, lokasi_rak)
            VALUES (?, ?, ?, ?, ?)
        `, [kode_produk.trim().toUpperCase(), nama_produk, kategori_id, deskripsi || null, lokasi_rak || null]);

        const productId = prodResult.insertId;
        const insertedVariants = [];

        // Simpan varian jika disertakan
        if (Array.isArray(variants) && variants.length > 0) {
            for (const v of variants) {
                // Auto generate SKU jika tidak diisi: KODE-UKURAN-WARNA
                const sku = v.sku || `${kode_produk}-${v.ukuran}-${v.warna}`.toUpperCase().replace(/\s+/g, '');
                const qrPayload = v.qr_code_payload || sku;
                const stok = Math.max(0, parseInt(v.stok || 0, 10));
                const stokMin = parseInt(v.stok_minimum || 3, 10);

                const [varResult] = await connection.query(`
                    INSERT INTO product_variants (produk_id, sku, ukuran, warna, stok, stok_minimum, qr_code_payload)
                    VALUES (?, ?, ?, ?, ?, ?, ?)
                `, [productId, sku, v.ukuran, v.warna, stok, stokMin, qrPayload]);

                insertedVariants.push({
                    id: varResult.insertId,
                    sku,
                    ukuran: v.ukuran,
                    warna: v.warna,
                    stok,
                    stok_minimum: stokMin,
                    status: getStockStatus(stok, stokMin)
                });
            }
        }

        await connection.commit();

        return res.status(201).json({
            status: 'success',
            message: 'Produk dan varian berhasil ditambahkan!',
            data: {
                id: productId,
                kode_produk: kode_produk.trim().toUpperCase(),
                nama_produk,
                kategori_id,
                lokasi_rak,
                variants: insertedVariants
            }
        });

    } catch (error) {
        await connection.rollback();
        console.error('Error saat membuat produk:', error);
        return res.status(500).json({
            status: 'error',
            message: 'Gagal membuat produk baru',
            error: error.message
        });
    } finally {
        connection.release();
    }
}

/**
 * 4. UPDATE PRODUCT
 * Endpoint: PUT /api/products/:id
 */
async function updateProduct(req, res) {
    try {
        const { id } = req.params;
        const { nama_produk, kategori_id, deskripsi, lokasi_rak } = req.body;

        const [prod] = await pool.query('SELECT id FROM products WHERE id = ?', [id]);
        if (prod.length === 0) {
            return res.status(404).json({
                status: 'error',
                message: 'Produk tidak ditemukan'
            });
        }

        await pool.query(`
            UPDATE products
            SET nama_produk = COALESCE(?, nama_produk),
                kategori_id = COALESCE(?, kategori_id),
                deskripsi   = COALESCE(?, deskripsi),
                lokasi_rak  = COALESCE(?, lokasi_rak)
            WHERE id = ?
        `, [nama_produk, kategori_id, deskripsi, lokasi_rak, id]);

        return res.status(200).json({
            status: 'success',
            message: 'Data produk berhasil diperbarui'
        });

    } catch (error) {
        return res.status(500).json({
            status: 'error',
            message: 'Gagal memperbarui data produk',
            error: error.message
        });
    }
}

/**
 * 5. DELETE PRODUCT
 * Endpoint: DELETE /api/products/:id
 */
async function deleteProduct(req, res) {
    try {
        const { id } = req.params;

        const [prod] = await pool.query('SELECT id, nama_produk FROM products WHERE id = ?', [id]);
        if (prod.length === 0) {
            return res.status(404).json({
                status: 'error',
                message: 'Produk tidak ditemukan'
            });
        }

        // Karena FOREIGN KEY cascade, varian otomatis terhapus
        await pool.query('DELETE FROM products WHERE id = ?', [id]);

        return res.status(200).json({
            status: 'success',
            message: `Produk '${prod[0].nama_produk}' dan seluruh variannya berhasil dihapus`
        });

    } catch (error) {
        return res.status(500).json({
            status: 'error',
            message: 'Gagal menghapus produk',
            error: error.message
        });
    }
}

/**
 * 6. TAMBAH VARIAN KE PRODUK TERTENTU
 * Endpoint: POST /api/products/:id/variants
 */
async function addVariant(req, res) {
    try {
        const { id } = req.params;
        const { sku, ukuran, warna, stok, stok_minimum } = req.body;

        if (!ukuran || !warna) {
            return res.status(400).json({
                status: 'error',
                message: 'Ukuran dan warna varian wajib diisi!'
            });
        }

        const [prod] = await pool.query('SELECT kode_produk FROM products WHERE id = ?', [id]);
        if (prod.length === 0) {
            return res.status(404).json({
                status: 'error',
                message: 'Produk tidak ditemukan'
            });
        }

        const finalSku = sku || `${prod[0].kode_produk}-${ukuran}-${warna}`.toUpperCase().replace(/\s+/g, '');
        const finalStok = Math.max(0, parseInt(stok || 0, 10));
        const finalStokMin = parseInt(stok_minimum || 3, 10);

        const [result] = await pool.query(`
            INSERT INTO product_variants (produk_id, sku, ukuran, warna, stok, stok_minimum, qr_code_payload)
            VALUES (?, ?, ?, ?, ?, ?, ?)
        `, [id, finalSku, ukuran, warna, finalStok, finalStokMin, finalSku]);

        return res.status(201).json({
            status: 'success',
            message: 'Varian berhasil ditambahkan',
            data: {
                id: result.insertId,
                produk_id: id,
                sku: finalSku,
                ukuran,
                warna,
                stok: finalStok,
                stok_minimum: finalStokMin,
                status: getStockStatus(finalStok, finalStokMin)
            }
        });

    } catch (error) {
        if (error.code === 'ER_DUP_ENTRY') {
            return res.status(409).json({
                status: 'error',
                message: 'SKU varian ini sudah digunakan!'
            });
        }
        return res.status(500).json({
            status: 'error',
            message: 'Gagal menambahkan varian produk',
            error: error.message
        });
    }
}

module.exports = {
    getAllProducts,
    getProductById,
    createProduct,
    updateProduct,
    deleteProduct,
    addVariant,
    getStockStatus
};
