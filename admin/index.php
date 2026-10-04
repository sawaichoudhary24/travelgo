<?php

session_start();

require_once "../config/database.php";


$error = "";


/* =========================
   ALREADY LOGGED IN
========================= */

if (
    isset($_SESSION['user_id']) &&
    isset($_SESSION['user_role']) &&
    $_SESSION['user_role'] === 'admin'
) {

    header("Location: dashboard.php");
    exit;
}


/* =========================
   LOGIN
========================= */

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $email = trim($_POST['email'] ?? "");
    $password = $_POST['password'] ?? "";


    if (empty($email) || empty($password)) {

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
            $user['role'] === 'admin' &&
            password_verify($password, $user['password'])
        ) {

            session_regenerate_id(true);

            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_role'] = $user['role'];


            header("Location: dashboard.php");
            exit;

        } else {

            $error = "Invalid admin email or password.";

        }
    }
}

?>


<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        Admin Login - TravelGo
    </title>


    <script src="https://cdn.tailwindcss.com"></script>


    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
    >

</head>


<body
    class="min-h-screen bg-slate-100 flex items-center justify-center px-4"
>


<div class="w-full max-w-md">


    <!-- LOGO -->

    <div class="text-center mb-8">

        <div
            class="w-16 h-16 rounded-2xl bg-sky-500 flex items-center justify-center mx-auto shadow-lg"
        >

            <i
                class="fa-solid fa-plane-departure text-white text-2xl"
            ></i>

        </div>


        <h1
            class="text-3xl font-extrabold text-slate-900 mt-5"
        >

            Travel<span class="text-sky-500">Go</span>

        </h1>


        <p class="text-slate-500 mt-1">

            Admin Panel

        </p>

    </div>



    <!-- LOGIN CARD -->

    <div
        class="bg-white rounded-2xl shadow-xl border border-slate-200 p-7 sm:p-8"
    >


        <div class="mb-6">

            <h2
                class="text-2xl font-extrabold text-slate-900"
            >

                Admin Login

            </h2>


            <p class="text-slate-500 text-sm mt-1">

                Login to manage TravelGo.

            </p>

        </div>



        <!-- ERROR -->

        <?php if (!empty($error)): ?>

            <div
                class="bg-red-50 border border-red-200 text-red-700 rounded-xl p-4 mb-5 text-sm"
            >

                <i
                    class="fa-solid fa-circle-exclamation mr-2"
                ></i>

                <?php
                echo htmlspecialchars($error);
                ?>

            </div>

        <?php endif; ?>



        <!-- FORM -->

        <form
            method="POST"
            class="space-y-5"
        >


            <!-- EMAIL -->

            <div>

                <label
                    class="block text-sm font-semibold text-slate-700 mb-2"
                >

                    Email Address

                </label>


                <div class="relative">

                    <i
                        class="fa-solid fa-envelope absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"
                    ></i>


                    <input
                        type="email"
                        name="email"
                        required
                        autocomplete="email"
                        placeholder="admin@example.com"
                        value="<?php
                        echo htmlspecialchars($email ?? '');
                        ?>"
                        class="w-full border border-slate-300 rounded-xl pl-11 pr-4 py-3.5 outline-none focus:ring-2 focus:ring-sky-500 focus:border-sky-500"
                    >

                </div>

            </div>



            <!-- PASSWORD -->

            <div>

                <label
                    class="block text-sm font-semibold text-slate-700 mb-2"
                >

                    Password

                </label>


                <div class="relative">

                    <i
                        class="fa-solid fa-lock absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"
                    ></i>


                    <input
                        type="password"
                        id="password"
                        name="password"
                        required
                        autocomplete="current-password"
                        placeholder="Enter password"
                        class="w-full border border-slate-300 rounded-xl pl-11 pr-12 py-3.5 outline-none focus:ring-2 focus:ring-sky-500 focus:border-sky-500"
                    >


                    <button
                        type="button"
                        id="togglePassword"
                        class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-700"
                    >

                        <i
                            id="passwordIcon"
                            class="fa-solid fa-eye"
                        ></i>

                    </button>

                </div>

            </div>



            <!-- LOGIN BUTTON -->

            <button
                type="submit"
                class="w-full bg-sky-500 hover:bg-sky-600 text-white py-3.5 rounded-xl font-bold transition shadow-sm"
            >

                <i
                    class="fa-solid fa-right-to-bracket mr-2"
                ></i>

                Login to Admin

            </button>

        </form>



        <!-- BACK -->

        <div class="text-center mt-6">

            <a
                href="../index.php"
                class="text-sm text-slate-500 hover:text-sky-500"
            >

                <i
                    class="fa-solid fa-arrow-left mr-1"
                ></i>

                Back to Website

            </a>

        </div>

    </div>


    <p
        class="text-center text-xs text-slate-400 mt-6"
    >

        © 2026 TravelGo Tours & Travels

    </p>

</div>



<script>

const togglePassword =
    document.getElementById("togglePassword");

const password =
    document.getElementById("password");

const passwordIcon =
    document.getElementById("passwordIcon");


togglePassword.addEventListener("click", function () {

    if (password.type === "password") {

        password.type = "text";

        passwordIcon.classList.remove("fa-eye");

        passwordIcon.classList.add("fa-eye-slash");

    } else {

        password.type = "password";

        passwordIcon.classList.remove("fa-eye-slash");

        passwordIcon.classList.add("fa-eye");

    }

});

</script>


</body>

</html>