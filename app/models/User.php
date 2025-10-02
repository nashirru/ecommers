<?php
// File: app/models/User.php
// Model untuk mengelola data dan logika terkait pengguna

class User {
    private $conn;

    // Constructor untuk inisialisasi koneksi database
    public function __construct($db_connection) {
        $this->conn = $db_connection;
    }

    /**
     * Mendaftarkan pengguna baru ke database.
     * @param string $username
     * @param string $email
     * @param string $password
     * @return bool True jika berhasil, false jika gagal.
     */
    public function register($username, $email, $password) {
        // Hash password sebelum disimpan
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Siapkan statement untuk mencegah SQL Injection
        $stmt = $this->conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $username, $email, $hashed_password);

        // Eksekusi statement
        if ($stmt->execute()) {
            return true;
        } else {
            // Sebaiknya ditambahkan logging error di sini untuk production
            // error_log($stmt->error);
            return false;
        }
    }

    /**
     * Memeriksa kredensial login pengguna.
     * @param string $email
     * @param string $password
     * @return array|null Data pengguna jika login berhasil, null jika gagal.
     */
    public function login($email, $password) {
        // Cari pengguna berdasarkan email
        $stmt = $this->conn->prepare("SELECT id, username, email, password, is_admin FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            // Verifikasi password
            if (password_verify($password, $user['password'])) {
                return $user; // Login berhasil
            }
        }
        
        return null; // Login gagal
    }

    /**
     * Memeriksa apakah email sudah terdaftar.
     * @param string $email
     * @return bool True jika email sudah ada, false jika belum.
     */
    public function isEmailExists($email) {
        $stmt = $this->conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();
        return $stmt->num_rows > 0;
    }

    /**
     * Memeriksa apakah seorang user adalah admin.
     * @param int $userId
     * @return bool
     */
    public function isAdmin($userId) {
        $stmt = $this->conn->prepare("SELECT is_admin FROM users WHERE id = ?");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        return $user && $user['is_admin'] == 1;
    }
}