<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Category;
use App\Models\StockMovement;
use Exception;

class ProductController extends Controller
{
    private Product $productModel;
    private ProductVariant $variantModel;
    private Category $categoryModel;
    private StockMovement $movementModel;

    public function __construct()
    {
        parent::__construct();
        $this->productModel = new Product();
        $this->variantModel = new ProductVariant();
        $this->categoryModel = new Category();
        $this->movementModel = new StockMovement();
    }

    public function index(): void
    {
        $categoryId = !empty($_GET['category_id']) ? (int)$_GET['category_id'] : null;
        $keyword = !empty($_GET['keyword']) ? trim($_GET['keyword']) : null;
        $status = !empty($_GET['status']) ? trim($_GET['status']) : null;

        $products = $this->productModel->getAll($categoryId, $keyword, $status);
        $categories = $this->categoryModel->getAll();

        $this->render('products.index', [
            'products' => $products,
            'categories' => $categories,
            'selectedCategory' => $categoryId,
            'keyword' => $keyword,
            'selectedStatus' => $status,
            'pageTitle' => 'Manajemen Produk & Varian'
        ]);
    }

    public function create(): void
    {
        $categories = $this->categoryModel->getAll();
        $this->render('products.create', [
            'categories' => $categories,
            'pageTitle' => 'Tambah Produk Baru'
        ]);
    }

    public function store(): void
    {
        $categoryId = (int)($_POST['category_id'] ?? 0);
        $productName = trim($_POST['product_name'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $price = (float)($_POST['price'] ?? 0);
        $status = $_POST['status'] ?? 'active';

        if (empty($productName) || $categoryId <= 0 || $price <= 0) {
            $this->setFlash('error', 'Nama produk, kategori, dan harga wajib diisi dengan benar.');
            $this->redirect('/products/create');
            return;
        }

        // Upload foto produk jika ada
        $imageName = $this->handleUpload('image');

        $productId = $this->productModel->create([
            'category_id' => $categoryId,
            'product_name' => $productName,
            'description' => $description,
            'image' => $imageName,
            'price' => $price,
            'status' => $status
        ]);

        // Simpan varian-varian awal (ukuran, warna, stok, min_stock)
        $sizes = $_POST['variant_size'] ?? [];
        $colors = $_POST['variant_color'] ?? [];
        $stocks = $_POST['variant_stock'] ?? [];
        $minStocks = $_POST['variant_min_stock'] ?? [];

        $currentUser = $this->currentUser();

        if (!empty($sizes) && is_array($sizes)) {
            for ($i = 0; $i < count($sizes); $i++) {
                $size = trim($sizes[$i] ?? '');
                $color = trim($colors[$i] ?? '');
                $stock = (int)($stocks[$i] ?? 0);
                $minStock = (int)($minStocks[$i] ?? 3);

                if (!empty($size) && !empty($color)) {
                    $variantId = $this->variantModel->create([
                        'product_id' => $productId,
                        'size' => $size,
                        'color' => $color,
                        'stock' => $stock,
                        'min_stock' => $minStock
                    ]);

                    // Jika ada stok awal > 0, catat mutasi restock awal
                    if ($stock > 0 && $currentUser) {
                        $this->movementModel->restock($variantId, $currentUser['id'], $stock, 'Stok awal produk');
                    }
                }
            }
        }

        $this->setFlash('success', "Produk '{$productName}' berhasil disimpan.");
        $this->redirect('/products');
    }

    public function edit(string $id): void
    {
        $productId = (int)$id;
        $product = $this->productModel->getById($productId);

        if (!$product) {
            $this->setFlash('error', 'Produk tidak ditemukan.');
            $this->redirect('/products');
            return;
        }

        $categories = $this->categoryModel->getAll();
        $variants = $this->variantModel->getByProductId($productId);

        $this->render('products.edit', [
            'product' => $product,
            'categories' => $categories,
            'variants' => $variants,
            'pageTitle' => 'Edit Produk: ' . $product['product_name']
        ]);
    }

    public function update(string $id): void
    {
        $productId = (int)$id;
        $product = $this->productModel->getById($productId);

        if (!$product) {
            $this->setFlash('error', 'Produk tidak ditemukan.');
            $this->redirect('/products');
            return;
        }

        $categoryId = (int)($_POST['category_id'] ?? 0);
        $productName = trim($_POST['product_name'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $price = (float)($_POST['price'] ?? 0);
        $status = $_POST['status'] ?? 'active';

        if (empty($productName) || $categoryId <= 0 || $price <= 0) {
            $this->setFlash('error', 'Semua field utama wajib diisi dengan benar.');
            $this->redirect("/products/edit/{$productId}");
            return;
        }

        $imageName = $this->handleUpload('image');

        $updateData = [
            'category_id' => $categoryId,
            'product_name' => $productName,
            'description' => $description,
            'price' => $price,
            'status' => $status
        ];

        if ($imageName !== null) {
            $updateData['image'] = $imageName;
        }

        $this->productModel->update($productId, $updateData);

        $this->setFlash('success', "Informasi produk '{$productName}' berhasil diperbarui.");
        $this->redirect("/products/edit/{$productId}");
    }

    public function delete(string $id): void
    {
        $productId = (int)$id;
        $deleted = $this->productModel->delete($productId);

        if ($deleted) {
            $this->setFlash('success', 'Produk berhasil dihapus.');
        } else {
            $this->setFlash('error', 'Produk tidak dapat dihapus karena sudah memiliki riwayat transaksi penjualan. Anda dapat mengubah statusnya menjadi Non-Aktif.');
        }

        $this->redirect('/products');
    }

    public function addVariant(string $productId): void
    {
        $pId = (int)$productId;
        $size = trim($_POST['size'] ?? '');
        $color = trim($_POST['color'] ?? '');
        $stock = (int)($_POST['stock'] ?? 0);
        $minStock = (int)($_POST['min_stock'] ?? 3);

        if (empty($size) || empty($color)) {
            $this->setFlash('error', 'Ukuran dan warna varian wajib diisi.');
            $this->redirect("/products/edit/{$pId}");
            return;
        }

        $variantId = $this->variantModel->create([
            'product_id' => $pId,
            'size' => $size,
            'color' => $color,
            'stock' => $stock,
            'min_stock' => $minStock
        ]);

        if ($stock > 0) {
            $user = $this->currentUser();
            if ($user) {
                $this->movementModel->restock($variantId, $user['id'], $stock, 'Stok awal varian baru');
            }
        }

        $this->setFlash('success', "Varian baru ({$size} - {$color}) berhasil ditambahkan.");
        $this->redirect("/products/edit/{$pId}");
    }

    public function deleteVariant(string $variantId): void
    {
        $vId = (int)$variantId;
        $variant = $this->variantModel->getById($vId);
        $productId = $variant['product_id'] ?? 0;

        $deleted = $this->variantModel->delete($vId);
        if ($deleted) {
            $this->setFlash('success', 'Varian produk berhasil dihapus.');
        } else {
            $this->setFlash('error', 'Varian tidak dapat dihapus karena sudah memiliki riwayat transaksi penjualan.');
        }

        if ($productId > 0) {
            $this->redirect("/products/edit/{$productId}");
        } else {
            $this->redirect('/products');
        }
    }

    private function handleUpload(string $field): ?string
    {
        if (empty($_FILES[$field]['name'])) {
            return null;
        }

        $file = $_FILES[$field];
        if ($file['error'] !== UPLOAD_ERR_OK) {
            return null;
        }

        $allowedExt = ['jpg', 'jpeg', 'png', 'webp'];
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

        if (!in_array($ext, $allowedExt)) {
            return null;
        }

        $uploadDir = dirname(dirname(__DIR__)) . '/public/uploads/products/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $newFileName = 'prod_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
        $targetPath = $uploadDir . $newFileName;

        if (move_uploaded_file($file['tmp_name'], $targetPath)) {
            return $newFileName;
        }

        return null;
    }
}
