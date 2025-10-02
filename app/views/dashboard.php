<?php
// File: app/views/dashboard.php

// Cek apakah pengguna sudah login, jika belum, redirect ke halaman login
if (!isset($_SESSION['user_id'])) {
    header('Location: ../auth/login.php');
    exit();
}
?>

<header class="bg-white shadow">
    <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
        <h1 class="text-3xl font-bold tracking-tight text-gray-900">Dashboard Pengguna</h1>
    </div>
</header>
<div class="bg-white p-8 mt-6 rounded-lg shadow-lg">
    <h2 class="text-2xl font-semibold text-gray-800">Selamat Datang, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h2>
    <p class="mt-2 text-gray-600">Ini adalah halaman dashboard Anda. Di sini Anda dapat mengelola produk dan profil Anda.</p>
    <ul class="mt-4 list-disc list-inside text-gray-700">
        <li>Email Anda: <?php echo htmlspecialchars($_SESSION['email']); ?></li>
        <li>User ID Anda: <?php echo htmlspecialchars($_SESSION['user_id']); ?></li>
    </ul>
</div>