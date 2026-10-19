/**
 * stockRoutes.js — Routing Endpoint Mutasi Stok & QR Code Scanner
 * Pengembang: Rana Shofa (Backend & Database Developer)
 */

const express = require('express');
const router = express.Router();
const stockController = require('../controllers/stockController');
const { verifyToken } = require('../middlewares/authMiddleware');

// Endpoint Transaksi Mutasi Stok (Bisa diakses oleh Admin atau Petugas yang login)
router.post('/in', verifyToken, stockController.stockIn);
router.post('/out', verifyToken, stockController.stockOut);

// Endpoint Riwayat Transaksi Mutasi Stok
router.get('/transactions', stockController.getTransactions);

module.exports = router;
