<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
$role = $_SESSION['role'] ?? null; // 'buyer' | 'seller' | 'admin'
$cartCount = isset($_SESSION['cart']) ? array_sum(array_column($_SESSION['cart'], 'qty')) : 0;
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1"/>
  <title><?php echo isset($pageTitle) ? $pageTitle.' • ' : ''; ?>FOS</title>
  <link rel="stylesheet" href="/assets/css/global.css">
  <!-- role-specific optional css -->
  <?php if($role==='admin'): ?><link rel="stylesheet" href="/assets/css/admin.css"><?php endif; ?>
  <?php if($role==='seller'): ?><link rel="stylesheet" href="/assets/css/seller.css"><?php endif; ?>
  <?php if($role==='buyer' || !$role): ?><link rel="stylesheet" href="/assets/css/buyer.css"><?php endif; ?>
  <script defer src="/assets/js/app.js"></script>
</head>
<body>
<header class="navbar">
  <div class="nav-inner container">
    <a class="brand" href="/index.php">
      <span class="logo" aria-hidden="true"><?php echo @file_get_contents($_SERVER['DOCUMENT_ROOT'].'/assets/icons/logo.svg'); ?></span>
      Food Ordering System <span class="badge">v2</span>
    </a>

    <div class="nav-actions">
      <?php if($role==='buyer' || !$role): ?>
        <input id="searchInput" class="input" style="max-width:280px" placeholder="Search dishes, categories…" />
        <a class="btn ghost" href="/buyer/browse.php">Browse</a>
        <a class="btn" href="/buyer/cart.php">Cart <span class="badge" id="cartCount"><?php echo (int)$cartCount; ?></span></a>
      <?php endif; ?>

      <?php if($role==='seller'): ?>
        <a class="btn ghost" href="/seller/dashboard.php">Seller Dashboard</a>
        <a class="btn ghost" href="/seller/add_product.php">Add Product</a>
        <a class="btn" href="/logout.php">Logout</a>
      <?php elseif($role==='admin'): ?>
        <a class="btn ghost" href="/admin/dashboard.php">Admin</a>
        <a class="btn ghost" href="/admin/products.php">Products</a>
        <a class="btn ghost" href="/admin/sellers.php">Sellers</a>
        <a class="btn" href="/admin/logout.php">Logout</a>
      <?php else: ?>
        <a class="btn ghost" href="/login.php">Login</a>
        <a class="btn secondary" href="/register.php">Register</a>
      <?php endif; ?>
    </div>
  </div>
</header>
<main class="container">
