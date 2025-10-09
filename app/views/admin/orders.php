<?php
// File: app/views/admin/orders.php
require_once BASE_PATH . '/app/models/Order.php';
$order_model = new Order($conn);

// Logika untuk Tabs
$sub_page = $_GET['sub'] ?? 'belum_dicetak';

$orders = [];
$page_title = 'Manajemen Pesanan';

if ($sub_page === 'belum_dicetak') {
    $orders = $order_model->getAllOrders('Belum Dicetak');
    $page_title = 'Pesanan Belum Dicetak';
} elseif ($sub_page === 'sedang_diproses') {
    $orders = $order_model->getAllOrders('Sedang Diproses');
    $page_title = 'Pesanan Sedang Diproses';
} elseif ($sub_page === 'dalam_pengiriman') {
    $orders = $order_model->getAllOrders('Dalam Pengiriman');
    $page_title = 'Pesanan Dalam Pengiriman';
} else { // 'lainnya'
    $exclude = ['Belum Dicetak', 'Sedang Diproses', 'Dalam Pengiriman'];
    $orders = $order_model->getAllOrders(null, $exclude);
    $page_title = 'Pesanan Lainnya';
}

$status_updated = $_GET['status'] ?? '';
?>

<header class="bg-white shadow">
    <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
        <h1 class="text-3xl font-bold tracking-tight text-gray-900"><?php echo $page_title; ?></h1>
    </div>
</header>
<div class="mt-6">
    <?php if ($status_updated === 'success'): ?>
        <div class="p-4 mb-4 text-sm text-green-700 bg-green-100 rounded-lg">Status pesanan berhasil diperbarui.</div>
    <?php endif; ?>

    <!-- Tombol Cetak Semua hanya muncul di tab "Belum Dicetak" -->
    <?php if ($sub_page === 'belum_dicetak' && !empty($orders)):
        $all_order_ids = array_column($orders, 'id');
    ?>
    <div class="mb-4">
        <a href="cetak_resi.php?ids=<?php echo implode(',', $all_order_ids); ?>" target="_blank" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
            <svg class="w-5 h-5 mr-2 -ml-1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M5 4v3H4a2 2 0 00-2 2v6a2 2 0 002 2h12a2 2 0 002-2V9a2 2 0 00-2-2h-1V4a2 2 0 00-2-2H7a2 2 0 00-2 2zm8 0H7v3h6V4zm0 8H7v4h6v-4z" clip-rule="evenodd" />
            </svg>
            Cetak Semua Resi (<?php echo count($all_order_ids); ?>)
        </a>
    </div>
    <?php endif; ?>

    <!-- Navigasi Tabs -->
    <div class="mb-4 border-b border-gray-200">
        <nav class="-mb-px flex space-x-8" aria-label="Tabs">
            <a href="index.php?page=orders&sub=belum_dicetak" class="<?php echo $sub_page === 'belum_dicetak' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'; ?> whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                Belum Dicetak
            </a>
            <a href="index.php?page=orders&sub=sedang_diproses" class="<?php echo $sub_page === 'sedang_diproses' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'; ?> whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                Sedang Diproses
            </a>
            <a href="index.php?page=orders&sub=dalam_pengiriman" class="<?php echo $sub_page === 'dalam_pengiriman' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'; ?> whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                Dalam Pengiriman
            </a>
            <a href="index.php?page=orders&sub=lainnya" class="<?php echo $sub_page === 'lainnya' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'; ?> whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                Lainnya
            </a>
        </nav>
    </div>

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
                <?php if (empty($orders)): ?>
                    <tr>
                        <td colspan="6" class="text-center py-10 text-gray-500">Tidak ada pesanan pada kategori ini.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach($orders as $order): ?>
                    <tr>
                        <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm text-indigo-600 font-semibold"><?php echo htmlspecialchars($order['invoice_number']); ?></td>
                        <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm"><?php echo htmlspecialchars($order['username']); ?></td>
                        <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">Rp <?php echo number_format($order['total_amount'], 0, ',', '.'); ?></td>
                        <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                <?php 
                                    $status_color = 'bg-gray-100 text-gray-800'; // Default
                                    if ($order['status'] == 'Belum Dicetak') $status_color = 'bg-yellow-100 text-yellow-800';
                                    if ($order['status'] == 'Sedang Diproses') $status_color = 'bg-blue-100 text-blue-800';
                                    if ($order['status'] == 'Dalam Pengiriman') $status_color = 'bg-purple-100 text-purple-800';
                                    if ($order['status'] == 'Selesai') $status_color = 'bg-green-100 text-green-800';
                                    if ($order['status'] == 'Dibatalkan') $status_color = 'bg-red-100 text-red-800';
                                    echo $status_color;
                                ?>">
                                <?php echo htmlspecialchars($order['status']); ?>
                            </span>
                        </td>
                        <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm"><?php echo date('d M Y', strtotime($order['created_at'])); ?></td>
                        <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                            <a href="index.php?page=order_detail&id=<?php echo $order['id']; ?>" class="text-indigo-600 hover:text-indigo-900 mr-4">Lihat Detail</a>
                            <?php if ($order['status'] === 'Belum Dicetak'): ?>
                                <a href="cetak_resi.php?ids=<?php echo $order['id']; ?>" target="_blank" class="text-green-600 hover:text-green-900">Cetak Resi</a>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>