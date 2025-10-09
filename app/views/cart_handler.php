<?php
// File: app/controllers/cart_handler.php

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../../public/index.php');
    exit();
}

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

$action = $_POST['action'] ?? '';
$product_id = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
$quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;

switch ($action) {
    case 'add':
        if ($product_id > 0 && $quantity > 0) {
            if (isset($_SESSION['cart'][$product_id])) {
                $_SESSION['cart'][$product_id]['quantity'] += $quantity;
            } else {
                $_SESSION['cart'][$product_id] = ['quantity' => $quantity];
            }
            
            // PERBAIKAN: Mengambil URL dari input 'return_url' untuk kembali ke halaman asal.
            $return_url = $_POST['return_url'] ?? '../../public/index.php?page=home';
            
            // Menambahkan parameter status untuk memicu notifikasi toast
            $separator = (parse_url($return_url, PHP_URL_QUERY) == NULL) ? '?' : '&';
            $redirect_url = $return_url . $separator . 'status=added_toast';
            
            header('Location: ' . $redirect_url);
            exit();
        }
        break;

    case 'update':
        if ($product_id > 0) {
            if ($quantity > 0) {
                $_SESSION['cart'][$product_id]['quantity'] = $quantity;
            } else {
                unset($_SESSION['cart'][$product_id]);
            }
            // Aksi update dan remove tetap di halaman keranjang
            header('Location: ../../public/index.php?page=cart&status=updated');
            exit();
        }
        break;

    case 'remove':
        if ($product_id > 0 && isset($_SESSION['cart'][$product_id])) {
            unset($_SESSION['cart'][$product_id]);
            header('Location: ../../public/index.php?page=cart&status=removed');
            exit();
        }
        break;
}

header('Location: ../../public/index.php');
exit();
?>