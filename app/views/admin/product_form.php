<?php
// File: app/views/admin/product_form.php
require_once '../app/models/User.php';
$user_model = new User($conn);

// Proteksi halaman
if (!isset($_SESSION['user_id']) || !$user_model->isAdmin($_SESSION['user_id'])) {
    header('Location: index.php?page=home');
    exit();
}

require_once '../app/models/Product.php';
$product_model = new Product($conn);

$product = null;
$is_edit = false;
if (isset($_GET['id'])) {
    $product = $product_model->getById($_GET['id']);
    if ($product) {
        $is_edit = true;
    }
}
?>
<header class="bg-white shadow">
    <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
        <h1 class="text-3xl font-bold tracking-tight text-gray-900"><?php echo $is_edit ? 'Edit Produk' : 'Tambah Produk Baru'; ?></h1>
    </div>
</header>
<div class="mt-6 bg-white p-8 rounded-lg shadow-lg">
    <form action="../app/controllers/admin_handler.php" method="POST" enctype="multipart/form-data" class="space-y-6">
        <input type="hidden" name="action" value="<?php echo $is_edit ? 'update' : 'create'; ?>">
        <?php if ($is_edit): ?>
            <input type="hidden" name="id" value="<?php echo $product['id']; ?>">
        <?php endif; ?>

        <div>
            <label for="name" class="block text-sm font-medium text-gray-700">Nama Produk</label>
            <input type="text" name="name" id="name" required value="<?php echo htmlspecialchars($product['name'] ?? ''); ?>" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
        </div>

        <div>
            <label for="description" class="block text-sm font-medium text-gray-700">Deskripsi</label>
            <textarea name="description" id="description" rows="4" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"><?php echo htmlspecialchars($product['description'] ?? ''); ?></textarea>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label for="price" class="block text-sm font-medium text-gray-700">Harga (Rp)</label>
                <input type="number" name="price" id="price" step="0.01" required value="<?php echo htmlspecialchars($product['price'] ?? ''); ?>" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
            </div>
            <div>
                <label for="stock" class="block text-sm font-medium text-gray-700">Stok</label>
                <input type="number" name="stock" id="stock" required value="<?php echo htmlspecialchars($product['stock'] ?? ''); ?>" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
            </div>
        </div>
        
        <div>
            <label for="image" class="block text-sm font-medium text-gray-700">Gambar Produk</label>
            <input type="file" name="image" id="image" accept="image/*" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
            <?php if ($is_edit && $product['image']): ?>
                <div class="mt-4">
                    <p class="text-sm text-gray-500">Gambar saat ini:</p>
                    <img src="assets/images/<?php echo htmlspecialchars($product['image']); ?>" alt="Current Image" class="mt-2 h-32 w-32 object-cover rounded-md">
                </div>
            <?php endif; ?>
        </div>
        
        <div class="flex justify-end space-x-4">
            <a href="index.php?page=admin_dashboard" class="bg-gray-200 text-gray-700 font-bold py-2 px-4 rounded hover:bg-gray-300">Batal</a>
            <button type="submit" class="bg-indigo-600 text-white font-bold py-2 px-4 rounded hover:bg-indigo-700">
                <?php echo $is_edit ? 'Simpan Perubahan' : 'Tambah Produk'; ?>
            </button>
        </div>
    </form>
</div>