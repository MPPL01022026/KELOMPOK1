<?php
/**
 * View Riwayat Pergerakan Stok (Stock Movements)
 * Developer: Salsabila (230504097)
 */
use App\Config\App;
?>

<div class="container-fluid px-0">
    <div class="row align-items-center mb-4">
        <div class="col-md-7">
            <h4 class="fw-bold text-dark mb-1">Riwayat Pergerakan Persediaan (Stock Movements)</h4>
            <p class="text-secondary small mb-0">Audit jejak digital mutasi persediaan: transaksi kasir, pasokan barang masuk, dan penyesuaian fisik.</p>
        </div>
        <div class="col-md-5 text-md-end mt-3 mt-md-0 d-flex gap-2 justify-content-md-end">
            <a href="<?= $baseUrl ?>/stock/restock" class="btn btn-success d-inline-flex align-items-center gap-1 rounded-3 fw-semibold">
                <i class="bi bi-box-arrow-in-down"></i> Restok
            </a>
            <a href="<?= $baseUrl ?>/stock/adjustment" class="btn btn-warning d-inline-flex align-items-center gap-1 rounded-3 fw-bold text-dark">
                <i class="bi bi-sliders"></i> Penyesuaian
            </a>
        </div>
    </div>

    <!-- Filter Tipe Mutasi -->
    <div class="card-custom p-3 mb-4">
        <div class="d-flex flex-wrap gap-2 align-items-center">
            <span class="small fw-bold text-secondary me-2">Filter Jenis Aktivitas:</span>
            <a href="<?= $baseUrl ?>/stock/movements" class="btn btn-sm <?= empty($selectedType) ? 'btn-dark' : 'btn-light border' ?> rounded-pill px-3">
                Semua Aktivitas
            </a>
            <a href="<?= $baseUrl ?>/stock/movements?type=sale" class="btn btn-sm <?= ($selectedType === 'sale') ? 'btn-danger' : 'btn-light border' ?> rounded-pill px-3">
                <i class="bi bi-cart-check me-1"></i> Penjualan Kasir (Sale)
            </a>
            <a href="<?= $baseUrl ?>/stock/movements?type=restock" class="btn btn-sm <?= ($selectedType === 'restock') ? 'btn-success' : 'btn-light border' ?> rounded-pill px-3">
                <i class="bi bi-box-arrow-in-down me-1"></i> Barang Masuk (Restock)
            </a>
            <a href="<?= $baseUrl ?>/stock/movements?type=adjustment" class="btn btn-sm <?= ($selectedType === 'adjustment') ? 'btn-warning text-dark' : 'btn-light border' ?> rounded-pill px-3">
                <i class="bi bi-sliders me-1"></i> Penyesuaian Fisik (Adjustment)
            </a>
        </div>
    </div>

    <!-- Tabel Mutasi -->
    <div class="card-custom p-4">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light small">
                    <tr>
                        <th style="width: 150px;">Tanggal & Waktu</th>
                        <th>Produk Pakaian & Varian</th>
                        <th class="text-center">Jenis Aktivitas</th>
                        <th class="text-center">Perubahan (Qty)</th>
                        <th class="text-center">Sebelum &rarr; Sesudah</th>
                        <th>Referensi / Keterangan</th>
                        <th>Petugas</th>
                    </tr>
                </thead>
                <tbody class="small">
                    <?php if (empty($movements)): ?>
                        <tr><td colspan="7" class="text-center text-muted py-5">Belum ada catatan mutasi persediaan.</td></tr>
                    <?php else: ?>
                        <?php foreach ($movements as $m): ?>
                            <tr>
                                <td class="text-muted"><?= App::formatTanggalPendek($m['created_at']) ?></td>
                                <td>
                                    <strong class="d-block text-dark"><?= htmlspecialchars($m['product_name']) ?></strong>
                                    <span class="badge bg-light text-muted border" style="font-size:0.7rem;">
                                        <?= htmlspecialchars($m['category_name']) ?> | <?= htmlspecialchars($m['size']) ?> - <?= htmlspecialchars($m['color']) ?>
                                    </span>
                                </td>
                                <td class="text-center">
                                    <?php if ($m['movement_type'] === 'sale'): ?>
                                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill px-2">Penjualan</span>
                                    <?php elseif ($m['movement_type'] === 'restock'): ?>
                                        <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2">Restok Masuk</span>
                                    <?php else: ?>
                                        <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle rounded-pill px-2">Penyesuaian</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <?php if ($m['quantity'] > 0): ?>
                                        <span class="badge bg-success text-white fw-bold">+<?= $m['quantity'] ?> pcs</span>
                                    <?php elseif ($m['quantity'] < 0): ?>
                                        <span class="badge bg-danger text-white fw-bold"><?= $m['quantity'] ?> pcs</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary text-white">0 pcs</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center text-muted">
                                    <?= $m['stock_before'] ?> &rarr; <strong class="text-dark"><?= $m['stock_after'] ?></strong>
                                </td>
                                <td>
                                    <?php if ($m['movement_type'] === 'sale' && !empty($m['reference_id'])): ?>
                                        <a href="<?= $baseUrl ?>/transactions/detail/<?= $m['reference_id'] ?>" class="text-danger fw-semibold text-decoration-none">
                                            Penjualan #<?= $m['reference_id'] ?> <i class="bi bi-box-arrow-up-right small"></i>
                                        </a>
                                    <?php else: ?>
                                        <span class="text-secondary"><?= htmlspecialchars($m['reference_type'] ?? '-') ?></span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="fw-semibold text-dark"><?= htmlspecialchars($m['user_name']) ?></span>
                                    <small class="text-muted d-block" style="font-size:0.68rem;">@<?= htmlspecialchars($m['username']) ?></small>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
