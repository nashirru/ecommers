<?php
// File: app/controllers/admin_handler.php
session_start();
require_once '../../config/db.php';
require_once '../../app/models/Product.php';
require_once '../../app/models/User.php';

// Cek jika user adalah admin
$user_model = new User($conn);
if (!isset($_SESSION['user_id']) || !$user_model->isAdmin($_SESSION['user_id'])) {
    die("Akses ditolak.");
}

$product_model = new Product($conn);
$action = $_POST['action'] ?? '';

// --- ACTION: CREATE PRODUCT ---
if ($action === 'create') {
    $name = trim($_POST['name']);
    $desc = trim($_POST['description']);
    $price = $_POST['price'];
    $stock = $_POST['stock'];
    $image_name = null;

    // Handle image upload
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $target_dir = "../../public/assets/images/";
        $image_name = uniqid() . '_' . basename($_FILES["image"]["name"]);
        $target_file = $target_dir . $image_name;
        move_uploaded_file($_FILES["image"]["tmp_name"], $target_file);
    } else {
        $image_name = 'default.jpg';
    }

    $product_model->create($name, $desc, $price, $stock, $image_name);
    header("Location: ../../public/index.php?page=admin_dashboard&status=created");
    exit();
}

// --- ACTION: UPDATE PRODUCT ---
if ($action === 'update') {
    $id = $_POST['id'];
    $name = trim($_POST['name']);
    $desc = trim($_POST['description']);
    $price = $_POST['price'];
    $stock = $_POST['stock'];
    $image_name = null;

    // Handle image upload if a new one is provided
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
         $target_dir = "../../public/assets/images/";
        $image_name = uniqid() . '_' . basename($_FILES["image"]["name"]);
        $target_file = $target_dir . $image_name;
        move_uploaded_file($_FILES["image"]["tmp_name"], $target_file);
        // Hapus gambar lama jika ada
        $old_product = $product_model->getById($id);
        if ($old_product['image'] && $old_product['image'] != 'default.jpg' && file_exists($target_dir . $old_product['image'])) {
            unlink($target_dir . $old_product['image']);
        }
    }

    $product_model->update($id, $name, $desc, $price, $stock, $image_name);
    header("Location: ../../public/index.php?page=admin_dashboard&status=updated");
    exit();
}

// --- ACTION: DELETE PRODUCT ---
if ($action === 'delete') {
    $id = $_POST['id'];
    $product_model->delete($id);
    header("Location: ../../public/index.php?page=admin_dashboard&status=deleted");
    exit();
}
?>