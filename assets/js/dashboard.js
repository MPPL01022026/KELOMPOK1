/**
 * dashboard.js — Rendering & Inisialisasi Komponen Dashboard
 *
 * Seluruh data bersumber dari DUMMY_DATA (data.js).
 * Komponen yang dirender:
 *  - Stats cards
 *  - Bar chart: Stok per Kategori (Chart.js)
 *  - Donut chart: Status Stok (Chart.js)
 *  - Legend status stok dengan progress bar
 *  - Tabel produk stok terendah
 *  - Daftar transaksi terbaru
 */

'use strict';

/* ================================================================
   INIT — Jalankan saat DOM siap
   ================================================================ */
document.addEventListener('DOMContentLoaded', () => {
    renderStats();
    renderStockChart();
    renderStatusChart();
    renderStatusLegend();
    renderLowStockTable();
    renderTransactionList();
});

/* ================================================================
   HELPER — Tentukan status stok berdasarkan jumlah
   Hijau  : stock >= 10
   Kuning : 4 <= stock <= 9
   Merah  : stock <= 3
   ================================================================ */
function getStockLevel(stock) {
    if (stock <= 3)  return { key: 'red',    label: 'Kritis',  badge: 'badge-danger',  cls: 'red'    };
    if (stock <= 9)  return { key: 'yellow', label: 'Waspada', badge: 'badge-warning', cls: 'yellow' };
    return               { key: 'green',  label: 'Aman',    badge: 'badge-success', cls: 'green'  };
}

/* ================================================================
   STATS CARDS
   ================================================================ */
function renderStats() {
    const { stats } = DUMMY_DATA;

    const cards = [
        {
            colorClass: 'purple',
            icon:       'fa-box-open',
            value:      stats.totalProducts,
            label:      'Total Produk',
            trend:      '<i class="fas fa-arrow-trend-up"></i>&nbsp;+3 produk bulan ini',
            trendClass: 'up'
        },
        {
            colorClass: 'blue',
            icon:       'fa-layer-group',
            value:      stats.totalVariants,
            label:      'Total Varian',
            trend:      '<i class="fas fa-arrow-trend-up"></i>&nbsp;+12 varian bulan ini',
            trendClass: 'up'
        },
        {
            colorClass: 'green',
            icon:       'fa-warehouse',
            value:      stats.totalStock.toLocaleString('id-ID'),
            label:      'Total Stok (pcs)',
            trend:      '<i class="fas fa-arrow-trend-up"></i>&nbsp;+5,2% dari bulan lalu',
            trendClass: 'up'
        },
        {
            colorClass: 'red',
            icon:       'fa-triangle-exclamation',
            value:      stats.criticalStock,
            label:      'Stok Kritis',
            trend:      '<i class="fas fa-circle-info"></i>&nbsp;Perlu segera direstok',
            trendClass: 'down'
        }
    ];

    const grid = document.getElementById('statsGrid');
    if (!grid) return;

    grid.innerHTML = cards.map(c => `
        <div class="stat-card ${c.colorClass}">
            <div class="stat-icon ${c.colorClass}">
                <i class="fas ${c.icon}"></i>
            </div>
            <div class="stat-content">
                <div class="stat-value">${c.value}</div>
                <div class="stat-label">${c.label}</div>
                <div class="stat-trend ${c.trendClass}">${c.trend}</div>
            </div>
        </div>
    `).join('');
}

/* ================================================================
   BAR CHART — Stok per Kategori
   ================================================================ */
let stockChartInst = null;

function renderStockChart() {
    const ctx = document.getElementById('stockChart');
    if (!ctx) return;

    const { categories } = DUMMY_DATA;

    if (stockChartInst) stockChartInst.destroy();

    stockChartInst = new Chart(ctx.getContext('2d'), {
        type: 'bar',
        data: {
            labels: categories.map(c => c.name),
            datasets: [{
                label: 'Jumlah Stok (pcs)',
                data:  categories.map(c => c.stock),
                backgroundColor: [
                    'rgba(124, 58,  237, 0.82)',
                    'rgba(59,  130, 246, 0.82)',
                    'rgba(236, 72,  153, 0.82)',
                    'rgba(245, 158, 11,  0.82)',
                    'rgba(16,  185, 129, 0.82)'
                ],
                borderColor: categories.map(c => c.color),
                borderWidth: 1.5,
                borderRadius: { topLeft: 7, topRight: 7 },
                borderSkipped: false
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#1E293B',
                    titleColor: 'rgba(255,255,255,0.7)',
                    bodyColor:  '#FFFFFF',
                    padding: 12,
                    cornerRadius: 8,
                    displayColors: false,
                    callbacks: {
                        label: ctx => `  ${ctx.parsed.y.toLocaleString('id-ID')} pcs`
                    }
                }
            },
            scales: {
                x: {
                    grid: { display: false },
                    border: { display: false },
                    ticks: {
                        font:  { family: 'Inter', size: 12 },
                        color: '#64748B'
                    }
                },
                y: {
                    grid: {
                        color:     '#F1F5F9',
                        lineWidth: 1
                    },
                    border: { display: false },
                    ticks: {
                        font:  { family: 'Inter', size: 11 },
                        color: '#94A3B8',
                        callback: v => v.toLocaleString('id-ID')
                    }
                }
            }
        }
    });
}

/* ================================================================
   DONUT CHART — Status Stok
   ================================================================ */
let statusChartInst = null;

function renderStatusChart() {
    const ctx = document.getElementById('statusChart');
    if (!ctx) return;

    const items = Object.values(DUMMY_DATA.stockStatus);

    if (statusChartInst) statusChartInst.destroy();

    statusChartInst = new Chart(ctx.getContext('2d'), {
        type: 'doughnut',
        data: {
            labels:   items.map(d => d.label),
            datasets: [{
                data:            items.map(d => d.count),
                backgroundColor: items.map(d => d.color),
                borderWidth: 0,
                hoverOffset: 5
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '72%',
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#1E293B',
                    padding: 12,
                    cornerRadius: 8,
                    displayColors: false,
                    callbacks: {
                        label: ctx => `  ${ctx.parsed} produk`
                    }
                }
            }
        },
        plugins: [{
            /* Teks di tengah donut */
            id: 'centerText',
            beforeDraw(chart) {
                const { ctx: c, chartArea: { left, right, top, bottom } } = chart;
                const total = items.reduce((s, d) => s + d.count, 0);
                const cx    = (left + right) / 2;
                const cy    = (top + bottom) / 2;

                c.save();
                c.textAlign    = 'center';
                c.textBaseline = 'middle';

                c.font = `800 22px Inter, sans-serif`;
                c.fillStyle = '#1E293B';
                c.fillText(total, cx, cy - 8);

                c.font = `400 11px Inter, sans-serif`;
                c.fillStyle = '#94A3B8';
                c.fillText('Produk', cx, cy + 10);
                c.restore();
            }
        }]
    });
}

/* ================================================================
   STATUS LEGEND — dengan mini progress bar
   ================================================================ */
function renderStatusLegend() {
    const container = document.getElementById('statusLegend');
    if (!container) return;

    const items = Object.values(DUMMY_DATA.stockStatus);
    const total = items.reduce((s, d) => s + d.count, 0);

    container.innerHTML = items.map(d => {
        const pct = Math.round((d.count / total) * 100);
        return `
            <div class="legend-row">
                <div class="legend-row-header">
                    <div class="legend-label-wrap">
                        <span class="legend-dot" style="background:${d.color}"></span>
                        <span>${d.label}</span>
                    </div>
                    <span class="legend-count">${d.count} produk</span>
                </div>
                <div class="legend-bar-track">
                    <div class="legend-bar-fill"
                         style="background:${d.color}; width:0%"
                         data-target-width="${pct}%">
                    </div>
                </div>
            </div>
        `;
    }).join('');

    /* Animasi progress bar (setelah render) */
    requestAnimationFrame(() => {
        container.querySelectorAll('.legend-bar-fill').forEach(el => {
            el.style.width = el.dataset.targetWidth;
        });
    });
}

/* ================================================================
   LOW STOCK TABLE
   ================================================================ */
function renderLowStockTable() {
    const tbody = document.getElementById('lowStockTbody');
    if (!tbody) return;

    tbody.innerHTML = DUMMY_DATA.lowStockProducts.map(p => {
        const lvl = getStockLevel(p.stock);
        return `
            <tr>
                <td><span class="product-code">${p.code}</span></td>
                <td><strong>${p.name}</strong></td>
                <td>${p.category}</td>
                <td style="color:var(--text-secondary)">${p.variant}</td>
                <td>
                    <span class="stock-indicator ${lvl.cls}">
                        <span class="stock-indicator-dot"></span>
                        ${p.stock} pcs
                    </span>
                </td>
                <td><span class="badge ${lvl.badge}">${lvl.label}</span></td>
            </tr>
        `;
    }).join('');
}

/* ================================================================
   TRANSACTION LIST
   ================================================================ */
function renderTransactionList() {
    const list = document.getElementById('transactionList');
    if (!list) return;

    list.innerHTML = DUMMY_DATA.recentTransactions.map(t => {
        const isIn = t.type === 'masuk';
        return `
            <div class="tx-item">
                <div class="tx-icon ${isIn ? 'in' : 'out'}">
                    <i class="fas ${isIn ? 'fa-arrow-down' : 'fa-arrow-up'}"></i>
                </div>
                <div class="tx-info">
                    <div class="tx-product">${t.product}</div>
                    <div class="tx-meta">${t.time} &middot; ${t.code}</div>
                </div>
                <div class="tx-amount ${isIn ? 'in' : 'out'}">
                    ${isIn ? '+' : ''}${t.amount} pcs
                </div>
            </div>
        `;
    }).join('');
}

/* ================================================================
   REFRESH DASHBOARD
   Dipanggil via onclick="refreshDashboard()" di tombol Refresh.
   ================================================================ */
function refreshDashboard() {
    const btn  = document.getElementById('refreshBtn');
    const icon = btn ? btn.querySelector('i') : null;

    if (btn) {
        btn.disabled = true;
        if (icon) icon.classList.add('fa-spin');
    }

    setTimeout(() => {
        /* Re-render semua komponen (data masih dummy di fase ini) */
        renderStats();
        renderStatusLegend();
        renderLowStockTable();
        renderTransactionList();

        if (btn) {
            btn.disabled = false;
            if (icon) icon.classList.remove('fa-spin');
        }
    }, 800);
}
