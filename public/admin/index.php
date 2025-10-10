<?php
// File: public/admin/index.php (Router Khusus Admin)
session_start();

// Mendefinisikan path dasar absolut ke direktori root proyek
define('BASE_PATH', dirname(__DIR__, 2));

require_once BASE_PATH . '/config/db.php';
require_once BASE_PATH . '/app/models/User.php';

// Keamanan: Cek apakah user sudah login dan merupakan admin
$user_model = new User($conn);
if (!isset($_SESSION['user_id']) || !$user_model->isAdmin($_SESSION['user_id'])) {
    // Jika tidak, redirect ke halaman login
    header('Location: ' . BASE_PATH . '/auth/login.php');
    exit();
}

// Tentukan halaman admin yang akan ditampilkan
$page = $_GET['page'] ?? 'dashboard';

// Validasi page untuk keamanan (mencegah directory traversal)
$allowed_pages = [
    'dashboard',
    'products',
    'categories',
    'banners',
    'orders',
    'order_detail',
    'general',
    'settings',
    'users',
    'reports'
];

if (!in_array($page, $allowed_pages)) {
    $page = 'dashboard';
}

// Load header
require_once BASE_PATH . '/app/views/admin/partial/header.php';

// Routing untuk halaman-halaman di dalam panel admin
$view_path = BASE_PATH . '/app/views/admin/' . $page . '.php';

if (file_exists($view_path)) {
    require_once $view_path;
} else {
    // Jika halaman tidak ditemukan, tampilkan dashboard
    require_once BASE_PATH . '/app/views/admin/dashboard.php';
}

// Load footer
require_once BASE_PATH . '/app/views/admin/partial/footer.php';