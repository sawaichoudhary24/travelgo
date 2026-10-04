<?php

require_once "../includes/auth.php";
require_once "../config/database.php";

/* =========================
   DELETE USER
========================= */

if (isset($_GET['delete'])) {

    $deleteId = (int) $_GET['delete'];

    if ($deleteId >
 0) {

        // Make sure only normal users can be deleted
        $deleteStmt = $pdo->prepare("
            DELETE FROM users
            WHERE id = ?
            AND role = 'user'
        ");

        $deleteStmt->execute([$deleteId]);
    }

    header("Location: users.php");
    exit;
}


/* =========================
   SEARCH
========================= */

$search = isset($_GET['search'])
    ? trim($_GET['search'])
    : "";


/* =========================
   USERS QUERY
========================= */

if ($search !== "") {

    $stmt = $pdo->prepare("
        SELECT
            users.*,
            (
                SELECT COUNT(*)
                FROM bookings
                WHERE bookings.user_id = users.id
            ) AS booking_count
        FROM users
        WHERE users.role = 'user'
        AND (
            users.name LIKE ?
            OR users.email LIKE ?
            OR users.phone LIKE ?
        )
        ORDER BY users.id DESC
    ");

    $searchValue = "%" . $search . "%";

    $stmt->execute([
        $searchValue,
        $searchValue,
        $searchValue
    ]);

} else {

    $stmt = $pdo->query("
        SELECT
            users.*,
            (
                SELECT COUNT(*)
                FROM bookings
                WHERE bookings.user_id = users.id
            ) AS booking_count
        FROM users
        WHERE users.role = 'user'
        ORDER BY users.id DESC
    ");
}

$users = $stmt->fetchAll(PDO::FETCH_ASSOC);


/* =========================
   TOTAL USERS
========================= */

$totalUsers = $pdo->query("
    SELECT COUNT(*)
    FROM users
    WHERE role = 'user'
")->fetchColumn();


/* =========================
   USERS WITH BOOKINGS
========================= */

$usersWithBookings = $pdo->query("
    SELECT COUNT(DISTINCT user_id)
    FROM bookings
    WHERE user_id IS NOT NULL
")->fetchColumn();


/* =========================
   RECENT USERS
========================= */

$recentUsers = $pdo->query("
    SELECT COUNT(*)
    FROM users
    WHERE role = 'user'
    AND created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
")->fetchColumn();

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Manage Users - TravelGo Admin</title>

    <script src="https://cdn.tailwindcss.com"></script>

    <link rel="preconnect"
          href="https://fonts.googleapis.com">

    <link rel="preconnect"
          href="https://fonts.gstatic.com"
          crossorigin>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap"
          rel="stylesheet">

    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

    <style>

        * {
            box-sizing: border-box;
        }

        body {
            font-family: "Inter", sans-serif;
        }

        .modal {
            display: none;
        }

        .modal.active {
            display: flex;
        }

    </style>

    <link rel="stylesheet" href="../assets/css/admin.css">
</head>


<body class="bg-slate-100 text-slate-800 tg-admin-layout">

<?php include_once "sidebar.php"; ?>


<!-- =========================
     TOP NAVBAR
========================= -->

<header class="bg-white border-b border-slate-200">
    <div class="w-full px-4 sm:px-6 lg:px-8">
        <div class="h-16 flex items-center justify-end">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-full bg-sky-100 text-sky-700 flex items-center justify-center">
                    <i class="fa-solid fa-user"></i>
                </div>
                <div class="leading-tight">
                    <p class="font-semibold text-sm text-slate-800">
                        <?php echo htmlspecialchars($_SESSION['user_name'] ?? 'Admin'); ?>
                    </p>
                    <p class="text-xs text-slate-500">Administrator</p>
                </div>
            </div>
        </div>
    </div>
</header>



<!-- =========================
     MAIN
========================= -->

<main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">


    <!-- PAGE HEADER -->

    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-8">

        <div>

            <div class="flex items-center gap-2 text-sm text-slate-500 mb-2">

                <a href="dashboard.php"
                   class="hover:text-sky-500">

                    Dashboard

                </a>

                <i class="fa-solid fa-chevron-right text-xs"></i>

                <span>
                    Users
                </span>

            </div>


            <h2 class="text-3xl font-extrabold text-slate-900">

                Manage Users

            </h2>


            <p class="text-slate-500 mt-1">

                View and manage registered TravelGo customers.

            </p>

        </div>


        <a href="../index.php"
           target="_blank"
           class="inline-flex items-center justify-center gap-2 px-5 py-3 rounded-xl bg-white border border-slate-200 hover:border-sky-400 hover:text-sky-600 transition font-semibold">

            <i class="fa-solid fa-globe"></i>

            View Website

        </a>

    </div>



    <!-- =========================
         STATS
    ========================= -->

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-5 mb-8">


        <!-- TOTAL USERS -->

        <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm">

            <div class="flex items-center justify-between">

                <div>

                    <p class="text-sm text-slate-500">
                        Total Users
                    </p>

                    <h3 class="text-3xl font-extrabold text-slate-900 mt-1">

                        <?php echo (int)$totalUsers; ?>

                    </h3>

                </div>


                <div class="w-12 h-12 rounded-xl bg-sky-100 text-sky-600 flex items-center justify-center">

                    <i class="fa-solid fa-users text-xl"></i>

                </div>

            </div>

        </div>



        <!-- USERS WITH BOOKINGS -->

        <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm">

            <div class="flex items-center justify-between">

                <div>

                    <p class="text-sm text-slate-500">
                        Users With Bookings
                    </p>

                    <h3 class="text-3xl font-extrabold text-slate-900 mt-1">

                        <?php echo (int)$usersWithBookings; ?>

                    </h3>

                </div>


                <div class="w-12 h-12 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center">

                    <i class="fa-solid fa-calendar-check text-xl"></i>

                </div>

            </div>

        </div>



        <!-- RECENT USERS -->

        <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm">

            <div class="flex items-center justify-between">

                <div>

                    <p class="text-sm text-slate-500">
                        New In Last 30 Days
                    </p>

                    <h3 class="text-3xl font-extrabold text-slate-900 mt-1">

                        <?php echo (int)$recentUsers; ?>

                    </h3>

                </div>


                <div class="w-12 h-12 rounded-xl bg-violet-100 text-violet-600 flex items-center justify-center">

                    <i class="fa-solid fa-user-plus text-xl"></i>

                </div>

            </div>

        </div>

    </div>



    <!-- =========================
         SEARCH
    ========================= -->

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5 mb-6">

        <form method="GET"
              class="flex flex-col sm:flex-row gap-3">

            <div class="relative flex-1">

                <i class="fa-solid fa-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>

                <input
                    type="text"
                    name="search"
                    value="<?php echo htmlspecialchars($search); ?>"
                    placeholder="Search by name, email or phone..."
                    class="w-full pl-11 pr-4 py-3 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-sky-400 focus:border-sky-400"
                >

            </div>


            <button
                type="submit"
                class="px-6 py-3 rounded-xl bg-sky-500 hover:bg-sky-600 text-white font-semibold transition">

                <i class="fa-solid fa-search mr-1"></i>

                Search

            </button>


            <?php if ($search !== ""): ?>

                <a
                    href="users.php"
                    class="px-6 py-3 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold transition text-center">

                    Clear

                </a>

            <?php endif; ?>

        </form>

    </div>



    <!-- =========================
         USERS TABLE
    ========================= -->

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">


        <div class="px-5 py-4 border-b border-slate-200 flex items-center justify-between">

            <div>

                <h3 class="font-bold text-lg text-slate-900">

                    Registered Users

                </h3>

                <p class="text-sm text-slate-500">

                    <?php echo count($users); ?> user(s) shown

                </p>

            </div>


            <span class="px-3 py-1 rounded-full bg-sky-50 text-sky-600 text-sm font-semibold">

                Customers

            </span>

        </div>



        <?php if (empty($users)): ?>

            <!-- EMPTY -->

            <div class="py-16 text-center">

                <div class="w-16 h-16 rounded-full bg-slate-100 flex items-center justify-center mx-auto mb-4">

                    <i class="fa-solid fa-users-slash text-2xl text-slate-400"></i>

                </div>

                <h3 class="text-lg font-bold text-slate-800">
                    No users found
                </h3>

                <p class="text-slate-500 mt-1">
                    Try another search.
                </p>

            </div>


        <?php else: ?>


            <!-- =========================
                 DESKTOP TABLE
            ========================= -->

            <div class="hidden md:block overflow-x-auto">

                <table class="w-full">

                    <thead class="bg-slate-50">

                        <tr class="text-left text-xs uppercase tracking-wider text-slate-500">

                            <th class="px-5 py-4">
                                User
                            </th>

                            <th class="px-5 py-4">
                                Contact
                            </th>

                            <th class="px-5 py-4">
                                Bookings
                            </th>

                            <th class="px-5 py-4">
                                Registered
                            </th>

                            <th class="px-5 py-4 text-right">
                                Actions
                            </th>

                        </tr>

                    </thead>


                    <tbody class="divide-y divide-slate-100">

                        <?php foreach ($users as $user): ?>

                            <tr class="hover:bg-slate-50 transition">


                                <!-- USER -->

                                <td class="px-5 py-4">

                                    <div class="flex items-center gap-3">

                                        <div class="w-11 h-11 rounded-full bg-sky-100 text-sky-600 flex items-center justify-center font-bold">

                                            <?php
                                            echo strtoupper(
                                                substr(
                                                    $user['name'],
                                                    0,
                                                    1
                                                )
                                            );
                                            ?>

                                        </div>


                                        <div>

                                            <p class="font-semibold text-slate-900">

                                                <?php
                                                echo htmlspecialchars($user['name']);
                                                ?>

                                            </p>

                                            <p class="text-xs text-slate-500">

                                                User ID:
                                                #<?php echo (int)$user['id']; ?>

                                            </p>

                                        </div>

                                    </div>

                                </td>



                                <!-- CONTACT -->

                                <td class="px-5 py-4">

                                    <p class="text-sm text-slate-700">

                                        <?php
                                        echo htmlspecialchars($user['email']);
                                        ?>

                                    </p>


                                    <?php if (!empty($user['phone'])): ?>

                                        <p class="text-xs text-slate-500 mt-1">

                                            <?php
                                            echo htmlspecialchars($user['phone']);
                                            ?>

                                        </p>

                                    <?php else: ?>

                                        <p class="text-xs text-slate-400 mt-1">
                                            No phone
                                        </p>

                                    <?php endif; ?>

                                </td>



                                <!-- BOOKINGS -->

                                <td class="px-5 py-4">

                                    <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-emerald-50 text-emerald-600 text-sm font-semibold">

                                        <i class="fa-solid fa-calendar-check text-xs"></i>

                                        <?php echo (int)$user['booking_count']; ?>

                                    </span>

                                </td>



                                <!-- DATE -->

                                <td class="px-5 py-4 text-sm text-slate-600">

                                    <?php
                                    echo date(
                                        "d M Y",
                                        strtotime($user['created_at'])
                                    );
                                    ?>

                                </td>



                                <!-- ACTIONS -->

                                <td class="px-5 py-4">

                                    <div class="flex items-center justify-end gap-2">


                                        <!-- VIEW -->

                                        <button
                                            type="button"
                                            onclick='openUserModal(<?php echo json_encode($user); ?>)'
                                            class="w-10 h-10 rounded-lg bg-slate-100 hover:bg-sky-100 hover:text-sky-600 transition"
                                            title="View Details">

                                            <i class="fa-solid fa-eye"></i>

                                        </button>


                                        <!-- EMAIL -->

                                        <a
                                            href="mailto:<?php echo htmlspecialchars($user['email']); ?>"
                                            class="w-10 h-10 rounded-lg bg-slate-100 hover:bg-emerald-100 hover:text-emerald-600 transition flex items-center justify-center"
                                            title="Send Email">

                                            <i class="fa-solid fa-envelope"></i>

                                        </a>


                                        <?php if (!empty($user['phone'])): ?>

                                            <a
                                                href="tel:<?php echo htmlspecialchars($user['phone']); ?>"
                                                class="w-10 h-10 rounded-lg bg-slate-100 hover:bg-violet-100 hover:text-violet-600 transition flex items-center justify-center"
                                                title="Call User">

                                                <i class="fa-solid fa-phone"></i>

                                            </a>

                                        <?php endif; ?>


                                        <!-- DELETE -->

                                        <a
                                            href="users.php?delete=<?php echo (int)$user['id']; ?>"
                                            onclick="return confirm('Are you sure you want to delete <?php echo htmlspecialchars($user['name'], ENT_QUOTES); ?>? This action cannot be undone.')"
                                            class="w-10 h-10 rounded-lg bg-red-50 text-red-500 hover:bg-red-100 transition flex items-center justify-center"
                                            title="Delete User">

                                            <i class="fa-solid fa-trash"></i>

                                        </a>

                                    </div>

                                </td>

                            </tr>

                        <?php endforeach; ?>

                    </tbody>

                </table>

            </div>



            <!-- =========================
                 MOBILE CARDS
            ========================= -->

            <div class="md:hidden divide-y divide-slate-100">

                <?php foreach ($users as $user): ?>

                    <div class="p-5">


                        <div class="flex items-start justify-between gap-4">


                            <div class="flex items-center gap-3">

                                <div class="w-11 h-11 rounded-full bg-sky-100 text-sky-600 flex items-center justify-center font-bold flex-shrink-0">

                                    <?php
                                    echo strtoupper(
                                        substr(
                                            $user['name'],
                                            0,
                                            1
                                        )
                                    );
                                    ?>

                                </div>


                                <div>

                                    <h3 class="font-bold text-slate-900">

                                        <?php
                                        echo htmlspecialchars($user['name']);
                                        ?>

                                    </h3>

                                    <p class="text-xs text-slate-500">

                                        ID #<?php echo (int)$user['id']; ?>

                                    </p>

                                </div>

                            </div>


                            <span class="px-2.5 py-1 rounded-full bg-emerald-50 text-emerald-600 text-xs font-semibold">

                                <?php echo (int)$user['booking_count']; ?>
                                bookings

                            </span>

                        </div>



                        <!-- DETAILS -->

                        <div class="mt-4 space-y-2">


                            <div class="flex items-center gap-3 text-sm text-slate-600">

                                <i class="fa-solid fa-envelope w-4 text-slate-400"></i>

                                <span class="break-all">

                                    <?php
                                    echo htmlspecialchars($user['email']);
                                    ?>

                                </span>

                            </div>


                            <?php if (!empty($user['phone'])): ?>

                                <div class="flex items-center gap-3 text-sm text-slate-600">

                                    <i class="fa-solid fa-phone w-4 text-slate-400"></i>

                                    <span>

                                        <?php
                                        echo htmlspecialchars($user['phone']);
                                        ?>

                                    </span>

                                </div>

                            <?php endif; ?>


                            <div class="flex items-center gap-3 text-sm text-slate-500">

                                <i class="fa-solid fa-calendar w-4 text-slate-400"></i>

                                Registered

                                <?php
                                echo date(
                                    "d M Y",
                                    strtotime($user['created_at'])
                                );
                                ?>

                            </div>

                        </div>



                        <!-- MOBILE ACTIONS -->

                        <div class="grid grid-cols-2 gap-2 mt-5">


                            <button
                                type="button"
                                onclick='openUserModal(<?php echo json_encode($user); ?>)'
                                class="px-3 py-2.5 rounded-lg bg-slate-100 hover:bg-sky-100 hover:text-sky-600 transition font-semibold text-sm">

                                <i class="fa-solid fa-eye mr-1"></i>

                                View

                            </button>


                            <a
                                href="mailto:<?php echo htmlspecialchars($user['email']); ?>"
                                class="px-3 py-2.5 rounded-lg bg-emerald-50 text-emerald-600 hover:bg-emerald-100 transition font-semibold text-sm text-center">

                                <i class="fa-solid fa-envelope mr-1"></i>

                                Email

                            </a>


                            <?php if (!empty($user['phone'])): ?>

                                <a
                                    href="tel:<?php echo htmlspecialchars($user['phone']); ?>"
                                    class="px-3 py-2.5 rounded-lg bg-violet-50 text-violet-600 hover:bg-violet-100 transition font-semibold text-sm text-center">

                                    <i class="fa-solid fa-phone mr-1"></i>

                                    Call

                                </a>

                            <?php endif; ?>


                            <a
                                href="users.php?delete=<?php echo (int)$user['id']; ?>"
                                onclick="return confirm('Are you sure you want to delete this user? This action cannot be undone.')"
                                class="px-3 py-2.5 rounded-lg bg-red-50 text-red-500 hover:bg-red-100 transition font-semibold text-sm text-center">

                                <i class="fa-solid fa-trash mr-1"></i>

                                Delete

                            </a>

                        </div>

                    </div>

                <?php endforeach; ?>

            </div>


        <?php endif; ?>

    </div>

</main>



<!-- =========================
     USER DETAILS MODAL
========================= -->

<div
    id="userModal"
    class="modal fixed inset-0 z-50 items-center justify-center bg-slate-900/60 px-4">


    <div
        class="bg-white rounded-2xl w-full max-w-lg shadow-2xl overflow-hidden">


        <!-- MODAL HEADER -->

        <div class="px-6 py-5 border-b border-slate-200 flex items-center justify-between">

            <div>

                <h3 class="text-xl font-bold text-slate-900">
                    User Details
                </h3>

                <p class="text-sm text-slate-500">
                    Customer information
                </p>

            </div>


            <button
                type="button"
                onclick="closeUserModal()"
                class="w-10 h-10 rounded-lg bg-slate-100 hover:bg-slate-200 transition">

                <i class="fa-solid fa-xmark"></i>

            </button>

        </div>



        <!-- MODAL BODY -->

        <div class="p-6">


            <div class="flex items-center gap-4 mb-6">


                <div
                    id="modalAvatar"
                    class="w-16 h-16 rounded-full bg-sky-100 text-sky-600 flex items-center justify-center text-xl font-bold">

                    U

                </div>


                <div>

                    <h4
                        id="modalName"
                        class="text-xl font-bold text-slate-900">

                        User

                    </h4>

                    <p
                        id="modalRole"
                        class="text-sm text-sky-600 font-semibold">

                        Customer

                    </p>

                </div>

            </div>



            <!-- INFO -->

            <div class="space-y-4">


                <div class="p-4 rounded-xl bg-slate-50">

                    <div class="flex items-start gap-3">

                        <i class="fa-solid fa-envelope text-sky-500 mt-1"></i>

                        <div>

                            <p class="text-xs text-slate-500">
                                Email
                            </p>

                            <a
                                id="modalEmail"
                                href="#"
                                class="font-semibold text-slate-800 break-all hover:text-sky-600">

                            </a>

                        </div>

                    </div>

                </div>



                <div class="p-4 rounded-xl bg-slate-50">

                    <div class="flex items-start gap-3">

                        <i class="fa-solid fa-phone text-emerald-500 mt-1"></i>

                        <div>

                            <p class="text-xs text-slate-500">
                                Phone
                            </p>

                            <a
                                id="modalPhone"
                                href="#"
                                class="font-semibold text-slate-800">

                            </a>

                        </div>

                    </div>

                </div>



                <div class="p-4 rounded-xl bg-slate-50">

                    <div class="flex items-start gap-3">

                        <i class="fa-solid fa-calendar-check text-violet-500 mt-1"></i>

                        <div>

                            <p class="text-xs text-slate-500">
                                Total Bookings
                            </p>

                            <p
                                id="modalBookings"
                                class="font-semibold text-slate-800">

                            </p>

                        </div>

                    </div>

                </div>



                <div class="p-4 rounded-xl bg-slate-50">

                    <div class="flex items-start gap-3">

                        <i class="fa-solid fa-clock text-orange-500 mt-1"></i>

                        <div>

                            <p class="text-xs text-slate-500">
                                Registered On
                            </p>

                            <p
                                id="modalDate"
                                class="font-semibold text-slate-800">

                            </p>

                        </div>

                    </div>

                </div>

            </div>


        </div>



        <!-- MODAL FOOTER -->

        <div class="px-6 py-4 bg-slate-50 border-t border-slate-200 flex justify-end">

            <button
                type="button"
                onclick="closeUserModal()"
                class="px-5 py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-semibold transition">

                Close

            </button>

        </div>

    </div>

</div>



<!-- =========================
     JAVASCRIPT
========================= -->

<script>

function openUserModal(user) {

    const modal = document.getElementById("userModal");

    const name = user.name || "User";

    const email = user.email || "";

    const phone = user.phone || "No phone number";

    const bookings = user.booking_count || 0;

    const createdAt = user.created_at || "";


    document.getElementById("modalName").textContent = name;

    document.getElementById("modalAvatar").textContent =
        name.charAt(0).toUpperCase();


    document.getElementById("modalEmail").textContent = email;

    document.getElementById("modalEmail").href =
        "mailto:" + email;


    document.getElementById("modalPhone").textContent = phone;

    if (user.phone) {

        document.getElementById("modalPhone").href =
            "tel:" + user.phone;

    } else {

        document.getElementById("modalPhone").removeAttribute("href");

    }


    document.getElementById("modalBookings").textContent =
        bookings + " booking(s)";


    if (createdAt) {

        const date = new Date(
            createdAt.replace(" ", "T")
        );

        document.getElementById("modalDate").textContent =
            date.toLocaleDateString(
                "en-IN",
                {
                    day: "2-digit",
                    month: "short",
                    year: "numeric"
                }
            );

    } else {

        document.getElementById("modalDate").textContent =
            "Not available";

    }


    modal.classList.add("active");

}


function closeUserModal() {

    document
        .getElementById("userModal")
        .classList.remove("active");

}


document
    .getElementById("userModal")
    .addEventListener("click", function(event) {

        if (event.target === this) {

            closeUserModal();

        }

    });


document.addEventListener("keydown", function(event) {

    if (event.key === "Escape") {

        closeUserModal();

    }

});

</script>


<script src="../assets/js/admin.js"></script>
</body>

</html>