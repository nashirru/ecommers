<?php
// File: app/views/admin/category_form.php
require_once '../../app/models/Category.php';
$category_model = new Category($conn);

$category = null;
$is_edit = false;
if (isset($_GET['id'])) {
    $category = $category_model->getById($_GET['id']);
    if ($category) {
        $is_edit = true;
    }
}
?>
<h1 class="text-3xl font-bold tracking-tight text-gray-900 mb-6"><?php echo $is_edit ? 'Edit Kategori' : 'Tambah Kategori Baru'; ?></h1>
<div class="bg-white p-8 rounded-lg shadow-lg">
    <form action="../../app/controllers/admin_handler.php" method="POST" class="space-y-6">
        <input type="hidden" name="action" value="<?php echo $is_edit ? 'update_category' : 'create_category'; ?>">
        <?php if ($is_edit): ?>
            <input type="hidden" name="id" value="<?php echo $category['id']; ?>">
        <?php endif; ?>

        <div>
            <label for="name" class="block text-sm font-medium text-gray-700">Nama Kategori</label>
            <input type="text" name="name" id="name" required value="<?php echo htmlspecialchars($category['name'] ?? ''); ?>" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
        </div>

        <div>
            <label for="icon" class="block text-sm font-medium text-gray-700">SVG Path Ikon</label>
            <textarea name="icon" id="icon" rows="3" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm font-mono"><?php echo htmlspecialchars($category['icon'] ?? ''); ?></textarea>
            <p class="mt-2 text-xs text-gray-500">Contoh: M13 10V3.212a2.25... (Salin path dari Heroicons atau sejenisnya)</p>
        </div>
        
        <div class="flex justify-end space-x-4">
            <a href="index.php?page=categories" class="bg-gray-200 text-gray-700 font-bold py-2 px-4 rounded hover:bg-gray-300">Batal</a>
            <button type="submit" class="bg-indigo-600 text-white font-bold py-2 px-4 rounded hover:bg-indigo-700">
                <?php echo $is_edit ? 'Simpan Perubahan' : 'Tambah Kategori'; ?>
            </button>
        </div>
    </form>
</div>