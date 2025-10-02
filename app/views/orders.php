<?php
// File: app/views/orders.php
if (!isset($_SESSION['user_id'])) {
    header('Location: ../auth/login.php');
    exit();
}
require_once '../app/models/Order.php';
$order_model = new Order($conn);
$orders = $order_model->getByUserId($_SESSION['user_id']);
?>

<header class="bg-white shadow">
    <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
        <h1 class="text-3xl font-bold tracking-tight text-gray-900">Riwayat Pesanan Saya</h1>
    </div>
</header>
<div class="mt-6">
    <div class="bg-white shadow overflow-hidden sm:rounded-md">
        <ul role="list" class="divide-y divide-gray-200">
            <?php if (empty($orders)): ?>
                <li class="p-6 text-center text-gray-500">Anda belum memiliki riwayat pesanan.</li>
            <?php else: ?>
                <?php foreach ($orders as $order): ?>
                    <li>
                        <a href="index.php?page=order_detail&id=<?php echo $order['id']; ?>" class="block hover:bg-gray-50">
                            <div class="px-4 py-4 sm:px-6">
                                <div class="flex items-center justify-between">
                                    <p class="text-sm font-medium text-indigo-600 truncate">
                                        Pesanan #<?php echo htmlspecialchars($order['invoice_number']); ?>
                                    </p>
                                    <div class="ml-2 flex-shrink-0 flex">
                                        <p class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                            <?php 
                                                $status_color = 'bg-yellow-100 text-yellow-800';
                                                if ($order['status'] == 'Diproses') $status_color = 'bg-blue-100 text-blue-800';
                                                if ($order['status'] == 'Dikirim') $status_color = 'bg-purple-100 text-purple-800';
                                                if ($order['status'] == 'Selesai') $status_color = 'bg-green-100 text-green-800';
                                                if ($order['status'] == 'Dibatalkan') $status_color = 'bg-red-100 text-red-800';
                                                echo $status_color;
                                            ?>">
                                            <?php echo htmlspecialchars($order['status']); ?>
                                        </p>
                                    </div>
                                </div>
                                <div class="mt-2 sm:flex sm:justify-between">
                                    <div class="sm:flex">
                                        <p class="flex items-center text-sm text-gray-500">
                                            <svg class="flex-shrink-0 mr-1.5 h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                                <path fill-rule="evenodd" d="M5.75 2a.75.75 0 01.75.75v.5a.75.75 0 01-1.5 0v-.5A.75.75 0 015.75 2zM14.25 2a.75.75 0 01.75.75v.5a.75.75 0 01-1.5 0v-.5a.75.75 0 01.75-.75zM3.75 5.5a.75.75 0 000 1.5h12.5a.75.75 0 000-1.5H3.75zM2 9.75A.75.75 0 012.75 9h14.5a.75.75 0 010 1.5H2.75A.75.75 0 012 9.75zM2.75 12a.75.75 0 01.75.75v2.5a.75.75 0 01-1.5 0v-2.5a.75.75 0 01.75-.75zM17.25 12a.75.75 0 01.75.75v2.5a.75.75 0 01-1.5 0v-2.5A.75.75 0 0117.25 12z"></path>
                                            </svg>
                                            Rp <?php echo number_format($order['total_amount'], 0, ',', '.'); ?>
                                        </p>
                                    </div>
                                    <div class="mt-2 flex items-center text-sm text-gray-500 sm:mt-0">
                                        <svg class="flex-shrink-0 mr-1.5 h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                            <path fill-rule="evenodd" d="M5.75 2a.75.75 0 01.75.75v.5a.75.75 0 01-1.5 0v-.5A.75.75 0 015.75 2zM14.25 2a.75.75 0 01.75.75v.5a.75.75 0 01-1.5 0v-.5a.75.75 0 01.75-.75zM3.75 5.5a.75.75 0 000 1.5h12.5a.75.75 0 000-1.5H3.75zM2 9.75A.75.75 0 012.75 9h14.5a.75.75 0 010 1.5H2.75A.75.75 0 012 9.75zM2.75 12a.75.75 0 01.75.75v2.5a.75.75 0 01-1.5 0v-2.5a.75.75 0 01.75-.75zM17.25 12a.75.75 0 01.75.75v2.5a.75.75 0 01-1.5 0v-2.5A.75.75 0 0117.25 12z"></path>
                                        </svg>
                                        <p>
                                            Dipesan pada <time datetime="<?php echo $order['created_at']; ?>"><?php echo date('d M Y', strtotime($order['created_at'])); ?></time>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </li>
                <?php endforeach; ?>
            <?php endif; ?>
        </ul>
    </div>
</div>