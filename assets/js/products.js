/**
 * products.js — Logika Antarmuka Manajemen Produk
 * Pengembang: Rana Shofa (Frontend Developer)
 *
 * Fitur:
 * - Menampilkan daftar produk & varian pakaian
 * - Pencarian real-time & Filter berdasarkan kategori & status stok
 * - Tambah produk baru dengan varian dinamis (Ukuran, Warna, Stok)
 * - Edit & Hapus produk
 * - Ekspor data produk ke CSV
 * - Hybrid: Mendukung API Backend live + Fallback data dummy otomatis
 */

'use strict';

/* ================================================================
   DATA STATE
   ================================================================ */
let productsList = [];
let isBackendConnected = false;

// Data awal produk (digunakan jika backend offline)
const INITIAL_PRODUCTS = [
    {
        id: 1,
        kode_produk: 'YF-001',
        nama_produk: 'Kemeja Polos Premium',
        kategori: 'Atasan',
        kategori_warna: '#7C3AED',
        lokasi_rak: 'Rak A-01',
        deskripsi: 'Kemeja katun premium lengan panjang bahan adem',
        variants: [
            { id: 1, ukuran: 'M', warna: 'Putih', stok: 2, status: 'kritis' },
            { id: 2, ukuran: 'L', warna: 'Putih', stok: 8, status: 'waspada' }
        ]
    },
    {
        id: 2,
        kode_produk: 'YF-015',
        nama_produk: 'Celana Chino Slim',
        kategori: 'Bawahan',
        kategori_warna: '#3B82F6',
        lokasi_rak: 'Rak B-02',
        deskripsi: 'Celana chino stretch slim fit kasual',
        variants: [
            { id: 3, ukuran: '32', warna: 'Khaki', stok: 3, status: 'kritis' },
            { id: 4, ukuran: '34', warna: 'Khaki', stok: 14, status: 'aman' }
        ]
    },
    {
        id: 3,
        kode_produk: 'YF-028',
        nama_produk: 'Dress Midi Floral',
        kategori: 'Dress',
        kategori_warna: '#EC4899',
        lokasi_rak: 'Rak C-01',
        deskripsi: 'Dress motif bunga kasual wanita dengan tali pinggang',
        variants: [
            { id: 5, ukuran: 'M', warna: 'Navy', stok: 3, status: 'kritis' },
            { id: 6, ukuran: 'L', warna: 'Navy', stok: 12, status: 'aman' }
        ]
    },
    {
        id: 4,
        kode_produk: 'YF-042',
        nama_produk: 'Cardigan Rajut',
        kategori: 'Outerwear',
        kategori_warna: '#F59E0B',
        lokasi_rak: 'Rak D-01',
        deskripsi: 'Cardigan rajut tebal wanita kancing depan',
        variants: [
            { id: 7, ukuran: 'L', warna: 'Cream', stok: 5, status: 'waspada' },
            { id: 8, ukuran: 'XL', warna: 'Cream', stok: 7, status: 'waspada' }
        ]
    },
    {
        id: 5,
        kode_produk: 'YF-009',
        nama_produk: 'Blouse Batik Modern',
        kategori: 'Atasan',
        kategori_warna: '#7C3AED',
        lokasi_rak: 'Rak A-03',
        deskripsi: 'Blouse batik printing katun halus',
        variants: [
            { id: 9, ukuran: 'S', warna: 'Coklat', stok: 6, status: 'waspada' }
        ]
    },
    {
        id: 6,
        kode_produk: 'YF-055',
        nama_produk: 'Rok Plisket',
        kategori: 'Bawahan',
        kategori_warna: '#3B82F6',
        lokasi_rak: 'Rak B-01',
        deskripsi: 'Rok plisket premium all size elastis',
        variants: [
            { id: 10, ukuran: 'All Size', warna: 'Hitam', stok: 7, status: 'waspada' }
        ]
    },
    {
        id: 7,
        kode_produk: 'YF-031',
        nama_produk: 'Hoodie Casual',
        kategori: 'Outerwear',
        kategori_warna: '#F59E0B',
        lokasi_rak: 'Rak D-02',
        deskripsi: 'Hoodie fleece tebal bertopi unisex',
        variants: [
            { id: 11, ukuran: 'XL', warna: 'Abu-abu', stok: 8, status: 'waspada' }
        ]
    },
    {
        id: 8,
        kode_produk: 'YF-023',
        nama_produk: 'T-Shirt Basic Oversize',
        kategori: 'Atasan',
        kategori_warna: '#7C3AED',
        lokasi_rak: 'Rak A-02',
        deskripsi: 'Kaos katun combed 24s oversize',
        variants: [
            { id: 12, ukuran: 'XL', warna: 'Hitam', stok: 25, status: 'aman' },
            { id: 13, ukuran: 'L', warna: 'Putih', stok: 18, status: 'aman' }
        ]
    },
    {
        id: 9,
        kode_produk: 'YF-008',
        nama_produk: 'Jeans Slim Fit Navy',
        kategori: 'Bawahan',
        kategori_warna: '#3B82F6',
        lokasi_rak: 'Rak B-03',
        deskripsi: 'Celana jeans denim stretch biru gelap',
        variants: [
            { id: 14, ukuran: '30', warna: 'Navy', stok: 40, status: 'aman' }
        ]
    }
];

/* ================================================================
   INIT & LOAD DATA
   ================================================================ */
document.addEventListener('DOMContentLoaded', () => {
    loadProductsData();
});

async function loadProductsData() {
    try {
        // Coba panggil Backend API
        const response = await fetch('http://localhost:5000/api/products');
        if (response.ok) {
            const result = await response.json();
            if (result.data && result.data.length > 0) {
                isBackendConnected = true;
                productsList = result.data.map(p => ({
                    id: p.id,
                    kode_produk: p.kode_produk,
                    nama_produk: p.nama_produk,
                    kategori: p.nama_kategori,
                    kategori_warna: p.kategori_warna || '#7C3AED',
                    lokasi_rak: p.lokasi_rak || 'Rak A-01',
                    deskripsi: p.deskripsi || '',
                    variants: (p.variants || []).map(v => ({
                        id: v.id,
                        ukuran: v.ukuran,
                        warna: v.warna,
                        stok: v.stok,
                        status: v.status || (v.stok <= 3 ? 'kritis' : (v.stok <= 9 ? 'waspada' : 'aman'))
                    }))
                }));
            } else {
                productsList = [...INITIAL_PRODUCTS];
            }
        } else {
            productsList = [...INITIAL_PRODUCTS];
        }
    } catch (e) {
        // Fallback jika backend offline
        productsList = [...INITIAL_PRODUCTS];
    }

    renderProductsTable(productsList);
}

/* ================================================================
   RENDER TABEL PRODUK
   ================================================================ */
function renderProductsTable(products) {
    const tbody = document.getElementById('productsTableBody');
    const countInfo = document.getElementById('productCountInfo');
    if (!tbody) return;

    if (products.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="8" style="text-align: center; padding: 40px 20px; color: var(--text-muted);">
                    <i class="fas fa-box-open" style="font-size: 28px; margin-bottom: 10px; display: block; opacity: 0.5;"></i>
                    Tidak ada produk yang sesuai dengan pencarian atau filter.
                </td>
            </tr>
        `;
        if (countInfo) countInfo.textContent = 'Menampilkan 0 produk';
        return;
    }

    if (countInfo) {
        countInfo.textContent = `Menampilkan ${products.length} dari ${productsList.length} produk`;
    }

    tbody.innerHTML = products.map(prod => {
        // Hitung total stok dari semua varian
        const totalStok = prod.variants.reduce((acc, v) => acc + Number(v.stok), 0);

        // Tentukan status ringkasan produk
        let statusBadge = '';
        if (prod.variants.some(v => v.stok <= 3)) {
            statusBadge = '<span class="badge badge-danger"><i class="fas fa-triangle-exclamation"></i> Kritis (≤ 3)</span>';
        } else if (prod.variants.some(v => v.stok <= 9)) {
            statusBadge = '<span class="badge badge-warning"><i class="fas fa-circle-exclamation"></i> Waspada (4–9)</span>';
        } else {
            statusBadge = '<span class="badge badge-success"><i class="fas fa-circle-check"></i> Aman (≥ 10)</span>';
        }

        // Render chips varian
        const variantChips = prod.variants.map(v => {
            const dotColor = v.stok <= 3 ? 'var(--danger)' : (v.stok <= 9 ? 'var(--warning)' : 'var(--success)');
            return `
                <span class="variant-chip" title="Ukuran: ${v.ukuran}, Warna: ${v.warna}, Stok: ${v.stok} pcs">
                    <span style="width:6px; height:6px; border-radius:50%; background:${dotColor};"></span>
                    ${v.ukuran} / ${v.warna} (<span class="v-stok">${v.stok}</span>)
                </span>
            `;
        }).join('');

        return `
            <tr>
                <td><span class="product-code">${prod.kode_produk}</span></td>
                <td>
                    <div style="font-weight: 600; color: var(--text-primary);">${prod.nama_produk}</div>
                    <div style="font-size: 11px; color: var(--text-muted);">${prod.deskripsi || '-'}</div>
                </td>
                <td>
                    <span class="badge" style="background: rgba(124, 58, 237, 0.1); color: ${prod.kategori_warna || 'var(--accent)'}; border: 1px solid rgba(124, 58, 237, 0.2);">
                        ${prod.kategori}
                    </span>
                </td>
                <td>
                    <span style="font-size: 12px; font-weight: 500; color: var(--text-secondary);">
                        <i class="fas fa-layer-group" style="color: var(--accent); margin-right: 4px; font-size: 11px;"></i>
                        ${prod.lokasi_rak || 'Rak A-01'}
                    </span>
                </td>
                <td>
                    <div style="display: flex; flex-wrap: wrap; max-width: 280px;">
                        ${variantChips}
                    </div>
                </td>
                <td>
                    <strong style="font-size: 14px; color: var(--text-primary);">${totalStok}</strong>
                    <span style="font-size: 11px; color: var(--text-muted);">&nbsp;pcs</span>
                </td>
                <td>${statusBadge}</td>
                <td style="text-align: right; padding-right: 20px;">
                    <div style="display: inline-flex; gap: 6px;">
                        <button class="btn-action" title="Edit Produk" onclick="openEditProductModal(${prod.id})">
                            <i class="fas fa-pen"></i>
                        </button>
                        <button class="btn-action delete" title="Hapus Produk" onclick="deleteProduct(${prod.id})">
                            <i class="fas fa-trash-can"></i>
                        </button>
                    </div>
                </td>
            </tr>
        `;
    }).join('');
}

/* ================================================================
   SEARCH & FILTER
   ================================================================ */
function filterProducts() {
    const searchVal = (document.getElementById('productSearchInput')?.value || '').toLowerCase().trim();
    const catVal = document.getElementById('productCategoryFilter')?.value || '';
    const statusVal = document.getElementById('productStatusFilter')?.value || '';

    const filtered = productsList.filter(prod => {
        // 1. Pencarian
        const matchSearch = !searchVal ||
            prod.nama_produk.toLowerCase().includes(searchVal) ||
            prod.kode_produk.toLowerCase().includes(searchVal) ||
            (prod.lokasi_rak && prod.lokasi_rak.toLowerCase().includes(searchVal));

        // 2. Kategori
        const matchCat = !catVal || prod.kategori === catVal;

        // 3. Status Stok
        let prodStatus = 'aman';
        if (prod.variants.some(v => v.stok <= 3)) {
            prodStatus = 'kritis';
        } else if (prod.variants.some(v => v.stok <= 9)) {
            prodStatus = 'waspada';
        }
        const matchStatus = !statusVal || prodStatus === statusVal;

        return matchSearch && matchCat && matchStatus;
    });

    renderProductsTable(filtered);
}

function handleProductSearch() {
    filterProducts();
}

function handleProductFilter() {
    filterProducts();
}

/* ================================================================
   MODAL TAMBAH & EDIT PRODUK
   ================================================================ */
function openAddProductModal() {
    document.getElementById('modalTitle').innerHTML = '<i class="fas fa-box-open" style="color:var(--accent);"></i> Tambah Produk Baru';
    document.getElementById('formProductId').value = '';
    document.getElementById('formKodeProduk').value = '';
    document.getElementById('formKodeProduk').disabled = false;
    document.getElementById('formNamaProduk').value = '';
    document.getElementById('formKategoriId').value = '1';
    document.getElementById('formLokasiRak').value = 'Rak A-01';
    document.getElementById('formDeskripsi').value = '';

    // Siapkan 1 baris varian default
    const container = document.getElementById('variantRowsContainer');
    container.innerHTML = '';
    addVariantRow('M', 'Putih', 10);

    document.getElementById('productModal').classList.add('active');
}

function openEditProductModal(productId) {
    const prod = productsList.find(p => p.id === productId);
    if (!prod) return;

    document.getElementById('modalTitle').innerHTML = '<i class="fas fa-pen-to-square" style="color:var(--accent);"></i> Edit Data Produk';
    document.getElementById('formProductId').value = prod.id;
    document.getElementById('formKodeProduk').value = prod.kode_produk;
    document.getElementById('formKodeProduk').disabled = true; // Kode produk unik tidak boleh diedit
    document.getElementById('formNamaProduk').value = prod.nama_produk;
    document.getElementById('formLokasiRak').value = prod.lokasi_rak || 'Rak A-01';
    document.getElementById('formDeskripsi').value = prod.deskripsi || '';

    // Set dropdown kategori
    const catSelect = document.getElementById('formKategoriId');
    const options = Array.from(catSelect.options);
    const matchOpt = options.find(o => o.text.toLowerCase() === prod.kategori.toLowerCase());
    if (matchOpt) catSelect.value = matchOpt.value;

    // Isi baris varian
    const container = document.getElementById('variantRowsContainer');
    container.innerHTML = '';
    if (prod.variants && prod.variants.length > 0) {
        prod.variants.forEach(v => addVariantRow(v.ukuran, v.warna, v.stok));
    } else {
        addVariantRow('M', 'Hitam', 10);
    }

    document.getElementById('productModal').classList.add('active');
}

function closeProductModal() {
    document.getElementById('productModal').classList.remove('active');
}

function addVariantRow(ukuran = '', warna = '', stok = 0) {
    const container = document.getElementById('variantRowsContainer');
    const rowId = 'var_row_' + Date.now() + '_' + Math.floor(Math.random() * 100);

    const div = document.createElement('div');
    div.className = 'variant-row';
    div.id = rowId;
    div.innerHTML = `
        <div>
            <label class="form-label" style="font-size:11px;">Ukuran</label>
            <input type="text" class="form-control var-size" placeholder="S / M / L / XL / 32" value="${ukuran}" required>
        </div>
        <div>
            <label class="form-label" style="font-size:11px;">Warna</label>
            <input type="text" class="form-control var-color" placeholder="Putih / Navy / Hitam" value="${warna}" required>
        </div>
        <div>
            <label class="form-label" style="font-size:11px;">Stok Awal</label>
            <input type="number" class="form-control var-stock" min="0" placeholder="0" value="${stok}" required>
        </div>
        <div style="padding-top: 18px;">
            <button type="button" class="btn-icon-danger" title="Hapus Varian" onclick="removeVariantRow('${rowId}')">
                <i class="fas fa-trash-can" style="font-size:11px;"></i>
            </button>
        </div>
    `;

    container.appendChild(div);
}

function removeVariantRow(rowId) {
    const container = document.getElementById('variantRowsContainer');
    if (container.children.length <= 1) {
        showToast('Minimal harus ada 1 varian produk!', 'error');
        return;
    }
    const row = document.getElementById(rowId);
    if (row) row.remove();
}

/* ================================================================
   SUBMIT PRODUK (TAMBAH / UPDATE)
   ================================================================ */
async function handleProductSubmit(event) {
    event.preventDefault();

    const editId = document.getElementById('formProductId').value;
    const kodeProduk = document.getElementById('formKodeProduk').value.trim().toUpperCase();
    const namaProduk = document.getElementById('formNamaProduk').value.trim();
    const kategoriSelect = document.getElementById('formKategoriId');
    const kategoriNama = kategoriSelect.options[kategoriSelect.selectedIndex].text;
    const lokasiRak = document.getElementById('formLokasiRak').value.trim();
    const deskripsi = document.getElementById('formDeskripsi').value.trim();

    // Kumpulkan seluruh baris varian
    const rows = document.querySelectorAll('#variantRowsContainer .variant-row');
    const variants = [];
    rows.forEach(r => {
        const u = r.querySelector('.var-size').value.trim();
        const w = r.querySelector('.var-color').value.trim();
        const s = parseInt(r.querySelector('.var-stock').value || 0, 10);
        if (u && w) {
            variants.push({
                ukuran: u,
                warna: w,
                stok: Math.max(0, s),
                status: s <= 3 ? 'kritis' : (s <= 9 ? 'waspada' : 'aman')
            });
        }
    });

    if (variants.length === 0) {
        showToast('Tambahkan minimal 1 varian produk!', 'error');
        return;
    }

    if (editId) {
        // Mode Update
        const target = productsList.find(p => p.id === parseInt(editId, 10));
        if (target) {
            target.nama_produk = namaProduk;
            target.kategori = kategoriNama;
            target.lokasi_rak = lokasiRak;
            target.deskripsi = deskripsi;
            target.variants = variants;
            showToast(`✅ Produk '${namaProduk}' berhasil diperbarui!`, 'success');
        }
    } else {
        // Mode Tambah Baru
        // Cek apakah kode sudah ada
        if (productsList.some(p => p.kode_produk === kodeProduk)) {
            showToast(`Kode produk '${kodeProduk}' sudah ada! Gunakan kode lain.`, 'error');
            return;
        }

        const newProduct = {
            id: Date.now(),
            kode_produk: kodeProduk,
            nama_produk: namaProduk,
            kategori: kategoriNama,
            kategori_warna: '#7C3AED',
            lokasi_rak: lokasiRak,
            deskripsi: deskripsi,
            variants: variants
        };

        productsList.unshift(newProduct);
        showToast(`🎉 Produk '${namaProduk}' berhasil ditambahkan ke katalog!`, 'success');
    }

    closeProductModal();
    filterProducts();
}

/* ================================================================
   HAPUS PRODUK
   ================================================================ */
function deleteProduct(productId) {
    const prod = productsList.find(p => p.id === productId);
    if (!prod) return;

    if (confirm(`Apakah Anda yakin ingin menghapus produk '${prod.nama_produk}' (${prod.kode_produk}) beserta seluruh variannya?`)) {
        productsList = productsList.filter(p => p.id !== productId);
        renderProductsTable(productsList);
        showToast(`🗑️ Produk '${prod.nama_produk}' berhasil dihapus.`, 'info');
    }
}

/* ================================================================
   EKSPOR KE CSV
   ================================================================ */
function exportProductsCSV() {
    if (productsList.length === 0) {
        showToast('Tidak ada data produk untuk diekspor!', 'error');
        return;
    }

    let csvContent = 'data:text/csv;charset=utf-8,';
    csvContent += 'Kode Produk,Nama Produk,Kategori,Lokasi Rak,Ukuran,Warna,Stok Varian,Status\n';

    productsList.forEach(p => {
        p.variants.forEach(v => {
            const row = [
                `"${p.kode_produk}"`,
                `"${p.nama_produk}"`,
                `"${p.kategori}"`,
                `"${p.lokasi_rak || '-'}"`,
                `"${v.ukuran}"`,
                `"${v.warna}"`,
                v.stok,
                `"${v.status}"`
            ].join(',');
            csvContent += row + '\n';
        });
    });

    const encodedUri = encodeURI(csvContent);
    const link = document.createElement('a');
    link.setAttribute('href', encodedUri);
    link.setAttribute('download', `Yunda_Fashion_Produk_${new Date().toISOString().slice(0, 10)}.csv`);
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);

    showToast('📊 File CSV produk berhasil diunduh!', 'success');
}

/* ================================================================
   TOAST NOTIFICATION HELPER
   ================================================================ */
function showToast(message, type = 'info') {
    const container = document.getElementById('toastContainer');
    if (!container) return;

    const toast = document.createElement('div');
    toast.className = `toast ${type}`;
    
    let icon = 'fa-circle-info';
    if (type === 'success') icon = 'fa-circle-check';
    if (type === 'error')   icon = 'fa-triangle-exclamation';

    toast.innerHTML = `
        <i class="fas ${icon}"></i>
        <span>${message}</span>
    `;

    container.appendChild(toast);

    setTimeout(() => {
        toast.style.opacity = '0';
        toast.style.transform = 'translateY(10px)';
        toast.style.transition = 'all 0.3s ease';
        setTimeout(() => toast.remove(), 300);
    }, 3200);
}
