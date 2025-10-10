<?php
// File: app/views/admin/order_detail.php
require_once BASE_PATH . '/app/models/Order.php';
require_once BASE_PATH . '/app/models/Payment.php';

$order_model = new Order($conn);
$payment_model = new Payment($conn);

$order_id = $_GET['id'] ?? 0;
$order = $order_model->getById($order_id);

if (!$order) {
    echo "<div class='max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10'>";
    echo "<div class='bg-red-50 border border-red-200 rounded-lg p-4'>";
    echo "<p class='text-red-700'>Pesanan tidak ditemukan.</p>";
    echo "<a href='index.php?page=orders' class='text-red-600 hover:text-red-800 font-medium mt-2 inline-block'>← Kembali ke Daftar Pesanan</a>";
    echo "</div></div>";
    return;
}

$order_items = $order_model->getOrderItems($order_id);
$payment = $payment_model->getByOrderId($order_id);

// Status yang tersedia
$possible_statuses = [
    'Menunggu Pembayaran', 
    'Belum Dicetak', 
    'Sedang Diproses', 
    'Dalam Pengiriman', 
    'Selesai', 
    'Dibatalkan'
];

// Hitung subtotal
$subtotal = 0;
foreach ($order_items as $item) {
    $subtotal += $item['price'] * $item['quantity'];
}
?>

<header class="bg-white shadow">
    <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold tracking-tight text-gray-900">
                    Detail Pesanan #<?php echo htmlspecialchars($order['invoice_number']); ?>
                </h1>
                <p class="mt-1 text-sm text-gray-500">
                    Dibuat pada <?php echo date('d M Y H:i', strtotime($order['created_at'])); ?> WIB
                </p>
            </div>
            <div>
                <a href="index.php?page=orders" class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                    ← Kembali
                </a>
            </div>
        </div>
    </div>
</header>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Kolom Kiri: Item Pesanan & Info Pengiriman -->
        <div class="lg:col-span-2 space-y-6">
            
            <!-- Item Pesanan -->
            <div class="bg-white shadow rounded-lg overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <h3 class="text-lg font-medium text-gray-900">Item Pesanan</h3>
                </div>
                <ul role="list" class="divide-y divide-gray-200">
                    <?php foreach($order_items as $item): ?>
                    <li class="p-6 flex">
                        <div class="flex-shrink-0">
                            <img src="../assets/images/<?php echo htmlspecialchars($item['image'] ?? 'default.jpg'); ?>" 
                                 class="h-24 w-24 rounded-md object-cover border border-gray-200">
                        </div>
                        <div class="ml-6 flex-1 flex flex-col">
                            <div>
                                <div class="flex justify-between text-base font-medium text-gray-900">
                                    <h3><?php echo htmlspecialchars($item['name'] ?? 'Produk Dihapus'); ?></h3>
                                    <p class="ml-4">Rp <?php echo number_format($item['price'] * $item['quantity'], 0, ',', '.'); ?></p>
                                </div>
                                <p class="mt-1 text-sm text-gray-500">
                                    Harga Satuan: Rp <?php echo number_format($item['price'], 0, ',', '.'); ?>
                                </p>
                            </div>
                            <div class="flex-1 flex items-end justify-between text-sm">
                                <p class="text-gray-500">Qty: <span class="font-medium"><?php echo $item['quantity']; ?></span></p>
                            </div>
                        </div>
                    </li>
                    <?php endforeach; ?>
                </ul>
            </div>

            <!-- Informasi Pengiriman -->
            <div class="bg-white shadow rounded-lg overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <h3 class="text-lg font-medium text-gray-900">Informasi Pengiriman</h3>
                </div>
                <div class="px-6 py-4">
                    <dl class="grid grid-cols-1 gap-x-4 gap-y-4 sm:grid-cols-2">
                        <div class="sm:col-span-1">
                            <dt class="text-sm font-medium text-gray-500">Nama Penerima</dt>
                            <dd class="mt-1 text-sm text-gray-900"><?php echo htmlspecialchars($order['username']); ?></dd>
                        </div>
                        <div class="sm:col-span-1">
                            <dt class="text-sm font-medium text-gray-500">Email</dt>
                            <dd class="mt-1 text-sm text-gray-900"><?php echo htmlspecialchars($order['email'] ?? '-'); ?></dd>
                        </div>
                        <div class="sm:col-span-2">
                            <dt class="text-sm font-medium text-gray-500">Alamat Pengiriman</dt>
                            <dd class="mt-1 text-sm text-gray-900"><?php echo nl2br(htmlspecialchars($order['shipping_address'])); ?></dd>
                        </div>
                        <div class="sm:col-span-1">
                            <dt class="text-sm font-medium text-gray-500">Metode Pembayaran</dt>
                            <dd class="mt-1 text-sm text-gray-900"><?php echo htmlspecialchars($order['payment_method']); ?></dd>
                        </div>
                    </dl>
                </div>
            </div>

        </div>

        <!-- Kolom Kanan: Update Status & Pembayaran -->
        <div class="space-y-6">
            
            <!-- Ringkasan Pembayaran -->
            <div class="bg-white shadow rounded-lg overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <h3 class="text-lg font-medium text-gray-900">Ringkasan Pembayaran</h3>
                </div>
                <div class="px-6 py-4 space-y-3">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Subtotal Produk</span>
                        <span class="font-medium text-gray-900">Rp <?php echo number_format($subtotal, 0, ',', '.'); ?></span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Biaya Pengiriman</span>
                        <span class="font-medium text-gray-900">Rp <?php echo number_format($order['shipping_cost'] ?? 0, 0, ',', '.'); ?></span>
                    </div>
                    <div class="border-t border-gray-200 pt-3 flex justify-between">
                        <span class="text-base font-medium text-gray-900">Total Pembayaran</span>
                        <span class="text-lg font-bold text-indigo-600">Rp <?php echo number_format($order['total_amount'], 0, ',', '.'); ?></span>
                    </div>
                </div>
            </div>

            <!-- Update Status -->
            <div class="bg-white shadow rounded-lg overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <h3 class="text-lg font-medium text-gray-900">Update Status Pesanan</h3>
                </div>
                <div class="px-6 py-4">
                    <form action="../../app/controllers/admin_handler.php" method="POST">
                        <input type="hidden" name="action" value="update_order_status">
                        <input type="hidden" name="order_id" value="<?php echo $order_id; ?>">
                        
                        <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                            Status Saat Ini: 
                            <span class="px-2 py-1 text-xs font-semibold rounded-full 
                                <?php 
                                    switch($order['status']) {
                                        case 'Menunggu Pembayaran': echo 'bg-red-100 text-red-800'; break;
                                        case 'Belum Dicetak': echo 'bg-yellow-100 text-yellow-800'; break;
                                        case 'Sedang Diproses': echo 'bg-blue-100 text-blue-800'; break;
                                        case 'Dalam Pengiriman': echo 'bg-purple-100 text-purple-800'; break;
                                        case 'Selesai': echo 'bg-green-100 text-green-800'; break;
                                        case 'Dibatalkan': echo 'bg-red-100 text-red-800'; break;
                                        default: echo 'bg-gray-100 text-gray-800';
                                    }
                                ?>">
                                <?php echo htmlspecialchars($order['status']); ?>
                            </span>
                        </label>
                        
                        <select id="status" name="status" 
                                class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                            <?php foreach ($possible_statuses as $stat): ?>
                                <option value="<?php echo $stat; ?>" <?php echo $order['status'] == $stat ? 'selected' : ''; ?>>
                                    <?php echo $stat; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        
                        <button type="submit" 
                                class="mt-4 w-full inline-flex justify-center items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                            </svg>
                            Update Status
                        </button>
                    </form>

                    <!-- Tombol Cetak Resi -->
                    <?php if ($order['status'] === 'Belum Dicetak'): ?>
                    <a href="cetak_resi.php?ids=<?php echo $order_id; ?>" 
                       target="_blank"
                       class="mt-3 w-full inline-flex justify-center items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                        </svg>
                        Cetak Resi Sekarang
                    </a>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Verifikasi Pembayaran -->
            <?php if($payment): ?>
            <div class="bg-white shadow rounded-lg overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 bg-green-50">
                    <h3 class="text-lg font-medium text-green-900">Bukti Pembayaran</h3>
                </div>
                <div class="px-6 py-4">
                    <p class="text-sm text-gray-600 mb-3">Pemesan telah mengunggah bukti pembayaran.</p>
                    
                    <a href="../uploads/<?php echo htmlspecialchars($payment['payment_proof']); ?>" 
                       target="_blank" 
                       class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-green-600 hover:bg-green-700">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                        Lihat Bukti Pembayaran
                    </a>
                    
                    <?php if($order['status'] == 'Menunggu Pembayaran'): ?>
                    <form action="../../app/controllers/admin_handler.php" method="POST" class="mt-4">
                        <input type="hidden" name="action" value="update_order_status">
                        <input type="hidden" name="order_id" value="<?php echo $order_id; ?>">
                        <input type="hidden" name="status" value="Belum Dicetak">
                        <button type="submit" 
                                class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            Verifikasi & Siapkan Pesanan
                        </button>
                    </form>
                    <?php endif; ?>
                </div>
            </div>
            <?php else: ?>
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-yellow-800">Menunggu Pembayaran</h3>
                        <p class="mt-1 text-sm text-yellow-700">Pemesan belum mengunggah bukti pembayaran.</p>
                    </div>
                </div>
            </div>
            <?php endif; ?>

        </div>
    </div>
</div>