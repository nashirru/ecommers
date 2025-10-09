<?php
// File: app/controllers/admin_handler.php
session_start();
require_once '../../config/db.php';
require_once '../../app/models/Product.php';
require_once '../../app/models/Category.php';
require_once '../../app/models/Banner.php';
require_once '../../app/models/Order.php';
require_once '../../app/models/User.php';
// Tambahkan model Setting
require_once '../../app/models/Setting.php';

// Cek jika user adalah admin
$user_model = new User($conn);
if (!isset($_SESSION['user_id']) || !$user_model->isAdmin($_SESSION['user_id'])) {
    die("Akses ditolak.");
}

$action = $_POST['action'] ?? '';

// --- CRUD PRODUK ---
if ($action === 'create_product') {
    $product_model = new Product($conn);
    $name = trim($_POST['name']);
    $desc = trim($_POST['description']);
    $price = $_POST['price'];
    $stock = $_POST['stock'];
    // PERBAIKAN: Ambil category_id dari form
    $categoryId = $_POST['category_id'];
    $image_name = null;

    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $target_dir = "../../public/assets/images/";
        $image_name = uniqid() . '_' . basename($_FILES["image"]["name"]);
        $target_file = $target_dir . $image_name;
        move_uploaded_file($_FILES["image"]["tmp_name"], $target_file);
    } else {
        $image_name = 'default.jpg';
    }

    // PERBAIKAN: Kirim categoryId ke model
    $product_model->create($name, $desc, $price, $stock, $categoryId, $image_name);
    header("Location: ../../public/admin/index.php?page=products&status=created");
    exit();
}

if ($action === 'update_product') {
    $product_model = new Product($conn);
    $id = $_POST['id'];
    $name = trim($_POST['name']);
    $desc = trim($_POST['description']);
    $price = $_POST['price'];
    $stock = $_POST['stock'];
    // PERBAIKAN: Ambil category_id dari form saat update
    $categoryId = $_POST['category_id'];
    $image_name = null;

    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
         $target_dir = "../../public/assets/images/";
        $image_name = uniqid() . '_' . basename($_FILES["image"]["name"]);
        $target_file = $target_dir . $image_name;
        move_uploaded_file($_FILES["image"]["tmp_name"], $target_file);
    }

    // PERBAIKAN: Kirim categoryId ke model saat update
    $product_model->update($id, $name, $desc, $price, $stock, $categoryId, $image_name);
    header("Location: ../../public/admin/index.php?page=products&status=updated");
    exit();
}

if ($action === 'delete_product') {
    $product_model = new Product($conn);
    $id = $_POST['id'];
    $product_model->delete($id);
    header("Location: ../../public/admin/index.php?page=products&status=deleted");
    exit();
}

// --- CRUD KATEGORI ---
if ($action === 'create_category') {
    $category_model = new Category($conn);
    $category_model->create($_POST['name'], $_POST['icon']);
    header("Location: ../../public/admin/index.php?page=categories&status=created");
    exit();
}

if ($action === 'update_category') {
    $category_model = new Category($conn);
    $category_model->update($_POST['id'], $_POST['name'], $_POST['icon']);
    header("Location: ../../public/admin/index.php?page=categories&status=updated");
    exit();
}

if ($action === 'delete_category') {
    $category_model = new Category($conn);
    $category_model->delete($_POST['id']);
    header("Location: ../../public/admin/index.php?page=categories&status=deleted");
    exit();
}

// --- CRUD BANNER ---
if ($action === 'create_banner') {
    $banner_model = new Banner($conn);
    $image_name = null;
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $target_dir = "../../public/assets/images/banners/";
        if (!is_dir($target_dir)) mkdir($target_dir, 0777, true);
        $image_name = uniqid() . '_' . basename($_FILES["image"]["name"]);
        move_uploaded_file($_FILES["image"]["tmp_name"], $target_dir . $image_name);
    }
    $banner_model->create($_POST['title'], $_POST['subtitle'], $_POST['link'], $image_name);
    header("Location: ../../public/admin/index.php?page=banners&status=created");
    exit();
}

if ($action === 'update_banner') {
    $banner_model = new Banner($conn);
    $image_name = null;
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $target_dir = "../../public/assets/images/banners/";
        $image_name = uniqid() . '_' . basename($_FILES["image"]["name"]);
        move_uploaded_file($_FILES["image"]["tmp_name"], $target_dir . $image_name);
    }
    $is_active = isset($_POST['is_active']) ? (int)$_POST['is_active'] : 0;
    $banner_model->update($_POST['id'], $_POST['title'], $_POST['subtitle'], $_POST['link'], $is_active, $image_name);
    header("Location: ../../public/admin/index.php?page=banners&status=updated");
    exit();
}

if ($action === 'delete_banner') {
    $banner_model = new Banner($conn);
    $banner_model->delete($_POST['id']);
    header("Location: ../../public/admin/index.php?page=banners&status=deleted");
    exit();
}

// --- PENGATURAN UMUM ---
if ($action === 'update_general_settings') {
    $setting_model = new Setting($conn);

    // Update email dan alamat
    $setting_model->updateSetting('store_email', $_POST['store_email']);
    $setting_model->updateSetting('store_address', $_POST['store_address']);

    // Handle upload logo jika ada
    if (isset($_FILES['store_logo']) && $_FILES['store_logo']['error'] == 0) {
        $target_dir = "../../public/assets/images/";
        $image_name = "store_logo_" . uniqid() . '.' . pathinfo($_FILES["store_logo"]["name"], PATHINFO_EXTENSION);
        $target_file = $target_dir . $image_name;
        
        if (move_uploaded_file($_FILES["store_logo"]["tmp_name"], $target_file)) {
            // Hapus logo lama jika ada dan bukan default
            $current_settings = $setting_model->getAllAsAssoc();
            $old_logo = $current_settings['store_logo'] ?? '';
            if ($old_logo && file_exists($target_dir . $old_logo) && strpos($old_logo, 'default') === false) {
                unlink($target_dir . $old_logo);
            }
            // Update nama logo baru di database
            $setting_model->updateSetting('store_logo', $image_name);
        }
    }

    header("Location: ../../public/admin/index.php?page=general&status=updated");
    exit();
}


// --- UPDATE STATUS PESANAN ---
if ($action === 'update_order_status') {
    $order_model = new Order($conn);
    $order_model->updateStatus($_POST['order_id'], $_POST['status']);
    header("Location: ../../public/admin/index.php?page=orders&status=success");
    exit();
}
?>