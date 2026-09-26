<?php
// edit.php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
}

if (!$id) {
    set_flash('error', 'ID Produk tidak valid.');
    header('Location: index.php');
    exit;
}

// Ambil data produk lama
$stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
$stmt->execute([$id]);
$product = $stmt->fetch();

if (!$product) {
    set_flash('error', 'Produk tidak ditemukan.');
    header('Location: index.php');
    exit;
}

$errors = [];
$name     = $product['name'];
$category = $product['category'];
$price    = $product['price'];
$stock    = $product['stock'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        die('Akses tidak sah: Token CSRF tidak valid.');
    }

    $name     = trim($_POST['name'] ?? '');
    $category = trim($_POST['category'] ?? '');
    $price    = trim($_POST['price'] ?? '');
    $stock    = trim($_POST['stock'] ?? '');

    // Validasi Nama Produk (Abaikan nama milik ID saat ini)
    if ($name === '') {
        $errors['name'] = 'Nama produk wajib diisi.';
    } elseif (mb_strlen($name) < 3) {
        $errors['name'] = 'Nama produk minimal harus 3 karakter.';
    } else {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM products WHERE name = ? AND id != ?");
        $stmt->execute([$name, $id]);
        if ($stmt->fetchColumn() > 0) {
            $errors['name'] = 'Nama produk sudah digunakan oleh produk lain.';
        }
    }

    // Validasi Kategori
    if ($category === '') {
        $errors['category'] = 'Kategori wajib diisi.';
    }

    // Validasi Harga
    if ($price === '') {
        $errors['price'] = 'Harga wajib diisi.';
    } elseif (!is_numeric($price) || (float)$price <= 0) {
        $errors['price'] = 'Harga harus berupa angka dan harus lebih besar dari 0.';
    }

    // Validasi Stok
    if ($stock === '') {
        $errors['stock'] = 'Stok wajib diisi.';
    } elseif (filter_var($stock, FILTER_VALIDATE_INT) === false || (int)$stock < 0) {
        $errors['stock'] = 'Stok harus berupa bilangan bulat dan tidak boleh negatif.';
    }

    // Eksekusi Update
    if (empty($errors)) {
        $stmt = $pdo->prepare("UPDATE products SET name = ?, category = ?, price = ?, stock = ? WHERE id = ?");
        $stmt->execute([$name, $category, (float)$price, (int)$stock, $id]);

        set_flash('success', 'Produk berhasil diperbarui.');
        header('Location: index.php');
        exit;
    }
}

require_once __DIR__ . '/../includes/header.php';
?>

<div style="max-width: 600px; margin: 0 auto; background: #fff; padding: 2rem; border-radius: 8px; border: 1px solid #e2e8f0;">
    <h2 style="margin-bottom: 1.5rem;">Edit Produk</h2>

    <form method="POST" action="edit.php">
        <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
        <input type="hidden" name="id" value="<?= (int)$id ?>">

        <div class="form-group">
            <label for="name">Nama Produk</label>
            <input type="text" id="name" name="name" value="<?= e($name) ?>">
            <?php if (isset($errors['name'])): ?>
                <div class="error-text"><?= e($errors['name']) ?></div>
            <?php endif; ?>
        </div>

        <div class="form-group">
            <label for="category">Kategori</label>
            <input type="text" id="category" name="category" value="<?= e($category) ?>">
            <?php if (isset($errors['category'])): ?>
                <div class="error-text"><?= e($errors['category']) ?></div>
            <?php endif; ?>
        </div>

        <div class="form-group">
            <label for="price">Harga (Rp)</label>
            <input type="number" id="price" name="price" step="0.01" value="<?= e($price) ?>">
            <?php if (isset($errors['price'])): ?>
                <div class="error-text"><?= e($errors['price']) ?></div>
            <?php endif; ?>
        </div>

        <div class="form-group">
            <label for="stock">Jumlah Stok</label>
            <input type="number" id="stock" name="stock" value="<?= e($stock) ?>">
            <?php if (isset($errors['stock'])): ?>
                <div class="error-text"><?= e($errors['stock']) ?></div>
            <?php endif; ?>
        </div>

        <div style="display: flex; gap: 0.5rem; justify-content: flex-end; margin-top: 2rem;">
            <a href="index.php" class="btn btn-secondary">Batal</a>
            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
        </div>
    </form>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>