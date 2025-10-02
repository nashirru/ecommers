<?php
// File: app/views/admin/product_form.php
// Menggunakan BASE_PATH untuk path yang andal
require_once BASE_PATH . '/app/models/Product.php';
require_once BASE_PATH . '/app/models/Category.php';

$product_model = new Product($conn);
$category_model = new Category($conn);

$product = null;
$is_edit = false;
if (isset($_GET['id'])) {
    $product = $product_model->getById($_GET['id']);
    if ($product) {
        $is_edit = true;
    }
}
// Mengambil semua data kategori untuk ditampilkan di dropdown
$categories = $category_model->getAll();
?>
<h1 class="text-3xl font-bold tracking-tight text-gray-900 mb-6"><?php echo $is_edit ? 'Edit Produk' : 'Tambah Produk Baru'; ?></h1>

<div class="mt-6 bg-white p-8 rounded-lg shadow-lg">
    <form action="../../app/controllers/admin_handler.php" method="POST" enctype="multipart/form-data" class="space-y-6">
        <input type="hidden" name="action" value="<?php echo $is_edit ? 'update_product' : 'create_product'; ?>">
        <?php if ($is_edit): ?>
            <input type="hidden" name="id" value="<?php echo $product['id']; ?>">
        <?php endif; ?>

        <div>
            <label for="name" class="block text-sm font-medium text-gray-700">Nama Produk</label>
            <input type="text" name="name" id="name" required value="<?php echo htmlspecialchars($product['name'] ?? ''); ?>" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
        </div>

        <!-- Dropdown untuk memilih kategori -->
        <div>
            <label for="category_id" class="block text-sm font-medium text-gray-700">Kategori</label>
            <select name="category_id" id="category_id" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                <option value="">-- Pilih Kategori --</option>
                <?php foreach ($categories as $category): ?>
                    <option value="<?php echo $category['id']; ?>" <?php echo (isset($product['category_id']) && $product['category_id'] == $category['id']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($category['name']); ?>
                    </option>
                <?php endforeach; ?>
                 <?php if (empty($categories)): ?>
                    <option disabled>Belum ada kategori. Silakan tambah dulu.</option>
                <?php endif; ?>
            </select>
        </div>

        <div>
            <label for="description" class="block text-sm font-medium text-gray-700">Deskripsi</label>
            <textarea name="description" id="description" rows="4" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"><?php echo htmlspecialchars($product['description'] ?? ''); ?></textarea>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label for="price" class="block text-sm font-medium text-gray-700">Harga (Rp)</label>
                <input type="number" name="price" id="price" step="1" required value="<?php echo htmlspecialchars($product['price'] ?? ''); ?>" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
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
                    <img src="../assets/images/<?php echo htmlspecialchars($product['image']); ?>" alt="Current Image" class="mt-2 h-32 w-32 object-cover rounded-md">
                </div>
            <?php endif; ?>
        </div>
        
        <div class="flex justify-end space-x-4">
            <a href="index.php?page=products" class="bg-gray-200 text-gray-700 font-bold py-2 px-4 rounded hover:bg-gray-300">Batal</a>
            <button type="submit" class="bg-indigo-600 text-white font-bold py-2 px-4 rounded hover:bg-indigo-700">
                <?php echo $is_edit ? 'Simpan Perubahan' : 'Tambah Produk'; ?>
            </button>
        </div>
    </form>
</div>