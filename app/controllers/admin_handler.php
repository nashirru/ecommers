<?php
// File: app/controllers/admin_handler.php
session_start();
require_once '../../config/db.php';
require_once '../../app/models/Product.php';
require_once '../../app/models/Category.php';
require_once '../../app/models/Banner.php';
require_once '../../app/models/Order.php';
require_once '../../app/models/User.php';

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
    $image_name = null;

    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $target_dir = "../../public/assets/images/";
        $image_name = uniqid() . '_' . basename($_FILES["image"]["name"]);
        $target_file = $target_dir . $image_name;
        move_uploaded_file($_FILES["image"]["tmp_name"], $target_file);
    } else {
        $image_name = 'default.jpg';
    }

    $product_model->create($name, $desc, $price, $stock, $image_name);
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
    $image_name = null;

    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
         $target_dir = "../../public/assets/images/";
        $image_name = uniqid() . '_' . basename($_FILES["image"]["name"]);
        $target_file = $target_dir . $image_name;
        move_uploaded_file($_FILES["image"]["tmp_name"], $target_file);
    }

    $product_model->update($id, $name, $desc, $price, $stock, $image_name);
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
    $banner_model->update($_POST['id'], $_POST['title'], $_POST['subtitle'], $_POST['link'], $_POST['is_active'], $image_name);
    header("Location: ../../public/admin/index.php?page=banners&status=updated");
    exit();
}

if ($action === 'delete_banner') {
    $banner_model = new Banner($conn);
    $banner_model->delete($_POST['id']);
    header("Location: ../../public/admin/index.php?page=banners&status=deleted");
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