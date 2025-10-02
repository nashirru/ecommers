<?php
// File: app/models/Banner.php

class Banner {
    private $conn;

    public function __construct($db_connection) {
        $this->conn = $db_connection;
    }

    public function getAllActive() {
        $result = $this->conn->query("SELECT * FROM banners WHERE is_active = 1 ORDER BY created_at DESC");
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getAll() {
        $result = $this->conn->query("SELECT * FROM banners ORDER BY created_at DESC");
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    
    public function getById($id) {
        $stmt = $this->conn->prepare("SELECT * FROM banners WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function create($title, $subtitle, $link, $image) {
        $stmt = $this->conn->prepare("INSERT INTO banners (title, subtitle, link, image) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $title, $subtitle, $link, $image);
        return $stmt->execute();
    }

    public function update($id, $title, $subtitle, $link, $is_active, $image = null) {
        if ($image) {
            $stmt = $this->conn->prepare("UPDATE banners SET title = ?, subtitle = ?, link = ?, is_active = ?, image = ? WHERE id = ?");
            $stmt->bind_param("sssisi", $title, $subtitle, $link, $is_active, $image, $id);
        } else {
            $stmt = $this->conn->prepare("UPDATE banners SET title = ?, subtitle = ?, link = ?, is_active = ? WHERE id = ?");
            $stmt->bind_param("sssii", $title, $subtitle, $link, $is_active, $id);
        }
        return $stmt->execute();
    }

    public function delete($id) {
        // Hapus juga file gambar terkait
        $banner = $this->getById($id);
        if ($banner && !empty($banner['image'])) {
            $image_path = '../public/assets/images/banners/' . $banner['image'];
            if (file_exists($image_path)) {
                unlink($image_path);
            }
        }
        $stmt = $this->conn->prepare("DELETE FROM banners WHERE id = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
}
?>