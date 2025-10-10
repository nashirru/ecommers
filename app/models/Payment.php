<?php
// File: app/models/Payment.php

class Payment {
    private $conn;

    public function __construct($db_connection) {
        $this->conn = $db_connection;
    }

    /**
     * Membuat record pembayaran baru.
     * @param int $orderId
     * @param string $paymentProof (nama file bukti pembayaran)
     * @return bool
     */
    public function create($orderId, $paymentProof) {
        $stmt = $this->conn->prepare("INSERT INTO payments (order_id, payment_proof, payment_status) VALUES (?, ?, 'Pending')");
        $stmt->bind_param("is", $orderId, $paymentProof);
        return $stmt->execute();
    }

    /**
     * Mengambil data pembayaran berdasarkan order_id.
     * @param int $orderId
     * @return array|null
     */
    public function getByOrderId($orderId) {
        $stmt = $this->conn->prepare("SELECT * FROM payments WHERE order_id = ? ORDER BY created_at DESC LIMIT 1");
        $stmt->bind_param("i", $orderId);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    /**
     * Mengupdate status pembayaran.
     * @param int $paymentId
     * @param string $status ('Pending', 'Verified', 'Rejected')
     * @return bool
     */
    public function updateStatus($paymentId, $status) {
        $stmt = $this->conn->prepare("UPDATE payments SET payment_status = ?, updated_at = NOW() WHERE id = ?");
        $stmt->bind_param("si", $status, $paymentId);
        return $stmt->execute();
    }

    /**
     * Menghapus pembayaran (jarang digunakan).
     * @param int $paymentId
     * @return bool
     */
    public function delete($paymentId) {
        $stmt = $this->conn->prepare("DELETE FROM payments WHERE id = ?");
        $stmt->bind_param("i", $paymentId);
        return $stmt->execute();
    }

    /**
     * Mengambil semua pembayaran yang pending (untuk admin).
     * @return array
     */
    public function getAllPending() {
        $sql = "SELECT p.*, o.invoice_number, o.total_amount, u.username 
                FROM payments p
                JOIN orders o ON p.order_id = o.id
                JOIN users u ON o.user_id = u.id
                WHERE p.payment_status = 'Pending'
                ORDER BY p.created_at DESC";
        
        $result = $this->conn->query($sql);
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Mengambil semua pembayaran (untuk admin).
     * @param string|null $status
     * @return array
     */
    public function getAll($status = null) {
        $sql = "SELECT p.*, o.invoice_number, o.total_amount, u.username 
                FROM payments p
                JOIN orders o ON p.order_id = o.id
                JOIN users u ON o.user_id = u.id";
        
        if ($status) {
            $sql .= " WHERE p.payment_status = ?";
        }
        
        $sql .= " ORDER BY p.created_at DESC";

        if ($status) {
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("s", $status);
            $stmt->execute();
            $result = $stmt->get_result();
        } else {
            $result = $this->conn->query($sql);
        }
        
        return $result->fetch_all(MYSQLI_ASSOC);
    }
}