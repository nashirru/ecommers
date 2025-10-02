<?php
// File: app/views/admin/banner_form.php
require_once '../../app/models/Banner.php';
$banner_model = new Banner($conn);

$banner = null;
$is_edit = false;
if (isset($_GET['id'])) {
    $banner = $banner_model->getById($_GET['id']);
    if ($banner) {
        $is_edit = true;
    }
}
?>
<h1 class="text-3xl font-bold tracking-tight text-gray-900 mb-6"><?php echo $is_edit ? 'Edit Banner' : 'Tambah Banner Baru'; ?></h1>
<div class="bg-white p-8 rounded-lg shadow-lg">
    <form action="../../app/controllers/admin_handler.php" method="POST" enctype="multipart/form-data" class="space-y-6">
        <input type="hidden" name="action" value="<?php echo $is_edit ? 'update_banner' : 'create_banner'; ?>">
        <?php if ($is_edit): ?>
            <input type="hidden" name="id" value="<?php echo $banner['id']; ?>">
        <?php endif; ?>

        <div>
            <label for="title" class="block text-sm font-medium text-gray-700">Judul</label>
            <input type="text" name="title" id="title" required value="<?php echo htmlspecialchars($banner['title'] ?? ''); ?>" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
        </div>
        <div>
            <label for="subtitle" class="block text-sm font-medium text-gray-700">Subjudul</label>
            <input type="text" name="subtitle" id="subtitle" value="<?php echo htmlspecialchars($banner['subtitle'] ?? ''); ?>" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
        </div>
        <div>
            <label for="link" class="block text-sm font-medium text-gray-700">Link URL (opsional)</label>
            <input type="url" name="link" id="link" value="<?php echo htmlspecialchars($banner['link'] ?? ''); ?>" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" placeholder="https://contoh.com/produk/123">
        </div>
        <div>
            <label for="image" class="block text-sm font-medium text-gray-700">Gambar Banner</label>
            <input type="file" name="image" id="image" accept="image/*" <?php echo !$is_edit ? 'required' : ''; ?> class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
            <?php if ($is_edit && $banner['image']): ?>
                <div class="mt-2"><img src="../assets/images/banners/<?php echo htmlspecialchars($banner['image']); ?>" class="h-32 rounded"></div>
            <?php endif; ?>
        </div>
        
        <?php if ($is_edit): ?>
        <div>
            <label class="block text-sm font-medium text-gray-700">Status</label>
            <div class="mt-2 space-y-2">
                <div class="flex items-center"><input type="radio" name="is_active" value="1" <?php echo ($banner['is_active'] ?? 0) == 1 ? 'checked' : ''; ?> class="h-4 w-4"><label class="ml-2">Aktif</label></div>
                <div class="flex items-center"><input type="radio" name="is_active" value="0" <?php echo ($banner['is_active'] ?? 0) == 0 ? 'checked' : ''; ?> class="h-4 w-4"><label class="ml-2">Tidak Aktif</label></div>
            </div>
        </div>
        <?php endif; ?>

        <div class="flex justify-end space-x-4">
            <a href="index.php?page=banners" class="bg-gray-200 text-gray-700 font-bold py-2 px-4 rounded hover:bg-gray-300">Batal</a>
            <button type="submit" class="bg-indigo-600 text-white font-bold py-2 px-4 rounded hover:bg-indigo-700">
                <?php echo $is_edit ? 'Simpan Perubahan' : 'Tambah Banner'; ?>
            </button>
        </div>
    </form>
</div>