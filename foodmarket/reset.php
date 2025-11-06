<?php
include 'includes/db.php';
include 'includes/functions.php';

$token = $_GET['token'] ?? '';
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $new_password = $_POST['new_password'];
    $stmt = $pdo->prepare("SELECT id FROM users WHERE reset_token = ? AND reset_expires > NOW()");
    $stmt->execute([$token]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        $hashed = password_hash($new_password, PASSWORD_DEFAULT);
        $stmt_update = $pdo->prepare("UPDATE users SET password = ?, reset_token = NULL, reset_expires = NULL WHERE id = ?");
        $stmt_update->execute([$hashed, $user['id']]);
        $success = "✅ Password reset successfully! You can now <a href='login.php'>login</a>.";
    } else {
        $error = "❌ Invalid or expired token.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Reset Password</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">
    <div class="w-full max-w-md bg-white p-8 rounded-lg shadow-md">
        <h2 class="text-2xl font-bold mb-6 text-center text-gray-800">🔑 Reset Password</h2>
        <?php if ($error): ?><div class="mb-4 text-red-600"><?php echo $error; ?></div><?php endif; ?>
        <?php if ($success): ?><div class="mb-4 text-green-600"><?php echo $success; ?></div><?php else: ?>
        <form method="POST" class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700">New Password</label>
                <input type="password" name="new_password" required class="mt-1 w-full px-4 py-2 border rounded">
            </div>
            <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded">Reset</button>
        </form>
        <?php endif; ?>
    </div>
</body>
</html>