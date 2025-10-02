<?php
// File: app/views/admin/general.php
// $settings sudah tersedia dari router admin

$status = $_GET['status'] ?? '';
?>
<h1 class="text-3xl font-bold tracking-tight text-gray-900 mb-6">Pengaturan Umum</h1>

<?php if ($status === 'updated'): ?>
    <div class="p-4 mb-4 text-sm text-green-700 bg-green-100 rounded-lg">Pengaturan berhasil diperbarui.</div>
<?php elseif ($status === 'error'): ?>
     <div class="p-4 mb-4 text-sm text-red-700 bg-red-100 rounded-lg">Terjadi kesalahan saat memperbarui.</div>
<?php endif; ?>

<div class="bg-white p-8 rounded-lg shadow-lg">
    <form action="../../app/controllers/admin_handler.php" method="POST" enctype="multipart/form-data" class="space-y-6">
        <input type="hidden" name="action" value="update_general_settings">

        <div>
            <label for="store_logo" class="block text-sm font-medium text-gray-700">Logo Toko</label>
            <input type="file" name="store_logo" id="store_logo" accept="image/png, image/jpeg, image/gif" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
            <p class="mt-2 text-xs text-gray-500">Unggah logo baru untuk mengganti yang lama. Biarkan kosong jika tidak ingin mengubah.</p>
            <?php if (!empty($settings['store_logo'])): ?>
                <div class="mt-4">
                    <p class="text-sm text-gray-500">Logo saat ini:</p>
                    <img src="../assets/images/<?php echo htmlspecialchars($settings['store_logo']); ?>" alt="Logo Saat Ini" class="mt-2 h-16 w-auto bg-gray-200 p-2 rounded">
                </div>
            <?php endif; ?>
        </div>

        <div>
            <label for="store_email" class="block text-sm font-medium text-gray-700">Email Kontak Toko</label>
            <input type="email" name="store_email" id="store_email" required value="<?php echo htmlspecialchars($settings['store_email'] ?? ''); ?>" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
        </div>

        <div>
            <label for="store_address" class="block text-sm font-medium text-gray-700">Alamat Toko</label>
            <textarea name="store_address" id="store_address" rows="3" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"><?php echo htmlspecialchars($settings['store_address'] ?? ''); ?></textarea>
        </div>
        
        <div class="flex justify-end">
            <button type="submit" class="bg-indigo-600 text-white font-bold py-2 px-4 rounded hover:bg-indigo-700">
                Simpan Perubahan
            </button>
        </div>
    </form>
</div>