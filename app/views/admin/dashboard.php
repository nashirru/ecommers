<?php
// File: app/views/admin/dashboard.php
// Halaman ini sekarang menjadi dashboard utama admin
require_once '../../app/models/Order.php';
$order_model = new Order($conn);
$report = $order_model->getSalesReport();

// Tambahan data untuk dashboard
$result_total_orders = $conn->query("SELECT COUNT(*) as total FROM orders");
$total_orders = $result_total_orders->fetch_assoc()['total'];

$result_pending_orders = $conn->query("SELECT COUNT(*) as total FROM orders WHERE status = 'Menunggu Pembayaran' OR status = 'Diproses'");
$pending_orders = $result_pending_orders->fetch_assoc()['total'];

$result_total_products = $conn->query("SELECT COUNT(*) as total FROM products");
$total_products = $result_total_products->fetch_assoc()['total'];
?>

<h1 class="text-3xl font-bold tracking-tight text-gray-900">Dashboard</h1>

<div class="mt-8 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    <!-- Total Omzet Card -->
    <div class="bg-white p-6 rounded-lg shadow-lg">
        <h3 class="text-lg font-medium text-gray-500">Total Omzet</h3>
        <p class="mt-2 text-3xl font-bold text-green-600">Rp <?php echo number_format($report['total_revenue'] ?? 0, 0, ',', '.'); ?></p>
        <p class="text-sm text-gray-400 mt-1">Dari pesanan yang selesai</p>
    </div>

    <!-- Total Pesanan Card -->
    <div class="bg-white p-6 rounded-lg shadow-lg">
        <h3 class="text-lg font-medium text-gray-500">Total Pesanan</h3>
        <p class="mt-2 text-3xl font-bold text-indigo-600"><?php echo $total_orders; ?></p>
        <p class="text-sm text-gray-400 mt-1">Semua status pesanan</p>
    </div>

    <!-- Pesanan Perlu Diproses Card -->
    <div class="bg-white p-6 rounded-lg shadow-lg">
        <h3 class="text-lg font-medium text-gray-500">Perlu Diproses</h3>
        <p class="mt-2 text-3xl font-bold text-yellow-600"><?php echo $pending_orders; ?></p>
        <p class="text-sm text-gray-400 mt-1">Pesanan baru & menunggu</p>
    </div>
     <!-- Total Produk Card -->
    <div class="bg-white p-6 rounded-lg shadow-lg">
        <h3 class="text-lg font-medium text-gray-500">Jumlah Produk</h3>
        <p class="mt-2 text-3xl font-bold text-blue-600"><?php echo $total_products; ?></p>
        <p class="text-sm text-gray-400 mt-1">Item yang dijual</p>
    </div>
</div>