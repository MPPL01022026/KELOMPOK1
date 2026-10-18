/**
 * database.js — Konfigurasi MySQL Connection Pool
 * Menggunakan mysql2/promise untuk async/await
 */

const mysql = require('mysql2/promise');
require('dotenv').config();

// Buat Connection Pool untuk efisiensi koneksi simultan
const pool = mysql.createPool({
    host: process.env.DB_HOST || 'localhost',
    port: parseInt(process.env.DB_PORT || '3306', 10),
    user: process.env.DB_USER || 'root',
    password: process.env.DB_PASSWORD || '',
    database: process.env.DB_NAME || 'yunda_inventory',
    waitForConnections: true,
    connectionLimit: 10,
    queueLimit: 0,
    timezone: '+07:00' // WIB (Waktu Indonesia Barat)
});

// Fungsi untuk mengetes koneksi database saat server mulai berjalan
async function testConnection() {
    try {
        const connection = await pool.getConnection();
        console.log('✅ Terhubung ke database MySQL (' + (process.env.DB_NAME || 'yunda_inventory') + ')');
        connection.release();
        return true;
    } catch (error) {
        console.error('❌ Gagal terhubung ke database MySQL:', error.message);
        return false;
    }
}

module.exports = {
    pool,
    testConnection
};
