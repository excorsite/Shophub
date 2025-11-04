<?php
include 'includes/db.php';
include 'includes/functions.php';

$error = '';
$success = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = password_hash(trim($_POST['password']), PASSWORD_DEFAULT);
    $type = $_POST['type'];
    $approved = ($type === 'seller') ? 0 : 1;

    if (empty($username) || empty($email) || empty($_POST['password']) || empty($type)) {
        $error = "Please fill in all fields.";
    } else {
        $stmt = $pdo->prepare("INSERT INTO users (username, password, email, type, approved) VALUES (?, ?, ?, ?, ?)");
        if ($stmt->execute([$username, $password, $email, $type, $approved])) {
            $success = "✅ Registered successfully. " . ($type === 'seller' ? "Awaiting approval." : "You can now log in.");
        } else {
            $error = "❌ Error registering. Username or email may already exist.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register - FoodMarket</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">

    <div class="w-full max-w-md bg-white p-8 rounded-lg shadow-md">
        <h2 class="text-2xl font-bold mb-6 text-center text-gray-800">🍽️ Register for FoodMarket</h2>

        <?php if ($error): ?>
            <div class="mb-4 text-red-600 text-center font-medium"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="mb-4 text-green-600 text-center font-medium"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>

        <form method="POST" id="registerForm" onsubmit="return validateForm()" class="space-y-4">
            <div>
                <label for="username" class="block text-sm font-medium text-gray-700">Username</label>
                <input type="text" name="username" id="username" required
                    class="mt-1 w-full px-4 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-400">
            </div>

            <div>
                <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                <input type="email" name="email" id="email" required
                    class="mt-1 w-full px-4 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-400">
            </div>

            <div>
                <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                <input type="password" name="password" id="password" required
                    class="mt-1 w-full px-4 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-400">
            </div>

            <div>
                <label for="type" class="block text-sm font-medium text-gray-700">Account Type</label>
                <select name="type" id="type" required
                    class="mt-1 w-full px-4 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-400">
                    <option value="">Select Type</option>
                    <option value="buyer">Buyer</option>
                    <option value="seller">Seller</option>
                </select>
            </div>

            <button type="submit"
                class="w-full bg-blue-600 text-white py-2 rounded hover:bg-blue-700 transition">Register</button>
        </form>

        <p class="mt-6 text-center text-sm text-gray-600">
            Already have an account?
            <a href="login.php" class="text-blue-600 hover:underline">Login</a>
        </p>
    </div>

    <script>
        function validateForm() {
            const fields = ['username', 'email', 'password', 'type'];
            for (let field of fields) {
                if (!document.forms["registerForm"][field].value.trim()) {
                    alert("Please fill in all fields.");
                    return false;
                }
            }
            return true;
        }
    </script>

</body>
</html>
