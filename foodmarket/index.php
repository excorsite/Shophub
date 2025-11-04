<?php
include 'includes/functions.php';
if (isLoggedIn()) {
    if (isAdmin()) redirect('admin/dashboard.php');
    if (isSeller()) redirect('seller/dashboard.php');
    if (isBuyer()) redirect('buyer/dashboard.php');
}
redirect('login.php');
?>