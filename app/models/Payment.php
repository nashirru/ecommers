<?php
// File: app/models/Payment.php

class Payment {
    private $conn;

    public function __construct($db_connection) {
        $this->conn = $db_connection;
    }

    /**
     * Menyimpan bukti pembayaran ke database.
     * @param int $orderId
     * @param string $proofFileName
     * @return bool
     */
    public function create($orderId, $proofFileName) {
        $stmt = $this->conn->prepare("INSERT INTO payments (order_id, payment_proof) VALUES (?, ?)");
        $stmt->bind_param("is", $orderId, $proofFileName);
        return $stmt->execute();
    }

    /**
     * Mengambil bukti pembayaran berdasarkan order_id.
     * @param int $orderId
     * @return array|null
     */
    public function getByOrderId($orderId) {
        $stmt = $this->conn->prepare("SELECT * FROM payments WHERE order_id = ? ORDER BY uploaded_at DESC LIMIT 1");
        $stmt->bind_param("i", $orderId);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }
}
?>