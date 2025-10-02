<?php
// File: app/views/admin/dashboard.php
require_once '../app/models/User.php';
$user_model = new User($conn);

// Proteksi halaman, hanya untuk admin
if (!isset($_SESSION['user_id']) || !$user_model->isAdmin($_SESSION['user_id'])) {
    header('Location: index.php?page=home');
    exit();
}
require_once '../app/models/Product.php';
$product_model = new Product($conn);
$products = $product_model->getAll();
$status = $_GET['status'] ?? '';
?>

<header class="bg-white shadow">
    <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8 flex justify-between items-center">
        <h1 class="text-3xl font-bold tracking-tight text-gray-900">Manajemen Produk</h1>
        <a href="index.php?page=admin_product_form" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded">
            + Tambah Produk Baru
        </a>
    </div>
</header>
<div class="mt-6">
    <?php if ($status === 'created'): ?>
        <div class="p-4 mb-4 text-sm text-green-700 bg-green-100 rounded-lg">Produk berhasil ditambahkan.</div>
    <?php elseif ($status === 'updated'): ?>
        <div class="p-4 mb-4 text-sm text-blue-700 bg-blue-100 rounded-lg">Produk berhasil diperbarui.</div>
    <?php elseif ($status === 'deleted'): ?>
        <div class="p-4 mb-4 text-sm text-red-700 bg-red-100 rounded-lg">Produk berhasil dihapus.</div>
    <?php endif; ?>

    <div class="overflow-x-auto bg-white rounded-lg shadow">
        <table class="min-w-full leading-normal">
            <thead>
                <tr>
                    <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Gambar</th>
                    <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Nama Produk</th>
                    <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Harga</th>
                    <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Stok</th>
                    <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($products as $product): ?>
                <tr>
                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                        <img src="assets/images/<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" class="w-16 h-16 object-cover rounded">
                    </td>
                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                        <p class="text-gray-900 whitespace-no-wrap"><?php echo htmlspecialchars($product['name']); ?></p>
                    </td>
                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                        <p class="text-gray-900 whitespace-no-wrap">Rp <?php echo number_format($product['price'], 0, ',', '.'); ?></p>
                    </td>
                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                        <p class="text-gray-900 whitespace-no-wrap"><?php echo htmlspecialchars($product['stock']); ?></p>
                    </td>
                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                        <a href="index.php?page=admin_product_form&id=<?php echo $product['id']; ?>" class="text-indigo-600 hover:text-indigo-900">Edit</a>
                        <form action="../app/controllers/admin_handler.php" method="POST" onsubmit="return confirm('Anda yakin ingin menghapus produk ini?');" class="inline-block ml-4">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="id" value="<?php echo $product['id']; ?>">
                            <button type="submit" class="text-red-600 hover:text-red-900">Hapus</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
                 <?php if (empty($products)): ?>
                    <tr>
                        <td colspan="5" class="text-center py-10 text-gray-500">Belum ada produk.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>