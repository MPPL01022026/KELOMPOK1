/**
 * authMiddleware.js — Middleware Autentikasi & Pengaturan Hak Akses
 * Pengembang: Rana Shofa (Backend & Database Developer)
 */

const jwt = require('jsonwebtoken');
require('dotenv').config();

/**
 * Middleware untuk memverifikasi apakah request membawa Token JWT yang valid
 */
function verifyToken(req, res, next) {
    const authHeader = req.headers['authorization'];
    
    // Header format: "Bearer <token>"
    if (!authHeader || !authHeader.startsWith('Bearer ')) {
        return res.status(401).json({
            status: 'error',
            message: 'Akses ditolak! Token autentikasi tidak ditemukan.'
        });
    }

    const token = authHeader.split(' ')[1];

    try {
        const decoded = jwt.verify(token, process.env.JWT_SECRET || 'yunda_fashion_secret_key_2026_mppl');
        // Lampirkan data user ke object request
        req.user = decoded;
        next();
    } catch (error) {
        return res.status(401).json({
            status: 'error',
            message: 'Token tidak valid atau sudah kedaluwarsa. Silakan login kembali.'
        });
    }
}

/**
 * Middleware untuk membatasi akses hanya untuk role 'admin'
 */
function requireAdmin(req, res, next) {
    if (!req.user || req.user.role !== 'admin') {
        return res.status(403).json({
            status: 'error',
            message: 'Akses terlarang! Tindakan ini hanya dapat dilakukan oleh Administrator.'
        });
    }
    next();
}

module.exports = {
    verifyToken,
    requireAdmin
};
