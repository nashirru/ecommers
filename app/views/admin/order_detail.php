<?php
// File: app/views/admin/order_detail.php
require_once BASE_PATH . '/app/models/Order.php';
require_once BASE_PATH . '/app/models/Payment.php';

$order_model = new Order($conn);
$payment_model = new Payment($conn);
$order_id = $_GET['id'];
$order = $order_model->getById($order_id);

if (!$order) {
    echo "<p class='text-red-500 text-center'>Pesanan tidak ditemukan.</p>";
    return;
}

$order_items = $order_model->getOrderItems($order_id);
$payment = $payment_model->getByOrderId($order_id);
// Perubahan: Menambahkan status baru dan mengatur ulang urutan
$possible_statuses = ['Menunggu Pembayaran', 'Belum Dicetak', 'Sedang Diproses', 'Dalam Pengiriman', 'Selesai', 'Dibatalkan'];
?>
<header class="bg-white shadow">
    <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
        <h1 class="text-3xl font-bold tracking-tight text-gray-900">Detail Pesanan #<?php echo htmlspecialchars($order['invoice_number']); ?></h1>
    </div>
</header>
<div class="mt-6 grid grid-cols-1 lg:grid-cols-3 gap-8">
    <div class="lg:col-span-2 bg-white shadow rounded-lg p-6">
        <h3 class="text-lg font-medium text-gray-900">Item Pesanan</h3>
         <ul role="list" class="divide-y divide-gray-200 mt-4">
            <?php foreach($order_items as $item): ?>
            <li class="p-4 flex">
                <img src="../assets/images/<?php echo htmlspecialchars($item['product_image'] ?? 'default.jpg'); ?>" class="h-24 w-24 rounded-md object-cover">
                <div class="ml-4 flex-1 flex flex-col">
                    <div>
                        <div class="flex justify-between text-base font-medium text-gray-900">
                            <h3><?php echo htmlspecialchars($item['product_name'] ?? 'Produk Dihapus'); ?></h3>
                            <p class="ml-4">Rp <?php echo number_format($item['price'] * $item['quantity'], 0, ',', '.'); ?></p>
                        </div>
                        <p class="mt-1 text-sm text-gray-500">Harga Satuan: Rp <?php echo number_format($item['price'], 0, ',', '.'); ?></p>
                    </div>
                    <div class="flex-1 flex items-end justify-between text-sm">
                        <p class="text-gray-500">Qty <?php echo $item['quantity']; ?></p>
                    </div>
                </div>
            </li>
            <?php endforeach; ?>
        </ul>
    </div>
    <div>
        <div class="bg-white shadow p-6 rounded-lg">
            <h3 class="text-lg font-medium">Update Status</h3>
            <form action="../../app/controllers/admin_handler.php" method="POST" class="mt-4">
                <input type="hidden" name="action" value="update_order_status">
                <input type="hidden" name="order_id" value="<?php echo $order_id; ?>">
                <label for="status" class="block text-sm font-medium text-gray-700">Status Pesanan</label>
                <select id="status" name="status" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                    <?php foreach ($possible_statuses as $stat): ?>
                        <option value="<?php echo $stat; ?>" <?php echo $order['status'] == $stat ? 'selected' : ''; ?>><?php echo $stat; ?></option>
                    <?php endforeach; ?>
                </select>
                <button type="submit" class="mt-4 w-full bg-indigo-600 text-white font-bold py-2 px-4 rounded hover:bg-indigo-700">Update</button>
            </form>
        </div>
        <?php if($payment): ?>
        <div class="mt-6 bg-white shadow p-6 rounded-lg">
            <h3 class="text-lg font-medium">Verifikasi Pembayaran</h3>
            <p class="text-sm text-gray-600 mt-2">Pemesan telah mengunggah bukti pembayaran.</p>
            <a href="../uploads/<?php echo htmlspecialchars($payment['payment_proof']); ?>" target="_blank" class="mt-2 inline-block bg-green-500 text-white font-bold py-2 px-4 rounded hover:bg-green-600">Lihat Bukti Pembayaran</a>
            <?php if($order['status'] == 'Menunggu Pembayaran'): ?>
                <form action="../../app/controllers/admin_handler.php" method="POST" class="mt-4">
                    <input type="hidden" name="action" value="update_order_status">
                    <input type="hidden" name="order_id" value="<?php echo $order_id; ?>">
                    <!-- Perubahan: Setelah verifikasi, status menjadi 'Belum Dicetak' -->
                    <input type="hidden" name="status" value="Belum Dicetak">
                    <button type="submit" class="w-full bg-blue-600 text-white font-bold py-2 px-4 rounded hover:bg-blue-700">Verifikasi & Siapkan Pesanan</button>
                </form>
            <?php endif; ?>
        </div>
        <?php else: ?>
        <div class="mt-6 bg-yellow-50 border border-yellow-300 p-4 rounded-lg">
            <p class="text-sm text-yellow-800">Pemesan belum mengunggah bukti pembayaran.</p>
        </div>
        <?php endif; ?>
    </div>
</div>