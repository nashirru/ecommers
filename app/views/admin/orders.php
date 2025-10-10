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
} elseif ($sub_page === 'menunggu_pembayaran') {
    $orders = $order_model->getAllOrders('Menunggu Pembayaran');
    $page_title = 'Menunggu Pembayaran';
} else { // 'lainnya'
    $exclude = ['Belum Dicetak', 'Sedang Diproses', 'Dalam Pengiriman', 'Menunggu Pembayaran'];
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
        <div class="p-4 mb-4 text-sm text-green-700 bg-green-100 rounded-lg" role="alert">
            <strong>Berhasil!</strong> Status pesanan telah diperbarui.
        </div>
    <?php endif; ?>

    <!-- Tombol Cetak Semua hanya muncul di tab "Belum Dicetak" -->
    <?php if ($sub_page === 'belum_dicetak' && !empty($orders)):
        $all_order_ids = array_column($orders, 'id');
    ?>
    <div class="mb-4 flex items-center justify-between">
        <div>
            <a href="cetak_resi.php?ids=<?php echo implode(',', $all_order_ids); ?>" 
               target="_blank" 
               class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                <svg class="w-5 h-5 mr-2 -ml-1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M5 4v3H4a2 2 0 00-2 2v6a2 2 0 002 2h12a2 2 0 002-2V9a2 2 0 00-2-2h-1V4a2 2 0 00-2-2H7a2 2 0 00-2 2zm8 0H7v3h6V4zm0 8H7v4h6v-4z" clip-rule="evenodd" />
                </svg>
                Cetak Semua Resi (<?php echo count($all_order_ids); ?>)
            </a>
        </div>
        <div class="text-sm text-gray-600">
            Total: <strong><?php echo count($orders); ?></strong> pesanan
        </div>
    </div>
    <?php endif; ?>

    <!-- Navigasi Tabs -->
    <div class="mb-4 border-b border-gray-200">
        <nav class="-mb-px flex space-x-8" aria-label="Tabs">
            <a href="index.php?page=orders&sub=menunggu_pembayaran" 
               class="<?php echo $sub_page === 'menunggu_pembayaran' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'; ?> whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                <span class="inline-flex items-center">
                    Menunggu Pembayaran
                    <?php 
                    $count = $order_model->countByStatus('Menunggu Pembayaran');
                    if ($count > 0): 
                    ?>
                        <span class="ml-2 bg-red-100 text-red-600 py-0.5 px-2 rounded-full text-xs"><?php echo $count; ?></span>
                    <?php endif; ?>
                </span>
            </a>
            
            <a href="index.php?page=orders&sub=belum_dicetak" 
               class="<?php echo $sub_page === 'belum_dicetak' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'; ?> whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                <span class="inline-flex items-center">
                    Belum Dicetak
                    <?php 
                    $count = $order_model->countByStatus('Belum Dicetak');
                    if ($count > 0): 
                    ?>
                        <span class="ml-2 bg-yellow-100 text-yellow-600 py-0.5 px-2 rounded-full text-xs"><?php echo $count; ?></span>
                    <?php endif; ?>
                </span>
            </a>
            
            <a href="index.php?page=orders&sub=sedang_diproses" 
               class="<?php echo $sub_page === 'sedang_diproses' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'; ?> whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                <span class="inline-flex items-center">
                    Sedang Diproses
                    <?php 
                    $count = $order_model->countByStatus('Sedang Diproses');
                    if ($count > 0): 
                    ?>
                        <span class="ml-2 bg-blue-100 text-blue-600 py-0.5 px-2 rounded-full text-xs"><?php echo $count; ?></span>
                    <?php endif; ?>
                </span>
            </a>
            
            <a href="index.php?page=orders&sub=dalam_pengiriman" 
               class="<?php echo $sub_page === 'dalam_pengiriman' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'; ?> whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                <span class="inline-flex items-center">
                    Dalam Pengiriman
                    <?php 
                    $count = $order_model->countByStatus('Dalam Pengiriman');
                    if ($count > 0): 
                    ?>
                        <span class="ml-2 bg-purple-100 text-purple-600 py-0.5 px-2 rounded-full text-xs"><?php echo $count; ?></span>
                    <?php endif; ?>
                </span>
            </a>
            
            <a href="index.php?page=orders&sub=lainnya" 
               class="<?php echo $sub_page === 'lainnya' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'; ?> whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                Lainnya
            </a>
        </nav>
    </div>

    <!-- Tabel Pesanan -->
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
                        <td colspan="6" class="text-center py-10 text-gray-500">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                            </svg>
                            <p class="mt-2">Tidak ada pesanan pada kategori ini.</p>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach($orders as $order): ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                            <span class="text-indigo-600 font-semibold"><?php echo htmlspecialchars($order['invoice_number']); ?></span>
                        </td>
                        <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 w-10 h-10">
                                    <div class="w-10 h-10 rounded-full bg-indigo-100 flex items-center justify-center">
                                        <span class="text-indigo-600 font-semibold text-sm">
                                            <?php echo strtoupper(substr($order['username'], 0, 1)); ?>
                                        </span>
                                    </div>
                                </div>
                                <div class="ml-3">
                                    <p class="text-gray-900 whitespace-no-wrap font-medium">
                                        <?php echo htmlspecialchars($order['username']); ?>
                                    </p>
                                    <p class="text-gray-600 text-xs">
                                        <?php echo htmlspecialchars($order['email'] ?? ''); ?>
                                    </p>
                                </div>
                            </div>
                        </td>
                        <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                            <span class="font-semibold text-gray-900">
                                Rp <?php echo number_format($order['total_amount'], 0, ',', '.'); ?>
                            </span>
                        </td>
                        <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                            <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full 
                                <?php 
                                    $status_color = 'bg-gray-100 text-gray-800';
                                    switch($order['status']) {
                                        case 'Menunggu Pembayaran':
                                            $status_color = 'bg-red-100 text-red-800';
                                            break;
                                        case 'Belum Dicetak':
                                            $status_color = 'bg-yellow-100 text-yellow-800';
                                            break;
                                        case 'Sedang Diproses':
                                            $status_color = 'bg-blue-100 text-blue-800';
                                            break;
                                        case 'Dalam Pengiriman':
                                            $status_color = 'bg-purple-100 text-purple-800';
                                            break;
                                        case 'Selesai':
                                            $status_color = 'bg-green-100 text-green-800';
                                            break;
                                        case 'Dibatalkan':
                                            $status_color = 'bg-red-100 text-red-800';
                                            break;
                                    }
                                    echo $status_color;
                                ?>">
                                <?php echo htmlspecialchars($order['status']); ?>
                            </span>
                        </td>
                        <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                            <p class="text-gray-900 whitespace-no-wrap">
                                <?php echo date('d M Y', strtotime($order['created_at'])); ?>
                            </p>
                            <p class="text-gray-600 text-xs">
                                <?php echo date('H:i', strtotime($order['created_at'])); ?> WIB
                            </p>
                        </td>
                        <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                            <div class="flex items-center space-x-3">
                                <a href="index.php?page=order_detail&id=<?php echo $order['id']; ?>" 
                                   class="text-indigo-600 hover:text-indigo-900 font-medium"
                                   title="Lihat Detail">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                </a>
                                
                                <?php if ($order['status'] === 'Belum Dicetak'): ?>
                                    <a href="cetak_resi.php?ids=<?php echo $order['id']; ?>" 
                                       target="_blank" 
                                       class="text-green-600 hover:text-green-900 font-medium"
                                       title="Cetak Resi">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                                        </svg>
                                    </a>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Informasi tambahan -->
    <?php if (!empty($orders)): ?>
    <div class="mt-4 text-sm text-gray-600">
        Menampilkan <?php echo count($orders); ?> pesanan
    </div>
    <?php endif; ?>
</div>