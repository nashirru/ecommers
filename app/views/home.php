<?php
// File: app/views/home.php
// Pastikan BASE_PATH sudah terdefinisi dari public/index.php
require_once BASE_PATH . '/app/models/Product.php';
require_once BASE_PATH . '/app/models/Banner.php';
require_once BASE_PATH . '/app/models/Category.php';

$product_model = new Product($conn);
$latest_products = $product_model->getLatest(8);

// Mengambil data banner dan kategori yang aktif dari database
$banner_model = new Banner($conn);
$banners = $banner_model->getAllActive();

$category_model = new Category($conn);
$categories = $category_model->getAll();

// Mengubah array banners ke format JSON agar bisa dibaca oleh Alpine.js
$banners_json = json_encode(array_map(function($banner) {
    return [
        'id' => $banner['id'],
        'title' => $banner['title'],
        'subtitle' => $banner['subtitle'],
        'link' => $banner['link'],
        // Path gambar banner yang benar
        'image_url' => 'assets/images/banners/' . $banner['image']
    ];
}, $banners));
?>

<!-- Hero Section / Slider Banner -->
<div class="relative w-full -mt-6 -mx-8" 
     x-data="{ activeSlide: 1, banners: <?php echo $banners_json; ?>, interval: null }" 
     x-init="if (banners.length > 0) { interval = setInterval(() => { activeSlide = activeSlide % banners.length + 1 }, 5000) }" 
     x-on:before-destroy="clearInterval(interval)">
    
    <!-- Tampilkan slider hanya jika ada banner di database -->
    <template x-if="banners.length > 0">
        <div class="relative h-64 md:h-96 w-full">
            <!-- Slides -->
            <template x-for="(banner, index) in banners" :key="banner.id">
                <div x-show="activeSlide === index + 1" 
                     class="absolute inset-0 h-full w-full bg-cover bg-center transition-opacity duration-1000" 
                     :style="'background-image: url(' + banner.image_url + ')'">
                    <div class="container mx-auto h-full flex items-center bg-black/40">
                        <div class="text-white p-8">
                            <h2 class="text-4xl md:text-6xl font-bold mb-4" x-text="banner.title"></h2>
                            <p class="text-lg md:text-xl max-w-md" x-text="banner.subtitle"></p>
                            <a :href="banner.link || '#'" class="mt-6 inline-block bg-white text-indigo-600 font-bold py-3 px-6 rounded-lg hover:bg-gray-200 transition">
                                Lihat Detail
                            </a>
                        </div>
                    </div>
                </div>
            </template>
            
            <!-- Tombol Navigasi Slider -->
            <button @click="activeSlide = activeSlide === 1 ? banners.length : activeSlide - 1" class="absolute inset-y-0 left-0 flex items-center justify-center w-12 h-full text-white hover:bg-black/20 z-10">
                &#10094;
            </button>
            <button @click="activeSlide = activeSlide === banners.length ? 1 : activeSlide + 1" class="absolute inset-y-0 right-0 flex items-center justify-center w-12 h-full text-white hover:bg-black/20 z-10">
                &#10095;
            </button>
        </div>
    </template>
    
    <!-- Tampilkan placeholder jika tidak ada banner aktif di database -->
    <template x-if="banners.length === 0">
         <div class="h-64 md:h-96 w-full bg-cover bg-center" style="background-image: url(https://placehold.co/1600x600/6366f1/ffffff?text=Selamat+Datang!)">
             <div class="container mx-auto h-full flex items-center">
                <div class="text-white p-8">
                    <h2 class="text-4xl md:text-6xl font-bold mb-4">Selamat Datang di Warok Kite</h2>
                    <p class="text-lg md:text-xl max-w-md">Pusat layangan dan aksesoris terlengkap.</p>
                </div>
            </div>
         </div>
    </template>
</div>


<!-- Bagian Kategori -->
<div class="bg-white py-12">
    <div class="container mx-auto px-4">
        <h2 class="text-3xl font-bold text-center text-gray-800 mb-8">Jelajahi Kategori</h2>
        <div class="grid grid-cols-2 md:grid-cols-5 gap-8">
            <?php foreach ($categories as $category): ?>
                <a href="#" class="text-center group">
                    <div class="w-32 h-32 mx-auto bg-indigo-100 rounded-full flex items-center justify-center shadow-lg transform transition duration-300 group-hover:scale-105 group-hover:shadow-xl">
                        <svg class="w-16 h-16 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                           <path stroke-linecap="round" stroke-linejoin="round" d="<?php echo htmlspecialchars($category['icon']); ?>" />
                        </svg>
                    </div>
                    <h3 class="mt-4 text-lg font-semibold text-gray-700 group-hover:text-indigo-600"><?php echo htmlspecialchars($category['name']); ?></h3>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<!-- Bagian Produk Terbaru -->
<div class="py-12">
    <div class="container mx-auto px-4">
        <h2 class="text-3xl font-bold text-center text-gray-800 mb-8">Produk Terbaru</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
            <?php foreach ($latest_products as $product): ?>
                <div class="bg-white rounded-lg shadow-md overflow-hidden group transform transition duration-300 hover:-translate-y-2 hover:shadow-xl flex flex-col">
                    <a href="index.php?page=product_detail&id=<?php echo $product['id']; ?>" class="block">
                        <img src="assets/images/<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" class="w-full h-56 object-cover">
                    </a>
                    <div class="p-4 flex flex-col flex-grow">
                        <p class="text-sm text-gray-500"><?php echo htmlspecialchars($product['category_name'] ?? 'Tanpa Kategori'); ?></p>
                        <h3 class="text-lg font-semibold text-gray-800 truncate mt-1">
                             <a href="index.php?page=product_detail&id=<?php echo $product['id']; ?>" class="hover:text-indigo-600">
                                <?php echo htmlspecialchars($product['name']); ?>
                            </a>
                        </h3>
                        <p class="text-gray-600 mt-2 font-bold text-xl">Rp <?php echo number_format($product['price'], 0, ',', '.'); ?></p>
                        <div class="mt-4 mt-auto">
                            <form action="app/controllers/cart_handler.php" method="POST">
                                <input type="hidden" name="action" value="add">
                                <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                                <input type="hidden" name="quantity" value="1">
                                <button type="submit" class="w-full text-center block bg-indigo-600 text-white font-bold py-2 px-4 rounded-lg hover:bg-indigo-700 transition">
                                    + Keranjang
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<!-- Pastikan Alpine.js disertakan -->
<script src="//unpkg.com/alpinejs" defer></script>