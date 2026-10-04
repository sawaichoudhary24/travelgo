<?php

require_once "config/database.php";

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

$error = "";
$success = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $name = trim($_POST["name"] ?? "");
    $email = trim($_POST["email"] ?? "");
        $password = $_POST["password"] ?? "";
    $confirm_password = $_POST["confirm_password"] ?? "";

    if ($name === "" || $email === "" || $password === "" || $confirm_password === "") {

        $error = "Please fill all required fields.";

    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

        $error = "Please enter a valid email address.";

    } elseif (strlen($password) < 6) {

        $error = "Password must be at least 6 characters.";

    } elseif ($password !== $confirm_password) {

        $error = "Passwords do not match.";

    } else {

        $checkStmt = $pdo->prepare("
            SELECT id
            FROM users
            WHERE email = ?
            LIMIT 1
        ");

        $checkStmt->execute([$email]);

        if ($checkStmt->fetch()) {

            $error = "This email is already registered.";

        } else {

            $hashedPassword = password_hash(
                $password,
                PASSWORD_DEFAULT
            );

            $stmt = $pdo->prepare("
                INSERT INTO users
                (
                    name,
                    email,
                    password,
                    role
                )
                VALUES (?, ?, ?, 'user')
            ");

            $stmt->execute([
                $name,
                $email,
                $hashedPassword
            ]);

            $success = "Registration successful! You can now login.";

            $_POST = [];
        }
    }
}

require_once "includes/header.php";
require_once "includes/navbar.php";

?>

<section class="min-h-[80vh] flex items-center justify-center px-4 py-16">

    <div class="w-full max-w-lg">

        <div class="bg-white rounded-3xl shadow-xl border border-slate-200 p-8 md:p-10">

            <div class="text-center mb-8">

                <div class="w-16 h-16 bg-sky-100 text-sky-500 rounded-2xl flex items-center justify-center mx-auto mb-4">
                    <i class="fa-solid fa-user-plus text-2xl"></i>
                </div>

                <h1 class="text-3xl font-extrabold text-slate-900">
                    Create Account
                </h1>

                <p class="text-slate-500 mt-2">
                    Join TravelGo and start planning your trip.
                </p>

            </div>

            <?php if ($error): ?>

                <div class="mb-6 rounded-xl bg-red-50 border border-red-200 text-red-700 px-4 py-3">
                    <i class="fa-solid fa-circle-exclamation mr-2"></i>
                    <?php echo htmlspecialchars($error); ?>
                </div>

            <?php endif; ?>

            <?php if ($success): ?>

                <div class="mb-6 rounded-xl bg-green-50 border border-green-200 text-green-700 px-4 py-3">
                    <i class="fa-solid fa-circle-check mr-2"></i>
                    <?php echo htmlspecialchars($success); ?>

                    <div class="mt-2">
                        <a
                            href="login.php"
                            class="font-bold underline"
                        >
                            Login Now
                        </a>
                    </div>
                </div>

            <?php endif; ?>

            <form method="POST" class="space-y-5">

                <!-- Name -->

                <div>

                    <label class="block text-sm font-semibold text-slate-700 mb-2">
                        Full Name *
                    </label>

                    <div class="relative">

                        <i class="fa-solid fa-user absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>

                        <input
                            type="text"
                            name="name"
                            value="<?php echo htmlspecialchars($_POST['name'] ?? ''); ?>"
                            placeholder="Enter your full name"
                            required
                            class="w-full pl-11 pr-4 py-3.5 rounded-xl border border-slate-300 focus:border-sky-500 focus:ring-2 focus:ring-sky-100 outline-none"
                        >

                    </div>

                </div>

                <!-- Email -->

                <div>

                    <label class="block text-sm font-semibold text-slate-700 mb-2">
                        Email Address *
                    </label>

                    <div class="relative">

                        <i class="fa-solid fa-envelope absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>

                        <input
                            type="email"
                            name="email"
                            value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>"
                            placeholder="Enter your email"
                            required
                            class="w-full pl-11 pr-4 py-3.5 rounded-xl border border-slate-300 focus:border-sky-500 focus:ring-2 focus:ring-sky-100 outline-none"
                        >

                    </div>

                </div>

                <!-- Password -->

                <!-- Password -->

                <div>

                    <label class="block text-sm font-semibold text-slate-700 mb-2">
                        Password *
                    </label>

                    <div class="relative">

                        <i class="fa-solid fa-lock absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>

                        <input
                            type="password"
                            name="password"
                            id="password"
                            placeholder="Minimum 6 characters"
                            required
                            class="w-full pl-11 pr-12 py-3.5 rounded-xl border border-slate-300 focus:border-sky-500 focus:ring-2 focus:ring-sky-100 outline-none"
                        >

                        <button
                            type="button"
                            onclick="togglePassword('password', 'passwordIcon')"
                            class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-400"
                        >
                            <i id="passwordIcon" class="fa-solid fa-eye"></i>
                        </button>

                    </div>

                </div>

                <!-- Confirm Password -->

                <div>

                    <label class="block text-sm font-semibold text-slate-700 mb-2">
                        Confirm Password *
                    </label>

                    <div class="relative">

                        <i class="fa-solid fa-lock absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>

                        <input
                            type="password"
                            name="confirm_password"
                            id="confirm_password"
                            placeholder="Confirm your password"
                            required
                            class="w-full pl-11 pr-12 py-3.5 rounded-xl border border-slate-300 focus:border-sky-500 focus:ring-2 focus:ring-sky-100 outline-none"
                        >

                        <button
                            type="button"
                            onclick="togglePassword('confirm_password', 'confirmIcon')"
                            class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-400"
                        >
                            <i id="confirmIcon" class="fa-solid fa-eye"></i>
                        </button>

                    </div>

                </div>

                <!-- Submit -->

                <button
                    type="submit"
                    class="w-full bg-sky-500 hover:bg-sky-600 text-white font-bold py-3.5 rounded-xl transition"
                >
                    <i class="fa-solid fa-user-plus mr-2"></i>
                    Create Account
                </button>

            </form>

            <div class="text-center mt-7 pt-6 border-t border-slate-200">

                <p class="text-slate-500">

                    Already have an account?

                    <a
                        href="login.php"
                        class="text-sky-600 font-bold hover:text-sky-700"
                    >
                        Login
                    </a>

                </p>

            </div>

        </div>

    </div>

</section>

<script>

function togglePassword(inputId, iconId) {

    const input = document.getElementById(inputId);
    const icon = document.getElementById(iconId);

    if (input.type === "password") {

        input.type = "text";

        icon.classList.remove("fa-eye");
        icon.classList.add("fa-eye-slash");

    } else {

        input.type = "password";

        icon.classList.remove("fa-eye-slash");
        icon.classList.add("fa-eye");

    }

}

</script>

<?php require_once "includes/footer.php"; ?>