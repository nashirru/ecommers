<?php
// File: app/views/admin/orders.php
// Hapus pengecekan admin yang tidak perlu karena sudah ada di router.
require_once BASE_PATH . '/app/models/Order.php';
$order_model = new Order($conn);
$orders = $order_model->getAllOrders();
$status_updated = $_GET['status'] ?? '';
?>

<header class="bg-white shadow">
    <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
        <h1 class="text-3xl font-bold tracking-tight text-gray-900">Manajemen Pesanan</h1>
    </div>
</header>
<div class="mt-6">
    <?php if ($status_updated === 'success'): ?>
        <div class="p-4 mb-4 text-sm text-green-700 bg-green-100 rounded-lg">Status pesanan berhasil diperbarui.</div>
    <?php endif; ?>
    <div class="overflow-x-auto bg-white rounded-lg shadow">
        <table class="min-w-full leading-normal">
             <thead>
                <tr>
                    <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Invoice</th>
                    <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Pemesan</th>
                    <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Total</th>
                    <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
                    <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Tanggal</th>
                    <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($orders as $order): ?>
                <tr>
                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm text-indigo-600 font-semibold"><?php echo htmlspecialchars($order['invoice_number']); ?></td>
                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm"><?php echo htmlspecialchars($order['username']); ?></td>
                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">Rp <?php echo number_format($order['total_amount'], 0, ',', '.'); ?></td>
                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm"><?php echo htmlspecialchars($order['status']); ?></td>
                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm"><?php echo date('d M Y', strtotime($order['created_at'])); ?></td>
                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                        <a href="index.php?page=admin_order_detail&id=<?php echo $order['id']; ?>" class="text-indigo-600 hover:text-indigo-900">Lihat Detail</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>