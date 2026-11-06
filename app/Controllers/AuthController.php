<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\User;

class AuthController extends Controller
{
    private User $userModel;

    public function __construct()
    {
        parent::__construct();
        $this->userModel = new User();
    }

    public function rootRedirect(): void
    {
        if (!$this->currentUser()) {
            $this->redirect('/login');
            return;
        }

        if ($this->isAdmin()) {
            $this->redirect('/dashboard');
        } else {
            $this->redirect('/pos');
        }
    }

    public function showLogin(): void
    {
        // Jika sudah login, redirect sesuai role
        if ($this->currentUser()) {
            if ($this->isAdmin()) {
                $this->redirect('/dashboard');
            } else {
                $this->redirect('/pos');
            }
            return;
        }

        $this->render('auth.login', [], null); // Tanpa layout main
    }

    public function login(): void
    {
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';

        if (empty($username) || empty($password)) {
            $this->setFlash('error', 'Username dan password wajib diisi.');
            $this->redirect('/login');
            return;
        }

        $user = $this->userModel->findByUsername($username);

        // TC-02: Login menggunakan password salah / akun tidak ada -> sistem menolak
        if (!$user || !password_verify($password, $user['password'])) {
            $this->setFlash('error', 'Username atau password yang Anda masukkan salah.');
            $this->redirect('/login');
            return;
        }

        if ((int)$user['is_active'] !== 1) {
            $this->setFlash('error', 'Akun Anda dinonaktifkan oleh Administrator toko.');
            $this->redirect('/login');
            return;
        }

        // Login Berhasil (TC-01)
        $_SESSION['user'] = [
            'id' => $user['id'],
            'name' => $user['name'],
            'username' => $user['username'],
            'email' => $user['email'],
            'role' => $user['role'],
            'is_active' => $user['is_active']
        ];

        $this->setFlash('success', "Selamat datang kembali, {$user['name']}!");

        // Redirect sesuai role
        if ($user['role'] === 'admin') {
            $this->redirect('/dashboard');
        } else {
            $this->redirect('/pos');
        }
    }

    public function logout(): void
    {
        unset($_SESSION['user']);
        session_destroy();
        session_start();
        $this->setFlash('success', 'Anda telah berhasil keluar dari sistem.');
        $this->redirect('/login');
    }
}
