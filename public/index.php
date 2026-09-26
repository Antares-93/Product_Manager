<?php
// index.php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/header.php';

$search = trim($_GET['search'] ?? '');

if ($search !== '') {
    $stmt = $pdo->prepare("SELECT * FROM products WHERE name LIKE :s_name OR category LIKE :s_cat ORDER BY id DESC");
    $stmt->execute([
        ':s_name' => "%$search%",
        ':s_cat'  => "%$search%"
    ]);
} else {
    $stmt = $pdo->query("SELECT * FROM products ORDER BY id DESC");
}

$products = $stmt->fetchAll();
?>

<div style="margin-bottom: 1.5rem; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
    <form method="GET" action="index.php" style="display: flex; gap: 0.5rem; flex-grow: 1; max-width: 400px;">
        <input type="text" name="search" placeholder="Cari nama atau kategori..." value="<?= e($search) ?>">
        <button type="submit" class="btn btn-secondary">Cari</button>
        <?php if ($search !== ''): ?>
            <a href="index.php" class="btn btn-secondary">Reset</a>
        <?php endif; ?>
    </form>
</div>

<?php if (empty($products)): ?>
    <div style="background: #fff; padding: 3rem; text-align: center; border-radius: 8px; border: 1px solid #e2e8f0;">
        <p style="color: #64748b;">Belum ada produk yang ditemukan.</p>
    </div>
<?php else: ?>
    <div class="product-grid">
        <?php foreach ($products as $prod): ?>
            <div class="card">
                <div>
                    <span class="card-badge"><?= e($prod['category']) ?></span>
                    <h2 class="card-title"><?= e($prod['name']) ?></h2>
                    <div class="card-price">Rp <?= number_format($prod['price'], 0, ',', '.') ?></div>
                    <div class="card-stock">Stok: <?= (int)$prod['stock'] ?> unit</div>
                </div>
                <div class="card-actions">
                    <a href="edit.php?id=<?= (int)$prod['id'] ?>" class="btn btn-secondary" style="flex: 1; text-align: center;">Edit</a>
                    
                    <form method="POST" action="delete.php" style="flex: 1;" onsubmit="return confirm('Apakah Anda yakin ingin menghapus produk ini?');">
                        <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                        <input type="hidden" name="id" value="<?= (int)$prod['id'] ?>">
                        <button type="submit" class="btn btn-danger" style="width: 100%;">Hapus</button>
                    </form>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>