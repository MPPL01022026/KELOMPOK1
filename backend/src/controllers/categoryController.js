/**
 * categoryController.js — Pengelolaan Kategori Produk
 * Pengembang: Rana Shofa (Backend & Database Developer)
 */

const { pool } = require('../config/database');

/**
 * 1. GET ALL CATEGORIES
 * Endpoint: GET /api/categories
 */
async function getAllCategories(req, res) {
    try {
        const [categories] = await pool.query(
            `SELECT c.id, c.nama_kategori, c.warna_label, c.created_at,
                    COUNT(DISTINCT p.id) as total_produk,
                    COALESCE(SUM(v.stok), 0) as total_stok
             FROM categories c
             LEFT JOIN products p ON c.id = p.kategori_id
             LEFT JOIN product_variants v ON p.id = v.produk_id
             GROUP BY c.id, c.nama_kategori, c.warna_label, c.created_at
             ORDER BY c.id ASC`
        );

        return res.status(200).json({
            status: 'success',
            data: categories
        });
    } catch (error) {
        console.error('Error saat mengambil kategori:', error);
        return res.status(500).json({
            status: 'error',
            message: 'Gagal mengambil data kategori',
            error: error.message
        });
    }
}

/**
 * 2. CREATE CATEGORY (Khusus Admin)
 * Endpoint: POST /api/categories
 */
async function createCategory(req, res) {
    try {
        const { nama_kategori, warna_label } = req.body;

        if (!nama_kategori) {
            return res.status(400).json({
                status: 'error',
                message: 'Nama kategori wajib diisi!'
            });
        }

        const [existing] = await pool.query(
            'SELECT id FROM categories WHERE nama_kategori = ?',
            [nama_kategori]
        );

        if (existing.length > 0) {
            return res.status(409).json({
                status: 'error',
                message: `Kategori '${nama_kategori}' sudah ada!`
            });
        }

        const [result] = await pool.query(
            'INSERT INTO categories (nama_kategori, warna_label) VALUES (?, ?)',
            [nama_kategori, warna_label || '#7C3AED']
        );

        return res.status(201).json({
            status: 'success',
            message: 'Kategori baru berhasil ditambahkan',
            data: {
                id: result.insertId,
                nama_kategori,
                warna_label: warna_label || '#7C3AED'
            }
        });
    } catch (error) {
        return res.status(500).json({
            status: 'error',
            message: 'Gagal menambahkan kategori',
            error: error.message
        });
    }
}

module.exports = {
    getAllCategories,
    createCategory
};
