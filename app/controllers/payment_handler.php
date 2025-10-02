<?php
// File: app/controllers/payment_handler.php
session_start();
require_once '../../config/db.php';
require_once '../../app/models/Payment.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_SESSION['user_id'])) {
    die("Akses ditolak.");
}

$order_id = $_POST['order_id'];

if (isset($_FILES['payment_proof']) && $_FILES['payment_proof']['error'] == 0) {
    $target_dir = "../../public/uploads/";
    // Buat nama file unik untuk mencegah tumpang tindih
    $file_extension = pathinfo($_FILES["payment_proof"]["name"], PATHINFO_EXTENSION);
    $file_name = "proof_" . $order_id . "_" . time() . "." . $file_extension;
    $target_file = $target_dir . $file_name;

    // Pindahkan file yang diunggah
    if (move_uploaded_file($_FILES["payment_proof"]["tmp_name"], $target_file)) {
        $payment_model = new Payment($conn);
        $payment_model->create($order_id, $file_name);
        
        // Redirect kembali ke halaman detail pesanan dengan status sukses
        header("Location: ../../public/index.php?page=order_detail&id=$order_id&status=paid");
        exit();
    }
}
// Redirect jika gagal
header("Location: ../../public/index.php?page=order_detail&id=$order_id&status=failed");
exit();
?>