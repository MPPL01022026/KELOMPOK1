<?php use App\Config\App; ?>

<div class="container-fluid px-0">
    <div class="row align-items-center mb-4">
        <div class="col-md-7">
            <h4 class="fw-bold text-dark mb-1">Faktur Penjualan: <?= htmlspecialchars($sale['invoice_number']) ?></h4>
            <p class="text-secondary small mb-0">Rincian item pakaian yang terjual dan informasi pembayaran kasir.</p>
        </div>
        <div class="col-md-5 text-md-end mt-3 mt-md-0 d-flex gap-2 justify-content-md-end">
            <a href="<?= $baseUrl ?>/transactions" class="btn btn-light border d-inline-flex align-items-center gap-1 fw-semibold px-3 py-2 rounded-3">
                <i class="bi bi-arrow-left"></i> Kembali
            </a>
            <a href="<?= $baseUrl ?>/transactions/receipt/<?= $sale['id'] ?>" target="_blank" class="btn btn-danger d-inline-flex align-items-center gap-1 fw-semibold px-3 py-2 rounded-3" style="background:#be123c; border:none;">
                <i class="bi bi-printer"></i> Cetak Struk 80mm
            </a>
        </div>
    </div>

    <div class="row g-4">
        <!-- Rincian Produk Terjual -->
        <div class="col-lg-8">
            <div class="card-custom p-4 h-100">
                <h6 class="fw-bold text-dark mb-3">Item Pakaian Terjual</h6>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light small">
                            <tr>
                                <th>Produk Pakaian</th>
                                <th>Ukuran & Warna</th>
                                <th class="text-end">Harga Satuan</th>
                                <th class="text-center">Jumlah (Qty)</th>
                                <th class="text-end">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody class="small">
                            <?php foreach ($sale['items'] as $item): ?>
                                <tr>
                                    <td>
                                        <strong class="d-block text-dark"><?= htmlspecialchars($item['product_name']) ?></strong>
                                        <span class="badge bg-light text-muted border" style="font-size:0.7rem;"><?= htmlspecialchars($item['category_name']) ?></span>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark border">
                                            Ukuran: <?= htmlspecialchars($item['size']) ?> | <?= htmlspecialchars($item['color']) ?>
                                        </span>
                                    </td>
                                    <td class="text-end text-muted"><?= App::formatRupiah($item['unit_price']) ?></td>
                                    <td class="text-center fw-bold fs-6"><?= $item['quantity'] ?> pcs</td>
                                    <td class="text-end fw-bold text-dark"><?= App::formatRupiah($item['subtotal']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot class="border-top">
                            <tr>
                                <th colspan="4" class="text-end fs-6">Grand Total:</th>
                                <th class="text-end fs-5 fw-bold text-danger"><?= App::formatRupiah($sale['total_amount']) ?></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        <!-- Info Pembayaran & Kasir -->
        <div class="col-lg-4">
            <div class="card-custom p-4 h-100">
                <h6 class="fw-bold text-dark mb-3">Informasi Pembayaran</h6>

                <div class="list-group list-group-flush small mb-4">
                    <div class="list-group-item px-0 py-2 d-flex justify-content-between">
                        <span class="text-muted">Nomor Invoice:</span>
                        <strong class="text-danger"><?= htmlspecialchars($sale['invoice_number']) ?></strong>
                    </div>
                    <div class="list-group-item px-0 py-2 d-flex justify-content-between">
                        <span class="text-muted">Waktu Transaksi:</span>
                        <span><?= App::formatTanggal($sale['created_at']) ?></span>
                    </div>
                    <div class="list-group-item px-0 py-2 d-flex justify-content-between">
                        <span class="text-muted">Kasir Bertugas:</span>
                        <strong><?= htmlspecialchars($sale['cashier_name']) ?> (@<?= htmlspecialchars($sale['cashier_username']) ?>)</strong>
                    </div>
                    <div class="list-group-item px-0 py-2 d-flex justify-content-between">
                        <span class="text-muted">Metode Pembayaran:</span>
                        <span class="badge bg-success-subtle text-success border border-success-subtle">Tunai (Cash)</span>
                    </div>
                    <div class="list-group-item px-0 py-2 d-flex justify-content-between">
                        <span class="text-muted">Total Belanja:</span>
                        <strong class="fs-6 text-dark"><?= App::formatRupiah($sale['total_amount']) ?></strong>
                    </div>
                    <div class="list-group-item px-0 py-2 d-flex justify-content-between">
                        <span class="text-muted">Uang Dibayarkan:</span>
                        <span class="fw-bold text-dark"><?= App::formatRupiah($sale['paid_amount']) ?></span>
                    </div>
                    <div class="list-group-item px-0 py-2 d-flex justify-content-between">
                        <span class="text-muted">Kembalian:</span>
                        <span class="fw-bold text-success fs-6"><?= App::formatRupiah($sale['change_amount']) ?></span>
                    </div>
                </div>

                <div class="p-3 bg-light rounded-3 border text-center small text-muted">
                    <i class="bi bi-check-circle-fill text-success fs-5 d-block mb-1"></i>
                    Stok persediaan telah disinkronkan dan tercatat pada buku log <strong>stock_movements</strong> secara atomik.
                </div>
            </div>
        </div>
    </div>
</div>
