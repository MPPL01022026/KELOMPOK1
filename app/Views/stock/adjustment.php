<?php
/**
 * View Penyesuaian Stok Fisik (Stock Adjustment)
 * Developer: Salsabila (230504097)
 */
use App\Config\App;
?>

<div class="container-fluid px-0">
    <div class="row align-items-center mb-4">
        <div class="col-md-7">
            <h4 class="fw-bold text-dark mb-1">Penyesuaian Stok Fisik (Stock Adjustment)</h4>
            <p class="text-secondary small mb-0">Sinkronkan stok tercatat di sistem dengan hasil stock opname fisik toko Yunda Fashion.</p>
        </div>
        <div class="col-md-5 text-md-end mt-3 mt-md-0">
            <a href="<?= $baseUrl ?>/stock/movements" class="btn btn-light border d-inline-flex align-items-center gap-2 fw-semibold px-3 py-2 rounded-3">
                <i class="bi bi-clock-history"></i>
                <span>Lihat Mutasi</span>
            </a>
        </div>
    </div>

    <div class="row g-4">
        <!-- Form Penyesuaian Fisik -->
        <div class="col-lg-5">
            <div class="card-custom p-4 h-100">
                <h6 class="fw-bold text-dark mb-3">Form Penyesuaian Stok Fisik</h6>

                <form action="<?= $baseUrl ?>/stock/adjustment" method="POST">
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-secondary">Pilih Varian Pakaian <span class="text-danger">*</span></label>
                        <select name="variant_id" id="adjVariantSelect" class="form-select" required>
                            <option value="">-- Pilih Varian Pakaian --</option>
                            <?php foreach ($variants as $v): ?>
                                <option value="<?= $v['variant_id'] ?>" data-stock="<?= $v['stock'] ?>">
                                    <?= htmlspecialchars($v['product_name']) ?> — [<?= htmlspecialchars($v['size']) ?> | <?= htmlspecialchars($v['color']) ?>] (Sistem: <?= $v['stock'] ?> pcs)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Info Stok Sistem vs Fisik -->
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label small fw-bold text-secondary">Stok di Sistem</label>
                            <input type="text" id="adjSystemStock" class="form-control bg-light" value="0 pcs" readonly>
                        </div>
                        <div class="col-6">
                            <label class="form-label small fw-bold text-secondary">Stok Fisik Aktual <span class="text-danger">*</span></label>
                            <input type="number" name="physical_stock" id="adjPhysicalStock" class="form-control fw-bold" placeholder="0" min="0" required>
                        </div>
                    </div>

                    <!-- Perkiraan Selisih -->
                    <div class="p-2 mb-3 bg-light rounded-3 border d-flex justify-content-between align-items-center">
                        <span class="small text-muted fw-semibold">Estimasi Selisih:</span>
                        <span class="fw-bold fs-6 text-dark" id="adjDiffLabel">0 pcs</span>
                    </div>

                    <div class="mb-4">
                        <label class="form-label small fw-bold text-secondary">Alasan Penyesuaian <span class="text-danger">*</span></label>
                        <select name="reason" class="form-select mb-2" required>
                            <option value="Stock Opname Rutin">Stock Opname Rutin</option>
                            <option value="Barang Cacat / Rusak">Barang Cacat / Rusak</option>
                            <option value="Barang Hilang / Selisih Rak">Barang Hilang / Selisih Rak</option>
                            <option value="Sampel / Display Toko">Sampel / Display Toko</option>
                            <option value="Koreksi Salah Input Kasir">Koreksi Salah Input Kasir</option>
                        </select>
                    </div>

                    <button type="submit" class="btn btn-warning w-100 rounded-3 fw-bold py-2 text-dark">
                        <i class="bi bi-sliders me-1"></i> Sesuaikan Stok Sistem
                    </button>
                </form>
            </div>
        </div>

        <!-- Riwayat Penyesuaian -->
        <div class="col-lg-7">
            <div class="card-custom p-4 h-100">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <h6 class="fw-bold text-dark mb-0">Riwayat Penyesuaian Terakhir</h6>
                    <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle">Audit Fisik</span>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light small">
                            <tr>
                                <th>Waktu</th>
                                <th>Produk & Varian</th>
                                <th class="text-center">Sebelum &rarr; Sesudah</th>
                                <th class="text-center">Selisih</th>
                                <th>Alasan & Operator</th>
                            </tr>
                        </thead>
                        <tbody class="small">
                            <?php if (empty($recentAdjustments)): ?>
                                <tr><td colspan="5" class="text-center text-muted py-4">Belum ada riwayat penyesuaian fisik.</td></tr>
                            <?php else: ?>
                                <?php foreach ($recentAdjustments as $a): ?>
                                    <tr>
                                        <td class="text-muted"><?= App::formatTanggalPendek($a['created_at']) ?></td>
                                        <td>
                                            <strong class="d-block text-dark"><?= htmlspecialchars($a['product_name']) ?></strong>
                                            <span class="badge bg-light text-muted border" style="font-size:0.7rem;">
                                                <?= htmlspecialchars($a['size']) ?> - <?= htmlspecialchars($a['color']) ?>
                                            </span>
                                        </td>
                                        <td class="text-center text-muted">
                                            <?= $a['stock_before'] ?> &rarr; <strong class="text-dark"><?= $a['stock_after'] ?></strong>
                                        </td>
                                        <td class="text-center">
                                            <?php if ($a['quantity'] > 0): ?>
                                                <span class="badge bg-success-subtle text-success border border-success-subtle fw-bold">+<?= $a['quantity'] ?></span>
                                            <?php elseif ($a['quantity'] < 0): ?>
                                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle fw-bold"><?= $a['quantity'] ?></span>
                                            <?php else: ?>
                                                <span class="badge bg-light text-muted border">0</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <span class="text-dark small d-block"><?= htmlspecialchars($a['reference_type'] ?? '') ?></span>
                                            <small class="text-muted"><?= htmlspecialchars($a['user_name']) ?></small>
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

<script>
    const selectVariant = document.getElementById('adjVariantSelect');
    const systemStockInput = document.getElementById('adjSystemStock');
    const physicalStockInput = document.getElementById('adjPhysicalStock');
    const diffLabel = document.getElementById('adjDiffLabel');

    selectVariant.addEventListener('change', function() {
        const opt = this.options[this.selectedIndex];
        const stock = parseInt(opt.dataset.stock) || 0;
        systemStockInput.value = stock + ' pcs';
        calculateDiff();
    });

    physicalStockInput.addEventListener('input', calculateDiff);

    function calculateDiff() {
        const opt = selectVariant.options[selectVariant.selectedIndex];
        if (!opt || !opt.value) return;

        const systemStock = parseInt(opt.dataset.stock) || 0;
        const physicalStock = parseInt(physicalStockInput.value) || 0;
        const diff = physicalStock - systemStock;

        if (diff > 0) {
            diffLabel.textContent = `+${diff} pcs (Kelebihan Fisik)`;
            diffLabel.className = 'fw-bold fs-6 text-success';
        } else if (diff < 0) {
            diffLabel.textContent = `${diff} pcs (Kekurangan Fisik)`;
            diffLabel.className = 'fw-bold fs-6 text-danger';
        } else {
            diffLabel.textContent = '0 pcs (Sesuai)';
            diffLabel.className = 'fw-bold fs-6 text-dark';
        }
    }
</script>
