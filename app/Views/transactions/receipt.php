<?php use App\Config\App; ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk Pembayaran - <?= htmlspecialchars($sale['invoice_number']) ?></title>
    <style>
        /* Standar format Thermal Receipt 80mm */
        @page {
            size: 80mm auto;
            margin: 0;
        }

        body {
            font-family: 'Courier New', Courier, monospace;
            width: 76mm;
            margin: 0 auto;
            padding: 12px 6px;
            font-size: 11px;
            color: #000;
            background: #fff;
            line-height: 1.35;
        }

        .text-center { text-align: center; }
        .text-end { text-align: right; }
        .text-start { text-align: left; }
        .fw-bold { font-weight: bold; }

        .store-title {
            font-size: 14px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 2px;
        }

        .store-sub {
            font-size: 9.5px;
            margin-bottom: 6px;
        }

        .dashed-line {
            border-top: 1px dashed #000;
            margin: 6px 0;
        }

        .table-receipt {
            width: 100%;
            border-collapse: collapse;
        }

        .table-receipt th, .table-receipt td {
            padding: 2px 0;
            vertical-align: top;
        }

        .actions-bar {
            text-align: center;
            margin-top: 16px;
            padding: 10px;
        }

        .btn-print {
            padding: 8px 16px;
            background: #000;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-family: sans-serif;
            font-size: 12px;
            font-weight: bold;
        }

        @media print {
            .actions-bar {
                display: none !important;
            }
            body {
                width: 100%;
                padding: 0 4px;
            }
        }
    </style>
</head>
<body>

    <!-- Header Struk -->
    <div class="text-center">
        <div class="store-title">YUNDA FASHION</div>
        <div class="store-sub">Toko Busana Muslim & Pakaian Wanita Modern<br>Jl. Cut Nyak Dhien, Langsa - Aceh</div>
    </div>

    <div class="dashed-line"></div>

    <!-- Info Faktur -->
    <table class="table-receipt">
        <tr>
            <td style="width: 35%;">No. Invoice</td>
            <td style="width: 5%;">:</td>
            <td class="text-end fw-bold"><?= htmlspecialchars($sale['invoice_number']) ?></td>
        </tr>
        <tr>
            <td>Tanggal</td>
            <td>:</td>
            <td class="text-end"><?= date('d/m/Y H:i', strtotime($sale['created_at'])) ?></td>
        </tr>
        <tr>
            <td>Kasir</td>
            <td>:</td>
            <td class="text-end"><?= htmlspecialchars($sale['cashier_name']) ?></td>
        </tr>
        <tr>
            <td>Metode</td>
            <td>:</td>
            <td class="text-end">TUNAI (CASH)</td>
        </tr>
    </table>

    <div class="dashed-line"></div>

    <!-- Daftar Item -->
    <table class="table-receipt">
        <thead>
            <tr>
                <th class="text-start">Item Pakaian</th>
                <th class="text-center" style="width: 15%;">Qty</th>
                <th class="text-end" style="width: 35%;">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($sale['items'] as $item): ?>
                <tr>
                    <td colspan="3" class="fw-bold"><?= htmlspecialchars($item['product_name']) ?></td>
                </tr>
                <tr>
                    <td class="text-start" style="padding-left: 6px; font-size: 10px;">
                        <?= htmlspecialchars($item['size']) ?> / <?= htmlspecialchars($item['color']) ?>
                        <br>
                        @<?= number_format($item['unit_price'], 0, ',', '.') ?>
                    </td>
                    <td class="text-center"><?= $item['quantity'] ?></td>
                    <td class="text-end fw-bold"><?= number_format($item['subtotal'], 0, ',', '.') ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div class="dashed-line"></div>

    <!-- Ringkasan Total & Pembayaran -->
    <table class="table-receipt">
        <tr>
            <td class="fw-bold" style="font-size: 12px;">TOTAL BELANJA</td>
            <td class="text-end fw-bold" style="font-size: 12px;"><?= App::formatRupiah($sale['total_amount']) ?></td>
        </tr>
        <tr>
            <td>Tunai Diterima</td>
            <td class="text-end"><?= App::formatRupiah($sale['paid_amount']) ?></td>
        </tr>
        <tr>
            <td class="fw-bold">Kembalian</td>
            <td class="text-end fw-bold"><?= App::formatRupiah($sale['change_amount']) ?></td>
        </tr>
    </table>

    <div class="dashed-line"></div>

    <!-- Footer Struk -->
    <div class="text-center" style="margin-top: 6px; font-size: 9.5px;">
        *** TERIMA KASIH ***<br>
        Telah Berbelanja di Yunda Fashion<br>
        Pusat Busana Muslim & Wanita Terlengkap<br>
        <em>Barang yang dibeli tidak dapat ditukar kembali</em>
    </div>

    <!-- Tombol Cetak (Tidak tercetak saat diprint) -->
    <div class="actions-bar">
        <button class="btn-print" onclick="window.print()">
            🖨️ CETAK STRUK SEKARANG
        </button>
    </div>

    <script>
        // Cetak otomatis jika dibuka dari tombol kasir
        window.onload = function() {
            // Uncomment jika ingin auto popup print dialog:
            // window.print();
        };
    </script>
</body>
</html>
