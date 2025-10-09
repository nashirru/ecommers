<?php
// File: app/models/Product.php

class Product {
    private $conn;

    public function __construct($db_connection) {
        $this->conn = $db_connection;
    }

    /**
     * Membuat produk baru.
     */
    public function create($name, $description, $price, $stock, $categoryId, $image) {
        $stmt = $this->conn->prepare("INSERT INTO products (name, description, price, stock, category_id, image) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssdiis", $name, $description, $price, $stock, $categoryId, $image);
        return $stmt->execute();
    }

    /**
     * Mengambil semua produk dengan nama kategorinya.
     */
    public function getAll() {
        $sql = "SELECT p.*, c.name as category_name 
                FROM products p 
                LEFT JOIN categories c ON p.category_id = c.id 
                ORDER BY p.created_at DESC";
        $result = $this->conn->query($sql);
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Mengambil produk terbaru dengan limit.
     */
    public function getLatest($limit = 8) {
        $sql = "SELECT p.*, c.name as category_name 
                FROM products p 
                LEFT JOIN categories c ON p.category_id = c.id 
                ORDER BY p.created_at DESC LIMIT ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $limit);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Mengambil satu produk berdasarkan ID dengan nama kategorinya.
     */
    public function getById($id) {
        $sql = "SELECT p.*, c.name as category_name 
                FROM products p 
                LEFT JOIN categories c ON p.category_id = c.id 
                WHERE p.id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    /**
     * Mengambil beberapa produk berdasarkan array ID.
     */
    public function getMultipleByIds($ids) {
        if (empty($ids)) {
            return [];
        }
        $in_clause = str_repeat('?,', count($ids) - 1) . '?';
        $types = str_repeat('i', count($ids));
        $sql = "SELECT * FROM products WHERE id IN ($in_clause)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param($types, ...$ids);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Memperbarui data produk.
     * PERBAIKAN: Menambahkan parameter $categoryId
     */
    public function update($id, $name, $description, $price, $stock, $categoryId, $image = null) {
        if ($image) {
            // PERBAIKAN: Menambahkan category_id = ? di query UPDATE
            $stmt = $this->conn->prepare("UPDATE products SET name = ?, description = ?, price = ?, stock = ?, category_id = ?, image = ? WHERE id = ?");
            $stmt->bind_param("ssdiisi", $name, $description, $price, $stock, $categoryId, $image, $id);
        } else {
            // PERBAIKAN: Menambahkan category_id = ? di query UPDATE
            $stmt = $this->conn->prepare("UPDATE products SET name = ?, description = ?, price = ?, stock = ?, category_id = ? WHERE id = ?");
            $stmt->bind_param("ssdisi", $name, $description, $price, $stock, $categoryId, $id);
        }
        return $stmt->execute();
    }

    /**
     * Menghapus produk.
     */
    public function delete($id) {
        $stmt = $this->conn->prepare("DELETE FROM products WHERE id = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
}
?>