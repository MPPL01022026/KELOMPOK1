<?php use App\Config\App; ?>

<div class="container-fluid px-0">
    <div class="row align-items-center mb-4">
        <div class="col-md-6">
            <h4 class="fw-bold text-dark mb-1"><?= htmlspecialchars($pageTitle) ?></h4>
            <p class="text-secondary small mb-0">Catatan faktur penjualan, identitas kasir, dan ringkasan pembayaran.</p>
        </div>
        <div class="col-md-6 text-md-end mt-3 mt-md-0">
            <a href="<?= $baseUrl ?>/pos" class="btn btn-danger d-inline-flex align-items-center gap-2 fw-semibold px-3 py-2 rounded-3" style="background:#be123c; border:none;">
                <i class="bi bi-calculator"></i>
                <span>Buka Kasir POS</span>
            </a>
        </div>
    </div>

    <!-- Filter Form -->
    <div class="card-custom p-3 mb-4">
        <form method="GET" action="<?= $baseUrl ?>/transactions" class="row g-2 align-items-center">
            <div class="col-md-3">
                <label class="form-label small fw-bold text-secondary mb-1">Mulai Tanggal</label>
                <input type="date" name="start_date" class="form-control" value="<?= htmlspecialchars($startDate ?? '') ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-bold text-secondary mb-1">Sampai Tanggal</label>
                <input type="date" name="end_date" class="form-control" value="<?= htmlspecialchars($endDate ?? '') ?>">
            </div>

            <?php if (($currentUser['role'] ?? '') === 'admin'): ?>
                <div class="col-md-3">
                    <label class="form-label small fw-bold text-secondary mb-1">Petugas Kasir</label>
                    <select name="user_id" class="form-select">
                        <option value="">Semua Kasir</option>
                        <?php foreach ($cashiers as $c): ?>
                            <option value="<?= $c['id'] ?>" <?= ($selectedUserId == $c['id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($c['name']) ?> (<?= $c['role'] ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            <?php endif; ?>

            <div class="<?= ($currentUser['role'] ?? '') === 'admin' ? 'col-md-3' : 'col-md-6' ?> d-flex gap-2 align-items-end">
                <button type="submit" class="btn btn-dark w-100 rounded-3 fw-semibold">
                    <i class="bi bi-funnel me-1"></i> Terapkan Filter
                </button>
                <a href="<?= $baseUrl ?>/transactions" class="btn btn-light border rounded-3" title="Reset">
                    <i class="bi bi-arrow-counterclockwise"></i>
                </a>
            </div>
        </form>
    </div>

    <!-- Tabel Transaksi -->
    <div class="card-custom p-4">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light small">
                    <tr>
                        <th>No. Invoice</th>
                        <th>Waktu Transaksi</th>
                        <th>Kasir</th>
                        <th class="text-center">Total Item</th>
                        <th class="text-end">Total Belanja</th>
                        <th class="text-end">Uang Dibayar</th>
                        <th class="text-end">Kembalian</th>
                        <th class="text-center" style="width: 130px;">Aksi</th>
                    </tr>
                </thead>
                <tbody class="small">
                    <?php if (empty($sales)): ?>
                        <tr><td colspan="8" class="text-center text-muted py-5">Tidak ada riwayat transaksi yang ditemukan.</td></tr>
                    <?php else: ?>
                        <?php foreach ($sales as $s): ?>
                            <tr>
                                <td>
                                    <span class="fw-bold text-danger"><?= htmlspecialchars($s['invoice_number']) ?></span>
                                    <span class="badge bg-light text-muted border ms-1" style="font-size:0.65rem;">TUNAI</span>
                                </td>
                                <td class="text-muted"><?= App::formatTanggalPendek($s['created_at']) ?></td>
                                <td>
                                    <span class="fw-semibold text-dark"><?= htmlspecialchars($s['cashier_name']) ?></span>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-secondary-subtle text-dark border">
                                        <?= (int)$s['total_qty_sold'] ?> pcs
                                    </span>
                                </td>
                                <td class="text-end fw-bold text-dark"><?= App::formatRupiah($s['total_amount']) ?></td>
                                <td class="text-end text-muted"><?= App::formatRupiah($s['paid_amount']) ?></td>
                                <td class="text-end text-success fw-semibold"><?= App::formatRupiah($s['change_amount']) ?></td>
                                <td class="text-center">
                                    <a href="<?= $baseUrl ?>/transactions/detail/<?= $s['id'] ?>" class="btn btn-sm btn-light border py-1 px-2 me-1" title="Detail Transaksi">
                                        <i class="bi bi-eye text-primary"></i>
                                    </a>
                                    <a href="<?= $baseUrl ?>/transactions/receipt/<?= $s['id'] ?>" target="_blank" class="btn btn-sm btn-light border py-1 px-2" title="Cetak Struk Thermal 80mm">
                                        <i class="bi bi-printer text-dark"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
