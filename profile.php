<?php

session_start();

require_once "config/database.php";


/* =========================
   LOGIN CHECK
========================= */

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}


$userId = (int) $_SESSION['user_id'];

$success = "";
$error = "";


/* =========================
   FETCH USER
========================= */

$stmt = $pdo->prepare("
    SELECT id, name, email, phone, role, created_at
    FROM users
    WHERE id = ?
    LIMIT 1
");

$stmt->execute([$userId]);

$user = $stmt->fetch(PDO::FETCH_ASSOC);


if (!$user) {
    session_destroy();
    header("Location: login.php");
    exit;
}


/* =========================
   UPDATE PROFILE
========================= */

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $action = $_POST["action"] ?? "";


    /* =========================
       PROFILE UPDATE
    ========================= */

    if ($action === "update_profile") {

        $name = trim($_POST["name"] ?? "");
        $email = trim($_POST["email"] ?? "");
        $phone = trim($_POST["phone"] ?? "");


        if ($name === "") {

            $error = "Please enter your name.";

        } elseif ($email === "") {

            $error = "Please enter your email.";

        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

            $error = "Please enter a valid email address.";

        } else {

            /* Check duplicate email */

            $emailCheck = $pdo->prepare("
                SELECT id
                FROM users
                WHERE email = ?
                  AND id != ?
                LIMIT 1
            ");

            $emailCheck->execute([
                $email,
                $userId
            ]);


            if ($emailCheck->fetch()) {

                $error = "This email address is already registered.";

            } else {

                $updateStmt = $pdo->prepare("
                    UPDATE users
                    SET name = ?,
                        email = ?,
                        phone = ?
                    WHERE id = ?
                ");

                $updateStmt->execute([
                    $name,
                    $email,
                    $phone,
                    $userId
                ]);


                /* Update session */

                $_SESSION["user_name"] = $name;
                $_SESSION["user_email"] = $email;


                /* Refresh user */

                $stmt = $pdo->prepare("
                    SELECT id, name, email, phone, role, created_at
                    FROM users
                    WHERE id = ?
                    LIMIT 1
                ");

                $stmt->execute([$userId]);

                $user = $stmt->fetch(PDO::FETCH_ASSOC);


                $success = "Your profile has been updated successfully.";

            }

        }

    }


    /* =========================
       PASSWORD UPDATE
    ========================= */

    elseif ($action === "change_password") {

        $currentPassword = $_POST["current_password"] ?? "";
        $newPassword = $_POST["new_password"] ?? "";
        $confirmPassword = $_POST["confirm_password"] ?? "";


        if ($currentPassword === "") {

            $error = "Please enter your current password.";

        } elseif ($newPassword === "") {

            $error = "Please enter your new password.";

        } elseif (strlen($newPassword) < 6) {

            $error = "New password must be at least 6 characters.";

        } elseif ($newPassword !== $confirmPassword) {

            $error = "New password and confirm password do not match.";

        } else {

            /* Get password */

            $passwordStmt = $pdo->prepare("
                SELECT password
                FROM users
                WHERE id = ?
                LIMIT 1
            ");

            $passwordStmt->execute([$userId]);

            $passwordData = $passwordStmt->fetch(PDO::FETCH_ASSOC);


            if (!$passwordData || !password_verify(
                $currentPassword,
                $passwordData["password"]
            )) {

                $error = "Current password is incorrect.";

            } else {

                $hashedPassword = password_hash(
                    $newPassword,
                    PASSWORD_DEFAULT
                );


                $updatePassword = $pdo->prepare("
                    UPDATE users
                    SET password = ?
                    WHERE id = ?
                ");

                $updatePassword->execute([
                    $hashedPassword,
                    $userId
                ]);


                $success = "Your password has been changed successfully.";

            }

        }

    }

}


/* =========================
   BOOKING COUNT
========================= */

$bookingStmt = $pdo->prepare("
    SELECT COUNT(*)
    FROM bookings
    WHERE user_id = ?
");

$bookingStmt->execute([$userId]);

$bookingCount = (int) $bookingStmt->fetchColumn();


/* =========================
   TOTAL SPENT
========================= */

$spentStmt = $pdo->prepare("
    SELECT COALESCE(SUM(total_amount), 0)
    FROM bookings
    WHERE user_id = ?
      AND status != 'cancelled'
");

$spentStmt->execute([$userId]);

$totalSpent = (float) $spentStmt->fetchColumn();


require_once "includes/header.php";
require_once "includes/navbar.php";

?>


<!-- =========================
     HERO
========================= -->

<section class="relative bg-slate-900 text-white overflow-hidden">

    <div class="absolute inset-0 bg-gradient-to-r from-slate-950 via-slate-900 to-sky-900"></div>

    <div class="relative max-w-7xl mx-auto px-6 py-16 md:py-20">

        <div class="max-w-3xl">

            <span class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-sky-500/20 text-sky-300 text-sm font-semibold mb-5">

                <i class="fa-solid fa-user"></i>

                My Account

            </span>


            <h1 class="text-4xl md:text-5xl font-extrabold mb-4">

                My Profile

            </h1>


            <p class="text-slate-300 text-lg">

                Manage your TravelGo account information and password.

            </p>

        </div>

    </div>

</section>


<!-- =========================
     PROFILE SECTION
========================= -->

<section class="py-16 md:py-20 bg-slate-50">

    <div class="max-w-6xl mx-auto px-6">


        <!-- =========================
             SUCCESS / ERROR
        ========================= -->

        <?php if ($success): ?>

            <div class="mb-8 flex items-start gap-3 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-700 px-5 py-4">

                <i class="fa-solid fa-circle-check mt-1"></i>

                <p class="font-medium">
                    <?php echo htmlspecialchars($success); ?>
                </p>

            </div>

        <?php endif; ?>


        <?php if ($error): ?>

            <div class="mb-8 flex items-start gap-3 rounded-xl bg-red-50 border border-red-200 text-red-700 px-5 py-4">

                <i class="fa-solid fa-circle-exclamation mt-1"></i>

                <p class="font-medium">
                    <?php echo htmlspecialchars($error); ?>
                </p>

            </div>

        <?php endif; ?>


        <!-- =========================
             TOP PROFILE CARD
        ========================= -->

        <div class="bg-white rounded-3xl shadow-sm border border-slate-100 p-6 md:p-8 mb-8">

            <div class="flex flex-col md:flex-row md:items-center gap-6">


                <!-- Avatar -->

                <div class="w-24 h-24 shrink-0 rounded-3xl bg-gradient-to-br from-sky-500 to-blue-600 text-white flex items-center justify-center text-4xl font-extrabold shadow-lg">

                    <?php
                    echo strtoupper(
                        substr(
                            htmlspecialchars($user["name"]),
                            0,
                            1
                        )
                    );
                    ?>

                </div>


                <!-- User Info -->

                <div class="flex-1">

                    <h2 class="text-2xl md:text-3xl font-extrabold text-slate-900">

                        <?php echo htmlspecialchars($user["name"]); ?>

                    </h2>


                    <p class="text-slate-500 mt-1">

                        <?php echo htmlspecialchars($user["email"]); ?>

                    </p>


                    <div class="flex flex-wrap gap-3 mt-4">

                        <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-sky-50 text-sky-600 text-sm font-semibold">

                            <i class="fa-solid fa-user-check"></i>

                            <?php echo ucfirst(htmlspecialchars($user["role"])); ?>

                        </span>


                        <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-slate-100 text-slate-600 text-sm font-semibold">

                            <i class="fa-solid fa-calendar"></i>

                            Member since
                            <?php
                            echo date(
                                "M Y",
                                strtotime($user["created_at"])
                            );
                            ?>

                        </span>

                    </div>

                </div>


                <!-- Booking Button -->

                <div>

                    <a
                        href="my-bookings.php"
                        class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-sky-500 hover:bg-sky-600 text-white font-bold transition"
                    >

                        <i class="fa-solid fa-calendar-check"></i>

                        My Bookings

                    </a>

                </div>

            </div>

        </div>


        <!-- =========================
             STATS
        ========================= -->

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 mb-8">


            <!-- Bookings -->

            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6">

                <div class="flex items-center gap-4">

                    <div class="w-14 h-14 rounded-2xl bg-sky-100 text-sky-500 flex items-center justify-center text-xl">

                        <i class="fa-solid fa-suitcase-rolling"></i>

                    </div>


                    <div>

                        <p class="text-sm text-slate-500">
                            Total Bookings
                        </p>

                        <h3 class="text-2xl font-extrabold text-slate-900">
                            <?php echo $bookingCount; ?>
                        </h3>

                    </div>

                </div>

            </div>


            <!-- Spent -->

            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6">

                <div class="flex items-center gap-4">

                    <div class="w-14 h-14 rounded-2xl bg-emerald-100 text-emerald-500 flex items-center justify-center text-xl">

                        <i class="fa-solid fa-indian-rupee-sign"></i>

                    </div>


                    <div>

                        <p class="text-sm text-slate-500">
                            Total Travel Spend
                        </p>

                        <h3 class="text-2xl font-extrabold text-slate-900">

                            ₹<?php echo number_format($totalSpent, 2); ?>

                        </h3>

                    </div>

                </div>

            </div>

        </div>


        <!-- =========================
             TWO COLUMN
        ========================= -->

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">


            <!-- =========================
                 UPDATE PROFILE
            ========================= -->

            <div class="bg-white rounded-3xl shadow-sm border border-slate-100 p-7 md:p-8">

                <div class="mb-7">

                    <div class="w-12 h-12 rounded-xl bg-sky-100 text-sky-500 flex items-center justify-center text-lg mb-4">

                        <i class="fa-solid fa-user-pen"></i>

                    </div>


                    <h2 class="text-2xl font-extrabold text-slate-900">
                        Personal Information
                    </h2>


                    <p class="text-slate-500 mt-2">
                        Update your basic account information.
                    </p>

                </div>


                <form method="POST" class="space-y-5">

                    <input
                        type="hidden"
                        name="action"
                        value="update_profile"
                    >


                    <!-- Name -->

                    <div>

                        <label
                            for="name"
                            class="block text-sm font-semibold text-slate-700 mb-2"
                        >
                            Full Name
                        </label>


                        <div class="relative">

                            <i class="fa-solid fa-user absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>


                            <input
                                type="text"
                                id="name"
                                name="name"
                                value="<?php echo htmlspecialchars($user["name"]); ?>"
                                required
                                class="w-full pl-11 pr-4 py-3.5 rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-sky-500 focus:ring-2 focus:ring-sky-100 outline-none transition"
                            >

                        </div>

                    </div>


                    <!-- Email -->

                    <div>

                        <label
                            for="email"
                            class="block text-sm font-semibold text-slate-700 mb-2"
                        >
                            Email Address
                        </label>


                        <div class="relative">

                            <i class="fa-solid fa-envelope absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>


                            <input
                                type="email"
                                id="email"
                                name="email"
                                value="<?php echo htmlspecialchars($user["email"]); ?>"
                                required
                                class="w-full pl-11 pr-4 py-3.5 rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-sky-500 focus:ring-2 focus:ring-sky-100 outline-none transition"
                            >

                        </div>

                    </div>


                    <!-- Phone -->

                    <div>

                        <label
                            for="phone"
                            class="block text-sm font-semibold text-slate-700 mb-2"
                        >
                            Phone Number
                        </label>


                        <div class="relative">

                            <i class="fa-solid fa-phone absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>


                            <input
                                type="tel"
                                id="phone"
                                name="phone"
                                value="<?php echo htmlspecialchars($user["phone"] ?? ""); ?>"
                                placeholder="Enter phone number"
                                class="w-full pl-11 pr-4 py-3.5 rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-sky-500 focus:ring-2 focus:ring-sky-100 outline-none transition"
                            >

                        </div>

                    </div>


                    <!-- Button -->

                    <button
                        type="submit"
                        class="w-full inline-flex items-center justify-center gap-2 px-6 py-3.5 rounded-xl bg-sky-500 hover:bg-sky-600 text-white font-bold transition"
                    >

                        <i class="fa-solid fa-floppy-disk"></i>

                        Save Changes

                    </button>

                </form>

            </div>


            <!-- =========================
                 CHANGE PASSWORD
            ========================= -->

            <div class="bg-white rounded-3xl shadow-sm border border-slate-100 p-7 md:p-8">

                <div class="mb-7">

                    <div class="w-12 h-12 rounded-xl bg-orange-100 text-orange-500 flex items-center justify-center text-lg mb-4">

                        <i class="fa-solid fa-lock"></i>

                    </div>


                    <h2 class="text-2xl font-extrabold text-slate-900">
                        Change Password
                    </h2>


                    <p class="text-slate-500 mt-2">
                        Keep your TravelGo account secure.
                    </p>

                </div>


                <form method="POST" class="space-y-5">

                    <input
                        type="hidden"
                        name="action"
                        value="change_password"
                    >


                    <!-- Current Password -->

                    <div>

                        <label
                            for="current_password"
                            class="block text-sm font-semibold text-slate-700 mb-2"
                        >
                            Current Password
                        </label>


                        <div class="relative">

                            <i class="fa-solid fa-lock absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>


                            <input
                                type="password"
                                id="current_password"
                                name="current_password"
                                required
                                class="password-field w-full pl-11 pr-12 py-3.5 rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-sky-500 focus:ring-2 focus:ring-sky-100 outline-none transition"
                            >


                            <button
                                type="button"
                                onclick="togglePassword('current_password', this)"
                                class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 hover:text-sky-500"
                            >

                                <i class="fa-solid fa-eye"></i>

                            </button>

                        </div>

                    </div>


                    <!-- New Password -->

                    <div>

                        <label
                            for="new_password"
                            class="block text-sm font-semibold text-slate-700 mb-2"
                        >
                            New Password
                        </label>


                        <div class="relative">

                            <i class="fa-solid fa-key absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>


                            <input
                                type="password"
                                id="new_password"
                                name="new_password"
                                minlength="6"
                                required
                                class="w-full pl-11 pr-12 py-3.5 rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-sky-500 focus:ring-2 focus:ring-sky-100 outline-none transition"
                            >


                            <button
                                type="button"
                                onclick="togglePassword('new_password', this)"
                                class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 hover:text-sky-500"
                            >

                                <i class="fa-solid fa-eye"></i>

                            </button>

                        </div>

                        <p class="text-xs text-slate-400 mt-2">
                            Minimum 6 characters.
                        </p>

                    </div>


                    <!-- Confirm Password -->

                    <div>

                        <label
                            for="confirm_password"
                            class="block text-sm font-semibold text-slate-700 mb-2"
                        >
                            Confirm New Password
                        </label>


                        <div class="relative">

                            <i class="fa-solid fa-shield-halved absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>


                            <input
                                type="password"
                                id="confirm_password"
                                name="confirm_password"
                                minlength="6"
                                required
                                class="w-full pl-11 pr-12 py-3.5 rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-sky-500 focus:ring-2 focus:ring-sky-100 outline-none transition"
                            >


                            <button
                                type="button"
                                onclick="togglePassword('confirm_password', this)"
                                class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 hover:text-sky-500"
                            >

                                <i class="fa-solid fa-eye"></i>

                            </button>

                        </div>

                    </div>


                    <!-- Button -->

                    <button
                        type="submit"
                        class="w-full inline-flex items-center justify-center gap-2 px-6 py-3.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-bold transition"
                    >

                        <i class="fa-solid fa-key"></i>

                        Change Password

                    </button>

                </form>

            </div>

        </div>


        <!-- =========================
             QUICK LINKS
        ========================= -->

        <div class="mt-8 bg-white rounded-3xl border border-slate-100 shadow-sm p-7 md:p-8">

            <h2 class="text-2xl font-extrabold text-slate-900 mb-6">
                Quick Links
            </h2>


            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">


                <a
                    href="my-bookings.php"
                    class="flex items-center gap-4 p-4 rounded-xl bg-slate-50 hover:bg-sky-50 border border-slate-100 hover:border-sky-200 transition"
                >

                    <div class="w-11 h-11 rounded-xl bg-sky-100 text-sky-500 flex items-center justify-center">

                        <i class="fa-solid fa-calendar-check"></i>

                    </div>


                    <div>

                        <h3 class="font-bold text-slate-900">
                            My Bookings
                        </h3>

                        <p class="text-sm text-slate-500">
                            View your trips
                        </p>

                    </div>

                </a>


                <a
                    href="packages.php"
                    class="flex items-center gap-4 p-4 rounded-xl bg-slate-50 hover:bg-emerald-50 border border-slate-100 hover:border-emerald-200 transition"
                >

                    <div class="w-11 h-11 rounded-xl bg-emerald-100 text-emerald-500 flex items-center justify-center">

                        <i class="fa-solid fa-suitcase-rolling"></i>

                    </div>


                    <div>

                        <h3 class="font-bold text-slate-900">
                            Tour Packages
                        </h3>

                        <p class="text-sm text-slate-500">
                            Explore packages
                        </p>

                    </div>

                </a>


                <a
                    href="contact.php"
                    class="flex items-center gap-4 p-4 rounded-xl bg-slate-50 hover:bg-orange-50 border border-slate-100 hover:border-orange-200 transition"
                >

                    <div class="w-11 h-11 rounded-xl bg-orange-100 text-orange-500 flex items-center justify-center">

                        <i class="fa-solid fa-headset"></i>

                    </div>


                    <div>

                        <h3 class="font-bold text-slate-900">
                            Contact Us
                        </h3>

                        <p class="text-sm text-slate-500">
                            Get travel help
                        </p>

                    </div>

                </a>

            </div>

        </div>

    </div>

</section>


<!-- =========================
     PASSWORD JS
========================= -->

<script>

function togglePassword(inputId, button) {

    const input = document.getElementById(inputId);

    const icon = button.querySelector("i");


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