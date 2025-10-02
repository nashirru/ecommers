<?php
// File: public/index.php (Router Utama Toko)
session_start();

define('BASE_PATH', dirname(__DIR__));

require_once BASE_PATH . '/config/db.php';
require_once BASE_PATH . '/config/helpers.php';

// --- Perbaikan Redirect Admin ---
if (isset($_SESSION['is_admin']) && $_SESSION['is_admin']) {
    header('Location: admin/index.php');
    exit();
}
// --- Akhir Perbaikan ---

// Memuat semua pengaturan dari DB ke variabel $settings
$settings = load_settings($conn);

// Tentukan halaman yang akan ditampilkan
$page = isset($_GET['page']) ? $_GET['page'] : 'home';

// --- Perubahan Keamanan ---
if (strpos($page, 'admin') === 0) {
    header('Location: index.php?page=home');
    exit();
}
// --- Akhir Perubahan Keamanan ---


// DAFTAR HALAMAN YANG MEMBUTUHKAN LOGIN
$auth_required_pages = ['dashboard', 'checkout', 'orders', 'order_detail', 'order_success'];

// Pengecekan keamanan dijalankan SEBELUM output HTML apapun.
if (in_array($page, $auth_required_pages) && !isset($_SESSION['user_id'])) {
    header('Location: ../auth/login.php?redirect=' . urlencode($page));
    exit(); 
}

// Handler untuk form POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action']) && $_POST['action'] === 'checkout') {
        require_once BASE_PATH . '/app/models/Order.php';
        $order_model = new Order($conn);
        $userId = $_SESSION['user_id'];
        $address = $_POST['address'];
        $paymentMethod = $_POST['payment_method'];
        $cart = $_SESSION['cart'] ?? [];
        
        if (!empty($cart)) {
            require_once BASE_PATH . '/app/models/Product.php';
            $product_model = new Product($conn);
            $product_ids = array_keys($cart);
            $products = $product_model->getMultipleByIds($product_ids);
            
            $total = 0;
            $cartItems = [];
            foreach ($products as $product) {
                $quantity = $cart[$product['id']]['quantity'];
                $total += $product['price'] * $quantity;
                $cartItems[] = [
                    'id' => $product['id'],
                    'quantity' => $quantity,
                    'price' => $product['price']
                ];
            }
            
            $result = $order_model->create($userId, $address, $total, $cartItems, $paymentMethod);
            if ($result) {
                unset($_SESSION['cart']);
                header('Location: index.php?page=order_success&order_id=' . $result['invoice']);
                exit();
            } else {
                header('Location: index.php?page=checkout&error=1');
                exit();
            }
        }
    }
}


// Setelah semua logika redirect selesai, baru kita panggil file tampilan
require_once BASE_PATH . '/app/views/partials/header.php';

$view_path = BASE_PATH . '/app/views/' . $page . '.php';

if (file_exists($view_path)) {
    require_once $view_path;
} else {
    require_once BASE_PATH . '/app/views/home.php';
}

require_once BASE_PATH . '/app/views/partials/footer.php';
?>