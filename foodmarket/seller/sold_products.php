<?php
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

// Query to track sold products: Join orders with products, sum quantities for approved/delivered orders
$sql = "
    SELECT 
        p.id AS product_id,
        p.name AS product_name,
        p.description,
        p.price,
        p.food_type,
        o.order_date,
        SUM(o.quantity) AS total_sold
    FROM 
        orders o
    INNER JOIN 
        products p ON o.product_id = p.id
    WHERE 
        o.status IN ('approved', 'delivered')
    GROUP BY 
        p.id
    ORDER BY 
        total_sold DESC
";

$result = $conn->query($sql);

// Close connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sold Products Tracker</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 font-sans">

    <!-- Nav bar -->
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
   
     <!--Header Main Content -->
  <main class="flex-grow">
    <div class="max-w-6xl mx-auto px-6 py-10">
      <h1 class="text-3xl font-bold mb-6 text-center">Sold Products Tracker</h1>

      <div class="bg-gray-100 p-6 rounded-lg shadow-md text-center">
        <p class="text-lg">Welcome back, seller! This page tracks all products that have been sold (approved or delivered orders), showing total quantities sold.</p>
      </div>
</main>

    <!-- Main Content -->
    <main class="container mx-auto p-6">
        <?php if (isset($result) && $result->num_rows > 0): ?>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php while ($row = $result->fetch_assoc()): ?>
                    <div class="bg-white rounded-lg shadow-md hover:shadow-lg transition-shadow duration-300 p-4">
                        <h2 class="text-xl font-semibold text-green-600"><?php echo htmlspecialchars($row["product_name"]); ?></h2>
                        <p class="text-gray-600">ID: <?php echo htmlspecialchars($row["product_id"]); ?></p>
                        <p class="text-gray-700">Price: Rs <?php echo number_format($row["price"], 2); ?></p>
                        <p class="text-gray-500">Type: <?php echo htmlspecialchars($row["food_type"]); ?></p>
                        <p class="text-green-500 font-bold mt-2">Sold: <?php echo htmlspecialchars($row["total_sold"]); ?> units</p>
                        <p class="text-gray-400 text-sm">Ordered: <?php echo date('F d, Y', strtotime($row['order_date'])); ?></p>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                <span class="block sm:inline">No sold products found.</span>
            </div>
        <?php endif; ?>
    </main>

    <!-- Footer -->
    <?php include '../assets/headersection/footer.php'; ?>
</body>
</html>