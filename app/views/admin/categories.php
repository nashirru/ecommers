<?php
// File: app/views/admin/categories.php
require_once '../../app/models/Category.php';
$category_model = new Category($conn);
$categories = $category_model->getAll();
$status = $_GET['status'] ?? '';
?>
<div class="flex justify-between items-center mb-6">
    <h1 class="text-3xl font-bold tracking-tight text-gray-900">Manajemen Kategori</h1>
    <a href="index.php?page=category_form" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded">
        + Tambah Kategori Baru
    </a>
</div>

<?php if ($status === 'created'): ?>
    <div class="p-4 mb-4 text-sm text-green-700 bg-green-100 rounded-lg">Kategori berhasil ditambahkan.</div>
<?php elseif ($status === 'updated'): ?>
    <div class="p-4 mb-4 text-sm text-blue-700 bg-blue-100 rounded-lg">Kategori berhasil diperbarui.</div>
<?php elseif ($status === 'deleted'): ?>
    <div class="p-4 mb-4 text-sm text-red-700 bg-red-100 rounded-lg">Kategori berhasil dihapus.</div>
<?php endif; ?>

<div class="overflow-x-auto bg-white rounded-lg shadow">
    <table class="min-w-full leading-normal">
        <thead>
            <tr>
                <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Ikon</th>
                <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Nama Kategori</th>
                <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($categories as $category): ?>
            <tr>
                <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                    <svg class="w-8 h-8 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                       <path stroke-linecap="round" stroke-linejoin="round" d="<?php echo htmlspecialchars($category['icon']); ?>" />
                    </svg>
                </td>
                <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                    <p class="text-gray-900 whitespace-no-wrap"><?php echo htmlspecialchars($category['name']); ?></p>
                </td>
                <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                    <a href="index.php?page=category_form&id=<?php echo $category['id']; ?>" class="text-indigo-600 hover:text-indigo-900">Edit</a>
                    <form action="../../app/controllers/admin_handler.php" method="POST" onsubmit="return confirm('Anda yakin ingin menghapus kategori ini?');" class="inline-block ml-4">
                        <input type="hidden" name="action" value="delete_category">
                        <input type="hidden" name="id" value="<?php echo $category['id']; ?>">
                        <button type="submit" class="text-red-600 hover:text-red-900">Hapus</button>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>