<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle ?? $appName) ?> - <?= htmlspecialchars($appName) ?></title>
    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Bootstrap 5.3.3 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        :root {
            --brand-primary: #be123c;
            --brand-hover: #9f1239;
            --brand-subtle: #fff1f2;
            --text-main: #0f172a;
            --text-muted: #64748b;
            --bg-page: #f8fafc;
            --border-color: #e2e8f0;
            --sidebar-width: 250px;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background-color: var(--bg-page);
            color: var(--text-main);
            margin: 0;
            padding: 0;
            min-height: 100vh;
            -webkit-font-smoothing: antialiased;
        }

        /* Clean Sidebar */
        #sidebar {
            width: var(--sidebar-width);
            background: #0f172a;
            position: fixed;
            top: 0;
            left: 0;
            bottom: 0;
            z-index: 1030;
            display: flex;
            flex-direction: column;
            border-right: 1px solid #1e293b;
            transition: all 0.25s ease;
        }

        .sidebar-brand-box {
            padding: 1.25rem 1.2rem;
            display: flex;
            align-items: center;
            gap: 10px;
            border-bottom: 1px solid #1e293b;
            text-decoration: none;
        }

        .brand-badge {
            width: 36px;
            height: 36px;
            background: #be123c;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #ffffff;
            font-size: 1.15rem;
        }

        .brand-text h6 {
            color: #ffffff;
            font-weight: 700;
            margin: 0;
            font-size: 0.98rem;
            letter-spacing: -0.2px;
        }

        .brand-text span {
            color: #94a3b8;
            font-size: 0.7rem;
            font-weight: 500;
            letter-spacing: 0.3px;
        }

        .sidebar-nav {
            padding: 1rem 0.75rem;
            list-style: none;
            margin: 0;
            overflow-y: auto;
            flex: 1;
        }

        .sidebar-heading {
            font-size: 0.68rem;
            font-weight: 700;
            color: #475569;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            padding: 0.75rem 0.75rem 0.25rem;
        }

        .sidebar-nav li a {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 0.6rem 0.85rem;
            color: #94a3b8;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 500;
            font-size: 0.88rem;
            transition: all 0.15s ease;
            margin-bottom: 2px;
        }

        .sidebar-nav li a:hover {
            color: #ffffff;
            background: #1e293b;
        }

        .sidebar-nav li a.active {
            color: #ffffff;
            background: #be123c;
            font-weight: 600;
        }

        .sidebar-nav li a i {
            font-size: 1.05rem;
        }

        /* Content Area */
        #content-wrapper {
            margin-left: var(--sidebar-width);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            transition: all 0.25s ease;
        }

        /* Clean Topbar */
        .app-topbar {
            height: 60px;
            background: #ffffff;
            border-bottom: 1px solid var(--border-color);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 1.5rem;
            position: sticky;
            top: 0;
            z-index: 1020;
        }

        .page-body {
            padding: 1.5rem;
            flex: 1;
        }

        /* Clean Card Design */
        .clean-card, .card-custom {
            background: #ffffff;
            border: 1px solid var(--border-color);
            border-radius: 12px;
            box-shadow: 0 1px 2px rgba(0,0,0,0.02);
        }

        /* Pulse Live Indicator */
        .pulse-live {
            width: 8px;
            height: 8px;
            background-color: #10b981;
            border-radius: 50%;
            display: inline-block;
            box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7);
            animation: pulse-ring 2s infinite;
        }

        @keyframes pulse-ring {
            0% { box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7); }
            70% { box-shadow: 0 0 0 6px rgba(16, 185, 129, 0); }
            100% { box-shadow: 0 0 0 0 rgba(16, 185, 129, 0); }
        }

        @media (max-width: 992px) {
            #sidebar {
                margin-left: calc(-1 * var(--sidebar-width));
            }
            #sidebar.open {
                margin-left: 0;
            }
            #content-wrapper {
                margin-left: 0;
            }
        }
    </style>
</head>
<body>

    <!-- Sidebar Navigasi -->
    <nav id="sidebar">
        <a href="<?= $baseUrl ?>/" class="sidebar-brand-box">
            <div class="brand-badge">
                <i class="bi bi-gem"></i>
            </div>
            <div class="brand-text">
                <h6>Yunda Fashion</h6>
                <span>POS & Digital Twin</span>
            </div>
        </a>

        <?php 
            $currentUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
            $role = $currentUser['role'] ?? 'kasir';
        ?>

        <ul class="sidebar-nav">
            <?php if ($role === 'admin'): ?>
                <div class="sidebar-heading">Menu Utama</div>
                <li>
                    <a href="<?= $baseUrl ?>/dashboard" class="<?= str_contains($currentUri, '/dashboard') ? 'active' : '' ?>">
                        <i class="bi bi-grid-1x2"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
            <?php endif; ?>

            <div class="sidebar-heading">Kasir & Etalase</div>
            <li>
                <a href="<?= $baseUrl ?>/pos" class="<?= str_contains($currentUri, '/pos') ? 'active' : '' ?>">
                    <i class="bi bi-calculator"></i>
                    <span>Kasir POS</span>
                </a>
            </li>
            <li>
                <a href="<?= $baseUrl ?>/digital-twin" class="<?= str_contains($currentUri, '/digital-twin') ? 'active' : '' ?>">
                    <i class="bi bi-cpu"></i>
                    <span>Digital Twin</span>
                    <span class="pulse-live ms-auto"></span>
                </a>
            </li>
            <li>
                <a href="<?= $baseUrl ?>/transactions" class="<?= str_contains($currentUri, '/transactions') ? 'active' : '' ?>">
                    <i class="bi bi-receipt"></i>
                    <span>Riwayat Transaksi</span>
                </a>
            </li>

            <?php if ($role === 'admin'): ?>
                <div class="sidebar-heading">Inventori & Produk</div>
                <li>
                    <a href="<?= $baseUrl ?>/categories" class="<?= str_contains($currentUri, '/categories') ? 'active' : '' ?>">
                        <i class="bi bi-tags"></i>
                        <span>Kategori</span>
                    </a>
                </li>
                <li>
                    <a href="<?= $baseUrl ?>/products" class="<?= str_contains($currentUri, '/products') ? 'active' : '' ?>">
                        <i class="bi bi-bag"></i>
                        <span>Produk & Varian</span>
                    </a>
                </li>
                <li>
                    <a href="<?= $baseUrl ?>/stock/restock" class="<?= str_contains($currentUri, '/stock/restock') ? 'active' : '' ?>">
                        <i class="bi bi-box-arrow-in-down"></i>
                        <span>Barang Masuk</span>
                    </a>
                </li>
                <li>
                    <a href="<?= $baseUrl ?>/stock/adjustment" class="<?= str_contains($currentUri, '/stock/adjustment') ? 'active' : '' ?>">
                        <i class="bi bi-sliders"></i>
                        <span>Penyesuaian Fisik</span>
                    </a>
                </li>
                <li>
                    <a href="<?= $baseUrl ?>/stock/movements" class="<?= str_contains($currentUri, '/stock/movements') ? 'active' : '' ?>">
                        <i class="bi bi-clock-history"></i>
                        <span>Mutasi Persediaan</span>
                    </a>
                </li>

                <div class="sidebar-heading">Laporan & Kasir</div>
                <li>
                    <a href="<?= $baseUrl ?>/reports" class="<?= str_contains($currentUri, '/reports') ? 'active' : '' ?>">
                        <i class="bi bi-bar-chart"></i>
                        <span>Laporan Penjualan</span>
                    </a>
                </li>
                <li>
                    <a href="<?= $baseUrl ?>/users" class="<?= str_contains($currentUri, '/users') ? 'active' : '' ?>">
                        <i class="bi bi-people"></i>
                        <span>Akun Kasir</span>
                    </a>
                </li>
            <?php endif; ?>

            <div class="sidebar-heading">Sistem</div>
            <li>
                <a href="<?= $baseUrl ?>/logout" onclick="return confirm('Keluar dari sesi?')">
                    <i class="bi bi-box-arrow-right text-danger"></i>
                    <span class="text-danger">Keluar</span>
                </a>
            </li>
        </ul>
    </nav>

    <!-- Main Content Wrapper -->
    <div id="content-wrapper">
        <!-- Topbar -->
        <header class="app-topbar">
            <div class="d-flex align-items-center gap-2">
                <button class="btn btn-sm btn-light border d-lg-none" id="sidebarToggle">
                    <i class="bi bi-list"></i>
                </button>
                <span class="fw-semibold text-dark fs-6"><?= htmlspecialchars($pageTitle ?? 'Yunda Fashion') ?></span>
            </div>

            <div class="d-flex align-items-center gap-3">
                <div class="d-none d-sm-flex align-items-center gap-2 px-2 py-1 bg-light rounded border text-muted small">
                    <span class="pulse-live"></span>
                    <span id="headerClock"><?= date('H:i:s') ?> WIB</span>
                </div>

                <div class="dropdown">
                    <button class="btn btn-sm btn-light border d-flex align-items-center gap-2 rounded px-2 py-1" data-bs-toggle="dropdown">
                        <div class="rounded bg-danger-subtle text-danger fw-bold d-flex align-items-center justify-content-center" style="width:26px; height:26px; font-size:0.75rem;">
                            <?= strtoupper(substr($currentUser['name'] ?? 'U', 0, 1)) ?>
                        </div>
                        <span class="small fw-semibold text-dark d-none d-md-inline"><?= htmlspecialchars($currentUser['name'] ?? '') ?></span>
                        <span class="badge bg-secondary-subtle text-dark border small" style="font-size:0.65rem;"><?= strtoupper($currentUser['role'] ?? 'KASIR') ?></span>
                        <i class="bi bi-chevron-down text-muted small"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow-sm border rounded-3 mt-1 small">
                        <li class="px-3 py-1 text-muted border-bottom mb-1">
                            <span class="d-block fw-semibold text-dark"><?= htmlspecialchars($currentUser['name'] ?? '') ?></span>
                            <small>@<?= htmlspecialchars($currentUser['username'] ?? '') ?></small>
                        </li>
                        <li><a class="dropdown-item py-1" href="<?= $baseUrl ?>/transactions"><i class="bi bi-receipt me-2"></i>Riwayat Transaksi</a></li>
                        <li><hr class="dropdown-divider my-1"></li>
                        <li><a class="dropdown-item py-1 text-danger" href="<?= $baseUrl ?>/logout"><i class="bi bi-box-arrow-right me-2"></i>Keluar</a></li>
                    </ul>
                </div>
            </div>
        </header>

        <!-- Main Body Content -->
        <main class="page-body">
            <?php if (!empty($flashSuccess)): ?>
                <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-3 mb-3 d-flex align-items-center py-2 px-3 small" role="alert">
                    <i class="bi bi-check-circle-fill me-2 fs-6"></i>
                    <div><?= htmlspecialchars($flashSuccess) ?></div>
                    <button type="button" class="btn-close small" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?php if (!empty($flashError)): ?>
                <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm rounded-3 mb-3 d-flex align-items-center py-2 px-3 small" role="alert">
                    <i class="bi bi-exclamation-circle-fill me-2 fs-6"></i>
                    <div><?= htmlspecialchars($flashError) ?></div>
                    <button type="button" class="btn-close small" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?= $content ?>
        </main>

        <footer class="py-2 px-4 text-center text-muted small border-top bg-white mt-auto">
            <span>&copy; <?= date('Y') ?> <strong>Yunda Fashion</strong>. Sistem Kasir & Digital Twin Persediaan Toko.</span>
        </footer>
    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function updateHeaderClock() {
            const now = new Date();
            const h = String(now.getHours()).padStart(2, '0');
            const m = String(now.getMinutes()).padStart(2, '0');
            const s = String(now.getSeconds()).padStart(2, '0');
            const el = document.getElementById('headerClock');
            if (el) el.textContent = `${h}:${m}:${s} WIB`;
        }
        setInterval(updateHeaderClock, 1000);

        const toggleBtn = document.getElementById('sidebarToggle');
        const sidebar = document.getElementById('sidebar');
        if (toggleBtn && sidebar) {
            toggleBtn.addEventListener('click', () => {
                sidebar.classList.toggle('open');
            });
        }
    </script>
</body>
</html>
