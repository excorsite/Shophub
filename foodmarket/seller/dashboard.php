<?php
include '../includes/db.php';
include '../includes/functions.php';
if (!isSeller()) redirect('../login.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Seller Dashboard</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <link rel="stylesheet" href="../assets/css/seller.css">
</head>
<body class="bg-white text-gray-800 min-h-screen flex flex-col">

  <!-- Navbar -->
  <nav class="bg-gradient-to-r from-blue-600 to-indigo-600 text-white shadow-lg">
    <div class="max-w-7xl mx-auto px-4 py-4 flex items-center justify-between">
      <div class="text-2xl font-bold tracking-wide">ShopHub</div>
      <div class="flex space-x-6">
        <a href="../home.php" class="px-4 py-2 rounded-lg hover:bg-white hover:text-blue-600 hover:shadow-md">Home</a>
        <a href="dashboard.php" class="px-4 py-2 rounded-lg hover:bg-white hover:text-blue-600 hover:shadow-md">Dashboard</a>
        <a href="add_product.php" class="px-4 py-2 rounded-lg hover:bg-white hover:text-blue-600 hover:shadow-md">Add Product</a>
        <a href="orders.php" class="px-4 py-2 rounded-lg hover:bg-white hover:text-blue-600 hover:shadow-md">Manage Orders</a>
        <a href="Sell_profile.php" class="px-4 py-2 rounded-lg hover:bg-white hover:text-blue-600 hover:shadow-md">Profile</a>
        <a href="logout.php" class="px-4 py-2 rounded-lg hover:bg-white hover:text-blue-600 hover:shadow-md">Logout</a>
      </div>
    </div>
  </nav>

  <!-- Main Content -->
  <main class="flex-grow">
    <div class="max-w-6xl mx-auto px-6 py-10">
      <h1 class="text-3xl font-bold mb-6 text-center">Seller Dashboard</h1>

      <div class="bg-gray-100 p-6 rounded-lg shadow-md text-center">
        <p class="text-lg">Welcome back, seller! Use the tools below to manage your store and track performance.</p>
      </div>

      <!-- View Bar -->
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-10">
        <div class="bg-white border-l-4 border-blue-500 shadow-md p-6 rounded-lg hover:shadow-lg transition">
          <h2 class="text-xl font-semibold mb-2">Add Product</h2>
          <p class="text-gray-600 mb-4">Create and publish new products to your store.</p>
          <a href="add_product.php" class="inline-block bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">Add Now</a>
        </div>
        <div class="bg-white border-l-4 border-green-500 shadow-md p-6 rounded-lg hover:shadow-lg transition">
          <h2 class="text-xl font-semibold mb-2">User Purchases</h2>
          <p class="text-gray-600 mb-4">View products purchased by users from your store.</p>
          <a href="sellerview.php" class="inline-block bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600">View Purchases</a>
        </div>
        <div class="bg-white border-l-4 border-yellow-500 shadow-md p-6 rounded-lg hover:shadow-lg transition">
          <h2 class="text-xl font-semibold mb-2">Sold Products</h2>
          <p class="text-gray-600 mb-4">Track all products that have been sold.</p>
          <a href="sold_products.php" class="inline-block bg-yellow-500 text-white px-4 py-2 rounded hover:bg-yellow-600">View Sales</a>
        </div>
        <div class="bg-white border-l-4 border-purple-500 shadow-md p-6 rounded-lg hover:shadow-lg transition">
          <h2 class="text-xl font-semibold mb-2">Approved Orders</h2>
          <p class="text-gray-600 mb-4">Manage orders approved by you for delivery.</p>
          <a href="orders.php" class="inline-block bg-purple-500 text-white px-4 py-2 rounded hover:bg-purple-600">Manage Orders</a>
        </div>
      </div>

      <!-- Chart Section -->
      <div class="mt-12 bg-white p-6 rounded-lg shadow-md">
        <h2 class="text-2xl font-bold mb-4 text-center">Monthly Analytics</h2>
        <canvas id="sellerChart" class="w-full h-96"></canvas>
      </div>
    </div>
  </main>

  <!-- Footer -->
  <?php include '../assets/headersection/footer.php'; ?>

  <!-- Chart.js Script -->
  <script>
    const ctx = document.getElementById('sellerChart').getContext('2d');
    const sellerChart = new Chart(ctx, {
      type: 'bar',
      data: {
        labels: ['March', 'April', 'May', 'June', 'July', 'August'],
        datasets: [
          {
            label: 'Products Added',
            data: [12, 19, 14, 20, 16, 22],
            backgroundColor: 'rgba(59, 130, 246, 0.6)'
          },
          {
            label: 'User Purchases',
            data: [8, 15, 10, 18, 12, 20],
            backgroundColor: 'rgba(34, 197, 94, 0.6)'
          },
          {
            label: 'Products Sold',
            data: [5, 12, 9, 14, 10, 17],
            backgroundColor: 'rgba(234, 179, 8, 0.6)'
          },
          {
            label: 'Approved Orders',
            data: [4, 10, 7, 13, 9, 15],
            backgroundColor: 'rgba(168, 85, 247, 0.6)'
          }
        ]
      },
      options: {
        responsive: true,
        plugins: {
          legend: {
            position: 'top'
          },
          title: {
            display: true,
            text: 'Seller Performance (March–August)'
          }
        },
        scales: {
          y: {
            beginAtZero: true
          }
        }
      }
    });
  </script>
</body>
</html>
