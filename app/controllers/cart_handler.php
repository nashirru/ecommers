<?php
// File: app/controllers/cart_handler.php
session_start();

// Inisialisasi keranjang jika belum ada
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

$action = $_POST['action'] ?? '';
$id = (int)($_POST['product_id'] ?? 0);
$quantity = (int)($_POST['quantity'] ?? 1);

// --- ACTION: ADD TO CART ---
if ($action === 'add' && $id > 0 && $quantity > 0) {
    // Jika produk sudah ada di keranjang, update jumlahnya
    if (isset($_SESSION['cart'][$id])) {
        $_SESSION['cart'][$id]['quantity'] += $quantity;
    } else {
        // Jika belum ada, tambahkan sebagai item baru
        $_SESSION['cart'][$id] = ['quantity' => $quantity];
    }
    header('Location: ../../public/index.php?page=cart&status=added');
    exit();
}

// --- ACTION: UPDATE CART ---
if ($action === 'update' && $id > 0) {
    if ($quantity > 0) {
        $_SESSION['cart'][$id]['quantity'] = $quantity;
    } else {
        // Jika kuantitas 0 atau kurang, hapus item
        unset($_SESSION['cart'][$id]);
    }
    header('Location: ../../public/index.php?page=cart&status=updated');
    exit();
}

// --- ACTION: REMOVE FROM CART ---
if ($action === 'remove' && $id > 0) {
    unset($_SESSION['cart'][$id]);
    header('Location: ../../public/index.php?page=cart&status=removed');
    exit();
}

// Jika tidak ada aksi yang cocok, kembali ke halaman utama
header('Location: ../../public/index.php');
exit();
?>