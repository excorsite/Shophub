<?php
include 'includes/db.php';
include 'includes/functions.php';

$message = '';
$forgot_message = '';
$forgot_error = '';
$show_forgot = isset($_GET['forgot']);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action']) && $_POST['action'] === 'forgot') {
        $forgot_username = trim($_POST['forgot_username']);
        $stmt = $pdo->prepare("SELECT id, email FROM users WHERE username = ?");
        $stmt->execute([$forgot_username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && !empty($user['email'])) {
            // Generate secure token
            $token = bin2hex(random_bytes(50));
            $expires = date('Y-m-d H:i:s', strtotime('+24 hours'));

            // Update user with token and expiration
            $stmt_update = $pdo->prepare("UPDATE users SET reset_token = ?, reset_expires = ? WHERE id = ?");
            $stmt_update->execute([$token, $expires, $user['id']]);

            // Reset link (assumes a separate reset.php file exists)
            $reset_link = "http://" . $_SERVER['HTTP_HOST'] . "/reset.php?token=" . $token;

            $subject = "Password Reset Request";
            $body = "Hello,\n\nYou have requested a password reset for your account.\n\nPlease click the following link to reset your password:\n" . $reset_link . "\n\nThis link will expire in 24 hours.\n\nIf you did not request this, please ignore this email.\n\nBest regards,\nYour Application Team";

            // Send email using PHP's mail() function (configure your server for Gmail SMTP if needed)
            if (mail($user['email'], $subject, $body, "From: noreply@yourdomain.com")) {
                $forgot_message = "✅ A password reset link has been sent to your email address. Please check your inbox (including spam).";
            } else {
                $forgot_error = "❌ Failed to send the reset email. Please try again later or contact support.";
            }
        } else {
            $forgot_error = "❌ No account found with that username, or email not configured.";
        }
    } else {
        // Original login handling
        $username = trim($_POST['username']);
        $password = $_POST['password'];

        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_type'] = $user['type'];
            $_SESSION['approved'] = $user['approved'];

            if ($user['type'] === 'admin') redirect('admin/dashboard.php');
            if ($user['type'] === 'seller' && $user['approved']) redirect('seller/dashboard.php');
            if ($user['type'] === 'buyer') redirect('buyer/dashboard.php');

            $message = "⏳ Pending approval or invalid user type.";
        } else {
            $message = "❌ Invalid username or password.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">

    <div class="w-full max-w-md bg-white p-8 rounded-lg shadow-md">
        <h2 class="text-2xl font-bold mb-6 text-center text-gray-800">🔐 Login to Your Account</h2>

        <?php if (!empty($message)): ?>
            <div class="mb-4 text-center text-red-600 font-medium"><?php echo $message; ?></div>
        <?php endif; ?>

        <form method="POST" class="space-y-4">
            <div>
                <label for="username" class="block text-sm font-medium text-gray-700">Username</label>
                <input type="text" name="username" id="username" required
                    class="mt-1 w-full px-4 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-400">
            </div>

            <div>
                <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                <input type="password" name="password" id="password" required
                    class="mt-1 w-full px-4 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-400">
            </div>

            <button type="submit"
                class="w-full bg-blue-600 text-white py-2 rounded hover:bg-blue-700 transition">Login</button>
        </form>

        <div class="mt-6 text-center">
            <a href="register.php" class="text-blue-600 hover:underline">Don't have an account? Register</a>
             <br>
            <a href="?forgot=1" class="text-blue-600 hover:underline">Forgot your password?</a>
        </div>

        <!-- <?php if ($show_forgot): ?>
            <div class="mt-8 pt-6 border-t border-gray-200">
                <h3 class="text-lg font-semibold mb-4 text-center text-gray-800">🔑 Reset Your Password</h3>

                <?php if (!empty($forgot_message)): ?>
                    <div class="mb-4 p-3 bg-green-50 border border-green-200 rounded text-green-700 text-sm"><?php echo $forgot_message; ?></div>
                <?php endif; ?>

                <?php if (!empty($forgot_error)): ?>
                    <div class="mb-4 p-3 bg-red-50 border border-red-200 rounded text-red-700 text-sm"><?php echo $forgot_error; ?></div>
                <?php endif; ?>

                <form method="POST" class="space-y-4">
                    <input type="hidden" name="action" value="forgot">
                    <div>
                        <label for="forgot_username" class="block text-sm font-medium text-gray-700">Enter your username</label>
                        <input type="text" name="forgot_username" id="forgot_username" required
                            class="mt-1 w-full px-4 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-green-400"
                            placeholder="Your username">
                    </div>
                    <button type="submit"
                        class="w-full bg-green-600 text-white py-2 rounded hover:bg-green-700 transition">Send Reset Link</button>
                </form>

                <div class="mt-4 text-center">
                    <a href="?=" class="text-blue-600 hover:underline">Back to Login</a>
                </div>
            </div>
        <?php endif; ?>
    </div> -->

</body>
</html>