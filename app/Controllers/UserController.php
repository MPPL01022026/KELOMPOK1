<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\User;

class UserController extends Controller
{
    private User $userModel;

    public function __construct()
    {
        parent::__construct();
        $this->userModel = new User();
    }

    public function index(): void
    {
        $users = $this->userModel->getAllUsers();
        $this->render('users.index', [
            'users' => $users,
            'pageTitle' => 'Manajemen Pengguna & Akun Kasir'
        ]);
    }

    public function store(): void
    {
        $name = trim($_POST['name'] ?? '');
        $username = trim($_POST['username'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $role = $_POST['role'] ?? 'kasir';

        if (empty($name) || empty($username) || empty($password)) {
            $this->setFlash('error', 'Nama, username, dan password wajib diisi.');
            $this->redirect('/users');
            return;
        }

        // Periksa apakah username sudah dipakai
        $existing = $this->userModel->findByUsername($username);
        if ($existing) {
            $this->setFlash('error', "Username '{$username}' sudah terdaftar. Gunakan username lain.");
            $this->redirect('/users');
            return;
        }

        $this->userModel->create([
            'name' => $name,
            'username' => $username,
            'email' => $email,
            'password' => $password,
            'role' => $role,
            'is_active' => 1
        ]);

        $this->setFlash('success', "Akun pengguna '{$name}' berhasil dibuat.");
        $this->redirect('/users');
    }

    public function toggle(string $id): void
    {
        $userId = (int)$id;
        $currentUser = $this->currentUser();

        // Jangan izinkan admin menonaktifkan akunnya sendiri
        if ($userId === (int)$currentUser['id']) {
            $this->setFlash('error', 'Anda tidak dapat menonaktifkan akun Anda sendiri.');
            $this->redirect('/users');
            return;
        }

        $this->userModel->toggleStatus($userId);
        $this->setFlash('success', 'Status akun pengguna berhasil diubah.');
        $this->redirect('/users');
    }
}
