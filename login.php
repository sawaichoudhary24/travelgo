<?php

require_once "config/database.php";

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (isset($_SESSION['user_id'])) {

    if (($_SESSION['user_role'] ?? '') === 'admin') {
        header("Location: admin/dashboard.php");
    } else {
        header("Location: index.php");
    }

    exit;
}

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $email = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";

    if ($email === "" || $password === "") {

        $error = "Please enter email and password.";

    } else {

        $stmt = $pdo->prepare("
            SELECT *
            FROM users
            WHERE email = ?
            LIMIT 1
        ");

        $stmt->execute([$email]);

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (
            $user &&
            password_verify($password, $user["password"])
        ) {

            session_regenerate_id(true);

            $_SESSION["user_id"] = $user["id"];
            $_SESSION["user_name"] = $user["name"];
            $_SESSION["user_email"] = $user["email"];
            $_SESSION["user_role"] = $user["role"];

            if ($user["role"] === "admin") {

                header("Location: admin/dashboard.php");

            } else {

                header("Location: index.php");

            }

            exit;

        } else {

            $error = "Invalid email or password.";

        }
    }
}

require_once "includes/header.php";
require_once "includes/navbar.php";

?>

<section class="min-h-[80vh] flex items-center justify-center px-4 py-16">

    <div class="w-full max-w-md">

        <div class="bg-white rounded-3xl shadow-xl border border-slate-200 p-8 md:p-10">

            <div class="text-center mb-8">

                <div class="w-16 h-16 bg-sky-100 text-sky-500 rounded-2xl flex items-center justify-center mx-auto mb-4">

                    <i class="fa-solid fa-right-to-bracket text-2xl"></i>

                </div>

                <h1 class="text-3xl font-extrabold text-slate-900">
                    Welcome Back
                </h1>

                <p class="text-slate-500 mt-2">
                    Login to your TravelGo account.
                </p>

            </div>

            <?php if ($error): ?>

                <div class="mb-6 rounded-xl bg-red-50 border border-red-200 text-red-700 px-4 py-3">

                    <i class="fa-solid fa-circle-exclamation mr-2"></i>

                    <?php echo htmlspecialchars($error); ?>

                </div>

            <?php endif; ?>

            <form method="POST" class="space-y-5">

                <!-- Email -->

                <div>

                    <label class="block text-sm font-semibold text-slate-700 mb-2">
                        Email Address
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

                <div>

                    <label class="block text-sm font-semibold text-slate-700 mb-2">
                        Password
                    </label>

                    <div class="relative">

                        <i class="fa-solid fa-lock absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>

                        <input
                            type="password"
                            name="password"
                            id="loginPassword"
                            placeholder="Enter your password"
                            required
                            class="w-full pl-11 pr-12 py-3.5 rounded-xl border border-slate-300 focus:border-sky-500 focus:ring-2 focus:ring-sky-100 outline-none"
                        >

                        <button
                            type="button"
                            onclick="toggleLoginPassword()"
                            class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-400"
                        >

                            <i id="loginPasswordIcon" class="fa-solid fa-eye"></i>

                        </button>

                    </div>

                </div>

                <!-- Submit -->

                <button
                    type="submit"
                    class="w-full bg-sky-500 hover:bg-sky-600 text-white font-bold py-3.5 rounded-xl transition"
                >

                    <i class="fa-solid fa-right-to-bracket mr-2"></i>

                    Login

                </button>

            </form>

            <div class="text-center mt-7 pt-6 border-t border-slate-200">

                <p class="text-slate-500">

                    Don't have an account?

                    <a
                        href="register.php"
                        class="text-sky-600 font-bold hover:text-sky-700"
                    >
                        Create Account
                    </a>

                </p>

            </div>

        </div>

    </div>

</section>

<script>

function toggleLoginPassword() {

    const input = document.getElementById("loginPassword");
    const icon = document.getElementById("loginPasswordIcon");

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