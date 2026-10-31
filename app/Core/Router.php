<?php
namespace App\Core;

use App\Config\App;

class Router
{
    private array $routes = [];

    public function get(string $path, callable|array|string $handler, array $middlewares = []): void
    {
        $this->addRoute('GET', $path, $handler, $middlewares);
    }

    public function post(string $path, callable|array|string $handler, array $middlewares = []): void
    {
        $this->addRoute('POST', $path, $handler, $middlewares);
    }

    private function addRoute(string $method, string $path, callable|array|string $handler, array $middlewares): void
    {
        $this->routes[] = [
            'method'      => $method,
            'path'        => '/' . trim($path, '/'),
            'handler'     => $handler,
            'middlewares' => $middlewares,
        ];
    }

    public function dispatch(): void
    {
        $requestMethod = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        $requestUri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);

        // Cari base subpath (contoh: /yundafashion)
        $scriptDir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? ''));
        $base = rtrim($scriptDir, '/');
        if (str_ends_with($base, '/public')) {
            $base = substr($base, 0, -7);
        }

        // Hilangkan base folder dari URI
        if ($base !== '' && str_starts_with($requestUri, $base)) {
            $requestUri = substr($requestUri, strlen($base));
        }

        // Hilangkan '/public' jika ada di URI
        if (str_starts_with($requestUri, '/public')) {
            $requestUri = substr($requestUri, 7);
        }

        $requestUri = '/' . trim($requestUri, '/');
        if ($requestUri === '') {
            $requestUri = '/';
        }

        foreach ($this->routes as $route) {
            if ($route['method'] !== $requestMethod) {
                continue;
            }

            // Pola regex untuk parameter seperti {id} atau {slug}
            $pattern = preg_replace('#\{([a-zA-Z0-9_]+)\}#', '(?P<$1>[^/]+)', $route['path']);
            $pattern = '#^' . $pattern . '$#';

            if (preg_match($pattern, $requestUri, $matches)) {
                // Jalankan Middleware
                foreach ($route['middlewares'] as $mw) {
                    $this->runMiddleware($mw);
                }

                $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);

                if (is_array($route['handler'])) {
                    [$controllerClass, $method] = $route['handler'];
                    $controller = new $controllerClass();
                    call_user_func_array([$controller, $method], $params);
                } elseif (is_callable($route['handler'])) {
                    call_user_func_array($route['handler'], $params);
                }
                return;
            }
        }

        // 404 Not Found
        http_response_code(404);
        echo "<div style='font-family:sans-serif; text-align:center; padding:50px;'>";
        echo "<h2>404 - Halaman Tidak Ditemukan</h2>";
        echo "<p>Rute URI <code>" . htmlspecialchars($requestUri) . "</code> tidak terdaftar.</p>";
        echo "<a href='" . App::getBaseUrl() . "/' style='display:inline-block; margin-top:15px; padding:8px 18px; background:#4f46e5; color:#fff; text-decoration:none; border-radius:6px;'>Kembali ke Beranda</a>";
        echo "</div>";
    }

    private function runMiddleware(string $middleware): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $user = $_SESSION['user'] ?? null;
        $baseUrl = App::getBaseUrl();

        if ($middleware === 'auth') {
            if (!$user) {
                $_SESSION['flash_error'] = 'Silakan login terlebih dahulu untuk mengakses sistem.';
                header("Location: $baseUrl/login");
                exit;
            }
            if (($user['is_active'] ?? 0) != 1) {
                unset($_SESSION['user']);
                $_SESSION['flash_error'] = 'Akun Anda dinonaktifkan. Hubungi Administrator.';
                header("Location: $baseUrl/login");
                exit;
            }
        }

        if ($middleware === 'admin') {
            if (!$user || ($user['role'] ?? '') !== 'admin') {
                $_SESSION['flash_error'] = 'Akses ditolak. Fitur ini hanya untuk Administrator Toko.';
                header("Location: $baseUrl/pos");
                exit;
            }
        }

        if ($middleware === 'guest') {
            if ($user) {
                $dest = ($user['role'] === 'admin') ? '/dashboard' : '/pos';
                header("Location: $baseUrl" . $dest);
                exit;
            }
        }
    }
}
