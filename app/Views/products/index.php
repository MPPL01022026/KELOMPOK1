<?php use App\Config\App; ?>

<div class="container-fluid px-0">
    <!-- Header -->
    <div class="row align-items-center mb-4">
        <div class="col-md-6">
            <h4 class="fw-bold text-dark mb-1">Katalog Produk & Varian</h4>
            <p class="text-secondary small mb-0">Kelola master data pakaian wanita, ukuran, warna, stok aktif, dan foto.</p>
        </div>
        <div class="col-md-6 text-md-end mt-3 mt-md-0">
            <a href="<?= $baseUrl ?>/products/create" class="btn btn-danger d-inline-flex align-items-center gap-2 fw-semibold px-3 py-2 rounded-3 shadow-sm" style="background:#be123c; border:none;">
                <i class="bi bi-plus-lg"></i>
                <span>Tambah Produk Baru</span>
            </a>
        </div>
    </div>

    <!-- Filter & Search Toolbar -->
    <div class="card-custom p-3 mb-4">
        <form method="GET" action="<?= $baseUrl ?>/products" class="row g-2 align-items-center">
            <div class="col-md-4">
                <div class="input-group">
                    <span class="input-group-text bg-white text-muted border-end-0"><i class="bi bi-search"></i></span>
                    <input type="text" name="keyword" class="form-control border-start-0" placeholder="Cari nama pakaian / deskripsi..." value="<?= htmlspecialchars($keyword ?? '') ?>">
                </div>
            </div>
            <div class="col-md-3">
                <select name="category_id" class="form-select">
                    <option value="">Semua Kategori Pakaian</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?= $cat['id'] ?>" <?= ($selectedCategory == $cat['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($cat['category_name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3">
                <select name="status" class="form-select">
                    <option value="">Semua Status</option>
                    <option value="active" <?= ($selectedStatus === 'active') ? 'selected' : '' ?>>Hanya Aktif</option>
                    <option value="inactive" <?= ($selectedStatus === 'inactive') ? 'selected' : '' ?>>Non-Aktif</option>
                </select>
            </div>
            <div class="col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-dark w-100 rounded-3 fw-semibold">
                    <i class="bi bi-funnel me-1"></i> Filter
                </button>
                <a href="<?= $baseUrl ?>/products" class="btn btn-light border rounded-3" title="Reset">
                    <i class="bi bi-arrow-counterclockwise"></i>
                </a>
            </div>
        </form>
    </div>

    <!-- Tabel Produk -->
    <div class="card-custom p-4">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light small">
                    <tr>
                        <th style="width: 70px;">Foto</th>
                        <th>Nama Produk</th>
                        <th>Kategori</th>
                        <th class="text-end">Harga Jual</th>
                        <th class="text-center">Jumlah Varian</th>
                        <th class="text-center">Total Stok</th>
                        <th class="text-center">Status</th>
                        <th class="text-end" style="width: 140px;">Aksi</th>
                    </tr>
                </thead>
                <tbody class="small">
                    <?php if (empty($products)): ?>
                        <tr><td colspan="8" class="text-center text-muted py-4">Tidak ada data produk yang sesuai dengan filter.</td></tr>
                    <?php else: ?>
                        <?php foreach ($products as $p): ?>
                            <tr>
                                <td>
                                    <div class="rounded-3 overflow-hidden bg-light border d-flex align-items-center justify-content-center" style="width:50px; height:50px;">
                                        <?php if (!empty($p['image']) && file_exists(dirname(dirname(dirname(__DIR__))) . '/public/uploads/products/' . $p['image'])): ?>
                                            <img src="<?= $baseUrl ?>/public/uploads/products/<?= htmlspecialchars($p['image']) ?>" alt="Foto" style="width:100%; height:100%; object-fit:cover;">
                                        <?php else: ?>
                                            <i class="bi bi-image text-muted fs-4"></i>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td>
                                    <a href="<?= $baseUrl ?>/products/edit/<?= $p['id'] ?>" class="fw-bold text-dark text-decoration-none">
                                        <?= htmlspecialchars($p['product_name']) ?>
                                    </a>
                                    <small class="text-muted d-block text-truncate" style="max-width:280px;"><?= htmlspecialchars($p['description'] ?? 'Tidak ada deskripsi') ?></small>
                                </td>
                                <td><span class="badge bg-light text-dark border"><?= htmlspecialchars($p['category_name']) ?></span></td>
                                <td class="text-end fw-bold text-dark"><?= App::formatRupiah($p['price']) ?></td>
                                <td class="text-center">
                                    <span class="badge bg-secondary-subtle text-dark border">
                                        <?= (int)$p['variant_count'] ?> varian
                                    </span>
                                </td>
                                <td class="text-center">
                                    <?php if ($p['total_stock'] <= 3): ?>
                                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1 fw-bold">
                                            <?= (int)$p['total_stock'] ?> pcs (Kritis)
                                        </span>
                                    <?php elseif ($p['total_stock'] <= 9): ?>
                                        <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle px-2 py-1 fw-bold">
                                            <?= (int)$p['total_stock'] ?> pcs (Menipis)
                                        </span>
                                    <?php else: ?>
                                        <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1 fw-bold">
                                            <?= (int)$p['total_stock'] ?> pcs (Aman)
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <?php if ($p['status'] === 'active'): ?>
                                        <span class="badge bg-success-subtle text-success rounded-pill px-2">Aktif</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary-subtle text-secondary rounded-pill px-2">Non-Aktif</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-end">
                                    <a href="<?= $baseUrl ?>/products/edit/<?= $p['id'] ?>" class="btn btn-sm btn-light border py-1 px-2 me-1" title="Kelola Varian & Edit">
                                        <i class="bi bi-pencil-square text-primary"></i>
                                    </a>
                                    <form action="<?= $baseUrl ?>/products/delete/<?= $p['id'] ?>" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus produk ini beserta semua variannya?')">
                                        <button type="submit" class="btn btn-sm btn-light border py-1 px-2" title="Hapus Produk">
                                            <i class="bi bi-trash text-danger"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
