<?php
// File: app/views/product_detail.php
require_once '../app/models/Product.php';
$product_model = new Product($conn);

$id = $_GET['id'] ?? 0;
$product = $product_model->getById($id);

if (!$product) {
    echo "<p>Produk tidak ditemukan.</p>";
    return; // Hentikan eksekusi jika produk tidak ada
}
?>
<div class="bg-white overflow-hidden shadow rounded-lg">
    <div class="grid grid-cols-1 md:grid-cols-2">
        <div class="p-4">
            <img class="w-full h-auto object-cover rounded-lg" src="assets/images/<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
        </div>
        <div class="p-6">
            <h1 class="text-3xl font-bold text-gray-900"><?php echo htmlspecialchars($product['name']); ?></h1>
            <p class="mt-2 text-2xl text-indigo-600 font-semibold">Rp <?php echo number_format($product['price'], 0, ',', '.'); ?></p>
            <div class="mt-4">
                <h2 class="text-lg font-medium text-gray-800">Deskripsi</h2>
                <p class="mt-2 text-gray-600">
                    <?php echo nl2br(htmlspecialchars($product['description'])); ?>
                </p>
            </div>
            <div class="mt-6">
                 <p class="text-sm text-gray-500"><?php echo $product['stock'] > 0 ? 'Sisa Stok: ' . htmlspecialchars($product['stock']) : 'Stok Habis'; ?></p>
            </div>
            <div class="mt-6">
                <?php if ($product['stock'] > 0): ?>
                <form action="../app/controllers/cart_handler.php" method="POST">
                    <input type="hidden" name="action" value="add">
                    <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                    <div class="flex items-center space-x-4">
                        <label for="quantity" class="font-medium">Jumlah:</label>
                        <input type="number" id="quantity" name="quantity" value="1" min="1" max="<?php echo $product['stock']; ?>" class="w-20 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <!-- PERBAIKAN: Input hidden untuk memberitahu handler halaman mana yang harus dituju setelah proses -->
                        <input type="hidden" name="return_url" value="<?php echo htmlspecialchars($_SERVER['REQUEST_URI']); ?>">
                        <button type="submit" class="w-full bg-indigo-600 border border-transparent rounded-md py-3 px-8 flex items-center justify-center text-base font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Tambah ke Keranjang
                        </button>
                    </div>
                </form>
                <?php else: ?>
                    <button disabled class="w-full bg-gray-400 border border-transparent rounded-md py-3 px-8 flex items-center justify-center text-base font-medium text-white cursor-not-allowed">
                        Stok Habis
                    </button>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>