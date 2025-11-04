<?php
include '../includes/db.php';
include '../includes/functions.php';
if (!isBuyer()) redirect('../login.php');

$orders = $pdo->prepare("SELECT o.*, p.name AS product FROM orders o JOIN products p ON o.product_id = p.id WHERE o.buyer_id = ?");
$orders->execute([$_SESSION['user_id']]);
$orders = $orders->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Buyer Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-white text-gray-800 min-h-screen flex flex-col">

    <!-- Navbar -->
     <!-- navbar section -->
 <nav class="bg-gradient-to-r from-blue-600 to-indigo-600 text-white shadow-lg">
    <div class="max-w-7xl mx-auto px-4 py-4 flex items-center justify-between">
      <div class="text-2xl font-bold tracking-wide">ShopHub</div>
      <div class="flex space-x-6">
        <a href="../home.php" class="px-4 py-2 rounded-lg hover:bg-white hover:text-blue-600 hover:shadow-md">Home</a>
        <a href="dashboard.php" class="px-4 py-2 rounded-lg hover:bg-white hover:text-blue-600 hover:shadow-md">Dashboard</a>
        <a href="browse.php" class="px-4 py-2 rounded-lg hover:bg-white hover:text-blue-600 hover:shadow-md">Menu</a>
        <a href="cart.php" class="px-4 py-2 rounded-lg hover:bg-white hover:text-blue-600 hover:shadow-md">Cart</a>
        <a href="wishlist.php" class="px-4 py-2 rounded-lg hover:bg-white hover:text-blue-600 hover:shadow-md">Wishlist</a>
        <a href="../profile.php" class="px-4 py-2 rounded-lg hover:bg-white hover:text-blue-600 hover:shadow-md">Profile</a>
        <a href="logout.php" class="px-4 py-2 rounded-lg hover:bg-white hover:text-blue-600 hover:shadow-md">Logout</a>
      </div>
    </div>
  </nav>

    <!-- Dashboard Content -->
    <main class="flex-grow">
        <div class="max-w-5xl mx-auto px-6 py-10">
            <h1 class="text-3xl font-bold mb-6 text-center">Buyer Dashboard</h1>

            <h2 class="text-xl font-semibold mb-4">Your Orders</h2>

            <div class="grid gap-4">
                <?php foreach ($orders as $order): ?>
                <div class="bg-gray-100 p-4 rounded-lg shadow-md">
                    <p><span class="font-semibold">Order ID:</span> <?php echo $order['id']; ?></p>
                    <p><span class="font-semibold">Product:</span> <?php echo $order['product']; ?></p>
                    <p><span class="font-semibold">Status:</span> 
                        <span class="<?php echo $order['status'] === 'delivered' ? 'text-green-600' : 'text-yellow-600'; ?>">
                            <?php echo ucfirst($order['status']); ?>
                        </span>
                    </p>
                </div>
                <?php endforeach; ?>
                <?php if (empty($orders)): ?>
                <p class="text-center text-gray-500">You have no orders yet.</p>
                <?php endif; ?>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <?php include '../assets/headersection/footer.php'; ?>

</body>
</html>
