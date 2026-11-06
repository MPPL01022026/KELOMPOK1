-- ==============================================================
-- DATABASE SEEDERS: Yunda Fashion POS & Inventory Monitoring System
-- Data Awal Pengguna, Kategori, Produk Pakaian Wanita, Varian & Transaksi
-- ==============================================================

USE `yundafashion`;

-- 1. Seed Users (Password: admin123 / kasir123)
INSERT INTO `users` (`id`, `name`, `username`, `email`, `password`, `role`, `is_active`, `created_at`) VALUES
(1, 'Adit Apriansyah (Owner/Admin)', 'admin', 'admin@yundafashion.com', '$2y$10$D4Cu7FQsCE/mlZ9jk3Ak9uIEanwBl6NYsqV0IVShTkeucPfqUhtFS', 'admin', 1, NOW()),
(2, 'Kasir Toko', 'kasir', 'kasir@yundafashion.com', '$2y$10$Yg1qHkvZz0udWW0NaLVUCuIBzhOeiC10eg/AOUW4VzIymspw/IKXi', 'kasir', 1, NOW())
ON DUPLICATE KEY UPDATE `name`=VALUES(`name`), `username`=VALUES(`username`), `email`=VALUES(`email`);

-- 2. Seed Categories (Pakaian Wanita Sesuai PRD)
INSERT INTO `categories` (`id`, `category_name`, `created_at`) VALUES
(1, 'Kemeja', NOW()),
(2, 'Blus', NOW()),
(3, 'Gamis', NOW()),
(4, 'Rok', NOW()),
(5, 'Celana Jeans', NOW()),
(6, 'Celana Kain', NOW()),
(7, 'Manset', NOW()),
(8, 'Legging', NOW())
ON DUPLICATE KEY UPDATE `category_name`=VALUES(`category_name`);

-- 3. Seed Products
INSERT INTO `products` (`id`, `category_id`, `product_name`, `description`, `image`, `price`, `status`, `created_at`) VALUES
(1, 3, 'Gamis Katun Rayon Floral', 'Gamis motif floral bahan katun rayon premium, adem, busui friendly dengan resleting depan.', 'gamis_rayon.jpg', 185000.00, 'active', NOW()),
(2, 2, 'Blus Crinkle Airflow Premium', 'Blus wanita lengan balon bahan crinkle airflow lembut tidak perlu disetrika, stylish dan elegan.', 'blus_crinkle.jpg', 95000.00, 'active', NOW()),
(3, 1, 'Kemeja Flanel Oversize Casual', 'Kemeja kotak-kotak katun flanel tebal bertekstur lembut model oversize ala korean look.', 'kemeja_flanel.jpg', 125000.00, 'active', NOW()),
(4, 4, 'Rok Plisket Premium Import', 'Rok lipit bahan hyget super tebal, jatuh, tidak menerawang, ban pinggang full karet nyaman.', 'rok_plisket.jpg', 85000.00, 'active', NOW()),
(5, 5, 'Boyfriend Jeans Denim Highwaist', 'Celana jeans model boyfriend potongan highwaist non-stretch denim berkualitas awet.', 'boyfriend_jeans.jpg', 145000.00, 'active', NOW()),
(6, 6, 'Celana Kulot Linen Scuba', 'Kulot panjang wanita bahan scuba linen adem dan jatuh dengan saku kanan-kiri aktif.', 'kulot_linen.jpg', 110000.00, 'active', NOW()),
(7, 7, 'Manset Baju Spandek Rayon', 'Inner manset badan bahan spandek rayon adem menyerap keringat, pas di badan dan lentur.', 'manset_spandek.jpg', 35000.00, 'active', NOW()),
(8, 8, 'Legging Stretch Seamless Premium', 'Legging wanita lentur anti robek bahan spandex lycra tebal cocok untuk harian maupun dalaman gamis.', 'legging_stretch.jpg', 45000.00, 'active', NOW())
ON DUPLICATE KEY UPDATE `product_name`=VALUES(`product_name`);

-- 4. Seed Product Variants (Variasi Ukuran, Warna, Stok & Min Stock)
-- Status indikator Digital Twin: Aman (>=10), Menipis (4-9), Kritis (<=3 atau <=min_stock)
INSERT INTO `product_variants` (`id`, `product_id`, `size`, `color`, `stock`, `min_stock`) VALUES
-- Gamis Katun Rayon Floral (ID 1)
(1, 1, 'M', 'Sage Green', 15, 3),    -- Aman
(2, 1, 'L', 'Sage Green', 6, 3),     -- Menipis
(3, 1, 'XL', 'Sage Green', 2, 3),    -- Kritis!
(4, 1, 'M', 'Dusty Pink', 12, 3),    -- Aman
(5, 1, 'L', 'Dusty Pink', 5, 3),     -- Menipis
(6, 1, 'XL', 'Terracotta', 3, 3),    -- Kritis!

-- Blus Crinkle Airflow (ID 2)
(7, 2, 'All Size', 'Mocca', 18, 5),   -- Aman
(8, 2, 'All Size', 'Broken White', 4, 5), -- Menipis / Kritis (<=min_stock)
(9, 2, 'All Size', 'Hitam', 22, 5),   -- Aman
(10, 2, 'All Size', 'Lilac', 3, 5),   -- Kritis!

-- Kemeja Flanel Oversize (ID 3)
(11, 3, 'M', 'Navy Black', 14, 3),    -- Aman
(12, 3, 'L', 'Navy Black', 7, 3),     -- Menipis
(13, 3, 'XL', 'Red Tartan', 2, 3),    -- Kritis!
(14, 3, 'L', 'Choco Brown', 11, 3),   -- Aman

-- Rok Plisket Premium (ID 4)
(15, 4, 'All Size', 'Hitam', 25, 4),   -- Aman
(16, 4, 'All Size', 'Mocca', 8, 4),    -- Menipis
(17, 4, 'All Size', 'Army', 1, 4),     -- Kritis!

-- Boyfriend Jeans Denim (ID 5)
(18, 5, '28', 'Light Blue', 16, 3),   -- Aman
(19, 5, '29', 'Light Blue', 6, 3),    -- Menipis
(20, 5, '30', 'Light Blue', 3, 3),    -- Kritis!
(21, 5, '29', 'Dark Blue', 14, 3),    -- Aman
(22, 5, '30', 'Dark Blue', 2, 3),     -- Kritis!

-- Celana Kulot Linen (ID 6)
(23, 6, 'All Size', 'Hitam', 20, 4),   -- Aman
(24, 6, 'All Size', 'Broken White', 7, 4), -- Menipis
(25, 6, 'All Size', 'Sage Green', 2, 4),   -- Kritis!

-- Manset Baju Spandek (ID 7)
(26, 7, 'All Size', 'Hitam', 40, 5),   -- Aman
(27, 7, 'All Size', 'Putih', 18, 5),   -- Aman
(28, 7, 'All Size', 'Kulit / Nude', 5, 5), -- Menipis / Kritis

-- Legging Stretch Seamless (ID 8)
(29, 8, 'All Size', 'Hitam', 35, 5),   -- Aman
(30, 8, 'All Size', 'Abu Tua', 8, 5),  -- Menipis
(31, 8, 'All Size', 'Navy', 3, 5)      -- Kritis!
ON DUPLICATE KEY UPDATE `stock`=VALUES(`stock`);

-- 5. Seed Initial Stock Movements (Restock Awal)
INSERT INTO `stock_movements` (`variant_id`, `user_id`, `movement_type`, `quantity`, `stock_before`, `stock_after`, `reference_type`, `reference_id`, `created_at`) VALUES
(1, 1, 'restock', 15, 0, 15, 'initial_setup', NULL, DATE_SUB(NOW(), INTERVAL 5 DAY)),
(2, 1, 'restock', 10, 0, 10, 'initial_setup', NULL, DATE_SUB(NOW(), INTERVAL 5 DAY)),
(3, 1, 'restock', 5, 0, 5, 'initial_setup', NULL, DATE_SUB(NOW(), INTERVAL 5 DAY)),
(7, 1, 'restock', 20, 0, 20, 'initial_setup', NULL, DATE_SUB(NOW(), INTERVAL 5 DAY)),
(15, 1, 'restock', 30, 0, 30, 'initial_setup', NULL, DATE_SUB(NOW(), INTERVAL 5 DAY)),
(18, 1, 'restock', 20, 0, 20, 'initial_setup', NULL, DATE_SUB(NOW(), INTERVAL 5 DAY)),
(26, 1, 'restock', 50, 0, 50, 'initial_setup', NULL, DATE_SUB(NOW(), INTERVAL 5 DAY));

-- 6. Seed Sample Sales & Sale Items (untuk visualisasi grafik & Top 5 Produk)
INSERT INTO `sales` (`id`, `invoice_number`, `user_id`, `total_amount`, `paid_amount`, `change_amount`, `payment_method`, `created_at`) VALUES
(1, 'INV-20261028-0001', 2, 280000.00, 300000.00, 20000.00, 'cash', DATE_SUB(NOW(), INTERVAL 2 DAY)),
(2, 'INV-20261029-0002', 2, 230000.00, 250000.00, 20000.00, 'cash', DATE_SUB(NOW(), INTERVAL 1 DAY)),
(3, 'INV-20261030-0003', 2, 370000.00, 400000.00, 30000.00, 'cash', NOW());

INSERT INTO `sale_items` (`id`, `sale_id`, `variant_id`, `quantity`, `unit_price`, `subtotal`) VALUES
(1, 1, 1, 1, 185000.00, 185000.00), -- Gamis Katun Rayon
(2, 1, 7, 1, 95000.00, 95000.00),   -- Blus Crinkle
(3, 2, 5, 1, 145000.00, 145000.00), -- Boyfriend Jeans
(4, 2, 4, 1, 85000.00, 85000.00),   -- Rok Plisket
(5, 3, 1, 2, 185000.00, 370000.00); -- Gamis Katun Rayon (2 pcs)

-- Catatan mutasi penjualan
INSERT INTO `stock_movements` (`variant_id`, `user_id`, `movement_type`, `quantity`, `stock_before`, `stock_after`, `reference_type`, `reference_id`, `created_at`) VALUES
(1, 2, 'sale', -1, 16, 15, 'sale', 1, DATE_SUB(NOW(), INTERVAL 2 DAY)),
(7, 2, 'sale', -1, 19, 18, 'sale', 1, DATE_SUB(NOW(), INTERVAL 2 DAY)),
(1, 2, 'sale', -2, 17, 15, 'sale', 3, NOW());
