<?php use App\Config\App; ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Laporan Penjualan - Yunda Fashion</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            color: #1e293b;
            padding: 24px;
            font-size: 12px;
        }

        .header-title {
            text-align: center;
            border-bottom: 2px solid #000;
            padding-bottom: 12px;
            margin-bottom: 20px;
        }

        .header-title h2 { margin: 0 0 4px 0; text-transform: uppercase; font-size: 18px; }
        .header-title p { margin: 0; font-size: 12px; color: #475569; }

        .summary-box {
            display: flex;
            gap: 15px;
            margin-bottom: 20px;
        }

        .summary-item {
            flex: 1;
            padding: 10px;
            border: 1px solid #cbd5e1;
            border-radius: 6px;
        }

        .summary-item small { color: #64748b; font-size: 10px; font-weight: bold; text-transform: uppercase; }
        .summary-item h3 { margin: 4px 0 0 0; font-size: 15px; }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        table th, table td {
            border: 1px solid #cbd5e1;
            padding: 6px 8px;
            text-align: left;
        }

        table th {
            background: #f1f5f9;
            font-weight: bold;
        }

        .text-end { text-align: right; }
        .text-center { text-align: center; }

        .btn-print {
            padding: 8px 16px;
            background: #be123c;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-weight: bold;
        }

        @media print {
            .no-print { display: none !important; }
            body { padding: 0; }
        }
    </style>
</head>
<body>

    <div class="no-print" style="margin-bottom: 20px; text-align: right;">
        <button class="btn-print" onclick="window.print()">🖨️ Cetak / Simpan PDF</button>
    </div>

    <div class="header-title">
        <h2>YUNDA FASHION</h2>
        <p>Laporan Penjualan & Rekapitulasi Kasir | Periode: <?= date('d/m/Y', strtotime($startDate)) ?> - <?= date('d/m/Y', strtotime($endDate)) ?></p>
    </div>

    <div class="summary-box">
        <div class="summary-item">
            <small>Total Omzet</small>
            <h3><?= App::formatRupiah($totalRevenue) ?></h3>
        </div>
        <div class="summary-item">
            <small>Jumlah Transaksi</small>
            <h3><?= number_format($totalTransactions) ?> Struk</h3>
        </div>
        <div class="summary-item">
            <small>Total Terjual</small>
            <h3><?= number_format($totalQtySold) ?> Pcs Pakaian</h3>
        </div>
    </div>

    <h4 style="margin-bottom: 8px;">Daftar Transaksi Kasir</h4>
    <table>
        <thead>
            <tr>
                <th style="width: 30px;">No.</th>
                <th>No. Invoice</th>
                <th>Tanggal & Waktu</th>
                <th>Kasir Bertugas</th>
                <th class="text-center">Total Item</th>
                <th class="text-end">Total Belanja (Rp)</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($sales)): ?>
                <tr><td colspan="6" class="text-center">Tidak ada transaksi pada periode ini.</td></tr>
            <?php else: ?>
                <?php foreach ($sales as $idx => $s): ?>
                    <tr>
                        <td class="text-center"><?= $idx + 1 ?></td>
                        <td><strong><?= htmlspecialchars($s['invoice_number']) ?></strong></td>
                        <td><?= App::formatTanggalPendek($s['created_at']) ?></td>
                        <td><?= htmlspecialchars($s['cashier_name']) ?></td>
                        <td class="text-center"><?= $s['total_qty_sold'] ?> pcs</td>
                        <td class="text-end"><?= number_format($s['total_amount'], 0, ',', '.') ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
        <tfoot>
            <tr style="background:#f8fafc; font-weight:bold;">
                <td colspan="5" class="text-end">TOTAL KESELURUHAN:</td>
                <td class="text-end"><?= App::formatRupiah($totalRevenue) ?></td>
            </tr>
        </tfoot>
    </table>

    <table style="width: 100%; border: none; margin-top: 40px;">
        <tr style="border: none;">
            <td style="border: none; width: 60%;"></td>
            <td style="border: none; text-align: center;">
                <p>Langsa, <?= date('d F Y') ?></p>
                <p style="margin-top: 60px;"><strong>( Owner / Pemilik Toko )</strong><br>Yunda Fashion</p>
            </td>
        </tr>
    </table>

</body>
</html>
