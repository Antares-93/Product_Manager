<?php
// create.php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';

$errors = [];
$name = '';
$category = '';
$price = '';
$stock = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validasi Anti-CSRF
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        die('Akses tidak sah: Token CSRF tidak valid.');
    }

    $name     = trim($_POST['name'] ?? '');
    $category = trim($_POST['category'] ?? '');
    $price    = trim($_POST['price'] ?? '');
    $stock    = trim($_POST['stock'] ?? '');

    // 1. Validasi Nama
    if ($name === '') {
        $errors['name'] = 'Nama produk wajib diisi.';
    } elseif (mb_strlen($name) < 3) {
        $errors['name'] = 'Nama produk minimal harus 3 karakter.';
    } else {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM products WHERE name = ?");
        $stmt->execute([$name]);
        if ($stmt->fetchColumn() > 0) {
            $errors['name'] = 'Nama produk sudah terdaftar, gunakan nama lain.';
        }
    }

    // 2. Validasi Kategori
    if ($category === '') {
        $errors['category'] = 'Kategori wajib diisi.';
    }

    // 3. Validasi Harga
    if ($price === '') {
        $errors['price'] = 'Harga wajib diisi.';
    } elseif (!is_numeric($price) || (float)$price <= 0) {
        $errors['price'] = 'Harga harus berupa angka dan harus lebih besar dari 0.';
    }

    // 4. Validasi Stok
    if ($stock === '') {
        $errors['stock'] = 'Stok wajib diisi.';
    } elseif (filter_var($stock, FILTER_VALIDATE_INT) === false || (int)$stock < 0) {
        $errors['stock'] = 'Stok harus berupa bilangan bulat dan tidak boleh bernilai negatif.';
    }

    // Jika lolos validasi, simpan data ke database
    if (empty($errors)) {
        $stmt = $pdo->prepare("INSERT INTO products (name, category, price, stock) VALUES (?, ?, ?, ?)");
        $stmt->execute([$name, $category, (float)$price, (int)$stock]);

        set_flash('success', 'Produk berhasil ditambahkan.');
        header('Location: index.php');
        exit;
    }
}

require_once __DIR__ . '/../includes/header.php';
?>

<div style="max-width: 600px; margin: 0 auto; background: #fff; padding: 2rem; border-radius: 8px; border: 1px solid #e2e8f0;">
    <h2 style="margin-bottom: 1.5rem;">Tambah Produk Baru</h2>

    <form method="POST" action="create.php">
        <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">

        <div class="form-group">
            <label for="name">Nama Produk</label>
            <input type="text" id="name" name="name" value="<?= e($name) ?>" placeholder="Contoh: Teh Puncak">
            <?php if (isset($errors['name'])): ?>
                <div class="error-text"><?= e($errors['name']) ?></div>
            <?php endif; ?>
        </div>

        <div class="form-group">
            <label for="category">Kategori</label>
            <input type="text" id="category" name="category" value="<?= e($category) ?>" placeholder="Contoh: Minuman">
            <?php if (isset($errors['category'])): ?>
                <div class="error-text"><?= e($errors['category']) ?></div>
            <?php endif; ?>
        </div>

        <div class="form-group">
            <label for="price">Harga (Rp)</label>
            <input type="number" id="price" name="price" step="0.01" value="<?= e($price) ?>" placeholder="0">
            <?php if (isset($errors['price'])): ?>
                <div class="error-text"><?= e($errors['price']) ?></div>
            <?php endif; ?>
        </div>

        <div class="form-group">
            <label for="stock">Jumlah Stok</label>
            <input type="number" id="stock" name="stock" value="<?= e($stock) ?>" placeholder="0">
            <?php if (isset($errors['stock'])): ?>
                <div class="error-text"><?= e($errors['stock']) ?></div>
            <?php endif; ?>
        </div>

        <div style="display: flex; gap: 0.5rem; justify-content: flex-end; margin-top: 2rem;">
            <a href="index.php" class="btn btn-secondary">Batal</a>
            <button type="submit" class="btn btn-primary">Simpan Produk</button>
        </div>
    </form>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>