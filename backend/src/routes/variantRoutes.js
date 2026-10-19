/**
 * variantRoutes.js — Routing Endpoint Varian & QR Code Scanner
 * Pengembang: Rana Shofa (Backend & Database Developer)
 */

const express = require('express');
const router = express.Router();
const stockController = require('../controllers/stockController');

// Endpoint Khusus Pemindaian QR Code (Salsabila - html5-qrcode)
// Bebas diakses scanner kamera secara instan tanpa delay
router.get('/scan/:code', stockController.scanVariantQR);

module.exports = router;
