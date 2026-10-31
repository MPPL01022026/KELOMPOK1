<?php
namespace App\Core;

use App\Config\App;

abstract class Controller
{
    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    protected function render(string $viewPath, array $data = [], ?string $layout = 'main'): void
    {
        // Ekstrak data agar dapat diakses sebagai variabel di view
        extract($data);

        $baseUrl = App::getBaseUrl();
        $appName = App::APP_NAME;
        $currentUser = $this->currentUser();
        $flashSuccess = $this->getFlash('success');
        $flashError = $this->getFlash('error');
        $flashWarning = $this->getFlash('warning');

        $viewFile = dirname(__DIR__) . '/Views/' . str_replace('.', '/', $viewPath) . '.php';

        if (!file_exists($viewFile)) {
            die("File view tidak ditemukan: " . htmlspecialchars($viewFile));
        }

        // Render konten view
        ob_start();
        require $viewFile;
        $content = ob_get_clean();

        // Render dengan layout jika didefinisikan
        if ($layout !== null) {
            $layoutFile = dirname(__DIR__) . '/Views/layouts/' . $layout . '.php';
            if (!file_exists($layoutFile)) {
                die("File layout tidak ditemukan: " . htmlspecialchars($layoutFile));
            }
            require $layoutFile;
        } else {
            echo $content;
        }
    }

    protected function json(mixed $data, int $status = 200): void
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        exit;
    }

    protected function redirect(string $path): void
    {
        $baseUrl = App::getBaseUrl();
        $url = str_starts_with($path, 'http') ? $path : $baseUrl . '/' . ltrim($path, '/');
        header("Location: $url");
        exit;
    }

    protected function setFlash(string $type, string $message): void
    {
        $_SESSION['flash_' . $type] = $message;
    }

    protected function getFlash(string $type): ?string
    {
        $key = 'flash_' . $type;
        if (isset($_SESSION[$key])) {
            $msg = $_SESSION[$key];
            unset($_SESSION[$key]);
            return $msg;
        }
        return null;
    }

    protected function currentUser(): ?array
    {
        return $_SESSION['user'] ?? null;
    }

    protected function isAdmin(): bool
    {
        return ($this->currentUser()['role'] ?? '') === 'admin';
    }

    protected function isKasir(): bool
    {
        return ($this->currentUser()['role'] ?? '') === 'kasir';
    }
}
