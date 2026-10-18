/**
 * init_db.js — Script Otomatis Pembuat Database & Seeder
 * Dijalankan dengan: node database/init_db.js
 */

const fs = require('fs');
const path = require('path');
const mysql = require('mysql2/promise');
require('dotenv').config({ path: path.join(__dirname, '../.env') });

async function initDatabase() {
    console.log('⏳ Menghubungkan ke MySQL Server...');

    // Koneksi awal tanpa memilih database (untuk CREATE DATABASE)
    let connection;
    try {
        connection = await mysql.createConnection({
            host: process.env.DB_HOST || 'localhost',
            port: process.env.DB_PORT || 3306,
            user: process.env.DB_USER || 'root',
            password: process.env.DB_PASSWORD || '',
            multipleStatements: true
        });

        console.log('✅ Berhasil terhubung ke MySQL Server.');

        // 1. Eksekusi Schema
        console.log('⏳ Menjalankan schema.sql (membuat tabel)...');
        const schemaSql = fs.readFileSync(path.join(__dirname, 'schema.sql'), 'utf-8');
        await connection.query(schemaSql);
        console.log('✅ Skema tabel berhasil dibuat!');

        // 2. Eksekusi Seed
        console.log('⏳ Menjalankan seed.sql (memasukkan data awal)...');
        const seedSql = fs.readFileSync(path.join(__dirname, 'seed.sql'), 'utf-8');
        await connection.query(seedSql);
        console.log('✅ Data seeder awal berhasil dimasukkan!');

        console.log('\n🎉 Database yunda_inventory siap digunakan!');
    } catch (err) {
        console.error('❌ Terjadi kesalahan:', err.message);
        if (err.code === 'ECONNREFUSED') {
            console.error('👉 Pastikan Laragon / MySQL Anda sudah di-"Start All" atau berjalan di port 3306.');
        }
    } finally {
        if (connection) await connection.end();
    }
}

initDatabase();
