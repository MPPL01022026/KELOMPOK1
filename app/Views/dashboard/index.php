<?php use App\Config\App; ?>

<div class="container-fluid px-0">
    <!-- Header & Action -->
    <div class="row align-items-center mb-3">
        <div class="col-md-7">
            <h5 class="fw-bold text-dark mb-1">Dashboard Monitoring Toko</h5>
            <p class="text-secondary small mb-0">Ringkasan performa penjualan dan monitoring status persediaan fisik Yunda Fashion.</p>
        </div>
        <div class="col-md-5 text-md-end mt-2 mt-md-0 d-flex gap-2 justify-content-md-end">
            <a href="<?= $baseUrl ?>/pos" class="btn btn-sm btn-danger d-inline-flex align-items-center gap-1 fw-semibold px-3 py-2 rounded-2" style="background:#be123c; border:none;">
                <i class="bi bi-calculator"></i>
                <span>Buka Kasir POS</span>
            </a>
            <a href="<?= $baseUrl ?>/digital-twin" class="btn btn-sm btn-light border d-inline-flex align-items-center gap-1 fw-semibold px-3 py-2 rounded-2">
                <i class="bi bi-cpu text-info"></i>
                <span>Etalase Digital Twin</span>
            </a>
        </div>
    </div>

    <!-- 4 Metric Cards -->
    <div class="row g-3 mb-3">
        <div class="col-sm-6 col-xl-3">
            <div class="clean-card p-3 h-100">
                <span class="text-muted small fw-semibold text-uppercase d-block mb-1">Omzet Hari Ini</span>
                <h4 class="fw-bold text-dark mb-1"><?= App::formatRupiah($todaySummary['today_omzet']) ?></h4>
                <span class="badge bg-success-subtle text-success border border-success-subtle small fw-normal">
                    <i class="bi bi-arrow-up-right me-1"></i> Penjualan Berjalan
                </span>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="clean-card p-3 h-100">
                <span class="text-muted small fw-semibold text-uppercase d-block mb-1">Transaksi Hari Ini</span>
                <h4 class="fw-bold text-dark mb-1"><?= number_format($todaySummary['today_transactions']) ?> <small class="text-muted fs-6 fw-normal">struk</small></h4>
                <span class="text-muted small"><?= number_format($todaySummary['today_qty']) ?> pcs pakaian terjual</span>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="clean-card p-3 h-100 border-start border-4 border-danger">
                <span class="text-muted small fw-semibold text-uppercase d-block mb-1">Varian Stok Kritis</span>
                <h4 class="fw-bold text-danger mb-1"><?= number_format($criticalCount) ?> <small class="text-muted fs-6 fw-normal">item</small></h4>
                <a href="<?= $baseUrl ?>/stock/restock" class="text-danger small fw-semibold text-decoration-none">
                    Restok Segera <i class="bi bi-arrow-right"></i>
                </a>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="clean-card p-3 h-100">
                <span class="text-muted small fw-semibold text-uppercase d-block mb-1">Status Digital Twin</span>
                <div class="d-flex align-items-center gap-2 mb-1">
                    <span class="pulse-live"></span>
                    <h5 class="fw-bold text-dark mb-0">Live Sinkron</h5>
                </div>
                <span class="text-muted small">Representasi rak fisik aktif</span>
            </div>
        </div>
    </div>

    <!-- Charts & Stok Alert -->
    <div class="row g-3 mb-3">
        <!-- Grafik Tren Penjualan -->
        <div class="col-lg-8">
            <div class="clean-card p-4 h-100">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div>
                        <h6 class="fw-bold text-dark mb-0">Tren Penjualan 7 Hari Terakhir</h6>
                        <small class="text-muted">Omzet harian toko Yunda Fashion</small>
                    </div>
                    <span class="badge bg-light text-secondary border small">Mingguan</span>
                </div>
                <div style="height: 260px; position: relative;">
                    <canvas id="weeklySalesChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Varian Stok Kritis -->
        <div class="col-lg-4">
            <div class="clean-card p-4 h-100">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <h6 class="fw-bold text-dark mb-0">Perhatian Persediaan</h6>
                    <a href="<?= $baseUrl ?>/stock/restock" class="btn btn-sm btn-outline-danger py-0 px-2 small rounded">
                        Restok
                    </a>
                </div>
                <small class="text-muted d-block mb-3">Varian dengan stok &le; 3 pcs</small>

                <div class="list-group list-group-flush">
                    <?php if (empty($criticalVariants)): ?>
                        <div class="text-center py-4 text-muted small">
                            Semua stok dalam kondisi aman.
                        </div>
                    <?php else: ?>
                        <?php foreach (array_slice($criticalVariants, 0, 5) as $cv): ?>
                            <div class="list-group-item px-0 py-2 border-light d-flex align-items-center justify-content-between">
                                <div style="max-width: 65%;">
                                    <strong class="d-block text-dark small text-truncate"><?= htmlspecialchars($cv['product_name']) ?></strong>
                                    <span class="badge bg-light text-muted border" style="font-size:0.7rem;">
                                        <?= htmlspecialchars($cv['size']) ?> | <?= htmlspecialchars($cv['color']) ?>
                                    </span>
                                </div>
                                <div class="text-end">
                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill px-2 py-0" style="font-size:0.75rem;">
                                        <?= $cv['stock'] ?> pcs
                                    </span>
                                    <small class="d-block text-muted" style="font-size:0.68rem;">Min: <?= $cv['min_stock'] ?></small>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Top 5 Products & Recent Sales -->
    <div class="row g-3">
        <div class="col-lg-6">
            <div class="clean-card p-4 h-100">
                <h6 class="fw-bold text-dark mb-3">5 Produk Terlaris (Top Selling)</h6>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light small">
                            <tr>
                                <th>Produk</th>
                                <th>Kategori</th>
                                <th class="text-center">Terjual</th>
                                <th class="text-end">Omzet</th>
                            </tr>
                        </thead>
                        <tbody class="small">
                            <?php if (empty($topProducts)): ?>
                                <tr><td colspan="4" class="text-center text-muted py-3">Belum ada data penjualan.</td></tr>
                            <?php else: ?>
                                <?php foreach ($topProducts as $tp): ?>
                                    <tr>
                                        <td><strong class="text-dark"><?= htmlspecialchars($tp['product_name']) ?></strong></td>
                                        <td><span class="badge bg-light text-muted border"><?= htmlspecialchars($tp['category_name']) ?></span></td>
                                        <td class="text-center fw-semibold"><?= number_format($tp['total_sold']) ?> pcs</td>
                                        <td class="text-end text-success fw-semibold"><?= App::formatRupiah($tp['total_revenue']) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="clean-card p-4 h-100">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <h6 class="fw-bold text-dark mb-0">Transaksi Penjualan Terakhir</h6>
                    <a href="<?= $baseUrl ?>/transactions" class="small text-danger text-decoration-none fw-semibold">
                        Semua <i class="bi bi-chevron-right"></i>
                    </a>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light small">
                            <tr>
                                <th>No. Invoice</th>
                                <th>Kasir</th>
                                <th>Waktu</th>
                                <th class="text-end">Total</th>
                            </tr>
                        </thead>
                        <tbody class="small">
                            <?php if (empty($recentSales)): ?>
                                <tr><td colspan="4" class="text-center text-muted py-3">Belum ada transaksi.</td></tr>
                            <?php else: ?>
                                <?php foreach ($recentSales as $rs): ?>
                                    <tr>
                                        <td><span class="fw-semibold text-danger"><?= htmlspecialchars($rs['invoice_number']) ?></span></td>
                                        <td><?= htmlspecialchars($rs['cashier_name']) ?></td>
                                        <td class="text-muted"><?= App::formatTanggalPendek($rs['created_at']) ?></td>
                                        <td class="text-end fw-bold"><?= App::formatRupiah($rs['total_amount']) ?></td>
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
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('weeklySalesChart');
        if (!ctx) return;

        const labels = <?= json_encode($chartData['labels'] ?? []) ?>;
        const revenues = <?= json_encode($chartData['revenues'] ?? []) ?>;

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Omzet Harian (Rp)',
                    data: revenues,
                    borderColor: '#be123c',
                    backgroundColor: 'rgba(190, 18, 60, 0.05)',
                    borderWidth: 2.5,
                    pointBackgroundColor: '#be123c',
                    pointBorderColor: '#ffffff',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    tension: 0.3,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(v) {
                                if (v >= 1000000) return (v / 1000000) + ' Jt';
                                if (v >= 1000) return (v / 1000) + ' Rb';
                                return v;
                            }
                        },
                        grid: { color: '#f1f5f9' }
                    },
                    x: { grid: { display: false } }
                }
            }
        });
    });
</script>
