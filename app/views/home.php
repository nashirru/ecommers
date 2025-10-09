<?php
// File: app/views/home.php
// Pastikan BASE_PATH sudah terdefinisi dari public/index.php
require_once BASE_PATH . '/app/models/Product.php';
require_once BASE_PATH . '/app/models/Banner.php';
require_once BASE_PATH . '/app/models/Category.php';

$product_model = new Product($conn);
$latest_products = $product_model->getLatest(8);

// Mengambil data banner yang aktif dari database
$banner_model = new Banner($conn);
$banners = $banner_model->getAllActive();

$category_model = new Category($conn);
$categories = $category_model->getAll();

// --- PERBAIKAN UTAMA DI SINI ---
// Mengubah array banners ke format JSON yang aman untuk JavaScript
$banners_data = array_map(function($banner) {
    return [
        'id' => $banner['id'],
        'title' => $banner['title'],
        'subtitle' => $banner['subtitle'],
        'link' => $banner['link'],
        // PERBAIKAN: Ganti backslash menjadi forward slash untuk URL yang valid di JS/HTML
        'image_url' => str_replace('\\', '/', 'assets/images/banners/' . $banner['image'])
    ];
}, $banners);
// Meng-encode DENGAN AMAN untuk disisipkan ke dalam atribut HTML
$banners_json_string = htmlspecialchars(json_encode($banners_data, JSON_UNESCAPED_SLASHES), ENT_QUOTES, 'UTF-8');
?>

<!-- Bagian Banner Slider -->
<div class="w-full mb-12">
    <!-- PERBAIKAN: Alpine.js di-refactor agar lebih aman dan tangguh -->
    <div class="relative" 
        x-data="{
            banners: [],
            activeSlide: 1,
            initSlider() {
                try {
                    this.banners = JSON.parse(this.$el.getAttribute('data-banners'));
                    if (this.banners.length > 1) {
                        setInterval(() => { 
                            this.activeSlide = (this.activeSlide % this.banners.length) + 1;
                        }, 5000);
                    }
                } catch (e) {
                    console.error('Error parsing banner data:', e);
                    this.banners = [];
                }
            }
        }"
        data-banners="<?php echo $banners_json_string; ?>"
        x-init="initSlider()">
        
        <!-- Tampilkan slider hanya jika ada banner di database -->
        <template x-if="banners.length > 0">
            <div class="relative h-64 md:h-96 w-full overflow-hidden rounded-lg shadow-lg">
                <!-- Slides -->
                <template x-for="(banner, index) in banners" :key="banner.id">
                    <div x-show="activeSlide === index + 1" 
                        class="absolute inset-0 h-full w-full bg-cover bg-center transition-all duration-700" 
                        :style="'background-image: url(' + banner.image_url + ')'"
                        x-transition:enter="ease-out"
                        x-transition:enter-start="opacity-0"
                        x-transition:enter-end="opacity-100"
                        x-transition:leave="ease-in"
                        x-transition:leave-start="opacity-100"
                        x-transition:leave-end="opacity-0">
                        <div class="absolute inset-0 bg-black/40 flex items-center justify-center p-4">
                            <div class="text-center text-white">
                                <h2 class="text-3xl md:text-5xl font-bold mb-2" x-text="banner.title"></h2>
                                <p class="text-md md:text-xl max-w-lg mx-auto" x-text="banner.subtitle"></p>
                                <a :href="banner.link || '#'" x-show="banner.link" class="mt-6 inline-block bg-indigo-600 text-white font-bold py-3 px-8 rounded-lg hover:bg-indigo-700 transition">
                                    Lihat Selengkapnya
                                </a>
                            </div>
                        </div>
                    </div>
                </template>
                
                <!-- Tombol Navigasi Slider (Hanya tampil jika lebih dari 1 banner) -->
                <template x-if="banners.length > 1">
                    <div class="absolute inset-0 flex items-center justify-between">
                        <button @click="activeSlide = activeSlide === 1 ? banners.length : activeSlide - 1" class="w-12 h-full text-white hover:bg-black/20 transition-colors">
                            &#10094;
                        </button>
                        <button @click="activeSlide = activeSlide === banners.length ? 1 : activeSlide + 1" class="w-12 h-full text-white hover:bg-black/20 transition-colors">
                            &#10095;
                        </button>
                    </div>
                </template>
            </div>
        </template>
        
        <!-- Tampilkan placeholder jika tidak ada banner aktif di database -->
        <template x-if="banners.length === 0">
            <div class="h-64 md:h-96 w-full bg-gray-200 rounded-lg flex items-center justify-center">
                <div class="text-center text-gray-500 p-8">
                    <h2 class="text-3xl font-bold mb-2">Selamat Datang</h2>
                    <p class="text-lg">Promosi menarik akan segera hadir!</p>
                </div>
            </div>
        </template>
    </div>
</div>


<!-- Bagian Kategori -->
<div class="bg-white py-12 rounded-lg shadow-lg">
    <div class="container mx-auto px-4">
        <h2 class="text-3xl font-bold text-center text-gray-800 mb-8">Jelajahi Kategori</h2>
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-8">
            <?php foreach ($categories as $category): ?>
                <a href="index.php?page=products&category=<?php echo $category['id']; ?>" class="text-center group">
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
<div class="py-12 mt-12">
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
                            <form action="../app/controllers/cart_handler.php" method="POST">
                                <input type="hidden" name="action" value="add">
                                <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                                <input type="hidden" name="quantity" value="1">
                                <input type="hidden" name="return_url" value="<?php echo htmlspecialchars($_SERVER['REQUEST_URI']); ?>">
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