<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk - Yunda Fashion</title>
    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Bootstrap 5.3.3 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background: #f8fafc;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
            margin: 0;
            color: #0f172a;
            -webkit-font-smoothing: antialiased;
        }

        .auth-container {
            width: 100%;
            max-width: 420px;
        }

        .auth-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 16px;
            box-shadow: 0 4px 20px -2px rgba(15, 23, 42, 0.04), 0 2px 6px -1px rgba(15, 23, 42, 0.02);
            padding: 2.5rem 2.2rem;
        }

        .brand-emblem {
            width: 48px;
            height: 48px;
            background: #fff1f2;
            color: #be123c;
            border: 1px solid #ffe4e6;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.4rem;
            margin: 0 auto 1.25rem;
        }

        .auth-title {
            font-size: 1.25rem;
            font-weight: 700;
            color: #0f172a;
            letter-spacing: -0.3px;
            margin-bottom: 4px;
        }

        .auth-subtitle {
            font-size: 0.85rem;
            color: #64748b;
            margin-bottom: 1.75rem;
        }

        .form-label {
            font-size: 0.8rem;
            font-weight: 600;
            color: #334155;
            margin-bottom: 6px;
        }

        .input-group-text {
            background: #f8fafc;
            border-color: #cbd5e1;
            color: #64748b;
            border-radius: 10px 0 0 10px;
            padding-left: 14px;
            padding-right: 14px;
        }

        .form-control {
            border-color: #cbd5e1;
            border-radius: 0 10px 10px 0;
            padding: 10px 14px;
            font-size: 0.9rem;
            color: #0f172a;
        }

        .form-control:focus {
            border-color: #be123c;
            box-shadow: 0 0 0 3px rgba(190, 18, 60, 0.1);
        }

        .btn-auth-primary {
            background: #be123c;
            color: #ffffff;
            border: none;
            border-radius: 10px;
            padding: 11px;
            font-size: 0.92rem;
            font-weight: 600;
            letter-spacing: 0.2px;
            transition: all 0.15s ease;
            width: 100%;
        }

        .btn-auth-primary:hover {
            background: #9f1239;
            color: #ffffff;
        }

        .auth-footer {
            text-align: center;
            margin-top: 1.5rem;
            color: #94a3b8;
            font-size: 0.78rem;
        }
    </style>
</head>
<body>

    <div class="auth-container">
        <div class="auth-card">
            <!-- Emblem & Branding -->
            <div class="brand-emblem">
                <i class="bi bi-gem"></i>
            </div>
            
            <div class="text-center">
                <h4 class="auth-title">Yunda Fashion</h4>
                <p class="auth-subtitle">Sistem Kasir & Monitoring Stok Digital Twin</p>
            </div>

            <!-- Flash Error -->
            <?php if (!empty($flashError)): ?>
                <div class="alert alert-danger alert-dismissible fade show border-0 py-2 px-3 small rounded-2 mb-3 d-flex align-items-center" role="alert">
                    <i class="bi bi-exclamation-circle-fill me-2 fs-6"></i>
                    <div><?= htmlspecialchars($flashError) ?></div>
                    <button type="button" class="btn-close small" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <!-- Flash Success -->
            <?php if (!empty($flashSuccess)): ?>
                <div class="alert alert-success alert-dismissible fade show border-0 py-2 px-3 small rounded-2 mb-3 d-flex align-items-center" role="alert">
                    <i class="bi bi-check-circle-fill me-2 fs-6"></i>
                    <div><?= htmlspecialchars($flashSuccess) ?></div>
                    <button type="button" class="btn-close small" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <!-- Form Login -->
            <form action="<?= $baseUrl ?>/login" method="POST">
                <div class="mb-3">
                    <label class="form-label">Username</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-person"></i></span>
                        <input type="text" name="username" id="usernameInput" class="form-control" placeholder="Masukkan username" required autofocus>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label">Kata Sandi</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-lock"></i></span>
                        <input type="password" name="password" id="passwordInput" class="form-control" placeholder="••••••••" required>
                    </div>
                </div>

                <button type="submit" class="btn btn-auth-primary">
                    Masuk ke Sistem
                </button>
            </form>
        </div>

        <div class="auth-footer">
            &copy; <?= date('Y') ?> Yunda Fashion. All rights reserved.
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
