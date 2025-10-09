<?php
// File: public/admin/cetak_resi.php
session_start();

define('BASE_PATH', dirname(__DIR__, 2));

require_once BASE_PATH . '/config/db.php';
require_once BASE_PATH . '/config/helpers.php';
require_once BASE_PATH . '/app/models/User.php';
require_once BASE_PATH . '/app/models/Order.php';
// Pastikan Anda sudah meletakkan file fpdf.php di folder lib
require_once BASE_PATH . '/lib/fpdf.php';

// Keamanan: Cek jika user adalah admin
$user_model = new User($conn);
if (!isset($_SESSION['user_id']) || !$user_model->isAdmin($_SESSION['user_id'])) {
    die("Akses ditolak. Anda harus menjadi admin untuk mengakses halaman ini.");
}

// Ambil ID pesanan dari URL
$order_ids_str = $_GET['ids'] ?? '';
if (empty($order_ids_str)) {
    die("Tidak ada ID pesanan yang dipilih.");
}
$order_ids = explode(',', $order_ids_str);

$order_model = new Order($conn);
$settings = load_settings($conn);

// Ambil data untuk semua pesanan yang akan dicetak
$orders = $order_model->getMultipleOrdersByIds($order_ids);

if (empty($orders)) {
    die("Pesanan tidak ditemukan.");
}

// Mulai membuat PDF
class PDF extends FPDF
{
    // Header halaman
    function Header()
    {
        // Arial bold 15
        $this->SetFont('Arial','B',15);
        // Pindah ke kanan
        $this->Cell(80);
        // Judul
        $this->Cell(30,10,'RESI PENGIRIMAN',0,0,'C');
        // Line break
        $this->Ln(20);
    }

    // Footer halaman
    function Footer()
    {
        // Posisi 1.5 cm dari bawah
        $this->SetY(-15);
        // Arial italic 8
        $this->SetFont('Arial','I',8);
        // Teks
        $this->Cell(0,10,'Terima kasih telah berbelanja di ' . ($GLOBALS['settings']['store_name'] ?? 'Warok Kite'),0,0,'C');
        $this->Ln();
        $this->Cell(0,10,'Halaman '.$this->PageNo().'/{nb}',0,0,'C');
    }

    function DrawResi($order, $items, $settings)
    {
        $this->AddPage('P', 'A5');
        $this->AliasNbPages();
        $this->SetFont('Arial','',10);

        // Informasi Toko (Pengirim)
        $this->SetFont('Arial','B',11);
        $this->Cell(40, 6, 'DARI:');
        $this->Ln();
        $this->SetFont('Arial','',10);
        $this->Cell(40, 6, $settings['store_name'] ?? 'Warok Kite');
        $this->Ln();
        $this->MultiCell(60, 5, $settings['store_address'] ?? 'Ponorogo, Jawa Timur');
        $this->Ln(5);

        // Informasi Pelanggan (Penerima)
        $this->SetFont('Arial','B',11);
        $this->Cell(40, 6, 'KEPADA:');
        $this->Ln();
        $this->SetFont('Arial','',10);
        $this->Cell(40, 6, $order['username']);
        $this->Ln();
        $this->MultiCell(60, 5, $order['shipping_address']);
        $this->Ln(5);

        // Detail Pesanan
        $this->SetFont('Arial','B',10);
        $this->Cell(40, 6, 'Invoice #: ' . $order['invoice_number'], 0, 0);
        $this->Cell(0, 6, 'Tanggal: ' . date('d M Y', strtotime($order['created_at'])), 0, 1, 'R');
        $this->Ln(5);

        // Tabel Item Pesanan
        $this->SetFont('Arial','B',10);
        $this->Cell(10, 7, 'No', 1);
        $this->Cell(80, 7, 'Nama Produk', 1);
        $this->Cell(30, 7, 'Jumlah', 1);
        $this->Ln();

        $this->SetFont('Arial','',10);
        $no = 1;
        foreach ($items as $item) {
            $this->Cell(10, 6, $no++, 1);
            // Gunakan MultiCell jika nama produk terlalu panjang
            $x = $this->GetX();
            $y = $this->GetY();
            $this->MultiCell(80, 6, $item['product_name'], 1);
            $this->SetXY($x + 80, $y); // Kembali ke posisi setelah nama produk
            $this->Cell(30, 6, $item['quantity'] . ' pcs', 1, 1, 'C'); // Posisi cell jumlah
        }
    }
}


$pdf = new PDF();

// Iterasi setiap pesanan dan buat halaman resi
foreach ($orders as $order) {
    $items = $order_model->getOrderItems($order['id']);
    $pdf->DrawResi($order, $items, $settings);
}

// Update status pesanan menjadi 'Sedang Diproses'
$order_model->updateStatusForMultiple($order_ids, 'Sedang Diproses');

// Output PDF
$pdf->Output('I', 'Resi_Pesanan.pdf');
exit();
?>