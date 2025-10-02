<?php
// File: app/views/products.php
require_once '../app/models/Product.php';
$product_model = new Product($conn);
$products = $product_model->getAll();
?>
<header class="bg-white shadow">
    <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
        <h1 class="text-3xl font-bold tracking-tight text-gray-900">Katalog Produk</h1>
    </div>
</header>
<div class="mt-6">
    <div class="grid grid-cols-1 gap-y-10 gap-x-6 sm:grid-cols-2 lg:grid-cols-4 xl:gap-x-8">
        <?php foreach ($products as $product): ?>
        <div class="group relative bg-white p-4 rounded-lg shadow-md flex flex-col">
            <div class="min-h-80 aspect-w-1 aspect-h-1 w-full overflow-hidden rounded-md bg-gray-200 group-hover:opacity-75 lg:aspect-none lg:h-80">
                <img src="assets/images/<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" class="h-full w-full object-cover object-center lg:h-full lg:w-full">
            </div>
            <div class="mt-4 flex justify-between flex-grow">
                <div>
                    <h3 class="text-sm text-gray-700">
                        <a href="index.php?page=product_detail&id=<?php echo $product['id']; ?>">
                            <span aria-hidden="true" class="absolute inset-0"></span>
                            <?php echo htmlspecialchars($product['name']); ?>
                        </a>
                    </h3>
                    <p class="mt-1 text-sm text-gray-500"><?php echo $product['stock'] > 0 ? 'Stok: ' . $product['stock'] : 'Stok Habis'; ?></p>
                </div>
            </div>
            <div class="mt-2 flex flex-col">
                 <p class="text-lg font-medium text-gray-900">Rp <?php echo number_format($product['price'], 0, ',', '.'); ?></p>
            </div>
        </div>
        <?php endforeach; ?>
        <?php if (empty($products)): ?>
            <p class="col-span-4 text-center text-gray-500">Saat ini belum ada produk yang tersedia.</p>
        <?php endif; ?>
    </div>
</div>