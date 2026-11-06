<?php use App\Config\App; ?>

<div class="container-fluid px-0">
    <div class="row align-items-center mb-4">
        <div class="col-md-8">
            <h4 class="fw-bold text-dark mb-1">Edit Produk: <?= htmlspecialchars($product['product_name']) ?></h4>
            <p class="text-secondary small mb-0">Perbarui rincian pakaian dan kelola variasi ukuran atau warna.</p>
        </div>
        <div class="col-md-4 text-md-end mt-3 mt-md-0">
            <a href="<?= $baseUrl ?>/products" class="btn btn-light border d-inline-flex align-items-center gap-2 fw-semibold px-3 py-2 rounded-3">
                <i class="bi bi-arrow-left"></i>
                <span>Kembali ke Katalog</span>
            </a>
        </div>
    </div>

    <div class="row g-4">
        <!-- Form Update Data Utama -->
        <div class="col-lg-5">
            <div class="card-custom p-4 h-100">
                <h6 class="fw-bold text-dark mb-3">Informasi Utama Pakaian</h6>

                <form action="<?= $baseUrl ?>/products/update/<?= $product['id'] ?>" method="POST" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-secondary">Nama Produk Pakaian <span class="text-danger">*</span></label>
                        <input type="text" name="product_name" class="form-control" value="<?= htmlspecialchars($product['product_name']) ?>" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold text-secondary">Kategori Pakaian <span class="text-danger">*</span></label>
                        <select name="category_id" class="form-select" required>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?= $cat['id'] ?>" <?= ($cat['id'] == $product['category_id']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($cat['category_name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold text-secondary">Harga Jual Satuan (Rp) <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-light fw-bold text-secondary">Rp</span>
                            <input type="number" name="price" class="form-control" value="<?= (int)$product['price'] ?>" min="1000" step="1000" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold text-secondary">Foto Produk Saat Ini</label>
                        <?php if (!empty($product['image']) && file_exists(dirname(dirname(dirname(__DIR__))) . '/public/uploads/products/' . $product['image'])): ?>
                            <div class="mb-2">
                                <img src="<?= $baseUrl ?>/public/uploads/products/<?= htmlspecialchars($product['image']) ?>" alt="Foto" class="rounded-3 border" style="max-height:100px;">
                            </div>
                        <?php endif; ?>
                        <input type="file" name="image" class="form-control" accept="image/*">
                        <small class="text-muted">Kosongkan jika tidak ingin mengubah foto</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold text-secondary">Status Produk</label>
                        <select name="status" class="form-select">
                            <option value="active" <?= ($product['status'] === 'active') ? 'selected' : '' ?>>Aktif (Tampil di POS & Etalase)</option>
                            <option value="inactive" <?= ($product['status'] === 'inactive') ? 'selected' : '' ?>>Non-Aktif (Sembunyikan)</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold text-secondary">Deskripsi Produk</label>
                        <textarea name="description" class="form-control" rows="3"><?= htmlspecialchars($product['description'] ?? '') ?></textarea>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 rounded-3 fw-semibold py-2">
                        <i class="bi bi-save me-1"></i> Simpan Perubahan Produk
                    </button>
                </form>
            </div>
        </div>

        <!-- Daftar Varian & Tambah Varian Baru -->
        <div class="col-lg-7">
            <div class="card-custom p-4 h-100">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div>
                        <h6 class="fw-bold text-dark mb-1">Daftar Varian Produk Ini</h6>
                        <small class="text-muted">Total <?= count($variants) ?> varian terdaftar</small>
                    </div>
                    <button type="button" class="btn btn-sm btn-outline-danger rounded-pill fw-semibold" data-bs-toggle="modal" data-bs-target="#modalAddVariant">
                        <i class="bi bi-plus-lg me-1"></i> Tambah Varian Baru
                    </button>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light small">
                            <tr>
                                <th>Ukuran (Size)</th>
                                <th>Warna</th>
                                <th class="text-center">Stok Aktif</th>
                                <th class="text-center">Batas Min</th>
                                <th class="text-center">Status</th>
                                <th class="text-end" style="width: 80px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="small">
                            <?php if (empty($variants)): ?>
                                <tr><td colspan="6" class="text-center text-muted py-4">Belum ada varian untuk produk ini.</td></tr>
                            <?php else: ?>
                                <?php foreach ($variants as $v): ?>
                                    <tr>
                                        <td class="fw-bold"><?= htmlspecialchars($v['size']) ?></td>
                                        <td>
                                            <span class="badge bg-light text-dark border">
                                                <?= htmlspecialchars($v['color']) ?>
                                            </span>
                                        </td>
                                        <td class="text-center fw-bold fs-6">
                                            <?= $v['stock'] ?> pcs
                                        </td>
                                        <td class="text-center text-muted"><?= $v['min_stock'] ?></td>
                                        <td class="text-center">
                                            <?php if ($v['stock'] <= 3 || $v['stock'] <= $v['min_stock']): ?>
                                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill">Kritis</span>
                                            <?php elseif ($v['stock'] <= 9): ?>
                                                <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle rounded-pill">Menipis</span>
                                            <?php else: ?>
                                                <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill">Aman</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-end">
                                            <form action="<?= $baseUrl ?>/products/variant/delete/<?= $v['id'] ?>" method="POST" class="d-inline" onsubmit="return confirm('Hapus varian ini?')">
                                                <button type="submit" class="btn btn-sm btn-light border py-1 px-2" title="Hapus Varian">
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

                <div class="mt-4 p-3 bg-light rounded-3 border">
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-info-circle text-primary fs-5"></i>
                        <span class="small text-muted">
                            Untuk menambah stok varian yang sudah ada, gunakan modul <strong><a href="<?= $baseUrl ?>/stock/restock" class="text-danger text-decoration-none">Barang Masuk (Restock)</a></strong> agar tercatat di log mutasi.
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Tambah Varian -->
<div class="modal fade" id="modalAddVariant" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-bottom">
                <h5 class="modal-title fw-bold">Tambah Varian Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?= $baseUrl ?>/products/<?= $product['id'] ?>/variant/add" method="POST">
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-secondary">Ukuran (Size) <span class="text-danger">*</span></label>
                        <input type="text" name="size" class="form-control" placeholder="Contoh: S, M, L, XL, 28, 29, 30, All Size" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-secondary">Warna <span class="text-danger">*</span></label>
                        <input type="text" name="color" class="form-control" placeholder="Contoh: Sage Green, Dusty Pink, Terracotta, Hitam" required>
                    </div>
                    <div class="row g-2">
                        <div class="col-6">
                            <label class="form-label small fw-bold text-secondary">Stok Awal</label>
                            <input type="number" name="stock" class="form-control" value="10" min="0" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label small fw-bold text-secondary">Batas Minimum Stok</label>
                            <input type="number" name="min_stock" class="form-control" value="3" min="1" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top bg-light">
                    <button type="button" class="btn btn-light rounded-3" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger rounded-3 fw-semibold px-4" style="background:#be123c; border:none;">Simpan Varian</button>
                </div>
            </form>
        </div>
    </div>
</div>
