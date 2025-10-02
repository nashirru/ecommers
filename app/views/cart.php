<?php
// File: app/views/cart.php
require_once '../app/models/Product.php';
$product_model = new Product($conn);

$cart_items_data = [];
$total_price = 0;
$cart_session = $_SESSION['cart'] ?? [];

if (!empty($cart_session)) {
    $product_ids = array_keys($cart_session);
    $products_in_cart = $product_model->getMultipleByIds($product_ids);

    foreach ($products_in_cart as $product) {
        $quantity = $cart_session[$product['id']]['quantity'];
        $subtotal = $product['price'] * $quantity;
        $total_price += $subtotal;
        $cart_items_data[] = [
            'id' => $product['id'],
            'name' => $product['name'],
            'price' => $product['price'],
            'image' => $product['image'],
            'quantity' => $quantity,
            'subtotal' => $subtotal,
            'stock' => $product['stock'],
        ];
    }
}
$status = $_GET['status'] ?? '';
?>
<header class="bg-white shadow">
    <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
        <h1 class="text-3xl font-bold tracking-tight text-gray-900">Keranjang Belanja</h1>
    </div>
</header>
<div class="mt-6">
    <?php if ($status === 'added'): ?>
        <div class="p-4 mb-4 text-sm text-green-700 bg-green-100 rounded-lg">Produk berhasil ditambahkan ke keranjang.</div>
    <?php elseif ($status === 'updated'): ?>
        <div class="p-4 mb-4 text-sm text-blue-700 bg-blue-100 rounded-lg">Keranjang berhasil diperbarui.</div>
    <?php elseif ($status === 'removed'): ?>
        <div class="p-4 mb-4 text-sm text-red-700 bg-red-100 rounded-lg">Produk berhasil dihapus dari keranjang.</div>
    <?php endif; ?>
    
    <?php if (empty($cart_items_data)): ?>
        <div class="bg-white p-8 rounded-lg shadow-lg text-center">
            <h2 class="text-2xl font-semibold text-gray-700">Keranjang Anda kosong.</h2>
            <a href="index.php?page=products" class="mt-4 inline-block bg-indigo-600 text-white font-bold py-2 px-4 rounded hover:bg-indigo-700">Mulai Belanja</a>
        </div>
    <?php else: ?>
        <div class="flex flex-col lg:flex-row gap-8">
            <!-- Items -->
            <div class="w-full lg:w-2/3">
                <div class="bg-white rounded-lg shadow-lg">
                <?php foreach ($cart_items_data as $item): ?>
                    <div class="flex items-center p-4 border-b">
                        <img src="assets/images/<?php echo htmlspecialchars($item['image']); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>" class="w-24 h-24 object-cover rounded-md">
                        <div class="ml-4 flex-grow">
                            <h3 class="font-semibold text-gray-800"><?php echo htmlspecialchars($item['name']); ?></h3>
                            <p class="text-gray-600">Rp <?php echo number_format($item['price'], 0, ',', '.'); ?></p>
                            <!-- Update Form -->
                            <form action="../app/controllers/cart_handler.php" method="POST" class="flex items-center mt-2">
                                <input type="hidden" name="action" value="update">
                                <input type="hidden" name="product_id" value="<?php echo $item['id']; ?>">
                                <input type="number" name="quantity" value="<?php echo $item['quantity']; ?>" min="1" max="<?php echo $item['stock']; ?>" class="w-16 rounded border-gray-300">
                                <button type="submit" class="ml-2 text-sm text-indigo-600 hover:text-indigo-800">Update</button>
                            </form>
                        </div>
                        <div class="text-right">
                             <p class="font-semibold">Rp <?php echo number_format($item['subtotal'], 0, ',', '.'); ?></p>
                             <!-- Remove Form -->
                             <form action="../app/controllers/cart_handler.php" method="POST" class="mt-2">
                                <input type="hidden" name="action" value="remove">
                                <input type="hidden" name="product_id" value="<?php echo $item['id']; ?>">
                                <button type="submit" class="text-sm text-red-500 hover:text-red-700">Hapus</button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
                </div>
            </div>
            <!-- Summary -->
            <div class="w-full lg:w-1/3">
                <div class="bg-white p-6 rounded-lg shadow-lg">
                    <h2 class="text-xl font-semibold border-b pb-4">Ringkasan Belanja</h2>
                    <div class="flex justify-between mt-4">
                        <span class="text-gray-600">Subtotal</span>
                        <span class="font-semibold">Rp <?php echo number_format($total_price, 0, ',', '.'); ?></span>
                    </div>
                    <div class="flex justify-between mt-2">
                        <span class="text-gray-600">Ongkos Kirim</span>
                        <span class="font-semibold">Akan dihitung</span>
                    </div>
                    <div class="border-t mt-4 pt-4">
                         <div class="flex justify-between font-bold text-lg">
                            <span>Total</span>
                            <span>Rp <?php echo number_format($total_price, 0, ',', '.'); ?></span>
                        </div>
                    </div>
                    <a href="index.php?page=checkout" class="mt-6 w-full text-center bg-indigo-600 text-white font-bold py-3 px-4 rounded hover:bg-indigo-700 block">
                        Lanjut ke Checkout
                    </a>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>