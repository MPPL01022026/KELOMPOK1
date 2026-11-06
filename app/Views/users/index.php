<?php use App\Config\App; ?>

<div class="container-fluid px-0">
    <div class="row align-items-center mb-4">
        <div class="col-md-6">
            <h4 class="fw-bold text-dark mb-1">Manajemen Pengguna & Akun Kasir</h4>
            <p class="text-secondary small mb-0">Kelola akun kasir, kontrol hak akses, dan status keaktifan pengguna sistem toko.</p>
        </div>
        <div class="col-md-6 text-md-end mt-3 mt-md-0">
            <button class="btn btn-danger d-inline-flex align-items-center gap-2 fw-semibold px-3 py-2 rounded-3 shadow-sm" style="background:#be123c; border:none;" data-bs-toggle="modal" data-bs-target="#modalAddUser">
                <i class="bi bi-person-plus-fill"></i>
                <span>Tambah Akun Baru</span>
            </button>
        </div>
    </div>

    <!-- Tabel Pengguna -->
    <div class="card-custom p-4">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light small">
                    <tr>
                        <th style="width: 60px;">No.</th>
                        <th>Nama Pengguna</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th class="text-center">Peran (Role)</th>
                        <th class="text-center">Status</th>
                        <th>Terdaftar</th>
                        <th class="text-end" style="width: 140px;">Aksi</th>
                    </tr>
                </thead>
                <tbody class="small">
                    <?php foreach ($users as $idx => $u): ?>
                        <tr>
                            <td class="text-muted"><?= $idx + 1 ?></td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="rounded-circle bg-danger-subtle text-danger fw-bold d-flex align-items-center justify-content-center" style="width:34px; height:34px;">
                                        <?= strtoupper(substr($u['name'], 0, 1)) ?>
                                    </div>
                                    <strong class="text-dark"><?= htmlspecialchars($u['name']) ?></strong>
                                </div>
                            </td>
                            <td><span class="badge bg-light text-dark border">@<?= htmlspecialchars($u['username']) ?></span></td>
                            <td class="text-muted"><?= htmlspecialchars($u['email']) ?></td>
                            <td class="text-center">
                                <?php if ($u['role'] === 'admin'): ?>
                                    <span class="badge bg-danger text-white rounded-pill px-3">ADMIN TOKO</span>
                                <?php else: ?>
                                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-3">KASIR SHIFT</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <?php if ((int)$u['is_active'] === 1): ?>
                                    <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2">Aktif</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary text-white rounded-pill px-2">Non-Aktif</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-muted"><?= App::formatTanggalPendek($u['created_at']) ?></td>
                            <td class="text-end">
                                <?php if ($u['id'] !== $currentUser['id']): ?>
                                    <form action="<?= $baseUrl ?>/users/toggle/<?= $u['id'] ?>" method="POST" class="d-inline" onsubmit="return confirm('Ubah status keaktifan akun pengguna ini?')">
                                        <button type="submit" class="btn btn-sm <?= ((int)$u['is_active'] === 1) ? 'btn-outline-danger' : 'btn-outline-success' ?> rounded-pill px-2 py-0" style="font-size:0.75rem;">
                                            <?= ((int)$u['is_active'] === 1) ? 'Nonaktifkan' : 'Aktifkan' ?>
                                        </button>
                                    </form>
                                <?php else: ?>
                                    <span class="badge bg-light text-muted border">Akun Anda</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Tambah Pengguna -->
<div class="modal fade" id="modalAddUser" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-bottom">
                <h5 class="modal-title fw-bold">Tambah Akun Pengguna Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?= $baseUrl ?>/users/store" method="POST">
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-secondary">Nama Lengkap Petugas <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" placeholder="Contoh: Salsabila (Kasir)" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold text-secondary">Username Login <span class="text-danger">*</span></label>
                        <input type="text" name="username" class="form-control" placeholder="Contoh: salsabila" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold text-secondary">Email</label>
                        <input type="email" name="email" class="form-control" placeholder="petugas@yundafashion.com" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold text-secondary">Password <span class="text-danger">*</span></label>
                        <input type="password" name="password" class="form-control" placeholder="Minimal 6 karakter" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold text-secondary">Peran Akses (Role)</label>
                        <select name="role" class="form-select" required>
                            <option value="kasir" selected>Kasir (Akses POS, Riwayat Transaksi & Etalase Digital)</option>
                            <option value="admin">Admin / Pemilik Toko (Akses Seluruh Modul)</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer border-top bg-light">
                    <button type="button" class="btn btn-light rounded-3" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger rounded-3 fw-semibold px-4" style="background:#be123c; border:none;">Buat Akun</button>
                </div>
            </form>
        </div>
    </div>
</div>
