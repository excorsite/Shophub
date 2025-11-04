<?php
// Start session (optional, for future authentication integration)
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ShopHub</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.3.0/fonts/remixicon.css" rel="stylesheet" />
</head>
<body class="bg-gray-50 font-sans text-gray-800">
    <!-- Navbar Section -->
    <!-- navbar section -->
 <nav class="bg-gradient-to-r from-blue-600 to-indigo-600 text-white shadow-lg">
    <div class="max-w-7xl mx-auto px-4 py-4 flex items-center justify-between">
      <div class="text-2xl font-bold tracking-wide">ShopHub</div>
      <div class="flex space-x-6">
        <a href="dashboard.php" class="px-4 py-2 rounded-lg hover:bg-white hover:text-blue-600 hover:shadow-md">Home</a>
        <a href="dashboard.php" class="px-4 py-2 rounded-lg hover:bg-white hover:text-blue-600 hover:shadow-md">Dashboard</a>
        <a href="browse.php" class="px-4 py-2 rounded-lg hover:bg-white hover:text-blue-600 hover:shadow-md">Menu</a>
        <a href="cart.php" class="px-4 py-2 rounded-lg hover:bg-white hover:text-blue-600 hover:shadow-md">Cart</a>
        <a href="wishlist.php" class="px-4 py-2 rounded-lg hover:bg-white hover:text-blue-600 hover:shadow-md">Wishlist</a>
        <a href="../profile.php" class="px-4 py-2 rounded-lg hover:bg-white hover:text-blue-600 hover:shadow-md">Profile</a>
        <a href="logout.php" class="px-4 py-2 rounded-lg hover:bg-white hover:text-blue-600 hover:shadow-md">Logout</a>
      </div>
    </div>
  </nav>

    <!-- Header Section -->
    <header class="relative w-full h-screen">
        <div class="absolute inset-0">
            <img src="./assets/uploads/header_img.png" alt="shophub" class="w-full h-full object-cover opacity-90" />
            <div class="absolute inset-0 bg-black opacity-40"></div>
        </div>
        <div class="relative z-10 flex items-center justify-center h-full text-center px-4">
            <div>
                <h1 class="text-white text-4xl md:text-5xl lg:text-6xl font-bold mb-4 drop-shadow-lg">Fresh Food Served Right on ShopHub</h1>
                <p class="text-white text-lg md:text-xl mb-6 max-w-xl mx-auto drop-shadow-md">
                    Enjoy a variety of delicious meals and snacks prepared for everyone.
                </p>
                <a href="#service">
                    <button class="bg-green-600 text-white px-6 py-3 rounded-lg hover:bg-green-700 transition duration-300 text-lg font-semibold shadow-md hover:shadow-lg">
                        Get Started
                    </button>
                </a>
            </div>
        </div>
    </header>

    <!-- Service Section -->
    <section class="py-16 bg-white" id="service">
        <div class="container mx-auto px-4 text-center">
            <p class="text-green-600 font-semibold uppercase tracking-wide mb-2">What We Offer</p>
            <h2 class="text-3xl md:text-4xl font-bold mb-10">Quick and Tasty Meals</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 max-w-6xl mx-auto">
                <div class="bg-gray-50 p-6 rounded-lg shadow-md hover:shadow-lg transition-shadow duration-300">
                    <i class="ri-restaurant-2-line text-4xl text-green-600 mb-4"></i>
                    <h4 class="text-xl font-semibold mb-2 text-gray-800">Variety of Options</h4>
                    <p class="text-gray-600">From snacks to full meals, we cater to all tastes.</p>
                </div>
                <div class="bg-gray-50 p-6 rounded-lg shadow-md hover:shadow-lg transition-shadow duration-300">
                    <i class="ri-time-line text-4xl text-green-600 mb-4"></i>
                    <h4 class="text-xl font-semibold mb-2 text-gray-800">Quick Service</h4>
                    <p class="text-gray-600">Grab your meal in minutes during breaktimes.</p>
                </div>
                <div class="bg-gray-50 p-6 rounded-lg shadow-md hover:shadow-lg transition-shadow duration-300">
                    <i class="ri-leaf-line text-4xl text-green-600 mb-4"></i>
                    <h4 class="text-xl font-semibold mb-2 text-gray-800">Fresh Ingredients</h4>
                    <p class="text-gray-600">We prioritize fresh and healthy ingredients for every dish.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer Section -->
    <?php include 'assets/headersection/footer.php'; ?>
</body>
</html>