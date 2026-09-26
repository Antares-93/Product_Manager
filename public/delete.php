<?php
// delete.php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';

// Pastikan hanya metode POST yang diizinkan
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    die('Metode tidak diizinkan.');
}

// Validasi Token CSRF
if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
    die('Akses tidak sah: Token CSRF tidak valid.');
}

$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

if ($id) {
    $stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
    $stmt->execute([$id]);

    if ($stmt->rowCount() > 0) {
        set_flash('success', 'Produk berhasil dihapus.');
    } else {
        set_flash('error', 'Produk gagal dihapus atau tidak ditemukan.');
    }
} else {
    set_flash('error', 'ID Produk tidak valid.');
}

header('Location: index.php');
exit;