<?php
session_start();

// Autentikasi: Pastikan pengguna adalah admin yang sudah login
if (!isset($_SESSION['user_id'])) {
    header('Location: ../../auth/login.php');
    exit;
}

// Menentukan path dasar (base path) aplikasi
define('BASE_PATH', dirname(__DIR__, 2));

// Memuat file-file yang diperlukan
require_once BASE_PATH . '/config/db.php';
require_once BASE_PATH . '/app/models/Order.php';
require_once BASE_PATH . '/app/models/Setting.php';
require_once BASE_PATH . '/app/models/User.php';
require_once BASE_PATH . '/vendor/autoload.php';

// Verifikasi admin
$user_model = new User($conn);
if (!$user_model->isAdmin($_SESSION['user_id'])) {
    die('ERROR: Akses ditolak. Hanya admin yang dapat mencetak resi.');
}

// Inisialisasi model
$orderModel = new Order($conn);
$settingModel = new Setting($conn);

// Ambil pengaturan toko
$settings = $settingModel->getAllAsAssoc();

// Cek apakah ada parameter 'ids' untuk cetak multiple atau 'order_id' untuk single
$order_ids = [];

if (isset($_GET['ids']) && !empty($_GET['ids'])) {
    // Multiple orders
    $ids_string = $_GET['ids'];
    $order_ids = array_map('intval', explode(',', $ids_string));
    $order_ids = array_filter($order_ids, function($id) { return $id > 0; });
} elseif (isset($_GET['order_id']) && filter_var($_GET['order_id'], FILTER_VALIDATE_INT)) {
    // Single order
    $order_ids = [(int)$_GET['order_id']];
}

if (empty($order_ids)) {
    die('ERROR: Order ID tidak valid atau tidak ditemukan.');
}

// Ambil data pesanan
if (count($order_ids) === 1) {
    $orders_data = [$orderModel->getById($order_ids[0])];
} else {
    $orders_data = $orderModel->getMultipleOrdersByIds($order_ids);
}

// Filter orders yang valid
$orders_data = array_filter($orders_data, function($order) { return $order !== null; });

if (empty($orders_data)) {
    die('ERROR: Tidak ada pesanan yang ditemukan.');
}

//============================================================+
// MEMBUAT DOKUMEN PDF
//============================================================+

$pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);

// Atur informasi dokumen
$pdf->SetCreator('Toko Online System');
$pdf->SetAuthor($settings['store_name'] ?? 'Admin Toko');
$pdf->SetTitle('Resi Pesanan');
$pdf->SetSubject('Detail Resi Pesanan');

// Hapus header dan footer bawaan
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);

// Atur margin dokumen
$pdf->SetMargins(15, 15, 15);
$pdf->SetAutoPageBreak(TRUE, 15);

// Atur font utama
$pdf->SetFont('helvetica', '', 10);

// Logo toko
$logo_path = BASE_PATH . '/public/assets/images/' . ($settings['store_logo'] ?? '');
$store_logo_html = '';
if (!empty($settings['store_logo']) && file_exists($logo_path)) {
    $store_logo_html = '<img src="@' . $logo_path . '" height="60px">';
} else {
    $store_logo_html = '<h1>' . htmlspecialchars($settings['store_name'] ?? 'Toko Online') . '</h1>';
}

// Loop untuk setiap pesanan
foreach ($orders_data as $index => $order) {
    // Tambah halaman baru
    $pdf->AddPage();
    
    // Ambil items untuk order ini
    $items = $orderModel->getOrderItems($order['id']);
    
    // Format tanggal
    $order_date = new DateTime($order['created_at']);
    $formatter = new IntlDateFormatter('id_ID', IntlDateFormatter::LONG, IntlDateFormatter::NONE);
    $formatted_date = $formatter->format($order_date);
    
    // Hitung subtotal produk
    $subtotal_produk = 0;
    foreach ($items as $item) {
        $subtotal_produk += $item['price'] * $item['quantity'];
    }
    
    // Format currency
    $formatted_subtotal = 'Rp ' . number_format($subtotal_produk, 0, ',', '.');
    $formatted_shipping = 'Rp ' . number_format($order['shipping_cost'] ?? 0, 0, ',', '.');
    $formatted_total = 'Rp ' . number_format($order['total_amount'], 0, ',', '.');
    
    // Build HTML untuk order ini
    $html = <<<EOD
<style>
    body {
        font-family: helvetica;
        font-size: 10pt;
        color: #333;
    }
    table {
        width: 100%;
        border-collapse: collapse;
    }
    th, td {
        padding: 8px;
        text-align: left;
    }
    .header-table td {
        vertical-align: top;
    }
    .header-info {
        text-align: right;
    }
    .items-table {
        margin-top: 20px;
    }
    .items-table th {
        background-color: #f2f2f2;
        border-bottom: 2px solid #ddd;
        font-weight: bold;
    }
    .items-table td {
        border-bottom: 1px solid #eee;
    }
    .total-table {
        margin-top: 20px;
        width: 50%;
        float: right;
    }
    .total-table td {
        padding: 5px;
    }
    .text-right {
        text-align: right;
    }
    .text-bold {
        font-weight: bold;
    }
    .title {
        font-size: 18pt;
        font-weight: bold;
        color: #555;
    }
    .footer-note {
        margin-top: 30px;
        text-align: center;
        font-size: 9pt;
        color: #888;
    }
    .barcode {
        text-align: center;
        margin-top: 20px;
    }
</style>

<body>
    <!-- HEADER -->
    <table class="header-table" cellpadding="0" cellspacing="0" border="0">
        <tr>
            <td style="width: 50%;">
                $store_logo_html
            </td>
            <td style="width: 50%;" class="header-info">
                <span class="title">RESI PENGIRIMAN</span><br><br>
                <strong>Nomor Pesanan:</strong> {$order['invoice_number']}<br>
                <strong>Tanggal:</strong> $formatted_date
            </td>
        </tr>
    </table>

    <hr style="margin: 20px 0;">

    <!-- INFORMASI PENGIRIMAN -->
    <table cellpadding="5" cellspacing="0" border="0">
        <tr>
            <td style="width: 50%;">
                <h4 style="margin-bottom: 5px;">DIKIRIM DARI:</h4>
                <strong>{$settings['store_name']}</strong><br>
                {$settings['store_address']}<br>
                Telepon: {$settings['store_phone']}
            </td>
            <td style="width: 50%;">
                <h4 style="margin-bottom: 5px;">DIKIRIM KEPADA:</h4>
                <strong>{$order['username']}</strong><br>
                {$order['shipping_address']}<br>
                Telepon: {$settings['store_phone']}
            </td>
        </tr>
    </table>

    <!-- TABEL ITEM -->
    <h3 style="margin-top: 20px;">Rincian Produk</h3>
    <table class="items-table" cellpadding="5" cellspacing="0" border="0">
        <thead>
            <tr>
                <th style="width: 50%;">Nama Produk</th>
                <th style="width: 15%;" class="text-right">Jumlah</th>
                <th style="width: 15%;" class="text-right">Harga</th>
                <th style="width: 20%;" class="text-right">Subtotal</th>
            </tr>
        </thead>
        <tbody>
EOD;

    // Loop items
    foreach ($items as $item) {
        $item_name = htmlspecialchars($item['product_name'] ?? 'Produk Dihapus');
        $item_qty = $item['quantity'];
        $item_price = 'Rp ' . number_format($item['price'], 0, ',', '.');
        $item_subtotal = 'Rp ' . number_format($item['price'] * $item['quantity'], 0, ',', '.');
        
        $html .= "<tr>";
        $html .= "<td>$item_name</td>";
        $html .= "<td class='text-right'>$item_qty</td>";
        $html .= "<td class='text-right'>$item_price</td>";
        $html .= "<td class='text-right'>$item_subtotal</td>";
        $html .= "</tr>";
    }

    $html .= <<<EOD
        </tbody>
    </table>

    <!-- TOTAL -->
    <table class="total-table" align="right" border="0">
        <tr>
            <td style="width: 60%;">Subtotal Produk</td>
            <td style="width: 40%;" class="text-right">{$formatted_subtotal}</td>
        </tr>
        <tr>
            <td>Biaya Pengiriman</td>
            <td class="text-right">{$formatted_shipping}</td>
        </tr>
        <tr>
            <td class="text-bold">TOTAL PEMBAYARAN</td>
            <td class="text-right text-bold">{$formatted_total}</td>
        </tr>
    </table>
    <div style="clear: both;"></div>

    <!-- FOOTER -->
    <div class="footer-note">
        <p>Terima kasih telah berbelanja di {$settings['store_name']}!<br>
        Ini adalah bukti pengiriman yang sah. Mohon simpan dengan baik.</p>
    </div>

    <!-- BARCODE -->
    <div class="barcode">
        <p>Nomor Pesanan: {$order['invoice_number']}</p>
    </div>
</body>
EOD;

    // Tulis HTML ke PDF
    $pdf->writeHTML($html, true, false, true, false, '');
    
    // Tambah barcode
    $barcode_style = array(
        'position' => 'C',
        'align' => 'C',
        'stretch' => false,
        'fitwidth' => true,
        'cellfitalign' => '',
        'border' => false,
        'hpadding' => 'auto',
        'vpadding' => 'auto',
        'fgcolor' => array(0, 0, 0),
        'bgcolor' => false,
        'text' => false,
        'font' => 'helvetica',
        'fontsize' => 8,
        'stretchtext' => 4
    );
    
    $pdf->write1DBarcode($order['invoice_number'], 'C128', '', '', '', 18, 0.4, $barcode_style, 'N');
    
    // UPDATE STATUS PESANAN MENJADI "SEDANG DIPROSES"
    if ($order['status'] === 'Belum Dicetak') {
        $orderModel->updateStatus($order['id'], 'Sedang Diproses');
    }
}

//============================================================+
// OUTPUT PDF
//============================================================+
ob_end_clean();
$filename = count($order_ids) === 1 
    ? 'resi_pesanan_' . $orders_data[0]['invoice_number'] . '.pdf'
    : 'resi_pesanan_multiple_' . date('YmdHis') . '.pdf';

$pdf->Output($filename, 'I');
exit;