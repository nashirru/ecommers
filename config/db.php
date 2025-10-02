<?php
// File: config/db.php
// Konfigurasi untuk koneksi ke database MySQL

define('DB_HOST', 'localhost');
define('DB_USER', 'root'); // Ganti dengan username database Anda
define('DB_PASS', ''); // Ganti dengan password database Anda
define('DB_NAME', 'toko'); // Ganti dengan nama database Anda

// Membuat koneksi menggunakan mysqli
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Cek koneksi
if ($conn->connect_error) {
    die("Koneksi ke database gagal: " . $conn->connect_error);
}

// Set charset ke utf8mb4 untuk mendukung berbagai karakter
$conn->set_charset("utf8mb4");
?>