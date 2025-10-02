<?php
// File: app/views/order_detail.php
if (!isset($_SESSION['user_id']) || !isset($_GET['id'])) {
    header('Location: index.php');
    exit();
}
require_once '../app/models/Order.php';
require_once '../app/models/Payment.php';

$order_model = new Order($conn);
$payment_model = new Payment($conn);
$order_id = $_GET['id'];
// Ambil order, pastikan milik user yang login
$order = $order_model->getById($order_id, $_SESSION['user_id']);

if (!$order) {
    echo "<p class='text-red-500 text-center'>Pesanan tidak ditemukan atau Anda tidak memiliki akses.</p>";
    return;
}

$order_items = $order_model->getOrderItems($order_id);
$payment = $payment_model->getByOrderId($order_id);
$status = $_GET['status'] ?? '';
?>
<header class="bg-white shadow">
    <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
        <h1 class="text-3xl font-bold tracking-tight text-gray-900">Detail Pesanan #<?php echo htmlspecialchars($order['invoice_number']); ?></h1>
    </div>
</header>
<div class="mt-6">
    <?php if ($status === 'paid'): ?>
        <div class="p-4 mb-4 text-sm text-green-700 bg-green-100 rounded-lg">Bukti pembayaran berhasil diunggah. Admin akan segera memverifikasi.</div>
    <?php endif; ?>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <div class="lg:col-span-2">
            <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                <div class="px-4 py-5 sm:px-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">Item yang Dipesan</h3>
                </div>
                <div class="border-t border-gray-200">
                    <ul role="list" class="divide-y divide-gray-200">
                        <?php foreach($order_items as $item): ?>
                        <li class="p-4 flex">
                            <img src="assets/images/<?php echo htmlspecialchars($item['product_image'] ?? 'default.jpg'); ?>" class="h-24 w-24 rounded-md object-cover">
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
            </div>
        </div>
        <div>
            <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                 <div class="px-4 py-5 sm:px-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">Ringkasan</h3>
                </div>
                <div class="border-t border-gray-200 px-4 py-5 sm:p-0">
                    <dl class="sm:divide-y sm:divide-gray-200">
                        <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">Total Harga</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2 font-bold">Rp <?php echo number_format($order['total_amount'], 0, ',', '.'); ?></dd>
                        </div>
                        <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">Status</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2"><?php echo htmlspecialchars($order['status']); ?></dd>
                        </div>
                        <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">Alamat Kirim</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2"><?php echo nl2br(htmlspecialchars($order['shipping_address'])); ?></dd>
                        </div>
                         <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500">Metode Bayar</dt>
                            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2"><?php echo htmlspecialchars($order['payment_method']); ?></dd>
                        </div>
                    </dl>
                </div>
            </div>
             <?php if ($order['status'] == 'Menunggu Pembayaran' && !$payment): ?>
                <div class="mt-6 bg-white shadow p-6 rounded-lg">
                    <h3 class="text-lg font-medium">Konfirmasi Pembayaran</h3>
                    <p class="text-sm text-gray-600 mt-2">Silakan transfer sejumlah <strong>Rp <?php echo number_format($order['total_amount'], 0, ',', '.'); ?></strong> ke rekening berikut dan unggah bukti transfer.</p>
                    <p class="text-sm text-gray-800 mt-2 font-mono">BCA: 123-456-7890 a.n. Warok Kite</p>
                    <form action="../app/controllers/payment_handler.php" method="POST" enctype="multipart/form-data" class="mt-4">
                        <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                        <div>
                            <label for="payment_proof" class="block text-sm font-medium text-gray-700">Unggah Bukti</label>
                            <input type="file" name="payment_proof" id="payment_proof" required class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                        </div>
                        <button type="submit" class="mt-4 w-full bg-indigo-600 text-white font-bold py-2 px-4 rounded hover:bg-indigo-700">Kirim Konfirmasi</button>
                    </form>
                </div>
            <?php elseif($payment): ?>
                 <div class="mt-6 bg-white shadow p-6 rounded-lg">
                    <h3 class="text-lg font-medium">Bukti Pembayaran</h3>
                    <p class="text-sm text-gray-600 mt-2">Anda telah mengunggah bukti pembayaran. Sedang menunggu verifikasi admin.</p>
                    <a href="../public/uploads/<?php echo htmlspecialchars($payment['payment_proof']); ?>" target="_blank" class="mt-2 text-indigo-600 hover:underline">Lihat Bukti</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>