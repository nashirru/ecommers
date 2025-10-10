<?php
// File: app/models/Setting.php

class Setting {
    private $conn;

    public function __construct($db_connection) {
        $this->conn = $db_connection;
    }

    /**
     * Mengambil semua pengaturan dan mengembalikannya sebagai array asosiatif (key => value).
     * @return array
     */
    public function getAllAsAssoc() {
        $settings = [];
        $result = $this->conn->query("SELECT setting_key, setting_value FROM settings");
        
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $settings[$row['setting_key']] = $row['setting_value'];
            }
        }
        
        return $settings;
    }

    /**
     * Alias untuk getAllAsAssoc (untuk kompatibilitas).
     * @return array
     */
    public function getSettings() {
        return $this->getAllAsAssoc();
    }

    /**
     * Mengambil satu nilai pengaturan berdasarkan key.
     * @param string $key
     * @return string|null
     */
    public function getSetting($key) {
        $stmt = $this->conn->prepare("SELECT setting_value FROM settings WHERE setting_key = ?");
        $stmt->bind_param("s", $key);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        return $row['setting_value'] ?? null;
    }

    /**
     * Memperbarui satu pengaturan berdasarkan key.
     * @param string $key
     * @param string $value
     * @return bool
     */
    public function updateSetting($key, $value) {
        // Cek apakah setting sudah ada
        $check = $this->conn->prepare("SELECT id FROM settings WHERE setting_key = ?");
        $check->bind_param("s", $key);
        $check->execute();
        $result = $check->get_result();
        
        if ($result->num_rows > 0) {
            // Update existing
            $stmt = $this->conn->prepare("UPDATE settings SET setting_value = ?, updated_at = NOW() WHERE setting_key = ?");
            $stmt->bind_param("ss", $value, $key);
        } else {
            // Insert new
            $stmt = $this->conn->prepare("INSERT INTO settings (setting_key, setting_value) VALUES (?, ?)");
            $stmt->bind_param("ss", $key, $value);
        }
        
        return $stmt->execute();
    }

    /**
     * Memperbarui beberapa pengaturan sekaligus.
     * @param array $settings (format: ['key' => 'value', ...])
     * @return bool
     */
    public function updateMultiple($settings) {
        $this->conn->begin_transaction();
        
        try {
            foreach ($settings as $key => $value) {
                $this->updateSetting($key, $value);
            }
            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollback();
            error_log($e->getMessage());
            return false;
        }
    }

    /**
     * Menambahkan pengaturan baru.
     * @param string $key
     * @param string $value
     * @return bool
     */
    public function createSetting($key, $value) {
        $stmt = $this->conn->prepare("INSERT INTO settings (setting_key, setting_value) VALUES (?, ?)");
        $stmt->bind_param("ss", $key, $value);
        return $stmt->execute();
    }

    /**
     * Menghapus pengaturan.
     * @param string $key
     * @return bool
     */
    public function deleteSetting($key) {
        $stmt = $this->conn->prepare("DELETE FROM settings WHERE setting_key = ?");
        $stmt->bind_param("s", $key);
        return $stmt->execute();
    }

    /**
     * Inisialisasi pengaturan default jika belum ada.
     * @return bool
     */
    public function initializeDefaults() {
        $defaults = [
            'store_name' => 'Toko Online Kami',
            'store_address' => 'Jl. Contoh No. 123, Jakarta',
            'store_phone' => '081234567890',
            'store_email' => 'info@tokoonline.com',
            'store_logo' => 'default_logo.png',
            'currency' => 'IDR',
            'timezone' => 'Asia/Jakarta',
            'items_per_page' => '12',
            'enable_registration' => '1',
            'maintenance_mode' => '0'
        ];

        $this->conn->begin_transaction();
        
        try {
            foreach ($defaults as $key => $value) {
                // Cek apakah sudah ada
                $check = $this->conn->prepare("SELECT id FROM settings WHERE setting_key = ?");
                $check->bind_param("s", $key);
                $check->execute();
                $result = $check->get_result();
                
                // Hanya insert jika belum ada
                if ($result->num_rows === 0) {
                    $this->createSetting($key, $value);
                }
            }
            
            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollback();
            error_log($e->getMessage());
            return false;
        }
    }
}