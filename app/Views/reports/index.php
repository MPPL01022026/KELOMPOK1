<?php use App\Config\App; ?>

<div class="container-fluid px-0">
    <div class="row align-items-center mb-4">
        <div class="col-md-6">
            <h4 class="fw-bold text-dark mb-1">Laporan Penjualan & Analitik</h4>
            <p class="text-secondary small mb-0">Evaluasi performa omzet toko, produk terlaris, dan rekapitulasi transaksi harian/bulanan.</p>
        </div>
        <div class="col-md-6 text-md-end mt-3 mt-md-0 d-flex gap-2 justify-content-md-end">
            <a href="<?= $baseUrl ?>/reports/export?start_date=<?= urlencode($startDate) ?>&end_date=<?= urlencode($endDate) ?>" class="btn btn-outline-success d-inline-flex align-items-center gap-1 rounded-3 fw-semibold">
                <i class="bi bi-file-earmark-spreadsheet"></i> Ekspor CSV / Excel
            </a>
            <a href="<?= $baseUrl ?>/reports/print?start_date=<?= urlencode($startDate) ?>&end_date=<?= urlencode($endDate) ?>" target="_blank" class="btn btn-danger d-inline-flex align-items-center gap-1 rounded-3 fw-semibold" style="background:#be123c; border:none;">
                <i class="bi bi-printer"></i> Cetak Laporan
            </a>
        </div>
    </div>

    <!-- Filter Rentang Tanggal -->
    <div class="card-custom p-3 mb-4">
        <form method="GET" action="<?= $baseUrl ?>/reports" class="row g-2 align-items-end">
            <div class="col-md-4">
                <label class="form-label small fw-bold text-secondary mb-1">Dari Tanggal</label>
                <input type="date" name="start_date" class="form-control" value="<?= htmlspecialchars($startDate) ?>" required>
            </div>
            <div class="col-md-4">
                <label class="form-label small fw-bold text-secondary mb-1">Sampai Tanggal</label>
                <input type="date" name="end_date" class="form-control" value="<?= htmlspecialchars($endDate) ?>" required>
            </div>
            <div class="col-md-4 d-flex gap-2">
                <button type="submit" class="btn btn-dark w-100 rounded-3 fw-semibold">
                    <i class="bi bi-funnel me-1"></i> Tampilkan Laporan
                </button>
                <a href="<?= $baseUrl ?>/reports" class="btn btn-light border rounded-3" title="Bulan Ini">
                    <i class="bi bi-arrow-counterclockwise"></i>
                </a>
            </div>
        </form>
    </div>

    <!-- 3 Rekapitulasi Angka Utama -->
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card-custom p-3 border-danger-subtle bg-danger-subtle bg-opacity-10">
                <span class="text-danger small fw-bold text-uppercase d-block mb-1">Total Pendapatan (Omzet)</span>
                <h3 class="fw-bold text-danger mb-0"><?= App::formatRupiah($totalRevenue) ?></h3>
                <small class="text-muted">Periode: <?= date('d M Y', strtotime($startDate)) ?> s/d <?= date('d M Y', strtotime($endDate)) ?></small>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card-custom p-3">
                <span class="text-secondary small fw-bold text-uppercase d-block mb-1">Total Transaksi Selesai</span>
                <h3 class="fw-bold text-dark mb-0"><?= number_format($totalTransactions) ?> <small class="fs-6 fw-normal text-muted">transaksi</small></h3>
                <small class="text-muted">Rata-rata: <?= $totalTransactions > 0 ? App::formatRupiah($totalRevenue / $totalTransactions) : 'Rp 0' ?> / transaksi</small>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card-custom p-3 border-success-subtle bg-success-subtle bg-opacity-10">
                <span class="text-success small fw-bold text-uppercase d-block mb-1">Total Pakaian Terjual</span>
                <h3 class="fw-bold text-success mb-0"><?= number_format($totalQtySold) ?> <small class="fs-6 fw-normal text-muted">pcs</small></h3>
                <small class="text-muted">Akumulasi seluruh varian pakaian</small>
            </div>
        </div>
    </div>

    <!-- Top 5 Produk & Detail Transaksi -->
    <div class="row g-4 mb-4">
        <!-- 5 Produk Terlaris -->
        <div class="col-lg-5">
            <div class="card-custom p-4 h-100">
                <h6 class="fw-bold text-dark mb-3">5 Produk Terlaris (Top Selling)</h6>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light small">
                            <tr>
                                <th>Produk</th>
                                <th class="text-center">Terjual</th>
                                <th class="text-end">Kontribusi</th>
                            </tr>
                        </thead>
                        <tbody class="small">
                            <?php if (empty($topProducts)): ?>
                                <tr><td colspan="3" class="text-center text-muted py-3">Belum ada produk terjual.</td></tr>
                            <?php else: ?>
                                <?php foreach ($topProducts as $idx => $tp): ?>
                                    <tr>
                                        <td>
                                            <span class="badge bg-danger-subtle text-danger rounded-circle me-1" style="width:20px;height:20px;line-height:12px;font-size:0.7rem;display:inline-flex;align-items:center;justify-content:center;">
                                                <?= $idx + 1 ?>
                                            </span>
                                            <strong class="text-dark"><?= htmlspecialchars($tp['product_name']) ?></strong>
                                            <small class="d-block text-muted"><?= htmlspecialchars($tp['category_name']) ?></small>
                                        </td>
                                        <td class="text-center fw-bold"><?= $tp['total_sold'] ?> pcs</td>
                                        <td class="text-end text-success fw-semibold"><?= App::formatRupiah($tp['total_revenue']) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Rincian Daftar Faktur -->
        <div class="col-lg-7">
            <div class="card-custom p-4 h-100">
                <h6 class="fw-bold text-dark mb-3">Daftar Transaksi Periode Terpilih</h6>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light small">
                            <tr>
                                <th>Invoice</th>
                                <th>Tanggal</th>
                                <th>Kasir</th>
                                <th class="text-center">Qty</th>
                                <th class="text-end">Total Belanja</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="small">
                            <?php if (empty($sales)): ?>
                                <tr><td colspan="6" class="text-center text-muted py-4">Tidak ada data transaksi pada rentang tanggal ini.</td></tr>
                            <?php else: ?>
                                <?php foreach ($sales as $s): ?>
                                    <tr>
                                        <td class="fw-bold text-danger"><?= htmlspecialchars($s['invoice_number']) ?></td>
                                        <td class="text-muted"><?= App::formatTanggalPendek($s['created_at']) ?></td>
                                        <td><?= htmlspecialchars($s['cashier_name']) ?></td>
                                        <td class="text-center"><?= $s['total_qty_sold'] ?> pcs</td>
                                        <td class="text-end fw-bold"><?= App::formatRupiah($s['total_amount']) ?></td>
                                        <td class="text-center">
                                            <a href="<?= $baseUrl ?>/transactions/detail/<?= $s['id'] ?>" class="btn btn-sm btn-light border py-0 px-2" title="Detail">
                                                <i class="bi bi-eye"></i>
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
    </div>
</div>
