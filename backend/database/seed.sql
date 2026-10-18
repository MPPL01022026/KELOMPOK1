-- ====================================================================
-- SEEDER DATA AWAL: Yunda Fashion Smart Retail
-- Selaras dengan data dummy frontend (data.js)
-- ====================================================================

USE yunda_inventory;

-- 1. Pengguna Awal
-- Password default: 'admin123' (hash bcrypt)
INSERT INTO users (id, nama, username, email, password, role) VALUES
(1, 'Admin Yunda', 'admin', 'admin@yundafashion.com', '$2b$10$JJ43ugLIWf2ZX/1FNWalmeE3rXqnTTcIJNfYHEUKxc/huqR27JAx.', 'admin'),
(2, 'Petugas Toko', 'petugas1', 'petugas1@yundafashion.com', '$2b$10$JJ43ugLIWf2ZX/1FNWalmeE3rXqnTTcIJNfYHEUKxc/huqR27JAx.', 'petugas')
ON DUPLICATE KEY UPDATE nama=VALUES(nama), password=VALUES(password);

-- 2. Kategori Produk Pakaian
INSERT INTO categories (id, nama_kategori, warna_label) VALUES
(1, 'Atasan', '#7C3AED'),
(2, 'Bawahan', '#3B82F6'),
(3, 'Dress', '#EC4899'),
(4, 'Outerwear', '#F59E0B'),
(5, 'Aksesoris', '#10B981')
ON DUPLICATE KEY UPDATE nama_kategori=VALUES(nama_kategori);

-- 3. Master Produk Pakaian
INSERT INTO products (id, kode_produk, nama_produk, kategori_id, deskripsi, lokasi_rak) VALUES
(1, 'YF-001', 'Kemeja Polos Premium', 1, 'Kemeja katun premium lengan panjang', 'Rak A-01'),
(2, 'YF-015', 'Celana Chino Slim', 2, 'Celana chino stretch slim fit', 'Rak B-02'),
(3, 'YF-028', 'Dress Midi Floral', 3, 'Dress motif bunga kasual wanita', 'Rak C-01'),
(4, 'YF-042', 'Cardigan Rajut', 4, 'Cardigan rajut rajut tebal', 'Rak D-01'),
(5, 'YF-009', 'Blouse Batik Modern', 1, 'Blouse batik printing katun halus', 'Rak A-03'),
(6, 'YF-055', 'Rok Plisket', 2, 'Rok plisket premium all size', 'Rak B-01'),
(7, 'YF-031', 'Hoodie Casual', 4, 'Hoodie fleece tebal bertopi', 'Rak D-02'),
(8, 'YF-023', 'T-Shirt Basic Oversize', 1, 'Kaos katun combed 24s oversize', 'Rak A-02'),
(9, 'YF-008', 'Jeans Slim Fit Navy', 2, 'Celana jeans denim stretch biru gelap', 'Rak B-03')
ON DUPLICATE KEY UPDATE nama_produk=VALUES(nama_produk);

-- 4. Varian Produk & Stok Awal
-- Aturan Status:
--   - Aman    : stok >= 10
--   - Waspada : stok 4 - 9
--   - Kritis  : stok <= 3
INSERT INTO product_variants (id, produk_id, sku, ukuran, warna, stok, stok_minimum, qr_code_payload) VALUES
-- Kritis (<= 3)
(1, 1, 'YF-001-M-PUTIH', 'M', 'Putih', 2, 3, 'YF-001-M-PUTIH'),
(2, 2, 'YF-015-32-KHAKI', '32', 'Khaki', 3, 3, 'YF-015-32-KHAKI'),
(3, 3, 'YF-028-M-NAVY', 'M', 'Navy', 3, 3, 'YF-028-M-NAVY'),
-- Waspada (4 - 9)
(4, 4, 'YF-042-L-CREAM', 'L', 'Cream', 5, 3, 'YF-042-L-CREAM'),
(5, 5, 'YF-009-S-COKLAT', 'S', 'Coklat', 6, 3, 'YF-009-S-COKLAT'),
(6, 6, 'YF-055-S-HITAM', 'S', 'Hitam', 7, 3, 'YF-055-S-HITAM'),
(7, 7, 'YF-031-XL-ABU', 'XL', 'Abu-abu', 8, 3, 'YF-031-XL-ABU'),
-- Aman (>= 10)
(8, 8, 'YF-023-XL-HITAM', 'XL', 'Hitam', 25, 3, 'YF-023-XL-HITAM'),
(9, 9, 'YF-008-30-NAVY', '30', 'Navy', 40, 3, 'YF-008-30-NAVY')
ON DUPLICATE KEY UPDATE stok=VALUES(stok);

-- 5. Contoh Riwayat Transaksi Stok Awal
INSERT INTO stock_transactions (kode_transaksi, varian_id, user_id, tipe, jumlah, stok_sebelum, stok_sesudah, keterangan) VALUES
('TRX-20261018-0001', 8, 1, 'keluar', 3, 28, 25, 'Penjualan kasir toko'),
('TRX-20261018-0002', 9, 1, 'masuk', 20, 20, 40, 'Restok dari supplier garmen'),
('TRX-20261018-0003', 1, 1, 'keluar', 2, 4, 2, 'Pembelian pelanggan (stok menjadi kritis)')
ON DUPLICATE KEY UPDATE kode_transaksi=VALUES(kode_transaksi);
