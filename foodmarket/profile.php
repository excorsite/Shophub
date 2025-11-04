<?php
// Start session
session_start();

// Database connection parameters
$servername = "127.0.0.1";
$username = "root"; // Change to your MySQL username
$password = ""; // Change to your MySQL password
$dbname = "foodmarket";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    die("Please log in to view your profile.");
}

$user_id = $_SESSION['user_id'];

// Query to get user details
$user_sql = "SELECT username, email, type, created_at FROM users WHERE id = ?";
$stmt_user = $conn->prepare($user_sql);
$stmt_user->bind_param("i", $user_id);
$stmt_user->execute();
$user_result = $stmt_user->get_result();
$user = $user_result->fetch_assoc();
$stmt_user->close();

// Query to get user's purchased products (approved/delivered orders)
$order_sql = "
    SELECT 
        o.id AS order_id,
        p.id AS product_id,
        p.name AS product_name,
        p.price,
        p.food_type,
        o.quantity,
        o.order_date
    FROM 
        orders o
    INNER JOIN 
        products p ON o.product_id = p.id
    WHERE 
        o.buyer_id = ? AND o.status IN ('approved', 'delivered')
    ORDER BY 
        o.order_date DESC
";
$stmt_order = $conn->prepare($order_sql);
$stmt_order->bind_param("i", $user_id);
$stmt_order->execute();
$order_result = $stmt_order->get_result();
$stmt_order->close();

// Close connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 font-sans">
    <!-- Header -->
    <header class="bg-green-600 text-white p-4 text-center">
        <h1 class="text-3xl font-bold">User Profile</h1>
    </header>

    <!-- Main Content -->
    <main class="container mx-auto p-6">
        <!-- Profile Section -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6 flex items-center space-x-6">
            <div class="w-20 h-20 bg-gray-300 rounded-full flex items-center justify-center">
                <span class="text-gray-600 text-lg">👤</span>
            </div>
            <div>
                <h2 class="text-2xl font-semibold text-green-600"><?php echo htmlspecialchars($user['username']); ?></h2>
                <p class="text-gray-600">Email: <?php echo htmlspecialchars($user['email']); ?></p>
                <p class="text-gray-500">Type: <?php echo htmlspecialchars($user['type']); ?></p>
                <p class="text-gray-500">Joined: <?php echo date('F d, Y', strtotime($user['created_at'])); ?></p>
            </div>
        </div>

        <!-- Purchased Products Section -->
        <h2 class="text-2xl font-semibold text-green-600 mb-4">Purchased Products</h2>
        <?php if ($order_result->num_rows > 0): ?>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php while ($row = $order_result->fetch_assoc()): ?>
                    <div class="bg-white rounded-lg shadow-md hover:shadow-lg transition-shadow duration-300 p-4">
                        <h3 class="text-lg font-semibold text-green-600"><?php echo htmlspecialchars($row['product_name']); ?></h3>
                        <p class="text-gray-600">ID: <?php echo htmlspecialchars($row['product_id']); ?></p>
                        <p class="text-gray-700">Price: Rs<?php echo number_format($row['price'], 2); ?></p>
                        <p class="text-gray-500">Type: <?php echo htmlspecialchars($row['food_type']); ?></p>
                        <p class="text-green-500 font-bold">Qty: <?php echo htmlspecialchars($row['quantity']); ?></p>
                        <p class="text-gray-400 text-sm">Ordered: <?php echo date('F d, Y', strtotime($row['order_date'])); ?></p>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                <span class="block sm:inline">No purchased products found.</span>
            </div>
        <?php endif; ?>
    </main>

    <!-- Footer -->
    <footer class="bg-gray-800 text-white p-4 text-center mt-6">
        <p>&copy; <?php echo date('Y'); ?> FoodMarket. All rights reserved. | Generated: <?php echo date('F d, Y h:i A T'); ?></p>
    </footer>
</body>
</html>