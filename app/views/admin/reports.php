<?php
// File: app/views/admin/reports.php

// Menggunakan BASE_PATH yang sudah didefinisikan di router admin
require_once BASE_PATH . '/app/models/Order.php';
$order_model = new Order($conn);
$report = $order_model->getSalesReport();
?>
<header class="bg-white shadow">
    <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
        <h1 class="text-3xl font-bold tracking-tight text-gray-900">Laporan Penjualan</h1>
    </div>
</header>
<div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-8">
    <div class="bg-white shadow rounded-lg p-6">
        <h3 class="text-lg font-medium text-gray-900">Total Omzet (dari pesanan selesai)</h3>
        <p class="mt-2 text-4xl font-bold text-green-600">Rp <?php echo number_format($report['total_revenue'], 0, ',', '.'); ?></p>
    </div>
    <div class="bg-white shadow rounded-lg p-6">
        <h3 class="text-lg font-medium text-gray-900">Produk Terlaris</h3>
        <ul class="mt-4 space-y-2">
            <?php foreach($report['best_selling'] as $item): ?>
            <li class="flex justify-between text-gray-700">
                <span><?php echo htmlspecialchars($item['name']); ?></span>
                <span class="font-semibold"><?php echo $item['total_sold']; ?> terjual</span>
            </li>
            <?php endforeach; ?>
            <?php if (empty($report['best_selling'])): ?>
                <li class="text-gray-500">Belum ada data.</li>
            <?php endif; ?>
        </ul>
    </div>
</div>