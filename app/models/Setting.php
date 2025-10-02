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
        while ($row = $result->fetch_assoc()) {
            $settings[$row['setting_key']] = $row['setting_value'];
        }
        return $settings;
    }

    /**
     * Memperbarui satu pengaturan berdasarkan key.
     * @param string $key
     * @param string $value
     * @return bool
     */
    public function updateSetting($key, $value) {
        $stmt = $this->conn->prepare("UPDATE settings SET setting_value = ? WHERE setting_key = ?");
        $stmt->bind_param("ss", $value, $key);
        return $stmt->execute();
    }
}
?>