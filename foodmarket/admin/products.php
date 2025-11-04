<?php
include '../includes/db.php';
include '../includes/functions.php';
if (!isAdmin()) redirect('../login.php');

if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $pdo->prepare("DELETE FROM products WHERE id=?")->execute([$id]);
    redirect('products.php');
}

$products = $pdo->query("SELECT p.*, u.username AS seller FROM products p JOIN users u ON p.seller_id = u.id")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Products</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-white text-gray-800 min-h-screen">

 <!-- navbar section -->
 <nav class="bg-gradient-to-r from-blue-600 to-indigo-600 text-white shadow-lg">
    <div class="max-w-7xl mx-auto px-4 py-4 flex items-center justify-between">
      <div class="text-2xl font-bold tracking-wide">ShopHub</div>
      <div class="flex space-x-6">
        <a href="dashboard.php" class="px-4 py-2 rounded-lg hover:bg-white hover:text-blue-600 hover:shadow-md">Home</a>
        <a href="products.php" class="px-4 py-2 rounded-lg hover:bg-white hover:text-blue-600 hover:shadow-md">Manage Products</a>
        <a href="sellers.php" class="px-4 py-2 rounded-lg hover:bg-white hover:text-blue-600 hover:shadow-md">Manage Seller</a>
        <a href="dashboard.php" class="px-4 py-2 rounded-lg hover:bg-white hover:text-blue-600 hover:shadow-md">Dashboard</a>
        <a href="logout.php" class="px-4 py-2 rounded-lg hover:bg-white hover:text-blue-600 hover:shadow-md">Logout</a>
      </div>
    </div>
  </nav>

    <div class="max-w-5xl mx-auto px-6 py-10">
        <h1 class="text-3xl font-bold mb-6 text-center">Product List Management</h1>

        <div class="grid gap-6">
            <?php foreach ($products as $product): ?>
            <div class="bg-gray-100 p-6 rounded-lg shadow-md flex justify-between items-center">
                <div>
                    <p class="text-lg font-semibold"><?php echo htmlspecialchars($product['name']); ?></p>
                    <p class="text-sm text-gray-600">ID: <?php echo $product['id']; ?></p>
                    <p class="text-sm text-gray-600">Price: Rs <?php echo number_format($product['price'], 2); ?></p>
                    <p class="text-sm text-gray-600">Seller: <?php echo htmlspecialchars($product['seller']); ?></p>
                </div>
                <a href="?delete=<?php echo $product['id']; ?>" class="text-red-500 hover:underline font-medium">Delete</a>
            </div>
            <?php endforeach; ?>
            <?php if (empty($products)): ?>
            <p class="text-center text-gray-500">No products found.</p>
            <?php endif; ?>
        </div>

        <div class="mt-10 text-center">
            <a href="dashboard.php" class="text-blue-600 hover:underline">← Back to Dashboard</a>
        </div>
    </div>

    <!-- Footer -->
    <?php include '../assets/headersection/footer.php'; ?>

</body>
</html>
