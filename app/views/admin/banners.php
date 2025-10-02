<?php
// File: app/views/admin/banners.php
require_once '../../app/models/Banner.php';
$banner_model = new Banner($conn);
$banners = $banner_model->getAll();
$status = $_GET['status'] ?? '';
?>
<div class="flex justify-between items-center mb-6">
    <h1 class="text-3xl font-bold tracking-tight text-gray-900">Manajemen Banner</h1>
    <a href="index.php?page=banner_form" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded">
        + Tambah Banner Baru
    </a>
</div>

<?php if ($status === 'created'): ?>
    <div class="p-4 mb-4 text-sm text-green-700 bg-green-100 rounded-lg">Banner berhasil ditambahkan.</div>
<?php elseif ($status === 'updated'): ?>
    <div class="p-4 mb-4 text-sm text-blue-700 bg-blue-100 rounded-lg">Banner berhasil diperbarui.</div>
<?php elseif ($status === 'deleted'): ?>
    <div class="p-4 mb-4 text-sm text-red-700 bg-red-100 rounded-lg">Banner berhasil dihapus.</div>
<?php endif; ?>

<div class="overflow-x-auto bg-white rounded-lg shadow">
    <table class="min-w-full leading-normal">
        <thead>
            <tr>
                <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Gambar</th>
                <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Judul</th>
                <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
                <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($banners as $banner): ?>
            <tr>
                <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                    <img src="../assets/images/banners/<?php echo htmlspecialchars($banner['image']); ?>" alt="<?php echo htmlspecialchars($banner['title']); ?>" class="w-32 object-cover rounded">
                </td>
                <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                    <p class="text-gray-900 whitespace-no-wrap"><?php echo htmlspecialchars($banner['title']); ?></p>
                </td>
                <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?php echo $banner['is_active'] ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'; ?>">
                        <?php echo $banner['is_active'] ? 'Aktif' : 'Tidak Aktif'; ?>
                    </span>
                </td>
                <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                    <a href="index.php?page=banner_form&id=<?php echo $banner['id']; ?>" class="text-indigo-600 hover:text-indigo-900">Edit</a>
                    <form action="../../app/controllers/admin_handler.php" method="POST" onsubmit="return confirm('Anda yakin ingin menghapus banner ini?');" class="inline-block ml-4">
                        <input type="hidden" name="action" value="delete_banner">
                        <input type="hidden" name="id" value="<?php echo $banner['id']; ?>">
                        <button type="submit" class="text-red-600 hover:text-red-900">Hapus</button>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>