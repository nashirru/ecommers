<?php
// File: app/views/checkout.php

// Logika pengecekan login sudah TIDAK diperlukan lagi di sini.
// File ini sekarang murni untuk tampilan.

require_once '../app/models/Product.php';

$cart = $_SESSION['cart'] ?? [];
$cart_products = [];
$total_price = 0;

if (!empty($cart)) {
    $product_model = new Product($conn);
    $product_ids = array_keys($cart);
    $products_in_cart = $product_model->getMultipleByIds($product_ids);

    foreach ($products_in_cart as $product) {
        $quantity = $cart[$product['id']]['quantity'];
        $subtotal = $product['price'] * $quantity;
        $total_price += $subtotal;
        $cart_products[] = [
            'id' => $product['id'],
            'name' => $product['name'],
            'price' => $product['price'],
            'image' => $product['image'],
            'quantity' => $quantity,
            'subtotal' => $subtotal
        ];
    }
}
?>

<div class="bg-gray-50">
    <div class="mx-auto max-w-2xl px-4 pt-16 pb-24 sm:px-6 lg:max-w-7xl lg:px-8">
        <h2 class="text-3xl font-bold tracking-tight text-gray-900 sm:text-4xl">Checkout</h2>

        <?php if (empty($cart_products)): ?>
            <div class="mt-12">
                <p class="text-center text-gray-500">Keranjang belanja Anda kosong.</p>
                <div class="mt-6 text-center">
                    <a href="index.php?page=products" class="text-base font-medium text-indigo-600 hover:text-indigo-500">
                        Lanjutkan Belanja<span aria-hidden="true"> &rarr;</span>
                    </a>
                </div>
            </div>
        <?php else: ?>
            <form action="index.php" method="POST" class="lg:grid lg:grid-cols-2 lg:gap-x-12 xl:gap-x-16">
                <input type="hidden" name="action" value="checkout">
                <!-- Kolom Alamat & Pembayaran -->
                <div class="mt-10 lg:mt-0">
                    <h3 class="text-lg font-medium text-gray-900">Alamat Pengiriman</h3>
                    <div class="mt-4">
                        <label for="address" class="block text-sm font-medium text-gray-700">Alamat Lengkap</label>
                        <div class="mt-1">
                            <textarea id="address" name="address" rows="4" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required></textarea>
                        </div>
                    </div>
                    
                    <h3 class="text-lg font-medium text-gray-900 mt-10">Metode Pembayaran</h3>
                    <div class="mt-4 space-y-4">
                        <div class="flex items-center">
                            <input id="bank_transfer" name="payment_method" type="radio" value="Bank Transfer" checked class="h-4 w-4 border-gray-300 text-indigo-600 focus:ring-indigo-500">
                            <label for="bank_transfer" class="ml-3 block text-sm font-medium text-gray-700">Bank Transfer</label>
                        </div>
                        <div class="flex items-center">
                            <input id="e_wallet" name="payment_method" type="radio" value="E-Wallet" class="h-4 w-4 border-gray-300 text-indigo-600 focus:ring-indigo-500">
                            <label for="e_wallet" class="ml-3 block text-sm font-medium text-gray-700">E-Wallet (GoPay, OVO, dll.)</label>
                        </div>
                    </div>
                </div>

                <!-- Ringkasan Pesanan -->
                <div class="mt-10 lg:mt-0">
                    <h3 class="text-lg font-medium text-gray-900">Ringkasan Pesanan</h3>
                    <div class="mt-4 rounded-lg border border-gray-200 bg-white shadow-sm">
                        <ul role="list" class="divide-y divide-gray-200">
                            <?php foreach ($cart_products as $item): ?>
                            <li class="flex py-6 px-4 sm:px-6">
                                <div class="flex-shrink-0">
                                    <img src="assets/images/<?= htmlspecialchars($item['image']) ?>" alt="<?= htmlspecialchars($item['name']) ?>" class="w-20 rounded-md">
                                </div>
                                <div class="ml-6 flex flex-1 flex-col">
                                    <div class="flex">
                                        <div class="min-w-0 flex-1">
                                            <h4 class="text-sm"><a href="index.php?page=product_detail&id=<?= $item['id'] ?>" class="font-medium text-gray-700 hover:text-gray-800"><?= htmlspecialchars($item['name']) ?></a></h4>
                                            <p class="mt-1 text-sm text-gray-500">Jumlah: <?= $item['quantity'] ?></p>
                                        </div>
                                    </div>
                                    <div class="flex flex-1 items-end justify-between pt-2">
                                        <p class="mt-1 text-sm font-medium text-gray-900">Rp <?= number_format($item['subtotal'], 0, ',', '.') ?></p>
                                    </div>
                                </div>
                            </li>
                            <?php endforeach; ?>
                        </ul>
                        <dl class="space-y-6 border-t border-gray-200 py-6 px-4 sm:px-6">
                            <div class="flex items-center justify-between">
                                <dt class="text-sm">Subtotal</dt>
                                <dd class="text-sm font-medium text-gray-900">Rp <?= number_format($total_price, 0, ',', '.') ?></dd>
                            </div>
                            <div class="flex items-center justify-between">
                                <dt class="text-sm">Pengiriman</dt>
                                <dd class="text-sm font-medium text-gray-900">Rp 0</dd>
                            </div>
                            <div class="flex items-center justify-between border-t border-gray-200 pt-6">
                                <dt class="text-base font-medium">Total</dt>
                                <dd class="text-base font-medium text-gray-900">Rp <?= number_format($total_price, 0, ',', '.') ?></dd>
                            </div>
                        </dl>
                        <div class="border-t border-gray-200 py-6 px-4 sm:px-6">
                            <button type="submit" class="w-full rounded-md border border-transparent bg-indigo-600 py-3 px-4 text-base font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 focus:ring-offset-gray-50">Konfirmasi dan Pesan</button>
                        </div>
                    </div>
                </div>
            </form>
        <?php endif; ?>
    </div>
</div>