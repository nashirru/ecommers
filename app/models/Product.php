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
    public function create($name, $description, $price, $stock, $image) {
        $stmt = $this->conn->prepare("INSERT INTO products (name, description, price, stock, image) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("ssdis", $name, $description, $price, $stock, $image);
        return $stmt->execute();
    }

    /**
     * Mengambil semua produk.
     */
    public function getAll() {
        $result = $this->conn->query("SELECT * FROM products ORDER BY created_at DESC");
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Mengambil produk terbaru dengan limit.
     */
    public function getLatest($limit = 8) {
        $stmt = $this->conn->prepare("SELECT * FROM products ORDER BY created_at DESC LIMIT ?");
        $stmt->bind_param("i", $limit);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Mengambil satu produk berdasarkan ID.
     */
    public function getById($id) {
        $stmt = $this->conn->prepare("SELECT * FROM products WHERE id = ?");
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
     */
    public function update($id, $name, $description, $price, $stock, $image = null) {
        if ($image) {
            $stmt = $this->conn->prepare("UPDATE products SET name = ?, description = ?, price = ?, stock = ?, image = ? WHERE id = ?");
            $stmt->bind_param("ssdisi", $name, $description, $price, $stock, $image, $id);
        } else {
            $stmt = $this->conn->prepare("UPDATE products SET name = ?, description = ?, price = ?, stock = ? WHERE id = ?");
            $stmt->bind_param("ssdisi", $name, $description, $price, $stock, $id);
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