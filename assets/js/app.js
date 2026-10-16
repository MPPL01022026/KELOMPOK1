/**
 * app.js — Logika Navigasi & Layout Yunda Fashion Inventory
 *
 * Mengelola:
 *  - Navigasi antar halaman (SPA routing sederhana)
 *  - Toggle sidebar (desktop collapse & mobile open/close)
 *  - Update navbar saat berpindah halaman
 *  - Keyboard shortcut (Ctrl+B)
 */

'use strict';

/* ================================================================
   STATE
   ================================================================ */
let sidebarCollapsed = false;

/* ================================================================
   NAVIGASI
   Dipanggil via onclick di setiap nav-link.
   Menyembunyikan semua .page, lalu menampilkan halaman target.
   ================================================================ */
function navigateTo(pageId, pageTitle, clickedLink) {
    /* 1. Sembunyikan semua halaman */
    document.querySelectorAll('.page').forEach(p => p.classList.remove('active'));

    /* 2. Tampilkan halaman target */
    const target = document.getElementById('page-' + pageId);
    if (target) target.classList.add('active');

    /* 3. Update state active pada nav-link */
    document.querySelectorAll('.nav-link').forEach(link => link.classList.remove('active'));
    if (clickedLink) clickedLink.classList.add('active');

    /* 4. Update judul navbar */
    const titleEl = document.getElementById('navPageTitle');
    const bcEl    = document.getElementById('navBreadcrumb');
    if (titleEl) titleEl.textContent = pageTitle;
    if (bcEl)    bcEl.textContent    = pageTitle;

    /* 5. Tutup sidebar mobile */
    closeMobileSidebar();

    /* 6. Scroll konten ke atas */
    const content = document.querySelector('.content');
    if (content) content.scrollTop = 0;
}

/* ================================================================
   SIDEBAR — Desktop (collapse / expand)
   ================================================================ */
function toggleSidebar() {
    sidebarCollapsed = !sidebarCollapsed;

    const sidebar = document.getElementById('sidebar');
    const icon    = document.getElementById('toggleIcon');
    const label   = document.getElementById('toggleLabel');

    sidebar.classList.toggle('collapsed', sidebarCollapsed);

    if (icon) {
        icon.className = sidebarCollapsed
            ? 'fas fa-angles-right toggle-icon'
            : 'fas fa-angles-left toggle-icon';
    }

    if (label) {
        label.textContent = sidebarCollapsed ? 'Perluas' : 'Ciutkan Sidebar';
    }
}

/* ================================================================
   SIDEBAR — Mobile (open / close overlay)
   ================================================================ */
function toggleMobileSidebar() {
    if (window.innerWidth <= 768) {
        const sidebar  = document.getElementById('sidebar');
        const overlay  = document.getElementById('sidebarOverlay');
        const isOpen   = sidebar.classList.contains('mobile-open');

        sidebar.classList.toggle('mobile-open', !isOpen);
        overlay.classList.toggle('active', !isOpen);
    } else {
        /* Di desktop, tombol hamburger juga bisa toggle sidebar */
        toggleSidebar();
    }
}

function closeMobileSidebar() {
    if (window.innerWidth <= 768) {
        document.getElementById('sidebar').classList.remove('mobile-open');
        document.getElementById('sidebarOverlay').classList.remove('active');
    }
}

/* Klik overlay menutup sidebar mobile */
document.addEventListener('DOMContentLoaded', () => {
    const overlay = document.getElementById('sidebarOverlay');
    if (overlay) overlay.addEventListener('click', closeMobileSidebar);
});

/* ================================================================
   KEYBOARD SHORTCUT
   Ctrl+B  → Toggle sidebar
   ================================================================ */
document.addEventListener('keydown', e => {
    if (e.ctrlKey && e.key === 'b') {
        e.preventDefault();
        toggleSidebar();
    }
});

/* ================================================================
   RESIZE — Tutup mobile sidebar saat layar diperbesar
   ================================================================ */
window.addEventListener('resize', () => {
    if (window.innerWidth > 768) {
        closeMobileSidebar();
    }
});
