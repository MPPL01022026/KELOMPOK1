<?php use App\Config\App; ?>

<style>
    /* Clean Modern POS Styles */
    .pos-product-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 14px;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        height: 100%;
        transition: all 0.15s ease-in-out;
    }

    .pos-product-card:hover {
        border-color: #cbd5e1;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.04);
        transform: translateY(-2px);
    }

    .pos-cat-pill {
        white-space: nowrap;
        border-radius: 20px;
        padding: 6px 14px;
        font-size: 0.8rem;
        font-weight: 500;
        border: 1px solid #e2e8f0;
        background: #ffffff;
        color: #475569;
        cursor: pointer;
        transition: all 0.15s;
    }

    .pos-cat-pill:hover {
        background: #f1f5f9;
        color: #0f172a;
    }

    .pos-cat-pill.active {
        background: #0f172a;
        color: #ffffff;
        border-color: #0f172a;
    }

    /* Cart Panel */
    .pos-cart-panel {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        display: flex;
        flex-direction: column;
        height: calc(100vh - 100px);
        position: sticky;
        top: 80px;
    }

    .cart-items-body {
        flex: 1;
        overflow-y: auto;
        padding: 12px;
    }

    .cart-item-row {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 10px 12px;
        margin-bottom: 8px;
    }

    .qty-btn {
        width: 26px;
        height: 26px;
        padding: 0;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 6px;
        border: 1px solid #cbd5e1;
        background: #ffffff;
        font-weight: 600;
        font-size: 0.85rem;
        color: #334155;
    }

    .qty-btn:hover {
        background: #e2e8f0;
    }

    .money-chip {
        font-size: 0.75rem;
        font-weight: 600;
        padding: 5px 8px;
        border-radius: 6px;
        border: 1px solid #e2e8f0;
        background: #ffffff;
        color: #334155;
        flex: 1;
        text-align: center;
        cursor: pointer;
        transition: all 0.15s;
    }

    .money-chip:hover {
        background: #f1f5f9;
        border-color: #cbd5e1;
    }
</style>

<div class="container-fluid px-0">
    <div class="row g-3">
        <!-- Kolom Kiri: Katalog & Filter Manual -->
        <div class="col-lg-8">
            <!-- Search & Categories Bar -->
            <div class="clean-card p-3 mb-3">
                <div class="row g-2 align-items-center mb-2">
                    <div class="col">
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-search"></i></span>
                            <input type="text" id="posSearchInput" class="form-control border-start-0" placeholder="Ketik nama pakaian wanita atau model (Pencarian manual)...">
                        </div>
                    </div>
                </div>

                <!-- Category Pills -->
                <div class="d-flex gap-2 overflow-auto pt-1 pb-1" id="categoryScroll">
                    <button type="button" class="pos-cat-pill active" data-cat="all">Semua Kategori</button>
                    <?php foreach ($categories as $cat): ?>
                        <button type="button" class="pos-cat-pill" data-cat="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['category_name']) ?></button>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Product Cards Grid -->
            <div class="row g-3" id="posProductGrid">
                <?php foreach ($products as $p): ?>
                    <div class="col-sm-6 col-md-4 pos-prod-col" 
                         data-cat="<?= $p['category_id'] ?>" 
                         data-name="<?= strtolower(htmlspecialchars($p['product_name'])) ?>">
                        <div class="pos-product-card">
                            <!-- Card Header: Category & Price -->
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="badge bg-light text-secondary border fw-normal" style="font-size:0.72rem;">
                                    <?= htmlspecialchars($p['category_name']) ?>
                                </span>
                                <span class="fw-bold fs-6 text-danger" style="color:#be123c !important;">
                                    <?= App::formatRupiah($p['price']) ?>
                                </span>
                            </div>

                            <!-- Title -->
                            <h6 class="fw-semibold text-dark mb-3" style="min-height: 40px; font-size: 0.92rem; line-height: 1.35;">
                                <?= htmlspecialchars($p['product_name']) ?>
                            </h6>

                            <!-- Variant Dropdown -->
                            <div class="mb-3">
                                <label class="small text-muted d-block mb-1" style="font-size:0.72rem;">Varian Ukuran & Warna:</label>
                                <select class="form-select form-select-sm pos-variant-select" style="font-size:0.8rem;">
                                    <?php if (empty($p['variants'])): ?>
                                        <option value="">Stok Kosong</option>
                                    <?php else: ?>
                                        <?php foreach ($p['variants'] as $v): ?>
                                            <option value="<?= $v['id'] ?>" 
                                                    data-size="<?= htmlspecialchars($v['size']) ?>" 
                                                    data-color="<?= htmlspecialchars($v['color']) ?>" 
                                                    data-stock="<?= $v['stock'] ?>"
                                                    data-price="<?= $p['price'] ?>"
                                                    data-name="<?= htmlspecialchars($p['product_name']) ?>"
                                                    <?= $v['stock'] <= 0 ? 'disabled' : '' ?>>
                                                <?= htmlspecialchars($v['size']) ?> - <?= htmlspecialchars($v['color']) ?> (Sisa: <?= $v['stock'] ?>)
                                            </option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </div>

                            <!-- Add Button -->
                            <button type="button" class="btn btn-outline-danger btn-sm w-100 fw-semibold py-2 rounded-2 mt-auto btn-add-pos" 
                                    style="border-color:#be123c; color:#be123c;"
                                    <?= empty($p['variants']) ? 'disabled' : '' ?>>
                                <i class="bi bi-cart-plus me-1"></i> Tambah ke Keranjang
                            </button>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Kolom Kanan: Keranjang & Pembayaran -->
        <div class="col-lg-4">
            <div class="pos-cart-panel">
                <!-- Cart Header -->
                <div class="p-3 border-bottom d-flex align-items-center justify-content-between">
                    <div>
                        <span class="fw-bold text-dark fs-6">Keranjang Transaksi</span>
                        <span class="badge bg-light text-secondary border ms-1" id="posCartBadge">0 item</span>
                    </div>
                    <button type="button" class="btn btn-sm btn-light border text-muted py-0 px-2" id="btnResetCart" title="Kosongkan">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>

                <!-- Items List -->
                <div class="cart-items-body" id="posCartItemsList">
                    <div class="text-center py-5 text-muted small" id="posEmptyCartNotice">
                        <i class="bi bi-basket text-secondary fs-2 d-block mb-2"></i>
                        Keranjang masih kosong.<br>Pilih produk dan varian dari katalog pakaian.
                    </div>
                </div>

                <!-- Payment Area -->
                <div class="p-3 bg-light border-top rounded-bottom-3">
                    <div class="d-flex justify-content-between align-items-baseline mb-2">
                        <span class="small text-muted fw-semibold">Total Belanja:</span>
                        <span class="fs-5 fw-bold text-dark" id="posTotalLabel">Rp 0</span>
                    </div>

                    <!-- Input Tunai -->
                    <div class="mb-2">
                        <label class="form-label small text-muted mb-1">Uang Tunai Diterima (Rp):</label>
                        <input type="number" id="posPaidInput" class="form-control form-control-sm fw-bold fs-6" placeholder="0" min="0">
                    </div>

                    <!-- Quick Denominations -->
                    <div class="d-flex gap-1 mb-2">
                        <button type="button" class="money-chip" data-amt="exact">Pas</button>
                        <button type="button" class="money-chip" data-amt="50000">50rb</button>
                        <button type="button" class="money-chip" data-amt="100000">100rb</button>
                        <button type="button" class="money-chip" data-amt="200000">200rb</button>
                        <button type="button" class="money-chip" data-amt="500000">500rb</button>
                    </div>

                    <!-- Kembalian -->
                    <div class="d-flex justify-content-between align-items-center p-2 bg-white rounded border mb-3 small">
                        <span class="text-muted fw-semibold">Kembalian:</span>
                        <span class="fw-bold fs-6 text-dark" id="posChangeLabel">Rp 0</span>
                    </div>

                    <!-- Checkout Button -->
                    <button type="button" class="btn btn-danger w-100 py-2 fw-semibold rounded-2" style="background:#be123c; border:none;" id="btnCheckoutSubmit">
                        <i class="bi bi-check2-circle me-1"></i> Selesaikan Transaksi (Tunai)
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Struk Sukses -->
<div class="modal fade" id="modalPosSuccess" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-0 shadow-lg rounded-4 text-center p-4">
            <div class="mx-auto rounded-circle bg-success-subtle text-success d-flex align-items-center justify-content-center mb-2" style="width:50px; height:50px;">
                <i class="bi bi-check-lg fs-2"></i>
            </div>
            <h6 class="fw-bold text-dark mb-1">Transaksi Berhasil!</h6>
            <p class="text-muted small mb-3" id="successInvoiceText">-</p>

            <div class="bg-light p-2 rounded-2 text-start small mb-3 border">
                <div class="d-flex justify-content-between mb-1">
                    <span class="text-muted">Total:</span>
                    <strong id="successTotalText">Rp 0</strong>
                </div>
                <div class="d-flex justify-content-between mb-1">
                    <span class="text-muted">Dibayar:</span>
                    <strong id="successPaidText">Rp 0</strong>
                </div>
                <div class="d-flex justify-content-between">
                    <span class="text-muted">Kembalian:</span>
                    <strong class="text-success" id="successChangeText">Rp 0</strong>
                </div>
            </div>

            <div class="d-flex flex-column gap-2">
                <a href="#" id="btnPrintReceiptLink" target="_blank" class="btn btn-sm btn-danger py-2 fw-semibold rounded-2" style="background:#be123c; border:none;">
                    <i class="bi bi-printer me-1"></i> Cetak Struk 80mm
                </a>
                <button type="button" class="btn btn-sm btn-light border py-2 rounded-2" data-bs-dismiss="modal">
                    Transaksi Baru
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    let posCart = [];

    const formatRp = (num) => 'Rp ' + Number(num).toLocaleString('id-ID');

    // Tambah ke keranjang
    document.querySelectorAll('.btn-add-pos').forEach(btn => {
        btn.addEventListener('click', function() {
            const card = this.closest('.pos-prod-col');
            const select = card.querySelector('.pos-variant-select');
            const opt = select.options[select.selectedIndex];

            if (!opt || !opt.value) {
                Swal.fire({ icon: 'warning', title: 'Perhatian', text: 'Pilih varian pakaian terlebih dahulu.' });
                return;
            }

            const variantId = parseInt(opt.value);
            const size = opt.dataset.size;
            const color = opt.dataset.color;
            const stock = parseInt(opt.dataset.stock);
            const price = parseFloat(opt.dataset.price);
            const name = opt.dataset.name;

            if (stock <= 0) {
                Swal.fire({ icon: 'error', title: 'Stok Habis', text: 'Varian ini sedang tidak tersedia.' });
                return;
            }

            const existing = posCart.find(i => i.variant_id === variantId);
            if (existing) {
                if (existing.quantity + 1 > stock) {
                    Swal.fire({ icon: 'warning', title: 'Batas Stok', text: `Stok hanya tersedia ${stock} pcs.` });
                    return;
                }
                existing.quantity += 1;
            } else {
                posCart.push({
                    variant_id: variantId,
                    name: name,
                    size: size,
                    color: color,
                    price: price,
                    quantity: 1,
                    max_stock: stock
                });
            }

            renderPosCart();
        });
    });

    function renderPosCart() {
        const container = document.getElementById('posCartItemsList');
        const badge = document.getElementById('posCartBadge');
        const totalLabel = document.getElementById('posTotalLabel');

        if (posCart.length === 0) {
            container.innerHTML = `
                <div class="text-center py-5 text-muted small" id="posEmptyCartNotice">
                    <i class="bi bi-basket text-secondary fs-2 d-block mb-2"></i>
                    Keranjang masih kosong.<br>Pilih produk dan varian dari katalog pakaian.
                </div>
            `;
            badge.textContent = '0 item';
            totalLabel.textContent = 'Rp 0';
            updateChangeCalculation();
            return;
        }

        let total = 0;
        let count = 0;
        let html = '';

        posCart.forEach((item, index) => {
            const sub = item.price * item.quantity;
            total += sub;
            count += item.quantity;

            html += `
                <div class="cart-item-row">
                    <div class="d-flex justify-content-between align-items-start mb-1">
                        <div>
                            <strong class="text-dark small d-block">${item.name}</strong>
                            <span class="badge bg-light text-muted border" style="font-size:0.7rem;">
                                ${item.size} | ${item.color}
                            </span>
                        </div>
                        <button type="button" class="btn btn-sm text-danger p-0 border-0" onclick="removePosItem(${index})">
                            <i class="bi bi-x-circle"></i>
                        </button>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mt-2">
                        <span class="small fw-semibold text-danger">${formatRp(sub)}</span>
                        <div class="d-flex align-items-center gap-1">
                            <button type="button" class="qty-btn" onclick="stepPosItem(${index}, -1)">-</button>
                            <span class="small fw-bold px-2">${item.quantity}</span>
                            <button type="button" class="qty-btn" onclick="stepPosItem(${index}, 1)">+</button>
                        </div>
                    </div>
                </div>
            `;
        });

        container.innerHTML = html;
        badge.textContent = `${count} pcs`;
        totalLabel.textContent = formatRp(total);
        updateChangeCalculation();
    }

    function stepPosItem(idx, change) {
        const itm = posCart[idx];
        const next = itm.quantity + change;
        if (next <= 0) {
            posCart.splice(idx, 1);
        } else if (next > itm.max_stock) {
            Swal.fire({ icon: 'warning', title: 'Stok Terbatas', text: `Maksimal stok tersedia hanya ${itm.max_stock} pcs.` });
            return;
        } else {
            itm.quantity = next;
        }
        renderPosCart();
    }

    function removePosItem(idx) {
        posCart.splice(idx, 1);
        renderPosCart();
    }

    document.getElementById('btnResetCart').addEventListener('click', () => {
        if (posCart.length > 0) {
            posCart = [];
            renderPosCart();
        }
    });

    const paidInput = document.getElementById('posPaidInput');
    paidInput.addEventListener('input', updateChangeCalculation);

    function getCartTotal() {
        return posCart.reduce((sum, i) => sum + (i.price * i.quantity), 0);
    }

    function updateChangeCalculation() {
        const total = getCartTotal();
        const paid = parseFloat(paidInput.value) || 0;
        const changeLabel = document.getElementById('posChangeLabel');

        if (paid >= total && total > 0) {
            changeLabel.textContent = formatRp(paid - total);
            changeLabel.className = 'fw-bold fs-6 text-success';
        } else if (paid < total && paid > 0) {
            changeLabel.textContent = 'Kurang ' + formatRp(total - paid);
            changeLabel.className = 'fw-bold fs-6 text-danger';
        } else {
            changeLabel.textContent = 'Rp 0';
            changeLabel.className = 'fw-bold fs-6 text-muted';
        }
    }

    // Quick denomination chips
    document.querySelectorAll('.money-chip').forEach(btn => {
        btn.addEventListener('click', function() {
            const amt = this.dataset.amt;
            const total = getCartTotal();
            if (amt === 'exact') {
                paidInput.value = total;
            } else {
                paidInput.value = parseInt(amt);
            }
            updateChangeCalculation();
        });
    });

    // Search & Category Filtering
    const searchField = document.getElementById('posSearchInput');
    searchField.addEventListener('input', runPosFilter);

    document.querySelectorAll('.pos-cat-pill').forEach(pill => {
        pill.addEventListener('click', function() {
            document.querySelectorAll('.pos-cat-pill').forEach(p => p.classList.remove('active'));
            this.classList.add('active');
            runPosFilter();
        });
    });

    function runPosFilter() {
        const q = searchField.value.toLowerCase().trim();
        const activeCat = document.querySelector('.pos-cat-pill.active').dataset.cat;

        document.querySelectorAll('.pos-prod-col').forEach(col => {
            const name = col.dataset.name;
            const cat = col.dataset.cat;

            const matchQ = name.includes(q);
            const matchCat = (activeCat === 'all' || cat === activeCat);

            col.style.display = (matchQ && matchCat) ? 'block' : 'none';
        });
    }

    // Process Checkout
    document.getElementById('btnCheckoutSubmit').addEventListener('click', async function() {
        if (posCart.length === 0) {
            Swal.fire({ icon: 'warning', title: 'Keranjang Kosong', text: 'Tambahkan pakaian ke keranjang terlebih dahulu.' });
            return;
        }

        const total = getCartTotal();
        const paid = parseFloat(paidInput.value) || 0;

        if (paid < total) {
            Swal.fire({ icon: 'error', title: 'Pembayaran Kurang', text: `Total belanja ${formatRp(total)}, pembayaran baru ${formatRp(paid)}.` });
            return;
        }

        const change = paid - total;

        try {
            const response = await fetch('<?= $baseUrl ?>/pos/checkout', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    total_amount: total,
                    paid_amount: paid,
                    change_amount: change,
                    items: posCart.map(i => ({ variant_id: i.variant_id, quantity: i.quantity }))
                })
            });

            const data = await response.json();

            if (data.success) {
                document.getElementById('successInvoiceText').textContent = data.invoice_number;
                document.getElementById('successTotalText').textContent = formatRp(data.total_amount);
                document.getElementById('successPaidText').textContent = formatRp(data.paid_amount);
                document.getElementById('successChangeText').textContent = formatRp(data.change_amount);
                document.getElementById('btnPrintReceiptLink').href = '<?= $baseUrl ?>' + data.redirect_receipt;

                new bootstrap.Modal(document.getElementById('modalPosSuccess')).show();

                posCart = [];
                paidInput.value = '';
                renderPosCart();
            } else {
                Swal.fire({ icon: 'error', title: 'Gagal Transaksi', text: data.message });
            }
        } catch (e) {
            Swal.fire({ icon: 'error', title: 'Kesalahan Sistem', text: e.message });
        }
    });
</script>
