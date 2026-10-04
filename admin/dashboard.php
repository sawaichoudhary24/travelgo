<?php

require_once "../includes/auth.php";
require_once "../config/database.php";


// ==================================================
// DASHBOARD STATISTICS
// ==================================================

// Total Users
$usersStmt = $pdo->query("
    SELECT COUNT(*)
    FROM users
    WHERE role = 'user'
");
$totalUsers = (int) $usersStmt->fetchColumn();


// Total Destinations
$destinationsStmt = $pdo->query("
    SELECT COUNT(*)
    FROM destinations
");
$totalDestinations = (int) $destinationsStmt->fetchColumn();


// Total Packages
$packagesStmt = $pdo->query("
    SELECT COUNT(*)
    FROM packages
");
$totalPackages = (int) $packagesStmt->fetchColumn();


// Total Bookings
$bookingsStmt = $pdo->query("
    SELECT COUNT(*)
    FROM bookings
");
$totalBookings = (int) $bookingsStmt->fetchColumn();


// Pending Bookings
$pendingStmt = $pdo->query("
    SELECT COUNT(*)
    FROM bookings
    WHERE status = 'pending'
");
$pendingBookings = (int) $pendingStmt->fetchColumn();


// Confirmed Revenue
$revenueStmt = $pdo->query("
    SELECT COALESCE(SUM(total_amount), 0)
    FROM bookings
    WHERE status = 'confirmed'
");
$confirmedRevenue = (float) $revenueStmt->fetchColumn();


// Total Contacts
$contactsStmt = $pdo->query("
    SELECT COUNT(*)
    FROM contacts
");
$totalContacts = (int) $contactsStmt->fetchColumn();


// Total Reviews
$reviewsStmt = $pdo->query("
    SELECT COUNT(*)
    FROM reviews
");
$totalReviews = (int) $reviewsStmt->fetchColumn();


// Average Rating
$averageRatingStmt = $pdo->query("
    SELECT COALESCE(AVG(rating), 0)
    FROM reviews
");
$averageRating = (float) $averageRatingStmt->fetchColumn();


// ==================================================
// RECENT BOOKINGS
// ==================================================

$recentBookingsStmt = $pdo->query("
    SELECT
        bookings.*,
        users.name AS user_name,
        users.email AS user_email,
        packages.title AS package_title,
        destinations.name AS destination_name
    FROM bookings

    LEFT JOIN users
        ON bookings.user_id = users.id

    LEFT JOIN packages
        ON bookings.package_id = packages.id

    LEFT JOIN destinations
        ON packages.destination_id = destinations.id

    ORDER BY bookings.created_at DESC

    LIMIT 6
");

$recentBookings = $recentBookingsStmt->fetchAll(PDO::FETCH_ASSOC);


// ==================================================
// RECENT PACKAGES
// ==================================================

$recentPackagesStmt = $pdo->query("
    SELECT
        packages.*,
        destinations.name AS destination_name
    FROM packages

    LEFT JOIN destinations
        ON packages.destination_id = destinations.id

    ORDER BY packages.id DESC

    LIMIT 5
");

$recentPackages = $recentPackagesStmt->fetchAll(PDO::FETCH_ASSOC);


// ==================================================
// RECENT CONTACTS
// ==================================================

$recentContactsStmt = $pdo->query("
    SELECT *
    FROM contacts
    ORDER BY created_at DESC
    LIMIT 5
");

$recentContacts = $recentContactsStmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Dashboard - TravelGo Admin</title>

    <script src="https://cdn.tailwindcss.com"></script>

    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
    >

    <link rel="stylesheet" href="../assets/css/admin.css">
</head>


<body class="bg-slate-100 text-slate-800 tg-admin-layout">

<?php include_once "sidebar.php"; ?>


<!-- ================================================== -->
<!-- HEADER -->
<!-- ================================================== -->

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



<!-- ================================================== -->
<!-- MAIN -->
<!-- ================================================== -->

<main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">


    <!-- PAGE TITLE -->

    <div class="mb-8">

        <h2 class="text-2xl sm:text-3xl font-bold text-slate-900">
            Dashboard
        </h2>

        <p class="text-slate-500 mt-1">
            Welcome to TravelGo administration panel.
        </p>

    </div>



    <!-- ================================================== -->
    <!-- STATISTICS CARDS -->
    <!-- ================================================== -->

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">


        <!-- USERS -->

        <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm">

            <div class="flex items-center justify-between">

                <div>

                    <p class="text-sm text-slate-500">
                        Total Users
                    </p>

                    <h3 class="text-3xl font-bold text-slate-900 mt-2">
                        <?php echo $totalUsers; ?>
                    </h3>

                </div>


                <div class="w-12 h-12 rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center">

                    <i class="fa-solid fa-users text-xl"></i>

                </div>

            </div>


            <a
                href="users.php"
                class="inline-flex items-center gap-2 mt-5 text-sm font-semibold text-sky-600 hover:text-sky-700"
            >

                Manage Users

                <i class="fa-solid fa-arrow-right"></i>

            </a>

        </div>



        <!-- DESTINATIONS -->

        <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm">

            <div class="flex items-center justify-between">

                <div>

                    <p class="text-sm text-slate-500">
                        Destinations
                    </p>

                    <h3 class="text-3xl font-bold text-slate-900 mt-2">
                        <?php echo $totalDestinations; ?>
                    </h3>

                </div>


                <div class="w-12 h-12 rounded-xl bg-green-100 text-green-600 flex items-center justify-center">

                    <i class="fa-solid fa-location-dot text-xl"></i>

                </div>

            </div>


            <a
                href="destinations.php"
                class="inline-flex items-center gap-2 mt-5 text-sm font-semibold text-sky-600 hover:text-sky-700"
            >

                Manage Destinations

                <i class="fa-solid fa-arrow-right"></i>

            </a>

        </div>



        <!-- PACKAGES -->

        <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm">

            <div class="flex items-center justify-between">

                <div>

                    <p class="text-sm text-slate-500">
                        Tour Packages
                    </p>

                    <h3 class="text-3xl font-bold text-slate-900 mt-2">
                        <?php echo $totalPackages; ?>
                    </h3>

                </div>


                <div class="w-12 h-12 rounded-xl bg-purple-100 text-purple-600 flex items-center justify-center">

                    <i class="fa-solid fa-suitcase-rolling text-xl"></i>

                </div>

            </div>


            <a
                href="packages.php"
                class="inline-flex items-center gap-2 mt-5 text-sm font-semibold text-sky-600 hover:text-sky-700"
            >

                Manage Packages

                <i class="fa-solid fa-arrow-right"></i>

            </a>

        </div>



        <!-- BOOKINGS -->

        <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm">

            <div class="flex items-center justify-between">

                <div>

                    <p class="text-sm text-slate-500">
                        Total Bookings
                    </p>

                    <h3 class="text-3xl font-bold text-slate-900 mt-2">
                        <?php echo $totalBookings; ?>
                    </h3>

                </div>


                <div class="w-12 h-12 rounded-xl bg-orange-100 text-orange-600 flex items-center justify-center">

                    <i class="fa-solid fa-calendar-check text-xl"></i>

                </div>

            </div>


            <a
                href="bookings.php"
                class="inline-flex items-center gap-2 mt-5 text-sm font-semibold text-sky-600 hover:text-sky-700"
            >

                Manage Bookings

                <i class="fa-solid fa-arrow-right"></i>

            </a>

        </div>



        <!-- REVIEWS -->

        <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm">

            <div class="flex items-center justify-between">

                <div>

                    <p class="text-sm text-slate-500">
                        Total Reviews
                    </p>

                    <h3 class="text-3xl font-bold text-slate-900 mt-2">
                        <?php echo $totalReviews; ?>
                    </h3>

                </div>


                <div class="w-12 h-12 rounded-xl bg-yellow-100 text-yellow-600 flex items-center justify-center">

                    <i class="fa-solid fa-star text-xl"></i>

                </div>

            </div>


            <div class="flex items-center gap-2 mt-4">

                <div class="flex items-center gap-1 text-yellow-400">

                    <?php

                    $roundedRating = round($averageRating);

                    for ($i = 1; $i <= 5; $i++):

                    ?>

                        <?php if ($i <= $roundedRating): ?>

                            <i class="fa-solid fa-star text-xs"></i>

                        <?php else: ?>

                            <i class="fa-regular fa-star text-xs text-slate-300"></i>

                        <?php endif; ?>

                    <?php endfor; ?>

                </div>


                <span class="text-xs text-slate-500">

                    <?php echo number_format($averageRating, 1); ?>/5

                </span>

            </div>


            <a
                href="reviews.php"
                class="inline-flex items-center gap-2 mt-4 text-sm font-semibold text-sky-600 hover:text-sky-700"
            >

                Manage Reviews

                <i class="fa-solid fa-arrow-right"></i>

            </a>

        </div>



        <!-- PENDING BOOKINGS -->

        <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm">

            <div class="flex items-center justify-between">

                <div>

                    <p class="text-sm text-slate-500">
                        Pending Bookings
                    </p>

                    <h3 class="text-3xl font-bold text-slate-900 mt-2">
                        <?php echo $pendingBookings; ?>
                    </h3>

                </div>


                <div class="w-12 h-12 rounded-xl bg-yellow-100 text-yellow-600 flex items-center justify-center">

                    <i class="fa-solid fa-clock text-xl"></i>

                </div>

            </div>


            <a
                href="bookings.php"
                class="inline-flex items-center gap-2 mt-5 text-sm font-semibold text-sky-600 hover:text-sky-700"
            >

                Review Bookings

                <i class="fa-solid fa-arrow-right"></i>

            </a>

        </div>



        <!-- REVENUE -->

        <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm">

            <div class="flex items-center justify-between">

                <div>

                    <p class="text-sm text-slate-500">
                        Confirmed Revenue
                    </p>

                    <h3 class="text-2xl font-bold text-slate-900 mt-2">

                        ₹<?php echo number_format($confirmedRevenue, 2); ?>

                    </h3>

                </div>


                <div class="w-12 h-12 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center">

                    <i class="fa-solid fa-indian-rupee-sign text-xl"></i>

                </div>

            </div>


            <a
                href="bookings.php"
                class="inline-flex items-center gap-2 mt-5 text-sm font-semibold text-sky-600 hover:text-sky-700"
            >

                View Bookings

                <i class="fa-solid fa-arrow-right"></i>

            </a>

        </div>



        <!-- CONTACTS -->

        <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm">

            <div class="flex items-center justify-between">

                <div>

                    <p class="text-sm text-slate-500">
                        Contact Messages
                    </p>

                    <h3 class="text-3xl font-bold text-slate-900 mt-2">
                        <?php echo $totalContacts; ?>
                    </h3>

                </div>


                <div class="w-12 h-12 rounded-xl bg-pink-100 text-pink-600 flex items-center justify-center">

                    <i class="fa-solid fa-envelope text-xl"></i>

                </div>

            </div>


            <a
                href="contacts.php"
                class="inline-flex items-center gap-2 mt-5 text-sm font-semibold text-sky-600 hover:text-sky-700"
            >

                View Messages

                <i class="fa-solid fa-arrow-right"></i>

            </a>

        </div>

    </div>



    <!-- ================================================== -->
    <!-- QUICK ACTIONS -->
    <!-- ================================================== -->

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 mb-8">

        <div class="flex items-center justify-between mb-5">

            <div>

                <h3 class="text-xl font-bold text-slate-900">
                    Quick Actions
                </h3>

                <p class="text-sm text-slate-500 mt-1">
                    Quickly manage your TravelGo website.
                </p>

            </div>

        </div>


        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">


            <!-- DESTINATION -->

            <a
                href="destinations.php"
                class="flex items-center gap-3 p-4 rounded-xl border border-slate-200 hover:border-sky-300 hover:bg-sky-50 transition"
            >

                <div class="w-10 h-10 rounded-lg bg-green-100 text-green-600 flex items-center justify-center">

                    <i class="fa-solid fa-location-dot"></i>

                </div>

                <div>

                    <p class="font-semibold text-slate-800">
                        Destinations
                    </p>

                    <p class="text-xs text-slate-500">
                        Add or manage destinations
                    </p>

                </div>

            </a>



            <!-- PACKAGES -->

            <a
                href="packages.php"
                class="flex items-center gap-3 p-4 rounded-xl border border-slate-200 hover:border-sky-300 hover:bg-sky-50 transition"
            >

                <div class="w-10 h-10 rounded-lg bg-purple-100 text-purple-600 flex items-center justify-center">

                    <i class="fa-solid fa-suitcase-rolling"></i>

                </div>

                <div>

                    <p class="font-semibold text-slate-800">
                        Packages
                    </p>

                    <p class="text-xs text-slate-500">
                        Manage tour packages
                    </p>

                </div>

            </a>



            <!-- BOOKINGS -->

            <a
                href="bookings.php"
                class="flex items-center gap-3 p-4 rounded-xl border border-slate-200 hover:border-sky-300 hover:bg-sky-50 transition"
            >

                <div class="w-10 h-10 rounded-lg bg-orange-100 text-orange-600 flex items-center justify-center">

                    <i class="fa-solid fa-calendar-check"></i>

                </div>

                <div>

                    <p class="font-semibold text-slate-800">
                        Bookings
                    </p>

                    <p class="text-xs text-slate-500">
                        Manage customer bookings
                    </p>

                </div>

            </a>



            <!-- REVIEWS -->

            <a
                href="reviews.php"
                class="flex items-center gap-3 p-4 rounded-xl border border-slate-200 hover:border-yellow-300 hover:bg-yellow-50 transition"
            >

                <div class="w-10 h-10 rounded-lg bg-yellow-100 text-yellow-600 flex items-center justify-center">

                    <i class="fa-solid fa-star"></i>

                </div>

                <div>

                    <p class="font-semibold text-slate-800">
                        Reviews
                    </p>

                    <p class="text-xs text-slate-500">
                        View and manage reviews
                    </p>

                </div>

            </a>



            <!-- USERS -->

            <a
                href="users.php"
                class="flex items-center gap-3 p-4 rounded-xl border border-slate-200 hover:border-blue-300 hover:bg-blue-50 transition"
            >

                <div class="w-10 h-10 rounded-lg bg-blue-100 text-blue-600 flex items-center justify-center">

                    <i class="fa-solid fa-users"></i>

                </div>

                <div>

                    <p class="font-semibold text-slate-800">
                        Users
                    </p>

                    <p class="text-xs text-slate-500">
                        Manage registered users
                    </p>

                </div>

            </a>



            <!-- BLOGS -->

            <a
                href="blogs.php"
                class="flex items-center gap-3 p-4 rounded-xl border border-slate-200 hover:border-pink-300 hover:bg-pink-50 transition"
            >

                <div class="w-10 h-10 rounded-lg bg-pink-100 text-pink-600 flex items-center justify-center">

                    <i class="fa-solid fa-blog"></i>

                </div>

                <div>

                    <p class="font-semibold text-slate-800">
                        Blogs
                    </p>

                    <p class="text-xs text-slate-500">
                        Manage travel blogs
                    </p>

                </div>

            </a>



            <!-- CONTACTS -->

            <a
                href="contacts.php"
                class="flex items-center gap-3 p-4 rounded-xl border border-slate-200 hover:border-red-300 hover:bg-red-50 transition"
            >

                <div class="w-10 h-10 rounded-lg bg-red-100 text-red-600 flex items-center justify-center">

                    <i class="fa-solid fa-envelope"></i>

                </div>

                <div>

                    <p class="font-semibold text-slate-800">
                        Contacts
                    </p>

                    <p class="text-xs text-slate-500">
                        View customer messages
                    </p>

                </div>

            </a>



            <!-- GALLERY -->

            <a
                href="../gallery.php"
                target="_blank"
                class="flex items-center gap-3 p-4 rounded-xl border border-slate-200 hover:border-indigo-300 hover:bg-indigo-50 transition"
            >

                <div class="w-10 h-10 rounded-lg bg-indigo-100 text-indigo-600 flex items-center justify-center">

                    <i class="fa-solid fa-images"></i>

                </div>

                <div>

                    <p class="font-semibold text-slate-800">
                        Gallery
                    </p>

                    <p class="text-xs text-slate-500">
                        View website gallery
                    </p>

                </div>

            </a>

        </div>

    </div>



    <!-- ================================================== -->
    <!-- RECENT BOOKINGS + RECENT PACKAGES -->
    <!-- ================================================== -->

    <div class="grid grid-cols-1 xl:grid-cols-2 gap-6 mb-8">


        <!-- RECENT BOOKINGS -->

        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">

            <div class="p-6 border-b border-slate-200 flex items-center justify-between">

                <div>

                    <h3 class="text-xl font-bold text-slate-900">
                        Recent Bookings
                    </h3>

                    <p class="text-sm text-slate-500 mt-1">
                        Latest customer bookings
                    </p>

                </div>


                <a
                    href="bookings.php"
                    class="text-sm font-semibold text-sky-600 hover:text-sky-700"
                >
                    View All
                </a>

            </div>


            <div class="divide-y divide-slate-100">

                <?php if (count($recentBookings) > 0): ?>

                    <?php foreach ($recentBookings as $booking): ?>

                        <div class="p-5">

                            <div class="flex items-start justify-between gap-4">


                                <div>

                                    <h4 class="font-semibold text-slate-900">

                                        <?php

                                        echo htmlspecialchars(
                                            $booking["package_title"] ?? "Package"
                                        );

                                        ?>

                                    </h4>


                                    <p class="text-sm text-slate-500 mt-1">

                                        <?php

                                        echo htmlspecialchars(
                                            $booking["user_name"] ?? "User"
                                        );

                                        ?>

                                    </p>


                                    <p class="text-xs text-slate-400 mt-1">

                                        <i class="fa-solid fa-location-dot mr-1"></i>

                                        <?php

                                        echo htmlspecialchars(
                                            $booking["destination_name"] ?? "N/A"
                                        );

                                        ?>

                                    </p>

                                </div>


                                <?php

                                $status = $booking["status"];

                                if ($status === "confirmed") {

                                    $statusClass =
                                        "bg-green-100 text-green-700";

                                } elseif ($status === "cancelled") {

                                    $statusClass =
                                        "bg-red-100 text-red-700";

                                } else {

                                    $statusClass =
                                        "bg-yellow-100 text-yellow-700";
                                }

                                ?>


                                <span
                                    class="px-3 py-1 rounded-full text-xs font-semibold <?php echo $statusClass; ?>"
                                >

                                    <?php echo ucfirst($status); ?>

                                </span>

                            </div>


                            <div class="flex items-center justify-between mt-4 text-sm">

                                <span class="text-slate-500">

                                    <?php echo (int)$booking["travelers"]; ?>
                                    Traveler(s)

                                </span>


                                <span class="font-bold text-slate-900">

                                    ₹<?php

                                    echo number_format(
                                        (float)$booking["total_amount"],
                                        2
                                    );

                                    ?>

                                </span>

                            </div>

                        </div>

                    <?php endforeach; ?>

                <?php else: ?>

                    <div class="p-10 text-center text-slate-500">

                        No bookings found.

                    </div>

                <?php endif; ?>

            </div>

        </div>



        <!-- RECENT PACKAGES -->

        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">

            <div class="p-6 border-b border-slate-200 flex items-center justify-between">

                <div>

                    <h3 class="text-xl font-bold text-slate-900">
                        Recent Packages
                    </h3>

                    <p class="text-sm text-slate-500 mt-1">
                        Latest tour packages
                    </p>

                </div>


                <a
                    href="packages.php"
                    class="text-sm font-semibold text-sky-600 hover:text-sky-700"
                >
                    View All
                </a>

            </div>


            <div class="divide-y divide-slate-100">

                <?php if (count($recentPackages) > 0): ?>

                    <?php foreach ($recentPackages as $package): ?>

                        <div class="p-5 flex items-center justify-between gap-4">


                            <div class="flex items-center gap-4">

                                <?php if (!empty($package["image"])): ?>

                                    <img
                                        src="../uploads/<?php echo htmlspecialchars($package["image"]); ?>"
                                        alt="<?php echo htmlspecialchars($package["title"]); ?>"
                                        class="w-16 h-16 rounded-xl object-cover"
                                    >

                                <?php else: ?>

                                    <div class="w-16 h-16 rounded-xl bg-slate-100 flex items-center justify-center text-slate-400">

                                        <i class="fa-solid fa-image"></i>

                                    </div>

                                <?php endif; ?>


                                <div>

                                    <h4 class="font-semibold text-slate-900">

                                        <?php

                                        echo htmlspecialchars(
                                            $package["title"]
                                        );

                                        ?>

                                    </h4>


                                    <p class="text-sm text-slate-500 mt-1">

                                        <?php

                                        echo htmlspecialchars(
                                            $package["destination_name"] ?? "N/A"
                                        );

                                        ?>

                                    </p>


                                    <p class="text-sm font-bold text-sky-600 mt-1">

                                        ₹<?php

                                        echo number_format(
                                            (float)$package["price"],
                                            2
                                        );

                                        ?>

                                    </p>

                                </div>

                            </div>


                            <a
                                href="packages.php?edit=<?php echo (int)$package["id"]; ?>"
                                class="w-9 h-9 rounded-lg bg-slate-100 hover:bg-sky-100 text-slate-600 hover:text-sky-600 flex items-center justify-center"
                                title="Edit Package"
                            >

                                <i class="fa-solid fa-pen"></i>

                            </a>

                        </div>

                    <?php endforeach; ?>

                <?php else: ?>

                    <div class="p-10 text-center text-slate-500">

                        No packages found.

                    </div>

                <?php endif; ?>

            </div>

        </div>

    </div>



    <!-- ================================================== -->
    <!-- RECENT CONTACTS -->
    <!-- ================================================== -->

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">

        <div class="p-6 border-b border-slate-200 flex items-center justify-between">

            <div>

                <h3 class="text-xl font-bold text-slate-900">
                    Recent Contact Messages
                </h3>

                <p class="text-sm text-slate-500 mt-1">
                    Latest messages from website visitors.
                </p>

            </div>


            <a
                href="contacts.php"
                class="text-sm font-semibold text-sky-600 hover:text-sky-700"
            >

                View All

            </a>

        </div>


        <div class="divide-y divide-slate-100">

            <?php if (count($recentContacts) > 0): ?>

                <?php foreach ($recentContacts as $contact): ?>

                    <div class="p-5">

                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">


                            <div>

                                <h4 class="font-semibold text-slate-900">

                                    <?php

                                    echo htmlspecialchars(
                                        $contact["name"]
                                    );

                                    ?>

                                </h4>


                                <p class="text-sm text-slate-500 mt-1">

                                    <?php

                                    echo htmlspecialchars(
                                        $contact["email"] ?? ""
                                    );

                                    ?>

                                </p>

                            </div>


                            <div class="text-sm text-slate-400">

                                <?php

                                echo date(
                                    "d M Y",
                                    strtotime($contact["created_at"])
                                );

                                ?>

                            </div>

                        </div>


                        <?php if (!empty($contact["subject"])): ?>

                            <p class="font-medium text-slate-700 mt-3">

                                <?php

                                echo htmlspecialchars(
                                    $contact["subject"]
                                );

                                ?>

                            </p>

                        <?php endif; ?>


                        <p class="text-sm text-slate-500 mt-1 line-clamp-2">

                            <?php

                            echo htmlspecialchars(
                                $contact["message"]
                            );

                            ?>

                        </p>

                    </div>

                <?php endforeach; ?>

            <?php else: ?>

                <div class="p-10 text-center text-slate-500">

                    No contact messages found.

                </div>

            <?php endif; ?>

        </div>

    </div>

</main>


<script src="../assets/js/admin.js"></script>
</body>

</html>