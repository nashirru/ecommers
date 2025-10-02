<?php
// File: public/index.php (Router Utama Toko)
session_start();
require_once '../config/db.php';

// Tentukan halaman yang akan ditampilkan
$page = isset($_GET['page']) ? $_GET['page'] : 'home';

// DAFTAR HALAMAN YANG MEMBUTUHKAN LOGIN
$auth_required_pages = ['dashboard', 'checkout', 'orders', 'order_detail', 'order_success'];

// --- BAGIAN PERBAIKAN UTAMA DIMULAI DI SINI ---
// Pengecekan keamanan dijalankan SEBELUM output HTML apapun.
if (in_array($page, $auth_required_pages) && !isset($_SESSION['user_id'])) {
    // Jika halaman butuh login tapi user belum login, alihkan ke halaman login.
    header('Location: ../auth/login.php?redirect=' . urlencode($page));
    exit(); // Penting: Hentikan eksekusi skrip setelah redirect.
}
// --- BAGIAN PERBAIKAN UTAMA SELESAI ---


// Handler untuk form POST (seperti saat checkout)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action']) && $_POST['action'] === 'checkout') {
        // Logika ini hanya akan berjalan jika user sudah pasti login (karena sudah dicek di atas)
        require_once '../app/models/Order.php';
        $order_model = new Order($conn);
        $userId = $_SESSION['user_id'];
        $address = $_POST['address'];
        $paymentMethod = $_POST['payment_method'];
        $cart = $_SESSION['cart'] ?? [];
        
        if (!empty($cart)) {
            require_once '../app/models/Product.php';
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
                header('Location: index.php?page=order_success&order_id=' . $result['order_id'] . '&invoice=' . $result['invoice']);
                exit();
            } else {
                header('Location: index.php?page=checkout&error=1');
                exit();
            }
        }
    }
}


// Setelah semua logika redirect selesai, baru kita panggil file tampilan
require_once '../app/views/partials/header.php';

$view_path = '../app/views/' . $page . '.php';

if (file_exists($view_path)) {
    require_once $view_path;
} else {
    // Jika halaman tidak ada, tampilkan halaman utama
    require_once '../app/views/home.php';
}

require_once '../app/views/partials/footer.php';
?>