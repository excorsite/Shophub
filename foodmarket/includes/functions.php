<?php
session_start();

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function isAdmin() {
    return isLoggedIn() && $_SESSION['user_type'] === 'admin';
}

function isSeller() {
    return isLoggedIn() && $_SESSION['user_type'] === 'seller' && $_SESSION['approved'] == 1;
}

function isBuyer() {
    return isLoggedIn() && $_SESSION['user_type'] === 'buyer';
}

function redirect($url) {
    header("Location: $url");
    exit();
}

function uploadImage($file) {
    $targetDir = "../assets/uploads/";
    $targetFile = $targetDir . basename($file["name"]);
    if (move_uploaded_file($file["tmp_name"], $targetFile)) {
        return basename($file["name"]);
    }
    return false;
}

// Add more helpers as needed
?>