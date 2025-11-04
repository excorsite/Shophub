<?php
include '../includes/db.php';
include '../includes/functions.php';
if (!isSeller()) redirect('../login.php');

if (isset($_GET['approve'])) {
    $id = $_GET['approve'];
    $pdo->prepare("UPDATE orders SET status='approved' WHERE id=? AND product_id IN (SELECT id FROM products WHERE seller_id=?)")->execute([$id, $_SESSION['user_id']]);
    redirect('orders.php');
}
if (isset($_GET['reject'])) {
    $id = $_GET['reject'];
    $pdo->prepare("UPDATE orders SET status='rejected' WHERE id=? AND product_id IN (SELECT id FROM products WHERE seller_id=?)")->execute([$id, $_SESSION['user_id']]);
    redirect('orders.php');
}

$orders = $pdo->prepare("SELECT o.*, p.name AS product, u.username AS buyer FROM orders o JOIN products p ON o.product_id = p.id JOIN users u ON o.buyer_id = u.id WHERE p.seller_id = ?");
$orders->execute([$_SESSION['user_id']]);
$orders = $orders->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Orders</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-white text-gray-800 min-h-screen">

<!-- Navbar section -->
     <nav class="bg-gradient-to-r from-blue-600 to-indigo-600 text-white shadow-lg">
    <div class="max-w-7xl mx-auto px-4 py-4 flex items-center justify-between">
      <div class="text-2xl font-bold tracking-wide">ShopHub</div>
      <div class="flex space-x-6">
        <a href="dashboard.php" class="px-4 py-2 rounded-lg hover:bg-white hover:text-blue-600 hover:shadow-md">Home</a>
        <a href="dashboard.php" class="px-4 py-2 rounded-lg hover:bg-white hover:text-blue-600 hover:shadow-md">Dashboard</a>
        <a href="add_product.php" class="px-4 py-2 rounded-lg hover:bg-white hover:text-blue-600 hover:shadow-md">Add Product</a>
        <a href="orders.php" class="px-4 py-2 rounded-lg hover:bg-white hover:text-blue-600 hover:shadow-md">Manage Orders</a>
        <a href="logout.php" class="px-4 py-2 rounded-lg hover:bg-white hover:text-blue-600 hover:shadow-md">Logout</a>
      </div>
    </div>
  </nav>

    <div class="max-w-5xl mx-auto px-6 py-10">
        <h1 class="text-3xl font-bold mb-6 text-center">Manage Orders</h1>

        <div class="grid gap-6">
            <?php foreach ($orders as $order): ?>
            <div class="bg-gray-100 p-6 rounded-lg shadow-md flex justify-between items-center">
                <div>
                    <p class="text-lg font-semibold">Order #<?php echo $order['id']; ?></p>
                    <p class="text-sm text-gray-600">Product: <?php echo htmlspecialchars($order['product']); ?></p>
                    <p class="text-sm text-gray-600">Buyer: <?php echo htmlspecialchars($order['buyer']); ?></p>
                    <p class="text-sm font-medium <?php echo $order['status'] === 'approved' ? 'text-green-600' : ($order['status'] === 'rejected' ? 'text-red-500' : 'text-yellow-600'); ?>">
                        Status: <?php echo ucfirst($order['status']); ?>
                    </p>
                </div>
                <?php if ($order['status'] === 'pending'): ?>
                <div class="flex gap-3">
                    <a href="?approve=<?php echo $order['id']; ?>" class="bg-green-500 text-white px-3 py-1 rounded hover:bg-green-600">Approve</a>
                    <a href="?reject=<?php echo $order['id']; ?>" class="bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600">Reject</a>
                </div>
                <?php endif; ?>
            </div>
            <?php endforeach; ?>
            <?php if (empty($orders)): ?>
            <p class="text-center text-gray-500">No orders to manage.</p>
            <?php endif; ?>
        </div>

        <div class="mt-10 text-center">
            <a href="dashboard.php" class="text-blue-600 hover:underline">← Back to Dashboard</a>
        </div>
    </div>
  <!-- footer section -->
      <?php include '../assets/headersection/footer.php'; ?>
</body>
</html>
