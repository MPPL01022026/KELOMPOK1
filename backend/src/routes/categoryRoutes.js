/**
 * categoryRoutes.js — Routing Endpoint Kategori Produk
 * Pengembang: Rana Shofa (Backend & Database Developer)
 */

const express = require('express');
const router = express.Router();
const categoryController = require('../controllers/categoryController');
const { verifyToken, requireAdmin } = require('../middlewares/authMiddleware');

// Public: Siapa saja bisa melihat daftar kategori untuk filter produk
router.get('/', categoryController.getAllCategories);

// Admin Only: Hanya Admin yang bisa menambah kategori
router.post('/', verifyToken, requireAdmin, categoryController.createCategory);

module.exports = router;
