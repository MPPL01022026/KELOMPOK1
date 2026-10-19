/**
 * server.js — Entry Point Aplikasi Backend Yunda Fashion Inventory
 * Pengembang: Rana Shofa (Backend & Database Developer)
 */

const express = require('express');
const http = require('http');
const cors = require('cors');
require('dotenv').config();

const path = require('path');

const { pool, testConnection } = require('./config/database');
const { initSocket } = require('./config/socket');

const app = express();
const server = http.createServer(app);

// Inisialisasi Socket.IO
initSocket(server);

// Middleware Global
app.use(cors());
app.use(express.json());
app.use(express.urlencoded({ extended: true }));

// Sajikan Web API Tester untuk kemudahan pengujian di browser
app.use(express.static(path.join(__dirname, '../public')));

// Import Routes
const authRoutes = require('./routes/authRoutes');
const categoryRoutes = require('./routes/categoryRoutes');
const productRoutes = require('./routes/productRoutes');
const dashboardRoutes = require('./routes/dashboardRoutes');
const stockRoutes = require('./routes/stockRoutes');
const variantRoutes = require('./routes/variantRoutes');

// Endpoint Cek Kesehatan Sistem (Health Check)
app.get('/api/health', async (req, res) => {
    try {
        const [dbCheck] = await pool.query('SELECT 1 + 1 AS result');
        res.status(200).json({
            status: 'success',
            message: 'Server Backend Yunda Fashion berjalan normal',
            database: dbCheck[0].result === 2 ? 'connected' : 'error',
            timestamp: new Date().toISOString()
        });
    } catch (error) {
        res.status(500).json({
            status: 'error',
            message: 'Server aktif tetapi gagal terhubung ke database',
            error: error.message
        });
    }
});

// Daftarkan Routes API
app.use('/api/auth', authRoutes);
app.use('/api/categories', categoryRoutes);
app.use('/api/products', productRoutes);
app.use('/api/dashboard', dashboardRoutes);
app.use('/api/stock', stockRoutes);
app.use('/api/variants', variantRoutes);

// Middleware 404 Route Not Found
app.use((req, res, next) => {
    res.status(404).json({
        status: 'error',
        message: `Route [${req.method} ${req.originalUrl}] tidak ditemukan di server.`
    });
});

// Global Error Handler
app.use((err, req, res, next) => {
    console.error('Unhandled Error:', err);
    res.status(err.status || 500).json({
        status: 'error',
        message: err.message || 'Terjadi kesalahan internal server'
    });
});

// Port & Server Listener
const PORT = process.env.PORT || 5000;

server.listen(PORT, async () => {
    console.log(`\n=================================================`);
    console.log(`🚀 Server Backend Yunda Fashion Aktif di Port ${PORT}`);
    console.log(`📡 URL Health Check: http://localhost:${PORT}/api/health`);
    console.log(`=================================================`);
    
    // Tes koneksi database
    await testConnection();
});
