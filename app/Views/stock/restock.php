<?php
/**
 * View Pencatatan Barang Masuk (Restock)
 * Developer: Salsabila (230504097)
 */
use App\Config\App;
?>

<div class="container-fluid px-0">
    <div class="row align-items-center mb-4">
        <div class="col-md-7">
            <h4 class="fw-bold text-dark mb-1">Barang Masuk (Restock Persediaan)</h4>
            <p class="text-secondary small mb-0">Catat penerimaan pasokan pakaian wanita baru dan perbarui stok sistem secara otomatis.</p>
        </div>
        <div class="col-md-5 text-md-end mt-3 mt-md-0">
            <a href="<?= $baseUrl ?>/stock/movements" class="btn btn-light border d-inline-flex align-items-center gap-2 fw-semibold px-3 py-2 rounded-3">
                <i class="bi bi-clock-history"></i>
                <span>Lihat Semua Mutasi</span>
            </a>
        </div>
    </div>

    <div class="row g-4">
        <!-- Form Restock -->
        <div class="col-lg-5">
            <div class="card-custom p-4 h-100">
                <h6 class="fw-bold text-dark mb-3">Formulir Tambah Pasokan Barang</h6>

                <form action="<?= $baseUrl ?>/stock/restock" method="POST">
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-secondary">Pilih Produk & Varian <span class="text-danger">*</span></label>
                        <select name="variant_id" class="form-select select2-enable" required>
                            <option value="">-- Pilih Varian Pakaian --</option>
                            <?php foreach ($variants as $v): ?>
                                <option value="<?= $v['variant_id'] ?>" data-stock="<?= $v['stock'] ?>">
                                    <?= htmlspecialchars($v['product_name']) ?> — [<?= htmlspecialchars($v['size']) ?> | <?= htmlspecialchars($v['color']) ?>] (Stok Sekarang: <?= $v['stock'] ?> pcs)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold text-secondary">Jumlah Barang Masuk (Pcs) <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="number" name="quantity" class="form-control" placeholder="Contoh: 10" min="1" required>
                            <span class="input-group-text bg-light text-secondary">pcs</span>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label small fw-bold text-secondary">Catatan / Supplier / No. Surat Jalan</label>
                        <textarea name="notes" class="form-control" rows="3" placeholder="Contoh: Pasokan konveksi Bandung, No. DO #8821"></textarea>
                    </div>

                    <button type="submit" class="btn btn-success w-100 rounded-3 fw-semibold py-2">
                        <i class="bi bi-box-arrow-in-down me-1"></i> Simpan Barang Masuk
                    </button>
                </form>
            </div>
        </div>

        <!-- Riwayat Restock Terbaru -->
        <div class="col-lg-7">
            <div class="card-custom p-4 h-100">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <h6 class="fw-bold text-dark mb-0">Riwayat Restock Terakhir</h6>
                    <span class="badge bg-success-subtle text-success border border-success-subtle">Mutasi Masuk</span>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light small">
                            <tr>
                                <th>Waktu</th>
                                <th>Produk & Varian</th>
                                <th class="text-center">Masuk</th>
                                <th class="text-center">Sebelum &rarr; Sesudah</th>
                                <th>Operator</th>
                            </tr>
                        </thead>
                        <tbody class="small">
                            <?php if (empty($recentRestocks)): ?>
                                <tr><td colspan="5" class="text-center text-muted py-4">Belum ada riwayat restock.</td></tr>
                            <?php else: ?>
                                <?php foreach ($recentRestocks as $r): ?>
                                    <tr>
                                        <td class="text-muted"><?= App::formatTanggalPendek($r['created_at']) ?></td>
                                        <td>
                                            <strong class="d-block text-dark"><?= htmlspecialchars($r['product_name']) ?></strong>
                                            <span class="badge bg-light text-muted border" style="font-size:0.7rem;">
                                                <?= htmlspecialchars($r['size']) ?> - <?= htmlspecialchars($r['color']) ?>
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-success text-white fw-bold">+<?= $r['quantity'] ?> pcs</span>
                                        </td>
                                        <td class="text-center text-muted">
                                            <?= $r['stock_before'] ?> &rarr; <strong class="text-dark"><?= $r['stock_after'] ?></strong>
                                        </td>
                                        <td>
                                            <span class="text-dark small"><?= htmlspecialchars($r['user_name']) ?></span>
                                            <small class="text-muted d-block" style="font-size:0.68rem;"><?= htmlspecialchars($r['reference_type'] ?? '') ?></small>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
