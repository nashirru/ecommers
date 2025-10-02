<?php
// File: app/views/home.php
require_once '../app/models/Product.php';
$product_model = new Product($conn);
$latest_products = $product_model->getLatest(8);

$categories = [
    ['name' => 'Layangan', 'icon' => 'M13 10V3.212a2.25 2.25 0 00-1.06-1.936l-7-3.5a2.25 2.25 0 00-2.88 0l-7 3.5A2.25 2.25 0 001 3.212V10l8 4 8-4z'],
    ['name' => 'Aksesoris', 'icon' => 'M11.42 15.17L17.25 21A2.652 2.652 0 0021 17.25l-5.83-5.83M11.42 15.17l-3.086-3.086a2.652 2.652 0 00-3.75 0L3 13.5M11.42 15.17L21 21'],
    ['name' => 'Benang', 'icon' => 'M9.53 16.122a3 3 0 00-5.78 1.128 2.25 2.25 0 01-2.4-2.4 3 3 0 001.128-5.78m1.128 5.78a3 3 0 015.78-1.128 2.25 2.25 0 002.4 2.4 3 3 0 01-1.128 5.78m-1.128-5.78l-5.78 1.128'],
    ['name' => 'Gelasan', 'icon' => 'M12 21a9.004 9.004 0 008.716-6.747M12 21a9.004 9.004 0 01-8.716-6.747M12 21c2.485 0 4.5-4.03 4.5-9S14.485 3 12 3m0 18c-2.485 0-4.5-4.03-4.5-9S9.515 3 12 3m0 0a8.997 8.997 0 017.843 4.582M12 3a8.997 8.997 0 00-7.843 4.582'],
];

?>

<!-- Hero Section -->
<div class="relative w-full -mt-6 -mx-8" x-data="{ activeSlide: 1, slides: [1, 2, 3], interval: null }" x-init="interval = setInterval(() => { activeSlide = activeSlide === 3 ? 1 : activeSlide + 1 }, 5000)" x-on:before-destroy="clearInterval(interval)">
    <!-- Slides -->
    <template x-for="slide in slides" :key="slide">
        <div x-show="activeSlide === slide" class="h-64 md:h-96 w-full bg-cover bg-center transition-opacity duration-1000 ease-in-out" 
             :style="'background-image: url(https://placehold.co/1600x600/6366f1/ffffff?text=Promo+Layangan+Keren+' + slide + ')'">
            <div class="container mx-auto h-full flex items-center">
                <div class="text-white p-8">
                    <h2 class="text-4xl md:text-6xl font-bold mb-4">Koleksi Terbaik <?php echo date('Y'); ?></h2>
                    <p class="text-lg md:text-xl max-w-md">Temukan layangan aduan, hias, dan aksesoris terlengkap hanya di Warok Kite.</p>
                    <a href="index.php?page=products" class="mt-6 inline-block bg-white text-indigo-600 font-bold py-3 px-6 rounded-lg hover:bg-gray-200 transition">Belanja Sekarang</a>
                </div>
            </div>
        </div>
    </template>
    
    <!-- Navigation -->
    <button @click="activeSlide = activeSlide === 1 ? 3 : activeSlide - 1" class="absolute inset-y-0 left-0 flex items-center justify-center w-12 h-full text-white hover:bg-black/20">
        &#10094;
    </button>
    <button @click="activeSlide = activeSlide === 3 ? 1 : activeSlide + 1" class="absolute inset-y-0 right-0 flex items-center justify-center w-12 h-full text-white hover:bg-black/20">
        &#10095;
    </button>
</div>

<!-- Category Section -->
<div class="bg-white py-12">
    <div class="container mx-auto px-4">
        <h2 class="text-3xl font-bold text-center text-gray-800 mb-8">Jelajahi Kategori</h2>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-8">
            <?php foreach ($categories as $category): ?>
                <a href="#" class="text-center group">
                    <div class="w-32 h-32 mx-auto bg-indigo-100 rounded-full flex items-center justify-center shadow-lg transform transition duration-300 group-hover:scale-105 group-hover:shadow-xl">
                        <svg class="w-16 h-16 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                           <path stroke-linecap="round" stroke-linejoin="round" d="<?php echo $category['icon']; ?>" />
                        </svg>
                    </div>
                    <h3 class="mt-4 text-lg font-semibold text-gray-700 group-hover:text-indigo-600"><?php echo $category['name']; ?></h3>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<!-- Newest Products Section -->
<div class="py-12">
    <div class="container mx-auto px-4">
        <h2 class="text-3xl font-bold text-center text-gray-800 mb-8">Produk Terbaru</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
            <?php foreach ($latest_products as $product): ?>
                <div class="bg-white rounded-lg shadow-md overflow-hidden group transform transition duration-300 hover:-translate-y-2 hover:shadow-xl">
                    <a href="index.php?page=product_detail&id=<?php echo $product['id']; ?>" class="block">
                        <img src="assets/images/<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" class="w-full h-56 object-cover">
                    </a>
                    <div class="p-4">
                        <h3 class="text-lg font-semibold text-gray-800 truncate">
                             <a href="index.php?page=product_detail&id=<?php echo $product['id']; ?>" class="hover:text-indigo-600">
                                <?php echo htmlspecialchars($product['name']); ?>
                            </a>
                        </h3>
                        <p class="text-gray-600 mt-2 font-bold text-xl">Rp <?php echo number_format($product['price'], 0, ',', '.'); ?></p>
                        <div class="mt-4">
                            <a href="../app/controllers/cart_handler.php?action=add&id=<?php echo $product['id']; ?>" class="w-full text-center block bg-indigo-600 text-white font-bold py-2 px-4 rounded-lg hover:bg-indigo-700 transition">
                                Tambah ke Keranjang
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<!-- Footer -->
<footer class="bg-gray-800 text-white mt-12 -mx-8 -mb-6">
    <div class="mx-auto max-w-7xl py-12 px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div>
                <h3 class="text-lg font-semibold">Tentang Warok Kite</h3>
                <p class="mt-4 text-gray-400">Pusat penjualan layangan dan aksesoris terbaik di Ponorogo. Kualitas terjamin, harga bersahabat.</p>
            </div>
            <div>
                <h3 class="text-lg font-semibold">Bantuan</h3>
                <ul class="mt-4 space-y-2">
                    <li><a href="#" class="text-gray-400 hover:text-white">Cara Pemesanan</a></li>
                    <li><a href="#" class="text-gray-400 hover:text-white">Status Pesanan</a></li>
                    <li><a href="#" class="text-gray-400 hover:text-white">Kebijakan Pengembalian</a></li>
                </ul>
            </div>
            <div>
                <h3 class="text-lg font-semibold">Hubungi Kami</h3>
                <ul class="mt-4 space-y-2">
                    <li class="text-gray-400">Jl. Batoro Katong, Ponorogo</li>
                    <li class="text-gray-400">Email: info@warokkite.com</li>
                </ul>
            </div>
        </div>
        <div class="mt-8 border-t border-gray-700 pt-8 text-center text-gray-400">
            &copy; <?php echo date('Y'); ?> Warok Kite. All rights reserved.
        </div>
    </div>
</footer>

<!-- Alpine.js untuk slider -->
<script src="//unpkg.com/alpinejs" defer></script>