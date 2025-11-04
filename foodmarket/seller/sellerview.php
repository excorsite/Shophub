<?php
include '../includes/db.php';
include '../includes/functions.php';
if (!isSeller()) redirect('../login.php');


// Assume seller is logged in and seller_id is stored in session
$seller_id = $_SESSION['seller_id'] ?? null;

if (!$seller_id) {
    echo "Unauthorized access.";
    exit;
}

try {
    // Fetch purchases of products owned by this seller
    $stmt = $pdo->prepare("
        SELECT 
            o.order_id,
            o.order_date,
            u.name AS buyer_name,
            m.name AS product_name,
            od.quantity,
            m.price,
            (od.quantity * m.price) AS total_price
        FROM orders o
        JOIN order_details od ON o.order_id = od.order_id
        JOIN menu_items m ON od.item_id = m.item_id
        JOIN users u ON o.buyer_id = u.user_id
        WHERE m.restaurant_id IN (
            SELECT restaurant_id FROM restaurants WHERE owner_id = :seller_id
        )
        ORDER BY o.order_date DESC
    ");
    $stmt->execute(['seller_id' => $seller_id]);
    $purchases = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error fetching purchases: " . $e->getMessage();
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Seller - User Purchases</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 p-6">
    <h1 class="text-2xl font-bold mb-4 text-red-600">User Purchases from Your Store</h1>

    <table class="min-w-full bg-white shadow-md rounded-lg overflow-hidden">
        <thead class="bg-red-100">
            <tr>
                <th class="py-2 px-4 text-left">Order ID</th>
                <th class="py-2 px-4 text-left">Date</th>
                <th class="py-2 px-4 text-left">Buyer</th>
                <th class="py-2 px-4 text-left">Product</th>
                <th class="py-2 px-4 text-left">Qty</th>
                <th class="py-2 px-4 text-left">Price</th>
                <th class="py-2 px-4 text-left">Total</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($purchases as $purchase): ?>
                <tr class="border-t">
                    <td class="py-2 px-4"><?= htmlspecialchars($purchase['order_id']) ?></td>
                    <td class="py-2 px-4"><?= htmlspecialchars($purchase['order_date']) ?></td>
                    <td class="py-2 px-4"><?= htmlspecialchars($purchase['buyer_name']) ?></td>
                    <td class="py-2 px-4"><?= htmlspecialchars($purchase['product_name']) ?></td>
                    <td class="py-2 px-4"><?= htmlspecialchars($purchase['quantity']) ?></td>
                    <td class="py-2 px-4">Rs. <?= htmlspecialchars($purchase['price']) ?></td>
                    <td class="py-2 px-4 font-semibold">Rs. <?= htmlspecialchars($purchase['total_price']) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>
