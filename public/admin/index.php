<?php
// File: public/admin/index.php (Router Khusus Admin)
session_start();

// --- Perbaikan Path ---
// Mendefinisikan path dasar absolut ke direktori root proyek.
// Ini membuat require_once lebih andal, tidak peduli dari mana skrip dijalankan.
define('BASE_PATH', dirname(__DIR__, 2));

require_once BASE_PATH . '/config/db.php';
require_once BASE_PATH . '/app/models/User.php';

// Keamanan: Cek apakah user sudah login dan merupakan admin.
$user_model = new User($conn);
if (!isset($_SESSION['user_id']) || !$user_model->isAdmin($_SESSION['user_id'])) {
    // Jika tidak, tendang ke halaman login publik.
    header('Location: ../../auth/login.php');
    exit();
}

// Tentukan halaman admin yang akan ditampilkan
$page = $_GET['page'] ?? 'dashboard';

// --- PERBAIKAN DI SINI ---
// Mengubah 'partials' menjadi 'partial' agar sesuai dengan nama folder yang benar.
require_once BASE_PATH . '/app/views/admin/partial/header.php';

// Routing untuk halaman-halaman di dalam panel admin
$view_path = BASE_PATH . '/app/views/admin/' . $page . '.php';

if (file_exists($view_path)) {
    require_once $view_path;
} else {
    // Jika halaman tidak ditemukan, tampilkan dashboard admin
    require_once BASE_PATH . '/app/views/admin/dashboard.php';
}

// --- PERBAIKAN DI SINI ---
// Mengubah 'partials' menjadi 'partial' agar sesuai dengan nama folder yang benar.
require_once BASE_PATH . '/app/views/admin/partial/footer.php';
?>