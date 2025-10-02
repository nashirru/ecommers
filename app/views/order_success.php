<?php
// File: app/views/order_success.php
if (!isset($_GET['order_id'])) {
    header('Location: index.php?page=home');
    exit();
}
?>
<div class="bg-white p-8 rounded-lg shadow-lg text-center">
    <svg class="mx-auto h-16 w-16 text-green-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
    </svg>
    <h1 class="mt-4 text-3xl font-bold text-gray-800">Terima Kasih!</h1>
    <p class="mt-2 text-lg text-gray-600">Pesanan Anda dengan nomor #<?php echo htmlspecialchars($_GET['order_id']); ?> telah berhasil dibuat.</p>
    <p class="mt-1 text-gray-600">Kami akan segera memproses pesanan Anda.</p>
    <div class="mt-8">
        <a href="index.php?page=products" class="bg-indigo-600 text-white font-bold py-3 px-6 rounded-lg hover:bg-indigo-700 transition duration-300">
            Kembali Belanja
        </a>
    </div>
</div>