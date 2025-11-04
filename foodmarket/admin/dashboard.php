<?php
include '../includes/db.php';
include '../includes/functions.php';
if (!isAdmin()) redirect('../login.php');

// Fetch data for charts and metrics
$userCount = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
$sellerCount = $pdo->query("SELECT COUNT(*) FROM users WHERE type='seller'")->fetchColumn();
$productCount = $pdo->query("SELECT COUNT(*) FROM products")->fetchColumn();
$orderCount = $pdo->query("SELECT COUNT(*) FROM orders")->fetchColumn();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .metric-card {
            background: linear-gradient(135deg, #ffffff 70%, #f3f4f6 100%);
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            padding: 1.5rem;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .metric-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1);
        }
        .chart-container {
            position: relative;
            height: 400px;
            margin-top: 1rem;
        }
    </style>
</head>
<body class="bg-gray-100 min-h-screen flex flex-col font-sans">
    <!-- Navbar -->
    <nav class="bg-gradient-to-r from-blue-600 to-indigo-600 text-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4 py-4 flex items-center justify-between">
            <div class="text-2xl font-bold tracking-wide">ShopHub</div>
            <div class="flex space-x-6">
                <a href="home.php" class="px-4 py-2 rounded-lg hover:bg-white hover:text-blue-600 hover:shadow-md">Home</a>
                <a href="dashboard.php" class="px-4 py-2 rounded-lg transition duration-300 ease-in-out hover:bg-white hover:text-blue-600 hover:shadow-md">Dashboard</a>
                <a href="sellers.php" class="px-4 py-2 rounded-lg transition duration-300 ease-in-out hover:bg-white hover:text-blue-600 hover:shadow-md">Manager Sellers</a>
                <a href="products.php" class="px-4 py-2 rounded-lg transition duration-300 ease-in-out hover:bg-white hover:text-blue-600 hover:shadow-md">Manage Products</a>
                <a href="logout.php" class="px-4 py-2 rounded-lg transition duration-300 ease-in-out hover:bg-white hover:text-blue-600 hover:shadow-md">Logout</a>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="flex-grow">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
            <h1 class="text-3xl font-bold mb-6 text-center text-gray-800">Admin Dashboard</h1>

            <!-- Additional Navigation -->
            <!-- <nav class="flex justify-center gap-6 mb-10">
                <a href="sellers.php" class="text-blue-600 hover:underline font-medium">Manage Sellers</a>
                <a href="products.php" class="text-blue-600 hover:underline font-medium">Manage Products</a>
                <a href="logout.php" class="text-red-500 hover:underline font-medium">Logout</a>
            </nav> -->

            <!-- Metrics Section -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">
                <div class="metric-card">
                    <h3 class="text-lg font-semibold text-gray-700">Total Users</h3>
                    <p class="text-2xl font-bold text-blue-600 mt-2"><?php echo htmlspecialchars($userCount); ?></p>
                </div>
                <div class="metric-card">
                    <h3 class="text-lg font-semibold text-gray-700">Total Sellers</h3>
                    <p class="text-2xl font-bold text-green-600 mt-2"><?php echo htmlspecialchars($sellerCount); ?></p>
                </div>
                <div class="metric-card">
                    <h3 class="text-lg font-semibold text-gray-700">Total Products</h3>
                    <p class="text-2xl font-bold text-yellow-600 mt-2"><?php echo htmlspecialchars($productCount); ?></p>
                </div>
                <div class="metric-card">
                    <h3 class="text-lg font-semibold text-gray-700">Total Orders</h3>
                    <p class="text-2xl font-bold text-red-600 mt-2"><?php echo htmlspecialchars($orderCount); ?></p>
                </div>
            </div>

            <!-- Monitoring Charts -->
            <div class="bg-white p-6 rounded-lg shadow-md">
                <h2 class="text-xl font-semibold mb-4 text-gray-800">Monitoring Charts</h2>
                <div class="chart-container">
                    <canvas id="monitoringChart"></canvas>
                </div>
            </div>

            <script>
                // Chart.js configuration
                const ctx = document.getElementById('monitoringChart').getContext('2d');
                const monitoringChart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: ['Users', 'Sellers', 'Products', 'Orders'],
                        datasets: [{
                            label: 'Counts',
                            data: [<?php echo $userCount; ?>, <?php echo $sellerCount; ?>, <?php echo $productCount; ?>, <?php echo $orderCount; ?>],
                            backgroundColor: ['#3B82F6', '#10B981', '#EAB308', '#EF4444'],
                            borderColor: ['#2563EB', '#059669', '#CA8A04', '#DC2626'],
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    stepSize: 1
                                }
                            }
                        },
                        plugins: {
                            legend: {
                                display: true,
                                position: 'top'
                            },
                            title: {
                                display: true,
                                text: 'Platform Statistics'
                            }
                        }
                    }
                });
            </script>
        </div>
    </main>

    <!-- Footer -->
    <?php include '../assets/headersection/footer.php'; ?>
</body>
</html>