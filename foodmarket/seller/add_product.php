<?php
include '../includes/db.php';
include '../includes/functions.php';
if (!isSeller()) redirect('../login.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $food_type = $_POST['food_type'];
    $image = uploadImage($_FILES['image']);

    if ($image) {
        $stmt = $pdo->prepare("INSERT INTO products (name, description, price, image, food_type, seller_id) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$name, $description, $price, $image, $food_type, $_SESSION['user_id']]);
        echo "<div class='text-green-600 text-center mt-4'>✅ Product added successfully.</div>";
    } else {
        echo "<div class='text-red-600 text-center mt-4'>❌ Image upload failed.</div>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Product</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 text-gray-800 min-h-screen flex items-center justify-center">

    <div class="w-full max-w-xl bg-white p-8 rounded-lg shadow-md">
        <h2 class="text-2xl font-bold mb-6 text-center">🍽️ Add New Food Item</h2>

        <form method="POST" enctype="multipart/form-data" class="space-y-4">
            <input type="text" name="name" placeholder="Name" required class="w-full px-4 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-400">

            <textarea name="description" placeholder="Description" class="w-full px-4 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-400"></textarea>

            <input type="number" name="price" placeholder="Price" required class="w-full px-4 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-400">

            <select name="food_type" required class="w-full px-4 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-400">
                <option value="">Select Food Type</option>
                <option value="veg">Veg</option>
                <option value="beverage">Beverage</option>
                <option value="fast-food">Fast Food</option>
                <option value="drink">Drink</option>
                <option value="thai">Thai</option>
                <option value="snack">Snack</option>
                <option value="Desert">Desert</option>
                <option value="Chinese">Chinese</option>
                <option value="japanese">Japanese</option>
                <option value="korean">Korean</option>
                <option value="combo">Combo</option>
                <option value="other">Other</option>
            </select>

            <input type="file" name="image" required class="w-full px-4 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-400">

            <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded hover:bg-blue-700 transition">Add Product</button>
        </form>

        <div class="mt-6 text-center">
            <a href="dashboard.php" class="text-blue-600 hover:underline">← Back to Dashboard</a>
        </div>
    </div>

</body>
</html>
