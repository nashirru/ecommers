<?php
// File: config/helpers.php

// Fungsi ini akan dipanggil di router untuk memuat semua pengaturan
function load_settings($conn) {
    require_once BASE_PATH . '/app/models/Setting.php';
    $setting_model = new Setting($conn);
    return $setting_model->getAllAsAssoc();
}
?>