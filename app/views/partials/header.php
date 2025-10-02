<?php
// File: app/views/partials/header.php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
// $settings sudah tersedia dari router

// Hitung item di keranjang
$cart_count = 0;
if (isset($_SESSION['cart']) && is_array($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $item) {
        $cart_count += $item['quantity'];
    }
}
?>
<!DOCTYPE html>
<html lang="id" class="h-full bg-gray-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Warok Kite - Toko Layangan Modern</title>
    <script src="https://cdn.tailwindcss.com/"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Poppins', sans-serif; }
    </style>
</head>
<body class="h-full">
    <div class="min-h-full">
        <header class="bg-white shadow-md sticky top-0 z-50">
            <nav class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="flex h-20 items-center justify-between">
                    <!-- Logo -->
                    <div class="flex items-center">
                        <a href="index.php" class="flex-shrink-0">
                           <?php if (!empty($settings['store_logo'])): ?>
                                <img class="h-12 w-auto" src="assets/images/<?php echo htmlspecialchars($settings['store_logo']); ?>" alt="Warok Kite Logo">
                           <?php else: ?>
                               <span class="text-2xl font-bold text-indigo-600">Warok Kite</span>
                           <?php endif; ?>
                        </a>
                    </div>

                    <!-- Search Bar -->
                    <div class="hidden md:flex flex-1 justify-center px-8 lg:px-16">
                         <div class="w-full max-w-lg">
                            <label for="search" class="sr-only">Search</label>
                            <div class="relative">
                                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                    <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                        <path fill-rule="evenodd" d="M9 3.5a5.5 5.5 0 100 11 5.5 5.5 0 000-11zM2 9a7 7 0 1112.452 4.391l3.328 3.329a.75.75 0 11-1.06 1.06l-3.329-3.328A7 7 0 012 9z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <input id="search" name="search" class="block w-full rounded-md border border-gray-300 bg-white py-2 pl-10 pr-3 leading-5 text-gray-900 placeholder-gray-500 focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500 sm:text-sm" placeholder="Cari layangan idamanmu..." type="search">
                            </div>
                        </div>
                    </div>

                    <!-- Icons & Auth -->
                    <div class="flex items-center space-x-4">
                        <a href="index.php?page=cart" class="relative rounded-full p-2 text-gray-500 hover:bg-gray-100 hover:text-gray-600">
                            <svg class="h-7 w-7" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 00-3 3h15.75m-12.75-3h11.218c.51 0 .962-.343 1.087-.835l1.823-6.44a1.125 1.125 0 00-.44-1.228L13.102 4.237a1.125 1.125 0 00-1.218 0l-5.644 3.25a1.125 1.125 0 00-.44 1.228l1.823 6.44zM7.5 14.25h11.218M7.5 14.25a3 3 0 01-3-3h.008v.008h-.008v-.008zm0 0h.008v.008h-.008v-.008zm7.5 0a3 3 0 01-3-3h.008v.008h-.008v-.008zm0 0h.008v.008h-.008v-.008z" />
                            </svg>
                            <?php if($cart_count > 0): ?>
                            <span class="absolute -top-1 -right-1 inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-red-100 bg-red-600 rounded-full"><?php echo $cart_count; ?></span>
                            <?php endif; ?>
                        </a>
                        
                        <div class="hidden sm:flex items-center space-x-2 border-l pl-4">
                            <?php if (isset($_SESSION['user_id'])): ?>
                                <?php if (isset($_SESSION['is_admin']) && $_SESSION['is_admin']): ?>
                                     <a href="admin/" class="text-sm font-medium text-red-600 hover:text-red-800">Admin Panel</a>
                                <?php else: ?>
                                     <a href="index.php?page=dashboard" class="text-sm font-medium text-gray-700 hover:text-indigo-600"><?php echo htmlspecialchars($_SESSION['username']); ?></a>
                                <?php endif; ?>
                                <span class="text-gray-300">|</span>
                                <a href="../auth/logout.php" class="text-sm font-medium text-gray-700 hover:text-indigo-600">Logout</a>
                            <?php else: ?>
                                <a href="../auth/login.php" class="whitespace-nowrap rounded-md border border-transparent bg-indigo-600 px-4 py-2 text-base font-medium text-white shadow-sm hover:bg-indigo-700">Login</a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <!-- Nav Links -->
                 <div class="hidden md:flex h-12 items-center justify-center space-x-8 border-t">
                    <a href="index.php?page=home" class="text-gray-600 hover:text-indigo-600 font-medium">Home</a>
                    <a href="index.php?page=products" class="text-gray-600 hover:text-indigo-600 font-medium">Semua Produk</a>
                    <?php if (isset($_SESSION['user_id']) && !(isset($_SESSION['is_admin']) && $_SESSION['is_admin'])): ?>
                        <a href="index.php?page=orders" class="text-gray-600 hover:text-indigo-600 font-medium">Pesanan Saya</a>
                    <?php endif; ?>
                </div>
            </nav>
        </header>
        <main>
            <div class="mx-auto max-w-7xl py-6 sm:px-6 lg:px-8">