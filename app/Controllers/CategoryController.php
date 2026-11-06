<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\Category;

class CategoryController extends Controller
{
    private Category $categoryModel;

    public function __construct()
    {
        parent::__construct();
        $this->categoryModel = new Category();
    }

    public function index(): void
    {
        $categories = $this->categoryModel->getAll();
        $this->render('categories.index', [
            'categories' => $categories,
            'pageTitle' => 'Manajemen Kategori Produk'
        ]);
    }

    public function store(): void
    {
        $name = trim($_POST['category_name'] ?? '');
        if (empty($name)) {
            $this->setFlash('error', 'Nama kategori tidak boleh kosong.');
            $this->redirect('/categories');
            return;
        }

        $this->categoryModel->create($name);
        $this->setFlash('success', "Kategori '{$name}' berhasil ditambahkan.");
        $this->redirect('/categories');
    }

    public function update(string $id): void
    {
        $catId = (int)$id;
        $name = trim($_POST['category_name'] ?? '');

        if (empty($name)) {
            $this->setFlash('error', 'Nama kategori tidak boleh kosong.');
            $this->redirect('/categories');
            return;
        }

        $this->categoryModel->update($catId, $name);
        $this->setFlash('success', "Kategori berhasil diperbarui menjadi '{$name}'.");
        $this->redirect('/categories');
    }

    public function delete(string $id): void
    {
        $catId = (int)$id;
        $deleted = $this->categoryModel->delete($catId);

        if ($deleted) {
            $this->setFlash('success', 'Kategori berhasil dihapus.');
        } else {
            $this->setFlash('error', 'Kategori tidak dapat dihapus karena masih digunakan oleh produk.');
        }

        $this->redirect('/categories');
    }
}
