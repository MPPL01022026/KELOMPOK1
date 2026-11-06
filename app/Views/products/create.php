<div class="container-fluid px-0">
    <div class="row align-items-center mb-4">
        <div class="col-md-8">
            <h4 class="fw-bold text-dark mb-1">Tambah Produk & Varian Baru</h4>
            <p class="text-secondary small mb-0">Lengkapi identitas produk pakaian dan tentukan variasi ukuran serta warna.</p>
        </div>
        <div class="col-md-4 text-md-end mt-3 mt-md-0">
            <a href="<?= $baseUrl ?>/products" class="btn btn-light border d-inline-flex align-items-center gap-2 fw-semibold px-3 py-2 rounded-3">
                <i class="bi bi-arrow-left"></i>
                <span>Kembali ke Katalog</span>
            </a>
        </div>
    </div>

    <form action="<?= $baseUrl ?>/products/store" method="POST" enctype="multipart/form-data">
        <div class="row g-4">
            <!-- Informasi Utama Produk -->
            <div class="col-lg-5">
                <div class="card-custom p-4 h-100">
                    <h6 class="fw-bold text-dark mb-3">Informasi Utama Pakaian</h6>

                    <div class="mb-3">
                        <label class="form-label small fw-bold text-secondary">Nama Produk Pakaian <span class="text-danger">*</span></label>
                        <input type="text" name="product_name" class="form-control" placeholder="Contoh: Gamis Katun Rayon Floral" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold text-secondary">Kategori Pakaian <span class="text-danger">*</span></label>
                        <select name="category_id" class="form-select" required>
                            <option value="">-- Pilih Kategori --</option>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['category_name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold text-secondary">Harga Jual Satuan (Rp) <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-light fw-bold text-secondary">Rp</span>
                            <input type="number" name="price" class="form-control" placeholder="150000" min="1000" step="1000" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold text-secondary">Foto Produk</label>
                        <input type="file" name="image" class="form-control" accept="image/*">
                        <small class="text-muted">Format: JPG, PNG, WEBP (Maks 2MB)</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold text-secondary">Status Produk</label>
                        <select name="status" class="form-select">
                            <option value="active" selected>Aktif (Tampil di POS & Etalase)</option>
                            <option value="inactive">Non-Aktif (Sembunyikan)</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold text-secondary">Deskripsi Produk</label>
                        <textarea name="description" class="form-control" rows="3" placeholder="Deskripsi bahan, kenyamanan, atau instruksi perawatan..."></textarea>
                    </div>
                </div>
            </div>

            <!-- Variasi Ukuran, Warna & Stok Awal -->
            <div class="col-lg-7">
                <div class="card-custom p-4 h-100">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div>
                            <h6 class="fw-bold text-dark mb-1">Varian Ukuran & Warna</h6>
                            <small class="text-muted">Setiap pakaian dapat memiliki beberapa variasi ukuran dan warna.</small>
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-danger rounded-pill fw-semibold" id="btnAddVariantRow">
                            <i class="bi bi-plus"></i> Tambah Baris Varian
                        </button>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered align-middle mb-0" id="variantsTable">
                            <thead class="table-light small">
                                <tr>
                                    <th>Ukuran (Size)</th>
                                    <th>Warna</th>
                                    <th style="width: 100px;">Stok Awal</th>
                                    <th style="width: 100px;">Batas Min</th>
                                    <th style="width: 50px;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="small" id="variantRowsContainer">
                                <tr>
                                    <td>
                                        <input type="text" name="variant_size[]" class="form-control form-control-sm" placeholder="S / M / L / 28 / All Size" value="All Size" required>
                                    </td>
                                    <td>
                                        <input type="text" name="variant_color[]" class="form-control form-control-sm" placeholder="Contoh: Hitam, Sage Green" value="Hitam" required>
                                    </td>
                                    <td>
                                        <input type="number" name="variant_stock[]" class="form-control form-control-sm text-center" value="10" min="0" required>
                                    </td>
                                    <td>
                                        <input type="number" name="variant_min_stock[]" class="form-control form-control-sm text-center" value="3" min="1" required>
                                    </td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-sm btn-light border py-0 px-2 btn-remove-row text-muted" disabled>
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4 pt-3 border-top d-flex justify-content-end gap-2">
                        <a href="<?= $baseUrl ?>/products" class="btn btn-light rounded-3 px-4">Batal</a>
                        <button type="submit" class="btn btn-danger rounded-3 fw-semibold px-4" style="background:#be123c; border:none;">
                            <i class="bi bi-check2-circle me-1"></i> Simpan Produk & Varian
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
    document.getElementById('btnAddVariantRow').addEventListener('click', function() {
        const container = document.getElementById('variantRowsContainer');
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td>
                <input type="text" name="variant_size[]" class="form-control form-control-sm" placeholder="S / M / L / All Size" required>
            </td>
            <td>
                <input type="text" name="variant_color[]" class="form-control form-control-sm" placeholder="Warna" required>
            </td>
            <td>
                <input type="number" name="variant_stock[]" class="form-control form-control-sm text-center" value="10" min="0" required>
            </td>
            <td>
                <input type="number" name="variant_min_stock[]" class="form-control form-control-sm text-center" value="3" min="1" required>
            </td>
            <td class="text-center">
                <button type="button" class="btn btn-sm btn-light border py-0 px-2 btn-remove-row text-danger" onclick="this.closest('tr').remove()">
                    <i class="bi bi-trash"></i>
                </button>
            </td>
        `;
        container.appendChild(tr);
    });
</script>
