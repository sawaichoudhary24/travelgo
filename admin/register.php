<?php
session_start();
require_once "../config/database.php";

$error = "";
$success = "";

// Admin registration is separate from normal website registration.
// Keep this code private in a real deployment.
$adminCode = "TRAVELGO-ADMIN-2026";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';
    $code = trim($_POST['admin_code'] ?? '');

    if ($name === '' || $email === '' || $password === '' || $confirm === '' || $code === '') {
        $error = 'Please fill all fields.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } elseif (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters.';
    } elseif ($password !== $confirm) {
        $error = 'Passwords do not match.';
    } elseif (!hash_equals($adminCode, $code)) {
        $error = 'Invalid admin registration code.';
    } else {
        $stmt = $pdo->prepare('SELECT id FROM users WHERE email = ? LIMIT 1');
        $stmt->execute([$email]);

        if ($stmt->fetch()) {
            $error = 'An account with this email already exists.';
        } else {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            $stmt = $pdo->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, 'admin')");
            $stmt->execute([$name, $email, $hashedPassword]);

            $success = 'Admin account created successfully. You can now login.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Register - TravelGo</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
</head>
<body class="min-h-screen bg-slate-100 flex items-center justify-center px-4 py-10">
<div class="w-full max-w-md">
    <div class="text-center mb-7">
        <div class="w-16 h-16 rounded-2xl bg-sky-500 flex items-center justify-center mx-auto shadow-lg">
            <i class="fa-solid fa-user-shield text-white text-2xl"></i>
        </div>
        <h1 class="text-3xl font-extrabold text-slate-900 mt-5">Travel<span class="text-sky-500">Go</span></h1>
        <p class="text-slate-500 mt-1">Admin Panel</p>
    </div>

    <div class="bg-white rounded-2xl shadow-xl border border-slate-200 p-7 sm:p-8">
        <h2 class="text-2xl font-extrabold text-slate-900">Create Admin Account</h2>
        <p class="text-slate-500 text-sm mt-1 mb-6">Register a new TravelGo administrator.</p>

        <?php if ($error): ?>
            <div class="bg-red-50 border border-red-200 text-red-700 rounded-xl p-4 mb-5 text-sm">
                <i class="fa-solid fa-circle-exclamation mr-2"></i><?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="bg-green-50 border border-green-200 text-green-700 rounded-xl p-4 mb-5 text-sm">
                <i class="fa-solid fa-circle-check mr-2"></i><?= htmlspecialchars($success) ?>
            </div>
        <?php endif; ?>

        <form method="POST" class="space-y-4">
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">Full Name</label>
                <input type="text" name="name" required value="<?= htmlspecialchars($_POST['name'] ?? '') ?>" class="w-full border border-slate-300 rounded-xl px-4 py-3.5 outline-none focus:ring-2 focus:ring-sky-500" placeholder="Admin name">
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">Email Address</label>
                <input type="email" name="email" required value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" class="w-full border border-slate-300 rounded-xl px-4 py-3.5 outline-none focus:ring-2 focus:ring-sky-500" placeholder="admin@example.com">
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">Password</label>
                <input type="password" name="password" required minlength="6" class="w-full border border-slate-300 rounded-xl px-4 py-3.5 outline-none focus:ring-2 focus:ring-sky-500" placeholder="Minimum 6 characters">
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">Confirm Password</label>
                <input type="password" name="confirm_password" required minlength="6" class="w-full border border-slate-300 rounded-xl px-4 py-3.5 outline-none focus:ring-2 focus:ring-sky-500" placeholder="Repeat password">
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">Admin Registration Code</label>
                <input type="password" name="admin_code" required class="w-full border border-slate-300 rounded-xl px-4 py-3.5 outline-none focus:ring-2 focus:ring-sky-500" placeholder="Enter admin code">
            </div>
            <button type="submit" class="w-full bg-sky-500 hover:bg-sky-600 text-white font-bold py-3.5 rounded-xl transition">
                <i class="fa-solid fa-user-plus mr-2"></i> Register Admin
            </button>
        </form>

        <div class="text-center mt-6 pt-5 border-t border-slate-200">
            <a href="index.php" class="text-sky-600 font-bold hover:text-sky-700">← Back to Admin Login</a>
        </div>
    </div>
</div>
</body>
</html>
