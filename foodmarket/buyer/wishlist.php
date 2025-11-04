<?php
include '../includes/db.php';
include '../includes/functions.php';
if (!isBuyer()) redirect('../login.php');

if (isset($_GET['remove'])) {
    $id = $_GET['remove'];
    $pdo->prepare("DELETE FROM wishlist WHERE id=? AND buyer_id=?")->execute([$id, $_SESSION['user_id']]);
    redirect('wishlist.php');
}

$wishlist = $pdo->prepare("SELECT w.*, p.name, p.price FROM wishlist w JOIN products p ON w.product_id = p.id WHERE w.buyer_id = ?");
$wishlist->execute([$_SESSION['user_id']]);
$wishlist = $wishlist->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Wishlist</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 text-gray-800 min-h-screen flex flex-col">

    <!-- Navbar -->
   <?php include '../assets/headersection/navbar.php'; ?>

    <!-- Wishlist Content -->
    <main class="flex-grow">
        <div class="max-w-4xl mx-auto px-6 py-10">
            <h2 class="text-3xl font-bold mb-6 text-center">💖 Your Wishlist</h2>

            <?php if (empty($wishlist)): ?>
                <p class="text-center text-gray-500">Your wishlist is currently empty.</p>
            <?php else: ?>
                <div class="space-y-4">
                    <?php foreach ($wishlist as $item): ?>
                    <div class="bg-white p-6 rounded-lg shadow-md flex justify-between items-center">
                        <div>
                            <p class="text-lg font-semibold"><?php echo htmlspecialchars($item['name']); ?></p>
                            <p class="text-sm text-gray-600">Price: NPR <?php echo number_format($item['price'], 2); ?></p>
                        </div>
                        <div class="flex gap-3">
                            <a href="?remove=<?php echo $item['id']; ?>" class="bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600">Remove</a>
                            <a href="checkout.php?product_id=<?php echo $item['product_id']; ?>" class="bg-blue-600 text-white px-3 py-1 rounded hover:bg-blue-700">Buy Now</a>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <div class="mt-8 flex justify-center gap-6">
                <a href="browse.php" class="text-blue-600 hover:underline">Browse More</a>
                <a href="dashboard.php" class="text-gray-600 hover:underline">Back to Dashboard</a>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <?php include '../assets/headersection/footer.php'; ?>

</body>
</html>
