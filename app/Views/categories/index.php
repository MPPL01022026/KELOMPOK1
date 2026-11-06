<?php use App\Config\App; ?>

<div class="container-fluid px-0">
    <div class="row align-items-center mb-4">
        <div class="col-md-6">
            <h4 class="fw-bold text-dark mb-1">Kategori Pakaian Wanita</h4>
            <p class="text-secondary small mb-0">Kelola kategori untuk klasifikasi produk di kasir dan etalase toko.</p>
        </div>
        <div class="col-md-6 text-md-end mt-3 mt-md-0">
            <button class="btn btn-danger d-inline-flex align-items-center gap-2 fw-semibold px-3 py-2 rounded-3 shadow-sm" style="background:#be123c; border:none;" data-bs-toggle="modal" data-bs-target="#modalAddCategory">
                <i class="bi bi-plus-circle"></i>
                <span>Tambah Kategori</span>
            </button>
        </div>
    </div>

    <!-- Tabel Kategori -->
    <div class="card-custom p-4">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light small">
                    <tr>
                        <th style="width: 70px;">No.</th>
                        <th>Nama Kategori</th>
                        <th>Jumlah Produk Terhubung</th>
                        <th>Dibuat Pada</th>
                        <th class="text-end" style="width: 150px;">Aksi</th>
                    </tr>
                </thead>
                <tbody class="small">
                    <?php if (empty($categories)): ?>
                        <tr><td colspan="5" class="text-center text-muted py-4">Belum ada kategori yang ditambahkan.</td></tr>
                    <?php else: ?>
                        <?php foreach ($categories as $index => $cat): ?>
                            <tr>
                                <td class="fw-semibold text-muted"><?= $index + 1 ?></td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="p-2 rounded-2 bg-danger-subtle text-danger">
                                            <i class="bi bi-tag-fill"></i>
                                        </div>
                                        <span class="fw-bold text-dark fs-6"><?= htmlspecialchars($cat['category_name']) ?></span>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-secondary-subtle text-dark border">
                                        <?= (int)$cat['product_count'] ?> Produk
                                    </span>
                                </td>
                                <td class="text-muted"><?= App::formatTanggalPendek($cat['created_at']) ?></td>
                                <td class="text-end">
                                    <button class="btn btn-sm btn-light border py-1 px-2 me-1" 
                                            onclick="editCategory(<?= $cat['id'] ?>, '<?= htmlspecialchars(addslashes($cat['category_name'])) ?>')" 
                                            title="Ubah Kategori">
                                        <i class="bi bi-pencil-square text-primary"></i>
                                    </button>
                                    <form action="<?= $baseUrl ?>/categories/delete/<?= $cat['id'] ?>" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus kategori ini?')">
                                        <button type="submit" class="btn btn-sm btn-light border py-1 px-2" title="Hapus Kategori" <?= $cat['product_count'] > 0 ? 'disabled' : '' ?>>
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

<!-- Modal Tambah Kategori -->
<div class="modal fade" id="modalAddCategory" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-bottom">
                <h5 class="modal-title fw-bold">Tambah Kategori Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?= $baseUrl ?>/categories/store" method="POST">
                <div class="modal-body p-4">
                    <label class="form-label small fw-bold text-secondary">Nama Kategori Pakaian</label>
                    <input type="text" name="category_name" class="form-control" placeholder="Contoh: Gamis, Kemeja, Blus, Rok, Jeans" required autofocus>
                    <small class="text-muted mt-2 d-block">Gunakan nama yang jelas untuk pengelompokan di POS dan rak etalase.</small>
                </div>
                <div class="modal-footer border-top bg-light">
                    <button type="button" class="btn btn-light rounded-3" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger rounded-3 fw-semibold px-4" style="background:#be123c; border:none;">Simpan Kategori</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Edit Kategori -->
<div class="modal fade" id="modalEditCategory" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-bottom">
                <h5 class="modal-title fw-bold">Ubah Nama Kategori</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formEditCategory" method="POST">
                <div class="modal-body p-4">
                    <label class="form-label small fw-bold text-secondary">Nama Kategori</label>
                    <input type="text" name="category_name" id="editCategoryName" class="form-control" required>
                </div>
                <div class="modal-footer border-top bg-light">
                    <button type="button" class="btn btn-light rounded-3" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary rounded-3 fw-semibold px-4">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function editCategory(id, name) {
        document.getElementById('editCategoryName').value = name;
        document.getElementById('formEditCategory').action = '<?= $baseUrl ?>/categories/update/' + id;
        new bootstrap.Modal(document.getElementById('modalEditCategory')).show();
    }
</script>
