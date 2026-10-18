/**
 * authController.js — Logika Bisnis Autentikasi & Akun Pengguna
 * Pengembang: Rana Shofa (Backend & Database Developer)
 */

const bcrypt = require('bcryptjs');
const jwt = require('jsonwebtoken');
const { pool } = require('../config/database');
require('dotenv').config();

/**
 * 1. LOGIN PENGGUNA
 * Endpoint: POST /api/auth/login
 * Body: { username (atau email), password }
 */
async function login(req, res) {
    try {
        const { username, password } = req.body;

        // Validasi input
        if (!username || !password) {
            return res.status(400).json({
                status: 'error',
                message: 'Username/Email dan Password wajib diisi!'
            });
        }

        // Cari pengguna berdasarkan username ATAU email
        const [rows] = await pool.query(
            'SELECT * FROM users WHERE username = ? OR email = ? LIMIT 1',
            [username, username]
        );

        if (rows.length === 0) {
            return res.status(401).json({
                status: 'error',
                message: 'Username atau Password salah!'
            });
        }

        const user = rows[0];

        // Verifikasi kecocokan password dengan bcrypt
        const isPasswordMatch = await bcrypt.compare(password, user.password);
        if (!isPasswordMatch) {
            return res.status(401).json({
                status: 'error',
                message: 'Username atau Password salah!'
            });
        }

        // Buat JWT Token
        const tokenPayload = {
            id: user.id,
            nama: user.nama,
            username: user.username,
            role: user.role
        };

        const token = jwt.sign(
            tokenPayload,
            process.env.JWT_SECRET || 'yunda_fashion_secret_key_2026_mppl',
            { expiresIn: process.env.JWT_EXPIRES_IN || '1d' }
        );

        return res.status(200).json({
            status: 'success',
            message: 'Login berhasil!',
            data: {
                token,
                user: {
                    id: user.id,
                    nama: user.nama,
                    username: user.username,
                    email: user.email,
                    role: user.role
                }
            }
        });

    } catch (error) {
        console.error('Error saat login:', error);
        return res.status(500).json({
            status: 'error',
            message: 'Terjadi kesalahan pada server saat proses login',
            error: error.message
        });
    }
}

/**
 * 2. GET CURRENT USER PROFILE (Cek Data User Sedang Login)
 * Endpoint: GET /api/auth/me
 * Header: Authorization: Bearer <token>
 */
async function getMe(req, res) {
    try {
        const userId = req.user.id;

        const [rows] = await pool.query(
            'SELECT id, nama, username, email, role, created_at FROM users WHERE id = ?',
            [userId]
        );

        if (rows.length === 0) {
            return res.status(404).json({
                status: 'error',
                message: 'Pengguna tidak ditemukan'
            });
        }

        return res.status(200).json({
            status: 'success',
            data: rows[0]
        });

    } catch (error) {
        console.error('Error saat mengambil data profil:', error);
        return res.status(500).json({
            status: 'error',
            message: 'Gagal mengambil data profil pengguna',
            error: error.message
        });
    }
}

/**
 * 3. REGISTER PENGGUNA BARU (Khusus Admin)
 * Endpoint: POST /api/auth/register
 * Body: { nama, username, email, password, role }
 */
async function register(req, res) {
    try {
        const { nama, username, email, password, role } = req.body;

        if (!nama || !username || !email || !password) {
            return res.status(400).json({
                status: 'error',
                message: 'Nama, username, email, dan password wajib diisi!'
            });
        }

        // Cek apakah username atau email sudah pernah terdaftar
        const [existing] = await pool.query(
            'SELECT id FROM users WHERE username = ? OR email = ?',
            [username, email]
        );

        if (existing.length > 0) {
            return res.status(409).json({
                status: 'error',
                message: 'Username atau email sudah digunakan oleh pengguna lain!'
            });
        }

        // Hash password menggunakan bcrypt
        const saltRounds = 10;
        const hashedPassword = await bcrypt.hash(password, saltRounds);
        const userRole = (role === 'admin' || role === 'petugas') ? role : 'petugas';

        // Simpan ke database
        const [result] = await pool.query(
            'INSERT INTO users (nama, username, email, password, role) VALUES (?, ?, ?, ?, ?)',
            [nama, username, email, hashedPassword, userRole]
        );

        return res.status(201).json({
            status: 'success',
            message: 'Pengguna baru berhasil didaftarkan!',
            data: {
                id: result.insertId,
                nama,
                username,
                email,
                role: userRole
            }
        });

    } catch (error) {
        console.error('Error saat mendaftarkan pengguna:', error);
        return res.status(500).json({
            status: 'error',
            message: 'Gagal mendaftarkan pengguna baru',
            error: error.message
        });
    }
}

/**
 * 4. DAFTAR SEMUA PENGGUNA (Khusus Admin)
 * Endpoint: GET /api/auth/users
 */
async function getAllUsers(req, res) {
    try {
        const [users] = await pool.query(
            'SELECT id, nama, username, email, role, created_at FROM users ORDER BY created_at DESC'
        );

        return res.status(200).json({
            status: 'success',
            data: users
        });
    } catch (error) {
        return res.status(500).json({
            status: 'error',
            message: 'Gagal mengambil daftar pengguna',
            error: error.message
        });
    }
}

module.exports = {
    login,
    getMe,
    register,
    getAllUsers
};
