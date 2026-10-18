-- ====================================================================
-- SKEMA BASIS DATA: Yunda Fashion Smart Retail & Inventory Monitoring
-- Pengembang: Rana Shofa (Backend & Database Developer)
-- ====================================================================

CREATE DATABASE IF NOT EXISTS yunda_inventory CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE yunda_inventory;

-- 1. TABEL USERS (Autentikasi & Hak Akses)
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(100) NOT NULL,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'petugas') NOT NULL DEFAULT 'petugas',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- 2. TABEL CATEGORIES (Kategori Pakaian)
CREATE TABLE IF NOT EXISTS categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama_kategori VARCHAR(50) NOT NULL UNIQUE,
    warna_label VARCHAR(20) DEFAULT '#7C3AED',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- 3. TABEL PRODUCTS (Master Produk Pakaian)
CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    kode_produk VARCHAR(20) NOT NULL UNIQUE,
    nama_produk VARCHAR(150) NOT NULL,
    kategori_id INT NOT NULL,
    deskripsi TEXT NULL,
    lokasi_rak VARCHAR(50) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_products_kategori FOREIGN KEY (kategori_id) REFERENCES categories(id) ON DELETE RESTRICT
) ENGINE=InnoDB;

-- 4. TABEL PRODUCT_VARIANTS (Varian Ukuran, Warna, Stok, dan QR Code)
--    Stok dikelola di level varian.
--    Constraint chk_stok_non_negatif memastikan stok tidak pernah minus.
CREATE TABLE IF NOT EXISTS product_variants (
    id INT AUTO_INCREMENT PRIMARY KEY,
    produk_id INT NOT NULL,
    sku VARCHAR(60) NOT NULL UNIQUE,
    ukuran VARCHAR(20) NOT NULL,
    warna VARCHAR(50) NOT NULL,
    stok INT NOT NULL DEFAULT 0,
    stok_minimum INT NOT NULL DEFAULT 3,
    qr_code_payload VARCHAR(100) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT chk_stok_non_negatif CHECK (stok >= 0),
    CONSTRAINT fk_variants_product FOREIGN KEY (produk_id) REFERENCES products(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- 5. TABEL STOCK_TRANSACTIONS (Riwayat Mutasi Barang Masuk & Keluar)
CREATE TABLE IF NOT EXISTS stock_transactions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    kode_transaksi VARCHAR(50) NOT NULL UNIQUE,
    varian_id INT NOT NULL,
    user_id INT NULL,
    tipe ENUM('masuk', 'keluar') NOT NULL,
    jumlah INT NOT NULL,
    stok_sebelum INT NOT NULL,
    stok_sesudah INT NOT NULL,
    keterangan VARCHAR(255) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_trans_variant FOREIGN KEY (varian_id) REFERENCES product_variants(id) ON DELETE CASCADE,
    CONSTRAINT fk_trans_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;
