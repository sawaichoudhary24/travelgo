<?php

require_once "../includes/auth.php";
require_once "../config/database.php";


// ===============================
// DELETE REVIEW
// ===============================
if (isset($_GET["delete"])) {

    $reviewId = (int) $_GET["delete"];

    if ($reviewId >
 0) {

        $deleteStmt = $pdo->prepare("
            DELETE FROM reviews
            WHERE id = ?
        ");

        $deleteStmt->execute([$reviewId]);
    }

    header("Location: reviews.php");
    exit;
}


// ===============================
// SEARCH
// ===============================
$search = trim($_GET["search"] ?? "");


// ===============================
// STATISTICS
// ===============================
$totalStmt = $pdo->query("
    SELECT COUNT(*) 
    FROM reviews
");

$totalReviews = (int) $totalStmt->fetchColumn();


$avgStmt = $pdo->query("
    SELECT COALESCE(AVG(rating), 0)
    FROM reviews
");

$averageRating = (float) $avgStmt->fetchColumn();


$fiveStarStmt = $pdo->query("
    SELECT COUNT(*)
    FROM reviews
    WHERE rating = 5
");

$fiveStarReviews = (int) $fiveStarStmt->fetchColumn();


$oneStarStmt = $pdo->query("
    SELECT COUNT(*)
    FROM reviews
    WHERE rating = 1
");

$oneStarReviews = (int) $oneStarStmt->fetchColumn();


// ===============================
// REVIEWS LIST
// ===============================
if ($search !== "") {

    $reviewStmt = $pdo->prepare("
        SELECT
            reviews.*,
            users.name AS user_name,
            users.email AS user_email,
            packages.title AS package_title,
            destinations.name AS destination_name
        FROM reviews

        LEFT JOIN users
            ON reviews.user_id = users.id

        LEFT JOIN packages
            ON reviews.package_id = packages.id

        LEFT JOIN destinations
            ON packages.destination_id = destinations.id

        WHERE
            users.name LIKE ?
            OR users.email LIKE ?
            OR packages.title LIKE ?
            OR reviews.review LIKE ?

        ORDER BY reviews.created_at DESC
    ");

    $searchValue = "%" . $search . "%";

    $reviewStmt->execute([
        $searchValue,
        $searchValue,
        $searchValue,
        $searchValue
    ]);

} else {

    $reviewStmt = $pdo->query("
        SELECT
            reviews.*,
            users.name AS user_name,
            users.email AS user_email,
            packages.title AS package_title,
            destinations.name AS destination_name
        FROM reviews

        LEFT JOIN users
            ON reviews.user_id = users.id

        LEFT JOIN packages
            ON reviews.package_id = packages.id

        LEFT JOIN destinations
            ON packages.destination_id = destinations.id

        ORDER BY reviews.created_at DESC
    ");
}

$reviews = $reviewStmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Reviews - TravelGo Admin</title>

    <script src="https://cdn.tailwindcss.com"></script>

    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
    >

    <link rel="stylesheet" href="../assets/css/admin.css">
</head>


<body class="bg-slate-100 text-slate-800 tg-admin-layout">

<?php include_once "sidebar.php"; ?>


<!-- =============================== -->
<!-- HEADER -->
<!-- =============================== -->

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



<!-- =============================== -->
<!-- MAIN -->
<!-- =============================== -->

<main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">


    <!-- PAGE TITLE -->

    <div class="mb-8">

        <h2 class="text-2xl sm:text-3xl font-bold text-slate-900">

            Reviews & Ratings

        </h2>

        <p class="text-slate-500 mt-1">

            Manage customer reviews and ratings for your tour packages.

        </p>

    </div>



    <!-- =============================== -->
    <!-- STATISTICS -->
    <!-- =============================== -->

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">


        <!-- TOTAL -->

        <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200">

            <div class="flex items-center justify-between">

                <div>

                    <p class="text-sm text-slate-500">
                        Total Reviews
                    </p>

                    <h3 class="text-3xl font-bold mt-2">
                        <?php echo $totalReviews; ?>
                    </h3>

                </div>

                <div class="w-12 h-12 rounded-xl bg-sky-100 text-sky-600 flex items-center justify-center">

                    <i class="fa-solid fa-comments text-xl"></i>

                </div>

            </div>

        </div>



        <!-- AVERAGE -->

        <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200">

            <div class="flex items-center justify-between">

                <div>

                    <p class="text-sm text-slate-500">
                        Average Rating
                    </p>

                    <h3 class="text-3xl font-bold mt-2">

                        <?php echo number_format($averageRating, 1); ?>

                    </h3>

                </div>

                <div class="w-12 h-12 rounded-xl bg-yellow-100 text-yellow-600 flex items-center justify-center">

                    <i class="fa-solid fa-star text-xl"></i>

                </div>

            </div>

        </div>



        <!-- FIVE STAR -->

        <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200">

            <div class="flex items-center justify-between">

                <div>

                    <p class="text-sm text-slate-500">
                        5 Star Reviews
                    </p>

                    <h3 class="text-3xl font-bold mt-2">
                        <?php echo $fiveStarReviews; ?>
                    </h3>

                </div>

                <div class="w-12 h-12 rounded-xl bg-green-100 text-green-600 flex items-center justify-center">

                    <i class="fa-solid fa-star text-xl"></i>

                </div>

            </div>

        </div>



        <!-- ONE STAR -->

        <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200">

            <div class="flex items-center justify-between">

                <div>

                    <p class="text-sm text-slate-500">
                        1 Star Reviews
                    </p>

                    <h3 class="text-3xl font-bold mt-2">
                        <?php echo $oneStarReviews; ?>
                    </h3>

                </div>

                <div class="w-12 h-12 rounded-xl bg-red-100 text-red-600 flex items-center justify-center">

                    <i class="fa-solid fa-star text-xl"></i>

                </div>

            </div>

        </div>

    </div>



    <!-- =============================== -->
    <!-- SEARCH -->
    <!-- =============================== -->

    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-5 mb-6">

        <form
            method="GET"
            class="flex flex-col sm:flex-row gap-3"
        >

            <div class="relative flex-1">

                <i class="fa-solid fa-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>

                <input
                    type="text"
                    name="search"
                    value="<?php echo htmlspecialchars($search); ?>"
                    placeholder="Search reviewer, email, package or review..."
                    class="w-full pl-11 pr-4 py-3 border border-slate-300 rounded-xl outline-none focus:ring-2 focus:ring-sky-500"
                >

            </div>


            <button
                type="submit"
                class="px-6 py-3 rounded-xl bg-sky-500 hover:bg-sky-600 text-white font-semibold transition"
            >

                <i class="fa-solid fa-search mr-2"></i>

                Search

            </button>


            <?php if ($search !== ""): ?>

                <a
                    href="reviews.php"
                    class="px-6 py-3 rounded-xl bg-slate-200 hover:bg-slate-300 text-slate-700 font-semibold text-center transition"
                >

                    Clear

                </a>

            <?php endif; ?>

        </form>

    </div>



    <!-- =============================== -->
    <!-- REVIEWS -->
    <!-- =============================== -->

    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">


        <!-- DESKTOP TABLE -->

        <div class="hidden lg:block overflow-x-auto">

            <table class="w-full">

                <thead class="bg-slate-50 border-b border-slate-200">

                    <tr>

                        <th class="text-left px-6 py-4 text-sm font-semibold">
                            Customer
                        </th>

                        <th class="text-left px-6 py-4 text-sm font-semibold">
                            Package
                        </th>

                        <th class="text-left px-6 py-4 text-sm font-semibold">
                            Rating
                        </th>

                        <th class="text-left px-6 py-4 text-sm font-semibold">
                            Review
                        </th>

                        <th class="text-left px-6 py-4 text-sm font-semibold">
                            Date
                        </th>

                        <th class="text-right px-6 py-4 text-sm font-semibold">
                            Action
                        </th>

                    </tr>

                </thead>


                <tbody class="divide-y divide-slate-100">

                    <?php if (count($reviews) > 0): ?>

                        <?php foreach ($reviews as $review): ?>

                            <tr class="hover:bg-slate-50 transition">


                                <!-- CUSTOMER -->

                                <td class="px-6 py-5">

                                    <div class="font-semibold text-slate-900">

                                        <?php
                                        echo htmlspecialchars(
                                            $review["user_name"] ?? "Deleted User"
                                        );
                                        ?>

                                    </div>

                                    <div class="text-xs text-slate-500 mt-1">

                                        <?php
                                        echo htmlspecialchars(
                                            $review["user_email"] ?? ""
                                        );
                                        ?>

                                    </div>

                                </td>



                                <!-- PACKAGE -->

                                <td class="px-6 py-5">

                                    <div class="font-semibold text-slate-800">

                                        <?php
                                        echo htmlspecialchars(
                                            $review["package_title"] ?? "Deleted Package"
                                        );
                                        ?>

                                    </div>

                                    <div class="text-xs text-slate-500 mt-1">

                                        <?php
                                        echo htmlspecialchars(
                                            $review["destination_name"] ?? "N/A"
                                        );
                                        ?>

                                    </div>

                                </td>



                                <!-- RATING -->

                                <td class="px-6 py-5 whitespace-nowrap">

                                    <div class="flex items-center gap-1">

                                        <?php for ($i = 1; $i <= 5; $i++): ?>

                                            <?php if ($i <= (int)$review["rating"]): ?>

                                                <i class="fa-solid fa-star text-yellow-400"></i>

                                            <?php else: ?>

                                                <i class="fa-regular fa-star text-slate-300"></i>

                                            <?php endif; ?>

                                        <?php endfor; ?>

                                    </div>

                                    <span class="text-xs text-slate-500">

                                        <?php echo (int)$review["rating"]; ?>/5

                                    </span>

                                </td>



                                <!-- REVIEW -->

                                <td class="px-6 py-5 max-w-sm">

                                    <p class="text-sm text-slate-600">

                                        <?php

                                        echo htmlspecialchars(
                                            $review["review"] ?? ""
                                        );

                                        ?>

                                    </p>

                                </td>



                                <!-- DATE -->

                                <td class="px-6 py-5 whitespace-nowrap text-sm text-slate-500">

                                    <?php

                                    echo date(
                                        "d M Y",
                                        strtotime($review["created_at"])
                                    );

                                    ?>

                                </td>



                                <!-- ACTION -->

                                <td class="px-6 py-5 text-right">

                                    <a
                                        href="reviews.php?delete=<?php echo (int)$review["id"]; ?>"
                                        onclick="return confirm('Are you sure you want to delete this review?');"
                                        class="inline-flex items-center justify-center w-10 h-10 rounded-lg bg-red-50 text-red-600 hover:bg-red-100 transition"
                                        title="Delete Review"
                                    >

                                        <i class="fa-solid fa-trash"></i>

                                    </a>

                                </td>


                            </tr>

                        <?php endforeach; ?>

                    <?php else: ?>

                        <tr>

                            <td
                                colspan="6"
                                class="px-6 py-16 text-center"
                            >

                                <div class="text-slate-400 text-5xl mb-4">

                                    <i class="fa-regular fa-comment-dots"></i>

                                </div>

                                <h3 class="text-lg font-bold text-slate-800">

                                    No Reviews Found

                                </h3>

                                <p class="text-slate-500 mt-1">

                                    <?php if ($search !== ""): ?>

                                        No reviews matched your search.

                                    <?php else: ?>

                                        Customer reviews will appear here.

                                    <?php endif; ?>

                                </p>

                            </td>

                        </tr>

                    <?php endif; ?>

                </tbody>

            </table>

        </div>



        <!-- =============================== -->
        <!-- MOBILE / TABLET CARDS -->
        <!-- =============================== -->

        <div class="lg:hidden divide-y divide-slate-200">

            <?php if (count($reviews) > 0): ?>

                <?php foreach ($reviews as $review): ?>

                    <div class="p-5">


                        <!-- TOP -->

                        <div class="flex items-start justify-between gap-4">

                            <div>

                                <h3 class="font-bold text-slate-900">

                                    <?php

                                    echo htmlspecialchars(
                                        $review["user_name"] ?? "Deleted User"
                                    );

                                    ?>

                                </h3>

                                <p class="text-xs text-slate-500 mt-1">

                                    <?php

                                    echo htmlspecialchars(
                                        $review["user_email"] ?? ""
                                    );

                                    ?>

                                </p>

                            </div>


                            <a
                                href="reviews.php?delete=<?php echo (int)$review["id"]; ?>"
                                onclick="return confirm('Are you sure you want to delete this review?');"
                                class="w-10 h-10 flex-shrink-0 rounded-lg bg-red-50 text-red-600 flex items-center justify-center"
                            >

                                <i class="fa-solid fa-trash"></i>

                            </a>

                        </div>



                        <!-- PACKAGE -->

                        <div class="mt-4 p-3 bg-slate-50 rounded-xl">

                            <p class="text-xs text-slate-500">
                                Package
                            </p>

                            <p class="font-semibold text-slate-800 mt-1">

                                <?php

                                echo htmlspecialchars(
                                    $review["package_title"] ?? "Deleted Package"
                                );

                                ?>

                            </p>

                            <p class="text-xs text-slate-500 mt-1">

                                <i class="fa-solid fa-location-dot mr-1"></i>

                                <?php

                                echo htmlspecialchars(
                                    $review["destination_name"] ?? "N/A"
                                );

                                ?>

                            </p>

                        </div>



                        <!-- RATING -->

                        <div class="mt-4 flex items-center gap-1">

                            <?php for ($i = 1; $i <= 5; $i++): ?>

                                <?php if ($i <= (int)$review["rating"]): ?>

                                    <i class="fa-solid fa-star text-yellow-400"></i>

                                <?php else: ?>

                                    <i class="fa-regular fa-star text-slate-300"></i>

                                <?php endif; ?>

                            <?php endfor; ?>

                            <span class="text-sm text-slate-500 ml-2">

                                <?php echo (int)$review["rating"]; ?>/5

                            </span>

                        </div>



                        <!-- REVIEW -->

                        <div class="mt-4">

                            <p class="text-sm text-slate-600 leading-6">

                                "<?php

                                echo htmlspecialchars(
                                    $review["review"] ?? ""
                                );

                                ?>"

                            </p>

                        </div>



                        <!-- DATE -->

                        <div class="mt-4 text-xs text-slate-400">

                            <i class="fa-regular fa-calendar mr-1"></i>

                            <?php

                            echo date(
                                "d M Y",
                                strtotime($review["created_at"])
                            );

                            ?>

                        </div>


                    </div>

                <?php endforeach; ?>

            <?php else: ?>

                <div class="p-12 text-center">

                    <div class="text-slate-400 text-5xl mb-4">

                        <i class="fa-regular fa-comment-dots"></i>

                    </div>

                    <h3 class="text-lg font-bold text-slate-800">

                        No Reviews Found

                    </h3>

                    <p class="text-slate-500 mt-1">

                        No customer reviews are available.

                    </p>

                </div>

            <?php endif; ?>

        </div>

    </div>

</main>


<script src="../assets/js/admin.js"></script>
</body>

</html>