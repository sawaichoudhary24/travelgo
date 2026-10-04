<?php

require_once "../includes/auth.php";
require_once "../config/database.php";

/*
|--------------------------------------------------------------------------
| STATUS UPDATE
|--------------------------------------------------------------------------
*/

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["update_status"])) {

    $bookingId = (int) ($_POST["booking_id"] ?? 0);
    $status = $_POST["status"] ?? "";

    $allowedStatuses = [
        "pending",
        "confirmed",
        "cancelled"
    ];

    if ($bookingId >
 0 && in_array($status, $allowedStatuses, true)) {

        $updateStmt = $pdo->prepare("
            UPDATE bookings
            SET status = ?
            WHERE id = ?
        ");

        $updateStmt->execute([
            $status,
            $bookingId
        ]);
    }

    header("Location: bookings.php");
    exit;
}


/*
|--------------------------------------------------------------------------
| DELETE BOOKING
|--------------------------------------------------------------------------
*/

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["delete_booking"])) {

    $bookingId = (int) ($_POST["booking_id"] ?? 0);

    if ($bookingId > 0) {

        $deleteStmt = $pdo->prepare("
            DELETE FROM bookings
            WHERE id = ?
        ");

        $deleteStmt->execute([
            $bookingId
        ]);
    }

    header("Location: bookings.php");
    exit;
}


/*
|--------------------------------------------------------------------------
| SEARCH + FILTER
|--------------------------------------------------------------------------
*/

$search = trim($_GET["search"] ?? "");
$statusFilter = $_GET["status"] ?? "";


/*
|--------------------------------------------------------------------------
| STATS
|--------------------------------------------------------------------------
*/

$totalStmt = $pdo->query("
    SELECT COUNT(*) 
    FROM bookings
");

$totalBookings = (int) $totalStmt->fetchColumn();


$pendingStmt = $pdo->query("
    SELECT COUNT(*)
    FROM bookings
    WHERE status = 'pending'
");

$pendingBookings = (int) $pendingStmt->fetchColumn();


$confirmedStmt = $pdo->query("
    SELECT COUNT(*)
    FROM bookings
    WHERE status = 'confirmed'
");

$confirmedBookings = (int) $confirmedStmt->fetchColumn();


$cancelledStmt = $pdo->query("
    SELECT COUNT(*)
    FROM bookings
    WHERE status = 'cancelled'
");

$cancelledBookings = (int) $cancelledStmt->fetchColumn();


$revenueStmt = $pdo->query("
    SELECT COALESCE(SUM(total_amount), 0)
    FROM bookings
    WHERE status = 'confirmed'
");

$confirmedRevenue = (float) $revenueStmt->fetchColumn();


/*
|--------------------------------------------------------------------------
| BOOKINGS QUERY
|--------------------------------------------------------------------------
*/

$sql = "
    SELECT
        bookings.*,

        users.name AS user_name,
        users.email AS user_email,
        users.phone AS user_phone,

        packages.title AS package_title,
        packages.price AS package_price,
        packages.image AS package_image,

        destinations.name AS destination_name,
        destinations.location AS destination_location

    FROM bookings

    LEFT JOIN users
        ON bookings.user_id = users.id

    LEFT JOIN packages
        ON bookings.package_id = packages.id

    LEFT JOIN destinations
        ON packages.destination_id = destinations.id

    WHERE 1=1
";

$params = [];


/*
|--------------------------------------------------------------------------
| SEARCH
|--------------------------------------------------------------------------
*/

if ($search !== "") {

    $sql .= "
        AND (
            CAST(bookings.id AS CHAR) LIKE ?
            OR users.name LIKE ?
            OR users.email LIKE ?
            OR packages.title LIKE ?
            OR destinations.name LIKE ?
        )
    ";

    $searchValue = "%" . $search . "%";

    $params[] = $searchValue;
    $params[] = $searchValue;
    $params[] = $searchValue;
    $params[] = $searchValue;
    $params[] = $searchValue;
}


/*
|--------------------------------------------------------------------------
| STATUS FILTER
|--------------------------------------------------------------------------
*/

if (
    in_array(
        $statusFilter,
        ["pending", "confirmed", "cancelled"],
        true
    )
) {

    $sql .= "
        AND bookings.status = ?
    ";

    $params[] = $statusFilter;
}


$sql .= "
    ORDER BY bookings.created_at DESC
";


$stmt = $pdo->prepare($sql);
$stmt->execute($params);

$bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);


/*
|--------------------------------------------------------------------------
| HELPER FUNCTIONS
|--------------------------------------------------------------------------
*/

function e($value)
{
    return htmlspecialchars(
        (string) $value,
        ENT_QUOTES,
        "UTF-8"
    );
}


function statusBadge($status)
{
    switch ($status) {

        case "confirmed":
            return "bg-emerald-100 text-emerald-700";

        case "cancelled":
            return "bg-red-100 text-red-700";

        default:
            return "bg-amber-100 text-amber-700";
    }
}


function statusIcon($status)
{
    switch ($status) {

        case "confirmed":
            return "fa-circle-check";

        case "cancelled":
            return "fa-circle-xmark";

        default:
            return "fa-clock";
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

    <title>Bookings - TravelGo Admin</title>

    <script src="https://cdn.tailwindcss.com"></script>

    <link
        rel="preconnect"
        href="https://fonts.googleapis.com"
    >

    <link
        rel="preconnect"
        href="https://fonts.googleapis.com"
    >

    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap"
        rel="stylesheet"
    >

    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
    >

    <style>

        * {
            box-sizing: border-box;
        }

        body {
            font-family: "Inter", sans-serif;
        }

        .stat-card {
            transition: 0.2s ease;
        }

        .stat-card:hover {
            transform: translateY(-3px);
        }

        .table-scroll::-webkit-scrollbar {
            height: 7px;
        }

        .table-scroll::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 20px;
        }

    </style>

    <link rel="stylesheet" href="../assets/css/admin.css">
</head>


<body class="bg-slate-100 text-slate-800 tg-admin-layout">

<?php include_once "sidebar.php"; ?>


<!-- ========================================================= -->
<!-- HEADER -->
<!-- ========================================================= -->

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


<!-- ========================================================= -->
<!-- MAIN -->
<!-- ========================================================= -->

<main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">


    <!-- PAGE TITLE -->

    <div class="mb-8">

        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">

            <div>

                <p class="text-sm font-semibold text-sky-600 mb-1">
                    Booking Management
                </p>

                <h1 class="text-3xl sm:text-4xl font-extrabold text-slate-900">
                    All Bookings
                </h1>

                <p class="mt-2 text-slate-500">
                    Manage customer bookings, payment amounts and booking status.
                </p>

            </div>


            <a
                href="dashboard.php"
                class="inline-flex items-center justify-center gap-2 px-5 py-3 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-semibold"
            >
                <i class="fa-solid fa-arrow-left"></i>
                Back to Dashboard
            </a>

        </div>

    </div>


    <!-- ========================================================= -->
    <!-- STATS -->
    <!-- ========================================================= -->

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 mb-8">


        <!-- TOTAL -->

        <div class="stat-card bg-white rounded-2xl border border-slate-200 p-5 shadow-sm">

            <div class="flex items-center justify-between">

                <div>

                    <p class="text-sm text-slate-500 font-medium">
                        Total Bookings
                    </p>

                    <p class="text-3xl font-extrabold mt-2 text-slate-900">
                        <?php echo $totalBookings; ?>
                    </p>

                </div>

                <div class="w-12 h-12 rounded-xl bg-sky-100 text-sky-600 flex items-center justify-center">
                    <i class="fa-solid fa-calendar-check text-xl"></i>
                </div>

            </div>

        </div>


        <!-- PENDING -->

        <div class="stat-card bg-white rounded-2xl border border-slate-200 p-5 shadow-sm">

            <div class="flex items-center justify-between">

                <div>

                    <p class="text-sm text-slate-500 font-medium">
                        Pending
                    </p>

                    <p class="text-3xl font-extrabold mt-2 text-amber-600">
                        <?php echo $pendingBookings; ?>
                    </p>

                </div>

                <div class="w-12 h-12 rounded-xl bg-amber-100 text-amber-600 flex items-center justify-center">
                    <i class="fa-solid fa-clock text-xl"></i>
                </div>

            </div>

        </div>


        <!-- CONFIRMED -->

        <div class="stat-card bg-white rounded-2xl border border-slate-200 p-5 shadow-sm">

            <div class="flex items-center justify-between">

                <div>

                    <p class="text-sm text-slate-500 font-medium">
                        Confirmed
                    </p>

                    <p class="text-3xl font-extrabold mt-2 text-emerald-600">
                        <?php echo $confirmedBookings; ?>
                    </p>

                </div>

                <div class="w-12 h-12 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center">
                    <i class="fa-solid fa-circle-check text-xl"></i>
                </div>

            </div>

        </div>


        <!-- CANCELLED -->

        <div class="stat-card bg-white rounded-2xl border border-slate-200 p-5 shadow-sm">

            <div class="flex items-center justify-between">

                <div>

                    <p class="text-sm text-slate-500 font-medium">
                        Cancelled
                    </p>

                    <p class="text-3xl font-extrabold mt-2 text-red-600">
                        <?php echo $cancelledBookings; ?>
                    </p>

                </div>

                <div class="w-12 h-12 rounded-xl bg-red-100 text-red-600 flex items-center justify-center">
                    <i class="fa-solid fa-circle-xmark text-xl"></i>
                </div>

            </div>

        </div>


        <!-- REVENUE -->

        <div class="stat-card bg-white rounded-2xl border border-slate-200 p-5 shadow-sm">

            <div class="flex items-center justify-between">

                <div>

                    <p class="text-sm text-slate-500 font-medium">
                        Confirmed Revenue
                    </p>

                    <p class="text-2xl font-extrabold mt-2 text-slate-900">
                        ₹<?php echo number_format($confirmedRevenue, 2); ?>
                    </p>

                </div>

                <div class="w-12 h-12 rounded-xl bg-violet-100 text-violet-600 flex items-center justify-center">
                    <i class="fa-solid fa-indian-rupee-sign text-xl"></i>
                </div>

            </div>

        </div>

    </div>


    <!-- ========================================================= -->
    <!-- SEARCH + FILTER -->
    <!-- ========================================================= -->

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5 mb-6">

        <form
            method="GET"
            class="grid grid-cols-1 md:grid-cols-4 gap-4"
        >

            <!-- SEARCH -->

            <div class="md:col-span-2">

                <label class="block text-sm font-semibold text-slate-700 mb-2">
                    Search Booking
                </label>

                <div class="relative">

                    <i
                        class="fa-solid fa-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"
                    ></i>

                    <input
                        type="text"
                        name="search"
                        value="<?php echo e($search); ?>"
                        placeholder="Booking ID, customer, email, package..."
                        class="w-full pl-11 pr-4 py-3 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-sky-500"
                    >

                </div>

            </div>


            <!-- STATUS -->

            <div>

                <label class="block text-sm font-semibold text-slate-700 mb-2">
                    Booking Status
                </label>

                <select
                    name="status"
                    class="w-full px-4 py-3 rounded-xl border border-slate-300 bg-white focus:outline-none focus:ring-2 focus:ring-sky-500"
                >

                    <option value="">
                        All Status
                    </option>

                    <option
                        value="pending"
                        <?php echo $statusFilter === "pending" ? "selected" : ""; ?>
                    >
                        Pending
                    </option>

                    <option
                        value="confirmed"
                        <?php echo $statusFilter === "confirmed" ? "selected" : ""; ?>
                    >
                        Confirmed
                    </option>

                    <option
                        value="cancelled"
                        <?php echo $statusFilter === "cancelled" ? "selected" : ""; ?>
                    >
                        Cancelled
                    </option>

                </select>

            </div>


            <!-- BUTTONS -->

            <div class="flex items-end gap-2">

                <button
                    type="submit"
                    class="flex-1 inline-flex items-center justify-center gap-2 px-5 py-3 rounded-xl bg-sky-500 hover:bg-sky-600 text-white font-bold"
                >
                    <i class="fa-solid fa-filter"></i>
                    Filter
                </button>

                <a
                    href="bookings.php"
                    class="inline-flex items-center justify-center px-4 py-3 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold"
                    title="Reset"
                >
                    <i class="fa-solid fa-rotate-left"></i>
                </a>

            </div>

        </form>

    </div>


    <!-- RESULT INFO -->

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-4">

        <div class="text-sm text-slate-500">

            Showing
            <span class="font-bold text-slate-800">
                <?php echo count($bookings); ?>
            </span>
            booking(s)

            <?php if ($search !== ""): ?>

                for
                <span class="font-semibold text-slate-800">
                    "<?php echo e($search); ?>"
                </span>

            <?php endif; ?>

        </div>

    </div>


    <!-- ========================================================= -->
    <!-- DESKTOP TABLE -->
    <!-- ========================================================= -->

    <div class="hidden lg:block bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">

        <div class="table-scroll overflow-x-auto">

            <table class="w-full min-w-[1100px]">

                <thead class="bg-slate-900 text-white">

                    <tr>

                        <th class="text-left px-5 py-4 text-sm font-bold">
                            Booking
                        </th>

                        <th class="text-left px-5 py-4 text-sm font-bold">
                            Customer
                        </th>

                        <th class="text-left px-5 py-4 text-sm font-bold">
                            Package
                        </th>

                        <th class="text-left px-5 py-4 text-sm font-bold">
                            Travel Date
                        </th>

                        <th class="text-left px-5 py-4 text-sm font-bold">
                            Travelers
                        </th>

                        <th class="text-left px-5 py-4 text-sm font-bold">
                            Amount
                        </th>

                        <th class="text-left px-5 py-4 text-sm font-bold">
                            Status
                        </th>

                        <th class="text-right px-5 py-4 text-sm font-bold">
                            Action
                        </th>

                    </tr>

                </thead>


                <tbody class="divide-y divide-slate-100">

                    <?php if (empty($bookings)): ?>

                        <tr>

                            <td
                                colspan="8"
                                class="px-6 py-16 text-center"
                            >

                                <div class="text-slate-400 text-5xl mb-4">
                                    <i class="fa-regular fa-calendar-xmark"></i>
                                </div>

                                <h3 class="text-lg font-bold text-slate-800">
                                    No bookings found
                                </h3>

                                <p class="text-slate-500 mt-1">
                                    Try changing your search or status filter.
                                </p>

                            </td>

                        </tr>

                    <?php else: ?>


                        <?php foreach ($bookings as $booking): ?>

                            <tr class="hover:bg-slate-50 transition">


                                <!-- BOOKING -->

                                <td class="px-5 py-5">

                                    <div class="font-extrabold text-slate-900">
                                        #<?php echo (int) $booking["id"]; ?>
                                    </div>

                                    <div class="text-xs text-slate-400 mt-1">
                                        <?php
                                        echo !empty($booking["created_at"])
                                            ? date("d M Y, h:i A", strtotime($booking["created_at"]))
                                            : "-";
                                        ?>
                                    </div>

                                </td>


                                <!-- CUSTOMER -->

                                <td class="px-5 py-5">

                                    <div class="font-bold text-slate-900">
                                        <?php echo e($booking["user_name"] ?? "Guest"); ?>
                                    </div>

                                    <div class="text-sm text-slate-500">
                                        <?php echo e($booking["user_email"] ?? "-"); ?>
                                    </div>

                                    <?php if (!empty($booking["user_phone"])): ?>

                                        <div class="text-xs text-slate-400 mt-1">
                                            <i class="fa-solid fa-phone mr-1"></i>
                                            <?php echo e($booking["user_phone"]); ?>
                                        </div>

                                    <?php endif; ?>

                                </td>


                                <!-- PACKAGE -->

                                <td class="px-5 py-5">

                                    <div class="font-bold text-slate-900 max-w-[220px]">
                                        <?php echo e($booking["package_title"] ?? "Package Deleted"); ?>
                                    </div>

                                    <div class="text-sm text-slate-500 mt-1">

                                        <i class="fa-solid fa-location-dot mr-1"></i>

                                        <?php echo e($booking["destination_name"] ?? "Unknown"); ?>

                                    </div>

                                </td>


                                <!-- DATE -->

                                <td class="px-5 py-5">

                                    <div class="font-semibold text-slate-800">

                                        <?php
                                        echo !empty($booking["booking_date"])
                                            ? date("d M Y", strtotime($booking["booking_date"]))
                                            : "-";
                                        ?>

                                    </div>

                                </td>


                                <!-- TRAVELERS -->

                                <td class="px-5 py-5">

                                    <span class="inline-flex items-center gap-2">

                                        <i class="fa-solid fa-users text-slate-400"></i>

                                        <span class="font-bold">
                                            <?php echo (int) $booking["travelers"]; ?>
                                        </span>

                                    </span>

                                </td>


                                <!-- AMOUNT -->

                                <td class="px-5 py-5">

                                    <div class="font-extrabold text-slate-900">
                                        ₹<?php echo number_format((float) $booking["total_amount"], 2); ?>
                                    </div>

                                </td>


                                <!-- STATUS -->

                                <td class="px-5 py-5">

                                    <form
                                        method="POST"
                                        class="flex items-center gap-2"
                                    >

                                        <input
                                            type="hidden"
                                            name="booking_id"
                                            value="<?php echo (int) $booking["id"]; ?>"
                                        >

                                        <input
                                            type="hidden"
                                            name="update_status"
                                            value="1"
                                        >

                                        <select
                                            name="status"
                                            onchange="this.form.submit()"
                                            class="text-xs font-bold rounded-lg px-3 py-2 border-0 <?php echo statusBadge($booking["status"]); ?>"
                                        >

                                            <option
                                                value="pending"
                                                <?php echo $booking["status"] === "pending" ? "selected" : ""; ?>
                                            >
                                                Pending
                                            </option>

                                            <option
                                                value="confirmed"
                                                <?php echo $booking["status"] === "confirmed" ? "selected" : ""; ?>
                                            >
                                                Confirmed
                                            </option>

                                            <option
                                                value="cancelled"
                                                <?php echo $booking["status"] === "cancelled" ? "selected" : ""; ?>
                                            >
                                                Cancelled
                                            </option>

                                        </select>

                                    </form>

                                </td>


                                <!-- ACTION -->

                                <td class="px-5 py-5 text-right">

                                    <form
                                        method="POST"
                                        onsubmit="return confirm('Are you sure you want to delete this booking?');"
                                    >

                                        <input
                                            type="hidden"
                                            name="booking_id"
                                            value="<?php echo (int) $booking["id"]; ?>"
                                        >

                                        <input
                                            type="hidden"
                                            name="delete_booking"
                                            value="1"
                                        >

                                        <button
                                            type="submit"
                                            class="w-9 h-9 inline-flex items-center justify-center rounded-lg bg-red-50 text-red-600 hover:bg-red-100"
                                            title="Delete Booking"
                                        >
                                            <i class="fa-solid fa-trash"></i>
                                        </button>

                                    </form>

                                </td>

                            </tr>

                        <?php endforeach; ?>


                    <?php endif; ?>

                </tbody>

            </table>

        </div>

    </div>


    <!-- ========================================================= -->
    <!-- MOBILE + TABLET CARDS -->
    <!-- ========================================================= -->

    <div class="lg:hidden space-y-4">


        <?php if (empty($bookings)): ?>

            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-10 text-center">

                <div class="text-slate-400 text-5xl mb-4">
                    <i class="fa-regular fa-calendar-xmark"></i>
                </div>

                <h3 class="text-lg font-bold text-slate-800">
                    No bookings found
                </h3>

                <p class="text-slate-500 mt-1">
                    Try changing your search or filter.
                </p>

            </div>

        <?php else: ?>


            <?php foreach ($bookings as $booking): ?>

                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">


                    <!-- CARD HEADER -->

                    <div class="p-5 border-b border-slate-100">

                        <div class="flex items-start justify-between gap-4">

                            <div>

                                <div class="text-xs font-semibold text-slate-400 uppercase tracking-wide">
                                    Booking ID
                                </div>

                                <div class="text-xl font-extrabold text-slate-900 mt-1">
                                    #<?php echo (int) $booking["id"]; ?>
                                </div>

                            </div>


                            <span
                                class="inline-flex items-center gap-2 px-3 py-2 rounded-lg text-xs font-bold <?php echo statusBadge($booking["status"]); ?>"
                            >

                                <i class="fa-solid <?php echo statusIcon($booking["status"]); ?>"></i>

                                <?php echo ucfirst(e($booking["status"])); ?>

                            </span>

                        </div>

                    </div>


                    <!-- CARD BODY -->

                    <div class="p-5 space-y-4">


                        <!-- CUSTOMER -->

                        <div>

                            <div class="text-xs text-slate-400 uppercase font-bold tracking-wide mb-1">
                                Customer
                            </div>

                            <div class="font-bold text-slate-900">
                                <?php echo e($booking["user_name"] ?? "Guest"); ?>
                            </div>

                            <div class="text-sm text-slate-500">
                                <?php echo e($booking["user_email"] ?? "-"); ?>
                            </div>

                            <?php if (!empty($booking["user_phone"])): ?>

                                <div class="text-sm text-slate-500 mt-1">

                                    <i class="fa-solid fa-phone mr-1"></i>

                                    <?php echo e($booking["user_phone"]); ?>

                                </div>

                            <?php endif; ?>

                        </div>


                        <!-- PACKAGE -->

                        <div>

                            <div class="text-xs text-slate-400 uppercase font-bold tracking-wide mb-1">
                                Package
                            </div>

                            <div class="font-bold text-slate-900">
                                <?php echo e($booking["package_title"] ?? "Package Deleted"); ?>
                            </div>

                            <div class="text-sm text-slate-500 mt-1">

                                <i class="fa-solid fa-location-dot mr-1"></i>

                                <?php echo e($booking["destination_name"] ?? "Unknown"); ?>

                            </div>

                        </div>


                        <!-- INFO GRID -->

                        <div class="grid grid-cols-2 gap-3">

                            <div class="rounded-xl bg-slate-50 p-3">

                                <div class="text-xs text-slate-400 font-semibold">
                                    Travel Date
                                </div>

                                <div class="font-bold text-slate-800 mt-1">

                                    <?php
                                    echo !empty($booking["booking_date"])
                                        ? date("d M Y", strtotime($booking["booking_date"]))
                                        : "-";
                                    ?>

                                </div>

                            </div>


                            <div class="rounded-xl bg-slate-50 p-3">

                                <div class="text-xs text-slate-400 font-semibold">
                                    Travelers
                                </div>

                                <div class="font-bold text-slate-800 mt-1">

                                    <i class="fa-solid fa-users mr-1"></i>

                                    <?php echo (int) $booking["travelers"]; ?>

                                </div>

                            </div>

                        </div>


                        <!-- AMOUNT -->

                        <div class="rounded-xl bg-sky-50 p-4">

                            <div class="text-xs text-sky-600 font-semibold">
                                Total Amount
                            </div>

                            <div class="text-2xl font-extrabold text-slate-900 mt-1">

                                ₹<?php echo number_format((float) $booking["total_amount"], 2); ?>

                            </div>

                        </div>


                        <!-- STATUS UPDATE -->

                        <form method="POST">

                            <input
                                type="hidden"
                                name="booking_id"
                                value="<?php echo (int) $booking["id"]; ?>"
                            >

                            <input
                                type="hidden"
                                name="update_status"
                                value="1"
                            >

                            <label class="block text-sm font-bold text-slate-700 mb-2">
                                Change Status
                            </label>

                            <select
                                name="status"
                                onchange="this.form.submit()"
                                class="w-full px-4 py-3 rounded-xl border border-slate-300 bg-white font-semibold focus:outline-none focus:ring-2 focus:ring-sky-500"
                            >

                                <option
                                    value="pending"
                                    <?php echo $booking["status"] === "pending" ? "selected" : ""; ?>
                                >
                                    Pending
                                </option>

                                <option
                                    value="confirmed"
                                    <?php echo $booking["status"] === "confirmed" ? "selected" : ""; ?>
                                >
                                    Confirmed
                                </option>

                                <option
                                    value="cancelled"
                                    <?php echo $booking["status"] === "cancelled" ? "selected" : ""; ?>
                                >
                                    Cancelled
                                </option>

                            </select>

                        </form>


                        <!-- DELETE -->

                        <form
                            method="POST"
                            onsubmit="return confirm('Are you sure you want to delete this booking?');"
                        >

                            <input
                                type="hidden"
                                name="booking_id"
                                value="<?php echo (int) $booking["id"]; ?>"
                            >

                            <input
                                type="hidden"
                                name="delete_booking"
                                value="1"
                            >

                            <button
                                type="submit"
                                class="w-full inline-flex items-center justify-center gap-2 px-4 py-3 rounded-xl bg-red-50 hover:bg-red-100 text-red-600 font-bold"
                            >

                                <i class="fa-solid fa-trash"></i>

                                Delete Booking

                            </button>

                        </form>

                    </div>

                </div>

            <?php endforeach; ?>


        <?php endif; ?>

    </div>


</main>


<!-- ========================================================= -->
<!-- FOOTER -->
<!-- ========================================================= -->

<footer class="mt-10 bg-slate-950 text-slate-400">

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">

        <div class="flex flex-col sm:flex-row items-center justify-between gap-3">

            <p class="text-sm">
                © <?php echo date("Y"); ?> TravelGo. All rights reserved.
            </p>

            <p class="text-sm">
                Admin Panel
            </p>

        </div>

    </div>

</footer>


<script src="../assets/js/admin.js"></script>
</body>

</html>