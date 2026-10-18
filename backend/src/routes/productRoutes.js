/**
 * productRoutes.js — Routing Endpoint Produk & Varian
 * Pengembang: Rana Shofa (Backend & Database Developer)
 */

const express = require('express');
const router = express.Router();
const productController = require('../controllers/productController');
const { verifyToken, requireAdmin } = require('../middlewares/authMiddleware');

// Endpoint Public / Read-Only (Bisa diakses untuk tampilan katalog & frontend)
router.get('/', productController.getAllProducts);
router.get('/:id', productController.getProductById);

// Endpoint Terproteksi (Hanya user yang login yang bisa menambah / mengedit produk)
router.post('/', verifyToken, productController.createProduct);
router.put('/:id', verifyToken, productController.updateProduct);
router.post('/:id/variants', verifyToken, productController.addVariant);

// Khusus Admin yang bisa menghapus produk
router.delete('/:id', verifyToken, requireAdmin, productController.deleteProduct);

module.exports = router;
