<?php
include '../includes/db.php';
include '../includes/functions.php';
if (!isAdmin()) redirect('../login.php');

if (isset($_GET['approve'])) {
    $id = $_GET['approve'];
    $pdo->prepare("UPDATE users SET approved=1 WHERE id=? AND type='seller'")->execute([$id]);
    redirect('sellers.php');
}
if (isset($_GET['reject'])) {
    $id = $_GET['reject'];
    $pdo->prepare("DELETE FROM users WHERE id=? AND type='seller'")->execute([$id]);
    redirect('sellers.php');
}

$sellers = $pdo->query("SELECT * FROM users WHERE type='seller'")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Sellers</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 text-gray-800 min-h-screen">

     <!-- navbar section -->
 <nav class="bg-gradient-to-r from-blue-600 to-indigo-600 text-white shadow-lg">
    <div class="max-w-7xl mx-auto px-4 py-4 flex items-center justify-between">
      <div class="text-2xl font-bold tracking-wide">ShopHub</div>
      <div class="flex space-x-6">
        <a href="dashboard.php" class="px-4 py-2 rounded-lg hover:bg-white hover:text-blue-600 hover:shadow-md">Home</a>
        <a href="sellers.php" class="px-4 py-2 rounded-lg hover:bg-white hover:text-blue-600 hover:shadow-md">Manage Seller</a>
        <a href="products.php" class="px-4 py-2 rounded-lg hover:bg-white hover:text-blue-600 hover:shadow-md">Manage Product</a>
        <a href="dashboard.php" class="px-4 py-2 rounded-lg hover:bg-white hover:text-blue-600 hover:shadow-md">Dashboard</a>
        <a href="logout.php" class="px-4 py-2 rounded-lg hover:bg-white hover:text-blue-600 hover:shadow-md">Logout</a>
      </div>
    </div>
  </nav>

    <div class="max-w-5xl mx-auto px-6 py-10">
        <h1 class="text-3xl font-bold mb-6 text-center">🛍️ Seller Management</h1>

        <div class="grid gap-6">
            <?php foreach ($sellers as $seller): ?>
            <div class="bg-white p-6 rounded-lg shadow-md flex justify-between items-center">
                <div>
                    <p class="text-lg font-semibold">Seller #<?php echo $seller['id']; ?></p>
                    <p class="text-sm text-gray-600">Username: <?php echo htmlspecialchars($seller['username']); ?></p>
                    <p class="text-sm text-gray-600">Email: <?php echo htmlspecialchars($seller['email']); ?></p>
                    <p class="text-sm font-medium <?php echo $seller['approved'] ? 'text-green-600' : 'text-red-500'; ?>">
                        Approved: <?php echo $seller['approved'] ? 'Yes' : 'No'; ?>
                    </p>
                </div>
                <div class="flex gap-3">
                    <?php if (!$seller['approved']): ?>
                    <a href="?approve=<?php echo $seller['id']; ?>" class="bg-green-500 text-white px-3 py-1 rounded hover:bg-green-600">Approve</a>
                    <?php endif; ?>
                    <a href="?reject=<?php echo $seller['id']; ?>" class="bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600">Reject</a>
                </div>
            </div>
            <?php endforeach; ?>
            <?php if (empty($sellers)): ?>
            <p class="text-center text-gray-500">No sellers found.</p>
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
