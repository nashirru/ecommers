Warok Kite - Struktur Proyek Marketplace SederhanaIni adalah struktur dasar untuk aplikasi web marketplace sederhana bernama "Warok Kite" yang dibangun menggunakan PHP Native dan Tailwind CSS.Struktur Folder/
├── app/
│   ├── controllers/
│   │   ├── admin_handler.php (Logika CRUD Produk)
│   │   └── cart_handler.php  (Logika Keranjang)
│   ├── models/
│   │   ├── Order.php
│   │   ├── Product.php
│   │   └── User.php
│   └── views/
│       ├── admin/
│       │   ├── dashboard.php
│       │   └── product_form.php
│       ├── partials/
│       │   ├── header.php
│       │   └── footer.php
│       ├── cart.php
│       ├── checkout.php
│       ├── dashboard.php
│       ├── home.php
│       ├── order_success.php
│       ├── product_detail.php
│       └── products.php
├── auth/
│   ├── login.php
│   ├── register.php
│   └── logout.php
├── config/
│   └── db.php
├── public/
│   ├── assets/
│   │   └── images/ (Tempat upload gambar produk)
│   └── index.php
└── README.md
Cara Menjalankan ProyekDatabase Setup:Buat database warok_kite_db.Jalankan SQL berikut untuk membuat semua tabel yang diperlukan.-- Tabel untuk pengguna
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `is_admin` tinyint(1) NOT NULL DEFAULT 0, -- 0 = User, 1 = Admin
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Jadikan user pertama sebagai admin (jalankan setelah register)
-- UPDATE `users` SET `is_admin` = 1 WHERE `id` = 1;

-- Tabel untuk produk
CREATE TABLE `products` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `stock` int(11) NOT NULL,
  `image` varchar(255) DEFAULT 'default.jpg',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabel untuk pesanan
CREATE TABLE `orders` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `shipping_address` text NOT NULL,
  `status` varchar(50) NOT NULL DEFAULT 'Pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabel untuk item dalam pesanan
CREATE TABLE `order_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) DEFAULT NULL, -- Diubah dari NOT NULL menjadi NULL
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `order_id` (`order_id`),
  KEY `product_id` (`product_id`),
  CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

Sesuaikan koneksi di config/db.php.Web Server:Arahkan web server Anda ke direktori /public. Contoh: http://localhost/warok_kite/public/.Pastikan direktori public/assets/images/ dapat ditulisi oleh web server untuk upload gambar.Mulai Menggunakan:Register akun pertama.Ubah status akun tersebut menjadi admin di database (UPDATE users SET is_admin = 1 WHERE id = 1;).Login sebagai admin untuk mengakses dashboard admin dan mengelola produk.