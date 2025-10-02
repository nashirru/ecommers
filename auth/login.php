<?php
// File: auth/login.php
session_start();

// Jika sudah login, redirect ke dashboard
if (isset($_SESSION['user_id'])) {
    header("Location: ../public/index.php?page=dashboard");
    exit();
}

require_once '../config/db.php';
require_once '../app/models/User.php';

$user_model = new User($conn);
$error_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if (empty($email) || empty($password)) {
        $error_message = "Email dan password harus diisi.";
    } else {
        $user = $user_model->login($email, $password);
        if ($user) {
            // Login berhasil, simpan data ke session
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['is_admin'] = $user['is_admin'];
            
            // Cek jika ada URL redirect setelah login (misal dari checkout)
            $redirect_url = $_SESSION['redirect_url'] ?? '../public/index.php?page=dashboard';
            unset($_SESSION['redirect_url']); // Hapus setelah digunakan
            
            header("Location: " . $redirect_url);
            exit();
        } else {
            $error_message = "Email atau password salah.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<!-- ... Sisa kode HTML sama seperti sebelumnya ... -->
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Warok Kite</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Poppins', sans-serif; }
    </style>
</head>
<body class="bg-gray-100 flex items-center justify-center h-screen">

    <div class="w-full max-w-md p-8 space-y-6 bg-white rounded-lg shadow-md">
        <div class="text-center">
            <h1 class="text-3xl font-bold text-gray-900">Login ke Akun Anda</h1>
            <p class="mt-2 text-sm text-gray-600">Selamat datang kembali di Warok Kite</p>
        </div>

        <?php if ($error_message): ?>
            <div class="p-4 mb-4 text-sm text-red-700 bg-red-100 rounded-lg" role="alert">
                <?php echo htmlspecialchars($error_message); ?>
            </div>
        <?php endif; ?>

        <form class="space-y-6" action="login.php" method="POST">
            <div>
                <label for="email" class="text-sm font-medium text-gray-700">Alamat Email</label>
                <input id="email" name="email" type="email" autocomplete="email" required class="w-full px-3 py-2 mt-1 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            <div>
                <label for="password" class="text-sm font-medium text-gray-700">Password</label>
                <input id="password" name="password" type="password" autocomplete="current-password" required class="w-full px-3 py-2 mt-1 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            <div>
                <button type="submit" class="w-full px-4 py-2 font-medium text-white bg-indigo-600 rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Masuk
                </button>
            </div>
        </form>
        <p class="text-sm text-center text-gray-600">
            Belum punya akun?
            <a href="register.php" class="font-medium text-indigo-600 hover:text-indigo-500">Daftar di sini</a>
        </p>
    </div>

</body>
</html>