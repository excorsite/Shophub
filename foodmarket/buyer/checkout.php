<?php
include '../includes/db.php';
include '../includes/functions.php';
if (!isBuyer()) redirect('../login.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST' || isset($_GET['product_id']) || isset($_GET['from_cart'])) {
    // For single product
    if (isset($_GET['product_id'])) {
        $product_id = $_GET['product_id'];
        $stmt = $pdo->prepare("INSERT INTO orders (buyer_id, product_id) VALUES (?, ?)");
        $stmt->execute([$_SESSION['user_id'], $product_id]);
        echo "Order placed (COD). Awaiting seller approval.";
    }
    // For cart
    if (isset($_GET['from_cart'])) {
        $cart = $pdo->prepare("SELECT * FROM cart WHERE buyer_id = ?");
        $cart->execute([$_SESSION['user_id']]);
        $cart = $cart->fetchAll();
        foreach ($cart as $item) {
            $stmt = $pdo->prepare("INSERT INTO orders (buyer_id, product_id, quantity) VALUES (?, ?, ?)");
            $stmt->execute([$_SESSION['user_id'], $item['product_id'], $item['quantity']]);
        }
        $pdo->prepare("DELETE FROM cart WHERE buyer_id = ?")->execute([$_SESSION['user_id']]);
        echo "Cart ordered (COD). Awaiting approvals.";
    }
} else {
    echo "Invalid access.";
}
?>
<!DOCTYPE html>
<html>
<head><title>Checkout</title><link rel="stylesheet" href="../assets/css/style.css"></head>
<body>
<a href="dashboard.php">Back to Dashboard</a>
<div class="container">
  <h1>Checkout</h1>
  <p>Your order has been placed successfully!</p>
</div>

</body>
</html>