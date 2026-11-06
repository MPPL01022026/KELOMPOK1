<?php use App\Config\App; ?>

<style>
    .twin-item-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 16px;
        transition: all 0.15s ease;
        display: flex;
        flex-direction: column;
        height: 100%;
    }

    .twin-item-card:hover {
        border-color: #cbd5e1;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.04);
        transform: translateY(-2px);
    }

    .twin-badge-aman {
        background-color: #ecfdf5;
        color: #065f46;
        border: 1px solid #a7f3d0;
    }

    .twin-badge-menipis {
        background-color: #fffbeb;
        color: #92400e;
        border: 1px solid #fde68a;
    }

    .twin-badge-kritis {
        background-color: #fff1f2;
        color: #9f1239;
        border: 1px solid #fecdd3;
    }
</style>

<div class="container-fluid px-0">
    <!-- Header -->
    <div class="row align-items-center mb-3">
        <div class="col-md-6">
            <h5 class="fw-bold text-dark mb-1">Etalase Digital Twin</h5>
            <p class="text-secondary small mb-0">Representasi digital stok fisik rak toko pakaian wanita Yunda Fashion.</p>
        </div>
        <div class="col-md-6 text-md-end mt-2 mt-md-0 d-flex align-items-center justify-content-md-end gap-2">
            <div class="d-inline-flex align-items-center gap-2 px-3 py-1 bg-white border rounded-pill small text-muted">
                <span class="pulse-live"></span>
                <span>Polling: <strong id="pollSec">5s</strong></span>
                <span class="ms-1">(Update: <span id="pollTime"><?= date('H:i:s') ?></span>)</span>
            </div>
            <button class="btn btn-sm btn-light border rounded px-3 py-1 fw-medium" id="btnSyncNow">
                <i class="bi bi-arrow-repeat me-1"></i> Sinkronkan
            </button>
        </div>
    </div>

    <!-- 3 Summary Metrics -->
    <div class="row g-3 mb-3">
        <div class="col-md-4">
            <div class="clean-card p-3 border-start border-4 border-success">
                <span class="text-muted small fw-semibold text-uppercase d-block mb-1">Persediaan Aman (&ge; 10 pcs)</span>
                <h4 class="fw-bold text-dark mb-0" id="cntAman">0 <small class="text-muted fs-6 fw-normal">produk</small></h4>
            </div>
        </div>
        <div class="col-md-4">
            <div class="clean-card p-3 border-start border-4 border-warning">
                <span class="text-muted small fw-semibold text-uppercase d-block mb-1">Persediaan Menipis (4–9 pcs)</span>
                <h4 class="fw-bold text-dark mb-0" id="cntMenipis">0 <small class="text-muted fs-6 fw-normal">produk</small></h4>
            </div>
        </div>
        <div class="col-md-4">
            <div class="clean-card p-3 border-start border-4 border-danger">
                <span class="text-muted small fw-semibold text-uppercase d-block mb-1">Persediaan Kritis (&le; 3 pcs)</span>
                <h4 class="fw-bold text-dark mb-0" id="cntKritis">0 <small class="text-muted fs-6 fw-normal">produk</small></h4>
            </div>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="clean-card p-3 mb-3">
        <div class="row g-2 align-items-center">
            <div class="col-md-5">
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-search"></i></span>
                    <input type="text" id="dtSearch" class="form-control border-start-0" placeholder="Filter nama pakaian wanita...">
                </div>
            </div>
            <div class="col-md-4">
                <select id="dtCategory" class="form-select">
                    <option value="">Semua Kategori</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['category_name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3">
                <select id="dtStatus" class="form-select">
                    <option value="all">Semua Status Stok</option>
                    <option value="kritis">Hanya Kritis (&le; 3 pcs)</option>
                    <option value="menipis">Hanya Menipis (4–9 pcs)</option>
                    <option value="aman">Hanya Aman (&ge; 10 pcs)</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Digital Twin Showcase Grid -->
    <div class="row g-3" id="twinItemsContainer">
        <!-- Rendered via JS -->
    </div>
</div>

<script>
    let dtCountdown = 5;
    let dtTimer = null;

    const formatRp = (num) => 'Rp ' + Number(num).toLocaleString('id-ID');

    async function loadDigitalTwinData() {
        const cat = document.getElementById('dtCategory').value;
        const q = document.getElementById('dtSearch').value;
        const status = document.getElementById('dtStatus').value;

        try {
            const res = await fetch(`<?= $baseUrl ?>/digital-twin/api?category_id=${encodeURIComponent(cat)}&keyword=${encodeURIComponent(q)}&status=${encodeURIComponent(status)}`);
            const data = await res.json();

            if (data.success) {
                renderTwinCards(data.products);
                document.getElementById('cntAman').innerHTML = `${data.summary.aman_count} <small class="text-muted fs-6 fw-normal">produk</small>`;
                document.getElementById('cntMenipis').innerHTML = `${data.summary.menipis_count} <small class="text-muted fs-6 fw-normal">produk</small>`;
                document.getElementById('cntKritis').innerHTML = `${data.summary.critical_count} <small class="text-muted fs-6 fw-normal">produk</small>`;
                document.getElementById('pollTime').textContent = data.timestamp + ' WIB';
            }
        } catch (e) {
            console.error('Twin polling error:', e);
        }
    }

    function renderTwinCards(products) {
        const container = document.getElementById('twinItemsContainer');
        if (!products || products.length === 0) {
            container.innerHTML = `
                <div class="col-12 text-center py-5">
                    <div class="p-4 bg-white rounded-3 border text-muted small">
                        Tidak ada pakaian yang sesuai dengan filter.
                    </div>
                </div>
            `;
            return;
        }

        let html = '';
        products.forEach(p => {
            let badgeClass = 'twin-badge-aman';
            if (p.overall_status === 'kritis') badgeClass = 'twin-badge-kritis';
            else if (p.overall_status === 'menipis') badgeClass = 'twin-badge-menipis';

            let variantsHtml = '';
            p.variants.forEach(v => {
                let vBadge = 'twin-badge-aman';
                if (v.status_indicator === 'kritis') vBadge = 'twin-badge-kritis';
                else if (v.status_indicator === 'menipis') vBadge = 'twin-badge-menipis';

                variantsHtml += `
                    <div class="d-flex justify-content-between align-items-center py-1 border-bottom border-light small">
                        <div>
                            <span class="fw-semibold text-dark">${v.size}</span>
                            <span class="text-muted">(${v.color})</span>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge ${vBadge} rounded-pill px-2 py-0" style="font-size:0.75rem;">
                                ${v.stock} pcs
                            </span>
                            <span class="text-muted" style="font-size:0.7rem;">Min: ${v.min_stock}</span>
                        </div>
                    </div>
                `;
            });

            html += `
                <div class="col-md-6 col-xl-4">
                    <div class="twin-item-card">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <span class="badge bg-light text-secondary border small">${p.category_name}</span>
                            <span class="badge ${badgeClass} rounded-pill px-2 py-1 small fw-semibold">
                                ${p.overall_label}
                            </span>
                        </div>

                        <h6 class="fw-semibold text-dark mb-1">${p.product_name}</h6>
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="fw-bold text-danger" style="color:#be123c !important;">${formatRp(p.price)}</span>
                            <span class="small text-muted">Total Stok: <strong>${p.total_stock} pcs</strong></span>
                        </div>

                        <div class="bg-light p-2 rounded-2 mb-3 flex-grow-1">
                            <span class="small fw-semibold text-muted d-block mb-1" style="font-size:0.72rem;">STATUS VARIAN DI RAK:</span>
                            ${variantsHtml}
                        </div>

                        <div class="d-flex gap-2 mt-auto">
                            <a href="<?= $baseUrl ?>/stock/restock" class="btn btn-sm btn-light border flex-fill fw-semibold small text-dark">
                                <i class="bi bi-box-arrow-in-down me-1"></i> Restok
                            </a>
                            <a href="<?= $baseUrl ?>/stock/adjustment" class="btn btn-sm btn-light border flex-fill fw-semibold small text-muted">
                                <i class="bi bi-sliders me-1"></i> Penyesuaian
                            </a>
                        </div>
                    </div>
                </div>
            `;
        });

        container.innerHTML = html;
    }

    function initPolling() {
        if (dtTimer) clearInterval(dtTimer);
        let s = dtCountdown;

        dtTimer = setInterval(() => {
            s--;
            const lbl = document.getElementById('pollSec');
            if (lbl) lbl.textContent = s + 's';

            if (s <= 0) {
                s = dtCountdown;
                loadDigitalTwinData();
            }
        }, 1000);
    }

    document.getElementById('btnSyncNow').addEventListener('click', () => {
        loadDigitalTwinData();
        initPolling();
    });

    document.getElementById('dtSearch').addEventListener('input', loadDigitalTwinData);
    document.getElementById('dtCategory').addEventListener('change', loadDigitalTwinData);
    document.getElementById('dtStatus').addEventListener('change', loadDigitalTwinData);

    loadDigitalTwinData();
    initPolling();
</script>
