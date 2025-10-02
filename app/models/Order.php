<?php
// File: app/models/Order.php

class Order {
    private $conn;

    public function __construct($db_connection) {
        $this->conn = $db_connection;
    }

    public function create($userId, $address, $total, $cartItems, $paymentMethod) {
        $this->conn->begin_transaction();
        try {
            $invoiceNumber = 'WK-' . strtoupper(uniqid());
            $stmt = $this->conn->prepare("INSERT INTO orders (user_id, invoice_number, total_amount, shipping_address, payment_method) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("isdss", $userId, $invoiceNumber, $total, $address, $paymentMethod);
            $stmt->execute();
            $orderId = $stmt->insert_id;

            $stmt_items = $this->conn->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
            foreach ($cartItems as $item) {
                $stmt_items->bind_param("iiid", $orderId, $item['id'], $item['quantity'], $item['price']);
                $stmt_items->execute();

                $stmt_stock = $this->conn->prepare("UPDATE products SET stock = stock - ? WHERE id = ?");
                $stmt_stock->bind_param("ii", $item['quantity'], $item['id']);
                $stmt_stock->execute();
            }

            $this->conn->commit();
            return ['order_id' => $orderId, 'invoice' => $invoiceNumber];

        } catch (Exception $e) {
            $this->conn->rollback();
            // error_log($e->getMessage()); // Aktifkan untuk debugging
            return false;
        }
    }

    /**
     * Mengambil semua pesanan milik seorang user.
     * @param int $userId
     * @return array
     */
    public function getByUserId($userId) {
        $stmt = $this->conn->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Mengambil detail satu pesanan.
     * @param int $orderId
     * @param int|null $userId (opsional, untuk verifikasi kepemilikan)
     * @return array|null
     */
    public function getById($orderId, $userId = null) {
        $sql = "SELECT * FROM orders WHERE id = ?";
        if ($userId) {
            $sql .= " AND user_id = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("ii", $orderId, $userId);
        } else {
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("i", $orderId);
        }
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    /**
     * Mengambil item-item dari sebuah pesanan.
     * @param int $orderId
     * @return array
     */
    public function getOrderItems($orderId) {
        $sql = "SELECT oi.*, p.name as product_name, p.image as product_image 
                FROM order_items oi 
                LEFT JOIN products p ON oi.product_id = p.id 
                WHERE oi.order_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $orderId);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * (ADMIN) Mengambil semua pesanan.
     * @return array
     */
    public function getAllOrders() {
        $sql = "SELECT o.*, u.username FROM orders o JOIN users u ON o.user_id = u.id ORDER BY o.created_at DESC";
        $result = $this->conn->query($sql);
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * (ADMIN) Mengupdate status pesanan.
     * @param int $orderId
     * @param string $status
     * @return bool
     */
    public function updateStatus($orderId, $status) {
        $stmt = $this->conn->prepare("UPDATE orders SET status = ? WHERE id = ?");
        $stmt->bind_param("si", $status, $orderId);
        return $stmt->execute();
    }

    /**
     * (ADMIN) Mengambil data laporan penjualan.
     * @return array
     */
    public function getSalesReport() {
        $report = [];
        // Total Omzet
        $result = $this->conn->query("SELECT SUM(total_amount) as total_revenue FROM orders WHERE status = 'Selesai'");
        $report['total_revenue'] = $result->fetch_assoc()['total_revenue'] ?? 0;

        // Produk Terlaris
        $sql = "SELECT p.name, SUM(oi.quantity) as total_sold
                FROM order_items oi
                JOIN products p ON oi.product_id = p.id
                JOIN orders o ON oi.order_id = o.id
                WHERE o.status = 'Selesai' AND p.name IS NOT NULL
                GROUP BY p.name
                ORDER BY total_sold DESC
                LIMIT 5";
        $result = $this->conn->query($sql);
        $report['best_selling'] = $result->fetch_all(MYSQLI_ASSOC);

        return $report;
    }
}
?>