/**
 * authRoutes.js — Routing Endpoint Autentikasi & Akun
 * Pengembang: Rana Shofa (Backend & Database Developer)
 */

const express = require('express');
const router = express.Router();
const authController = require('../controllers/authController');
const { verifyToken, requireAdmin } = require('../middlewares/authMiddleware');

// Public Route (Bebas diakses tanpa login)
router.post('/login', authController.login);

// Protected Routes (Wajib membawa Token JWT)
router.get('/me', verifyToken, authController.getMe);

// Admin Only Routes (Hanya untuk Admin)
router.post('/register', verifyToken, requireAdmin, authController.register);
router.get('/users', verifyToken, requireAdmin, authController.getAllUsers);

module.exports = router;
