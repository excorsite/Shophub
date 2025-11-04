<?php
include '../includes/db.php';
include '../includes/functions.php';
if (!isBuyer()) redirect('../login.php');

if (isset($_GET['remove'])) {
    $id = $_GET['remove'];
    $pdo->prepare("DELETE FROM cart WHERE id=? AND buyer_id=?")->execute([$id, $_SESSION['user_id']]);
    redirect('cart.php');
}

$cart = $pdo->prepare("SELECT c.*, p.name, p.price FROM cart c JOIN products p ON c.product_id = p.id WHERE c.buyer_id = ?");
$cart->execute([$_SESSION['user_id']]);
$cart = $cart->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Cart</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 text-gray-800 min-h-screen flex flex-col">

    <!-- Navbar -->
    <?php include '../assets/headersection/navbar.php'; ?>

    <!-- Cart Content -->
    <main class="flex-grow">
        <div class="max-w-4xl mx-auto px-6 py-10">
            <h2 class="text-3xl font-bold mb-6 text-center">🛒 Your Cart</h2>

            <?php if (empty($cart)): ?>
                <p class="text-center text-gray-500">Your cart is empty.</p>
            <?php else: ?>
                <div class="space-y-4">
                    <?php foreach ($cart as $item): ?>
                    <div class="bg-white p-6 rounded-lg shadow-md flex justify-between items-center">
                        <div>
                            <p class="text-lg font-semibold"><?php echo htmlspecialchars($item['name']); ?></p>
                            <p class="text-sm text-gray-600">Price: Rs <?php echo number_format($item['price'], 2); ?></p>
                            <p class="text-sm text-gray-600">Quantity: <?php echo $item['quantity']; ?></p>
                        </div>
                        <a href="?remove=<?php echo $item['id']; ?>" class="bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600">Remove</a>
                    </div>
                    <?php endforeach; ?>
                </div>

                <div class="mt-8 flex justify-center gap-6">
                    <a href="checkout.php?from_cart=1" class="bg-green-600 text-white px-5 py-2 rounded hover:bg-green-700">Checkout (COD)</a>
                    <a href="browse.php" class="text-blue-600 hover:underline">Continue Shopping</a>
                    <a href="dashboard.php" class="text-gray-600 hover:underline">Back to Dashboard</a>
                </div>
            <?php endif; ?>
        </div>
    </main>

    <!-- Footer -->
    <?php include '../assets/headersection/footer.php'; ?>

</body>
</html>
