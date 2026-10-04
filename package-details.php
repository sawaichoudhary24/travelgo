<?php

session_start();

require_once "config/database.php";

/* =========================================================
   PACKAGE ID
========================================================= */

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if ($id <= 0) {
    header("Location: packages.php");
    exit;
}


/* =========================================================
   GET PACKAGE
========================================================= */

$stmt = $pdo->prepare("
    SELECT
        packages.*,
        destinations.name AS destination_name,
        destinations.location AS destination_location
    FROM packages
    LEFT JOIN destinations
        ON packages.destination_id = destinations.id
    WHERE packages.id = ?
      AND packages.status = 'active'
    LIMIT 1
");

$stmt->execute([$id]);

$package = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$package) {
    header("Location: packages.php");
    exit;
}


/* =========================================================
   GET ITINERARY
========================================================= */

$itineraryStmt = $pdo->prepare("
    SELECT *
    FROM package_itinerary
    WHERE package_id = ?
    ORDER BY day_number ASC
");

$itineraryStmt->execute([$id]);

$itinerary = $itineraryStmt->fetchAll(PDO::FETCH_ASSOC);


/* =========================================================
   REVIEW VARIABLES
========================================================= */

$reviewSuccess = "";
$reviewError = "";


/* =========================================================
   DELETE OWN REVIEW
========================================================= */

if (
    isset($_GET["delete_review"]) &&
    isset($_SESSION["user_id"])
) {

    $reviewId = (int) $_GET["delete_review"];

    if ($reviewId > 0) {

        $deleteReview = $pdo->prepare("
            DELETE FROM reviews
            WHERE id = ?
              AND user_id = ?
              AND package_id = ?
        ");

        $deleteReview->execute([
            $reviewId,
            (int) $_SESSION["user_id"],
            $id
        ]);

        header("Location: package-details.php?id=" . $id . "#reviews");
        exit;
    }
}


/* =========================================================
   SUBMIT REVIEW
========================================================= */

if (
    $_SERVER["REQUEST_METHOD"] === "POST" &&
    isset($_POST["submit_review"])
) {

    if (!isset($_SESSION["user_id"])) {

        $reviewError = "Please login to submit a review.";

    } else {

        $reviewUserId = (int) $_SESSION["user_id"];

        $rating = (int) ($_POST["rating"] ?? 0);

        $reviewText = trim($_POST["review"] ?? "");


        /* VALIDATE RATING */

        if ($rating < 1 || $rating > 5) {

            $reviewError = "Please select a rating between 1 and 5.";

        }

        /* VALIDATE REVIEW */

        elseif ($reviewText === "") {

            $reviewError = "Please write your review.";

        }

        else {

            /* CHECK DUPLICATE REVIEW */

            $checkReview = $pdo->prepare("
                SELECT id
                FROM reviews
                WHERE user_id = ?
                  AND package_id = ?
                LIMIT 1
            ");

            $checkReview->execute([
                $reviewUserId,
                $id
            ]);


            if ($checkReview->fetch()) {

                $reviewError = "You have already reviewed this package.";

            }

            else {

                /* INSERT REVIEW */

                $insertReview = $pdo->prepare("
                    INSERT INTO reviews
                    (
                        user_id,
                        package_id,
                        rating,
                        review
                    )
                    VALUES (?, ?, ?, ?)
                ");

                $insertReview->execute([
                    $reviewUserId,
                    $id,
                    $rating,
                    $reviewText
                ]);

                $reviewSuccess = "Your review has been submitted successfully.";
            }
        }
    }
}


/* =========================================================
   REVIEW STATISTICS
========================================================= */

$reviewStatsStmt = $pdo->prepare("
    SELECT
        COUNT(*) AS total_reviews,
        COALESCE(AVG(rating), 0) AS average_rating
    FROM reviews
    WHERE package_id = ?
");

$reviewStatsStmt->execute([$id]);

$reviewStats = $reviewStatsStmt->fetch(PDO::FETCH_ASSOC);

$totalReviews = (int) ($reviewStats["total_reviews"] ?? 0);

$averageRating = (float) ($reviewStats["average_rating"] ?? 0);


/* =========================================================
   GET ALL REVIEWS
========================================================= */

$reviewStmt = $pdo->prepare("
    SELECT
        reviews.*,
        users.name AS user_name
    FROM reviews
    LEFT JOIN users
        ON reviews.user_id = users.id
    WHERE reviews.package_id = ?
    ORDER BY reviews.created_at DESC
");

$reviewStmt->execute([$id]);

$reviews = $reviewStmt->fetchAll(PDO::FETCH_ASSOC);


/* =========================================================
   HEADER
========================================================= */

require_once "includes/header.php";

require_once "includes/navbar.php";

?>

<!-- =======================================================
     PAGE
======================================================= -->

<div class="bg-slate-50 min-h-screen">


    <!-- ===================================================
         BREADCRUMB
    ==================================================== -->

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-8">

        <div class="flex flex-wrap items-center gap-2 text-sm text-slate-500">

            <a
                href="index.php"
                class="hover:text-sky-500 transition"
            >
                Home
            </a>

            <i class="fa-solid fa-chevron-right text-xs"></i>

            <a
                href="packages.php"
                class="hover:text-sky-500 transition"
            >
                Packages
            </a>

            <i class="fa-solid fa-chevron-right text-xs"></i>

            <span class="text-slate-800 font-medium">
                <?php echo htmlspecialchars($package["title"]); ?>
            </span>

        </div>

    </div>


    <!-- ===================================================
         PACKAGE HERO
    ==================================================== -->

    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">


            <!-- LEFT CONTENT -->

            <div class="lg:col-span-2">

                <!-- IMAGE -->

                <div class="relative overflow-hidden rounded-3xl shadow-lg">

                    <?php if (!empty($package["image"])): ?>

                        <img
                            src="uploads/<?php echo htmlspecialchars($package["image"]); ?>"
                            alt="<?php echo htmlspecialchars($package["title"]); ?>"
                            class="w-full h-[300px] sm:h-[420px] object-cover"
                        >

                    <?php else: ?>

                        <div class="w-full h-[300px] sm:h-[420px] bg-slate-200 flex items-center justify-center">

                            <i class="fa-solid fa-image text-6xl text-slate-400"></i>

                        </div>

                    <?php endif; ?>


                    <!-- DURATION -->

                    <div class="absolute top-5 left-5">

                        <span class="inline-flex items-center gap-2 bg-white/95 backdrop-blur px-4 py-2 rounded-full text-sm font-semibold text-slate-700 shadow">

                            <i class="fa-regular fa-clock text-sky-500"></i>

                            <?php echo htmlspecialchars($package["duration"]); ?>

                        </span>

                    </div>

                </div>


                <!-- TITLE -->

                <div class="mt-8">

                    <h1 class="text-3xl sm:text-4xl font-extrabold text-slate-900">

                        <?php echo htmlspecialchars($package["title"]); ?>

                    </h1>


                    <!-- DESTINATION -->

                    <div class="flex flex-wrap items-center gap-4 mt-4">

                        <div class="flex items-center gap-2 text-slate-600">

                            <i class="fa-solid fa-location-dot text-sky-500"></i>

                            <span>
                                <?php echo htmlspecialchars($package["destination_name"] ?? "Travel Destination"); ?>
                            </span>

                        </div>


                        <?php if (!empty($package["destination_location"])): ?>

                            <div class="flex items-center gap-2 text-slate-500">

                                <i class="fa-solid fa-map-pin text-slate-400"></i>

                                <span>
                                    <?php echo htmlspecialchars($package["destination_location"]); ?>
                                </span>

                            </div>

                        <?php endif; ?>

                    </div>

                </div>


                <!-- PACKAGE INFO -->

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mt-8">


                    <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-100">

                        <div class="w-11 h-11 rounded-xl bg-sky-50 flex items-center justify-center text-sky-500 mb-4">

                            <i class="fa-regular fa-clock"></i>

                        </div>

                        <p class="text-sm text-slate-500">
                            Duration
                        </p>

                        <p class="font-bold text-slate-900 mt-1">
                            <?php echo htmlspecialchars($package["duration"]); ?>
                        </p>

                    </div>


                    <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-100">

                        <div class="w-11 h-11 rounded-xl bg-emerald-50 flex items-center justify-center text-emerald-500 mb-4">

                            <i class="fa-solid fa-location-dot"></i>

                        </div>

                        <p class="text-sm text-slate-500">
                            Destination
                        </p>

                        <p class="font-bold text-slate-900 mt-1">
                            <?php echo htmlspecialchars($package["destination_name"] ?? "N/A"); ?>
                        </p>

                    </div>


                    <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-100">

                        <div class="w-11 h-11 rounded-xl bg-amber-50 flex items-center justify-center text-amber-500 mb-4">

                            <i class="fa-solid fa-indian-rupee-sign"></i>

                        </div>

                        <p class="text-sm text-slate-500">
                            Starting Price
                        </p>

                        <p class="font-bold text-slate-900 mt-1">
                            ₹<?php echo number_format((float)$package["price"], 2); ?>
                        </p>

                    </div>

                </div>


                <!-- DESCRIPTION -->

                <div class="bg-white rounded-3xl shadow-sm border border-slate-100 p-6 sm:p-8 mt-8">

                    <h2 class="text-2xl font-bold text-slate-900">
                        About This Package
                    </h2>

                    <div class="mt-5 text-slate-600 leading-8 whitespace-pre-line">

                        <?php echo htmlspecialchars($package["description"]); ?>

                    </div>

                </div>


                <!-- =================================================
                     ITINERARY
                ================================================== -->

                <div class="bg-white rounded-3xl shadow-sm border border-slate-100 p-6 sm:p-8 mt-8">

                    <div class="flex items-center justify-between gap-4 mb-7">

                        <div>

                            <p class="text-sm font-semibold text-sky-500 uppercase tracking-wider">
                                Trip Plan
                            </p>

                            <h2 class="text-2xl font-bold text-slate-900 mt-1">
                                Day-by-Day Itinerary
                            </h2>

                        </div>

                        <div class="hidden sm:flex w-12 h-12 rounded-xl bg-sky-50 items-center justify-center text-sky-500">

                            <i class="fa-solid fa-route"></i>

                        </div>

                    </div>


                    <?php if (!empty($itinerary)): ?>

                        <div class="space-y-5">

                            <?php foreach ($itinerary as $day): ?>

                                <div class="flex gap-4">

                                    <div class="flex-shrink-0">

                                        <div class="w-12 h-12 rounded-xl bg-sky-500 text-white flex items-center justify-center font-bold">

                                            <?php echo (int)$day["day_number"]; ?>

                                        </div>

                                    </div>


                                    <div class="flex-1 pb-5 border-b border-slate-100">

                                        <h3 class="font-bold text-lg text-slate-900">

                                            <?php echo htmlspecialchars($day["title"]); ?>

                                        </h3>

                                        <p class="text-slate-600 mt-2 leading-7">

                                            <?php echo nl2br(htmlspecialchars($day["description"])); ?>

                                        </p>

                                    </div>

                                </div>

                            <?php endforeach; ?>

                        </div>

                    <?php else: ?>

                        <div class="text-center py-10">

                            <div class="w-16 h-16 mx-auto rounded-2xl bg-slate-100 flex items-center justify-center text-slate-400">

                                <i class="fa-solid fa-route text-2xl"></i>

                            </div>

                            <p class="text-slate-500 mt-4">
                                Itinerary will be updated soon.
                            </p>

                        </div>

                    <?php endif; ?>

                </div>


                <!-- =================================================
                     REVIEWS SECTION
                ================================================== -->

                <div
                    id="reviews"
                    class="bg-white rounded-3xl shadow-sm border border-slate-100 p-6 sm:p-8 mt-8"
                >

                    <!-- REVIEW HEADER -->

                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-5">

                        <div>

                            <p class="text-sm font-semibold text-sky-500 uppercase tracking-wider">
                                Customer Feedback
                            </p>

                            <h2 class="text-2xl font-bold text-slate-900 mt-1">
                                Reviews & Ratings
                            </h2>

                        </div>


                        <!-- RATING SUMMARY -->

                        <div class="flex items-center gap-4">

                            <div class="text-center">

                                <div class="text-3xl font-extrabold text-slate-900">

                                    <?php echo number_format($averageRating, 1); ?>

                                </div>

                                <div class="flex items-center justify-center gap-1 text-amber-400 mt-1">

                                    <?php

                                    $roundedRating = round($averageRating);

                                    for ($i = 1; $i <= 5; $i++):

                                    ?>

                                        <?php if ($i <= $roundedRating): ?>

                                            <i class="fa-solid fa-star"></i>

                                        <?php else: ?>

                                            <i class="fa-regular fa-star"></i>

                                        <?php endif; ?>

                                    <?php endfor; ?>

                                </div>

                                <p class="text-xs text-slate-500 mt-1">

                                    <?php echo $totalReviews; ?>

                                    <?php echo $totalReviews == 1 ? "Review" : "Reviews"; ?>

                                </p>

                            </div>

                        </div>

                    </div>


                    <!-- SUCCESS MESSAGE -->

                    <?php if ($reviewSuccess): ?>

                        <div class="mt-6 p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-700">

                            <div class="flex items-center gap-3">

                                <i class="fa-solid fa-circle-check"></i>

                                <span>
                                    <?php echo htmlspecialchars($reviewSuccess); ?>
                                </span>

                            </div>

                        </div>

                    <?php endif; ?>


                    <!-- ERROR MESSAGE -->

                    <?php if ($reviewError): ?>

                        <div class="mt-6 p-4 rounded-xl bg-red-50 border border-red-200 text-red-700">

                            <div class="flex items-center gap-3">

                                <i class="fa-solid fa-circle-exclamation"></i>

                                <span>
                                    <?php echo htmlspecialchars($reviewError); ?>
                                </span>

                            </div>

                        </div>

                    <?php endif; ?>


                    <!-- =================================================
                         REVIEW FORM
                    ================================================== -->

                    <?php if (isset($_SESSION["user_id"])): ?>

                        <div class="mt-8 p-5 sm:p-6 bg-slate-50 rounded-2xl border border-slate-100">

                            <h3 class="font-bold text-lg text-slate-900">
                                Write a Review
                            </h3>

                            <p class="text-sm text-slate-500 mt-1">
                                Share your experience with this package.
                            </p>


                            <form
                                method="POST"
                                action="package-details.php?id=<?php echo $id; ?>#reviews"
                                class="mt-6"
                            >

                                <!-- RATING -->

                                <label class="block text-sm font-semibold text-slate-700 mb-3">

                                    Your Rating

                                </label>


                                <div class="flex flex-row-reverse justify-end gap-2 mb-6">

                                    <input
                                        type="radio"
                                        name="rating"
                                        id="star5"
                                        value="5"
                                        class="hidden peer"
                                    >

                                    <label
                                        for="star5"
                                        class="rating-star cursor-pointer text-2xl text-slate-300 hover:text-amber-400 transition"
                                        title="5 Stars"
                                    >
                                        <i class="fa-solid fa-star"></i>
                                    </label>


                                    <input
                                        type="radio"
                                        name="rating"
                                        id="star4"
                                        value="4"
                                        class="hidden"
                                    >

                                    <label
                                        for="star4"
                                        class="rating-star cursor-pointer text-2xl text-slate-300 hover:text-amber-400 transition"
                                        title="4 Stars"
                                    >
                                        <i class="fa-solid fa-star"></i>
                                    </label>


                                    <input
                                        type="radio"
                                        name="rating"
                                        id="star3"
                                        value="3"
                                        class="hidden"
                                    >

                                    <label
                                        for="star3"
                                        class="rating-star cursor-pointer text-2xl text-slate-300 hover:text-amber-400 transition"
                                        title="3 Stars"
                                    >
                                        <i class="fa-solid fa-star"></i>
                                    </label>


                                    <input
                                        type="radio"
                                        name="rating"
                                        id="star2"
                                        value="2"
                                        class="hidden"
                                    >

                                    <label
                                        for="star2"
                                        class="rating-star cursor-pointer text-2xl text-slate-300 hover:text-amber-400 transition"
                                        title="2 Stars"
                                    >
                                        <i class="fa-solid fa-star"></i>
                                    </label>


                                    <input
                                        type="radio"
                                        name="rating"
                                        id="star1"
                                        value="1"
                                        class="hidden"
                                    >

                                    <label
                                        for="star1"
                                        class="rating-star cursor-pointer text-2xl text-slate-300 hover:text-amber-400 transition"
                                        title="1 Star"
                                    >
                                        <i class="fa-solid fa-star"></i>
                                    </label>

                                </div>


                                <!-- REVIEW TEXT -->

                                <label
                                    for="review"
                                    class="block text-sm font-semibold text-slate-700 mb-2"
                                >
                                    Your Review
                                </label>

                                <textarea
                                    id="review"
                                    name="review"
                                    rows="5"
                                    placeholder="Tell us about your experience..."
                                    class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-sky-500 resize-none"
                                ></textarea>


                                <!-- SUBMIT -->

                                <button
                                    type="submit"
                                    name="submit_review"
                                    class="mt-4 inline-flex items-center justify-center gap-2 px-6 py-3 rounded-xl bg-sky-500 hover:bg-sky-600 text-white font-bold transition"
                                >

                                    <i class="fa-solid fa-paper-plane"></i>

                                    Submit Review

                                </button>

                            </form>

                        </div>

                    <?php else: ?>

                        <!-- LOGIN CTA -->

                        <div class="mt-8 p-6 bg-slate-50 rounded-2xl border border-slate-100 text-center">

                            <div class="w-14 h-14 mx-auto rounded-2xl bg-sky-50 flex items-center justify-center text-sky-500">

                                <i class="fa-regular fa-star text-xl"></i>

                            </div>

                            <h3 class="font-bold text-lg text-slate-900 mt-4">

                                Want to share your experience?

                            </h3>

                            <p class="text-slate-500 mt-2">

                                Login to submit your review and rating.

                            </p>

                            <a
                                href="login.php"
                                class="inline-flex items-center gap-2 mt-5 px-6 py-3 rounded-xl bg-sky-500 hover:bg-sky-600 text-white font-bold transition"
                            >

                                <i class="fa-solid fa-right-to-bracket"></i>

                                Login to Review

                            </a>

                        </div>

                    <?php endif; ?>


                    <!-- =================================================
                         REVIEW LIST
                    ================================================== -->

                    <div class="mt-8">

                        <div class="flex items-center justify-between mb-5">

                            <h3 class="text-lg font-bold text-slate-900">

                                Customer Reviews

                            </h3>

                            <span class="text-sm text-slate-500">

                                <?php echo $totalReviews; ?>
                                total

                            </span>

                        </div>


                        <?php if (!empty($reviews)): ?>

                            <div class="space-y-5">

                                <?php foreach ($reviews as $review): ?>

                                    <div class="p-5 rounded-2xl border border-slate-100 bg-white">

                                        <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">

                                            <div class="flex items-start gap-3">

                                                <!-- USER ICON -->

                                                <div class="w-11 h-11 rounded-full bg-sky-100 text-sky-600 flex items-center justify-center font-bold flex-shrink-0">

                                                    <?php

                                                    $reviewName = $review["user_name"] ?? "User";

                                                    echo strtoupper(
                                                        substr(
                                                            $reviewName,
                                                            0,
                                                            1
                                                        )
                                                    );

                                                    ?>

                                                </div>


                                                <div>

                                                    <h4 class="font-bold text-slate-900">

                                                        <?php echo htmlspecialchars($reviewName); ?>

                                                    </h4>


                                                    <div class="flex items-center gap-1 text-amber-400 mt-1">

                                                        <?php

                                                        $userRating = (int) $review["rating"];

                                                        for ($i = 1; $i <= 5; $i++):

                                                        ?>

                                                            <?php if ($i <= $userRating): ?>

                                                                <i class="fa-solid fa-star text-sm"></i>

                                                            <?php else: ?>

                                                                <i class="fa-regular fa-star text-sm"></i>

                                                            <?php endif; ?>

                                                        <?php endfor; ?>

                                                    </div>

                                                </div>

                                            </div>


                                            <!-- DATE -->

                                            <div class="text-sm text-slate-400">

                                                <?php

                                                echo date(
                                                    "d M Y",
                                                    strtotime($review["created_at"])
                                                );

                                                ?>

                                            </div>

                                        </div>


                                        <!-- REVIEW TEXT -->

                                        <p class="mt-4 text-slate-600 leading-7">

                                            <?php

                                            echo nl2br(
                                                htmlspecialchars(
                                                    $review["review"]
                                                )
                                            );

                                            ?>

                                        </p>


                                        <!-- DELETE OWN REVIEW -->

                                        <?php

                                        if (
                                            isset($_SESSION["user_id"]) &&
                                            (int)$_SESSION["user_id"] === (int)$review["user_id"]
                                        ):

                                        ?>

                                            <div class="mt-4">

                                                <a
                                                    href="package-details.php?id=<?php echo $id; ?>&delete_review=<?php echo (int)$review["id"]; ?>"
                                                    onclick="return confirm('Are you sure you want to delete your review?');"
                                                    class="inline-flex items-center gap-2 text-sm text-red-500 hover:text-red-600 font-semibold"
                                                >

                                                    <i class="fa-solid fa-trash"></i>

                                                    Delete My Review

                                                </a>

                                            </div>

                                        <?php endif; ?>

                                    </div>

                                <?php endforeach; ?>

                            </div>

                        <?php else: ?>

                            <!-- NO REVIEWS -->

                            <div class="text-center py-10 bg-slate-50 rounded-2xl">

                                <div class="w-16 h-16 mx-auto rounded-2xl bg-white shadow-sm flex items-center justify-center text-amber-400">

                                    <i class="fa-regular fa-star text-2xl"></i>

                                </div>

                                <h3 class="font-bold text-slate-900 mt-4">

                                    No reviews yet

                                </h3>

                                <p class="text-slate-500 mt-2">

                                    Be the first person to review this package.

                                </p>

                            </div>

                        <?php endif; ?>

                    </div>

                </div>

            </div>


            <!-- ===================================================
                 RIGHT SIDEBAR
            ==================================================== -->

            <div class="lg:col-span-1">

                <div class="lg:sticky lg:top-24">

                    <div class="bg-white rounded-3xl shadow-lg border border-slate-100 p-6">

                        <p class="text-sm text-slate-500">
                            Package Price
                        </p>

                        <div class="flex items-end gap-2 mt-1">

                            <span class="text-3xl font-extrabold text-slate-900">

                                ₹<?php echo number_format((float)$package["price"], 2); ?>

                            </span>

                            <span class="text-slate-500 mb-1">
                                / person
                            </span>

                        </div>


                        <!-- RATING -->

                        <?php if ($totalReviews > 0): ?>

                            <div class="flex items-center gap-2 mt-4">

                                <div class="flex gap-1 text-amber-400">

                                    <?php

                                    $sidebarRating = round($averageRating);

                                    for ($i = 1; $i <= 5; $i++):

                                    ?>

                                        <?php if ($i <= $sidebarRating): ?>

                                            <i class="fa-solid fa-star text-sm"></i>

                                        <?php else: ?>

                                            <i class="fa-regular fa-star text-sm"></i>

                                        <?php endif; ?>

                                    <?php endfor; ?>

                                </div>

                                <span class="text-sm text-slate-500">

                                    <?php echo number_format($averageRating, 1); ?>

                                    (<?php echo $totalReviews; ?>)

                                </span>

                            </div>

                        <?php endif; ?>


                        <div class="h-px bg-slate-100 my-6"></div>


                        <!-- BOOK BUTTON -->

                        <?php if (isset($_SESSION["user_id"])): ?>

                            <a
                                href="book-package.php?id=<?php echo (int)$package["id"]; ?>"
                                class="w-full inline-flex items-center justify-center gap-2 px-6 py-4 rounded-xl bg-sky-500 hover:bg-sky-600 text-white font-bold transition"
                            >

                                <i class="fa-solid fa-calendar-check"></i>

                                Book Now

                            </a>

                        <?php else: ?>

                            <a
                                href="login.php"
                                class="w-full inline-flex items-center justify-center gap-2 px-6 py-4 rounded-xl bg-sky-500 hover:bg-sky-600 text-white font-bold transition"
                            >

                                <i class="fa-solid fa-right-to-bracket"></i>

                                Login to Book

                            </a>

                        <?php endif; ?>


                        <div class="mt-6 space-y-4">


                            <div class="flex items-center gap-3 text-sm text-slate-600">

                                <div class="w-9 h-9 rounded-lg bg-sky-50 text-sky-500 flex items-center justify-center">

                                    <i class="fa-solid fa-shield-halved"></i>

                                </div>

                                <span>
                                    Secure Booking
                                </span>

                            </div>


                            <div class="flex items-center gap-3 text-sm text-slate-600">

                                <div class="w-9 h-9 rounded-lg bg-emerald-50 text-emerald-500 flex items-center justify-center">

                                    <i class="fa-solid fa-headset"></i>

                                </div>

                                <span>
                                    24/7 Travel Support
                                </span>

                            </div>


                            <div class="flex items-center gap-3 text-sm text-slate-600">

                                <div class="w-9 h-9 rounded-lg bg-amber-50 text-amber-500 flex items-center justify-center">

                                    <i class="fa-solid fa-circle-check"></i>

                                </div>

                                <span>
                                    Trusted Travel Packages
                                </span>

                            </div>

                        </div>

                    </div>


                    <!-- CONTACT CARD -->

                    <div class="bg-slate-900 rounded-3xl p-6 mt-6 text-white">

                        <div class="w-12 h-12 rounded-xl bg-white/10 flex items-center justify-center">

                            <i class="fa-solid fa-headset text-xl"></i>

                        </div>

                        <h3 class="text-xl font-bold mt-5">
                            Need Help?
                        </h3>

                        <p class="text-slate-300 mt-2 leading-6">
                            Have questions about this package? Contact our travel experts.
                        </p>

                        <a
                            href="contact.php"
                            class="inline-flex items-center gap-2 mt-5 px-5 py-3 rounded-xl bg-white text-slate-900 font-bold hover:bg-slate-100 transition"
                        >

                            Contact Us

                            <i class="fa-solid fa-arrow-right"></i>

                        </a>

                    </div>

                </div>

            </div>

        </div>

    </section>

</div>


<!-- =======================================================
     STAR SCRIPT
======================================================= -->

<script>

document.addEventListener("DOMContentLoaded", function () {

    const stars = document.querySelectorAll(".rating-star");

    const radios = document.querySelectorAll(
        'input[name="rating"]'
    );


    radios.forEach(function (radio) {

        radio.addEventListener("change", function () {

            const selectedRating = parseInt(this.value);

            stars.forEach(function (star) {

                const starInput = document.getElementById(
                    star.getAttribute("for")
                );

                const starValue = parseInt(
                    starInput.value
                );

                if (starValue <= selectedRating) {

                    star.classList.remove(
                        "text-slate-300"
                    );

                    star.classList.add(
                        "text-amber-400"
                    );

                } else {

                    star.classList.remove(
                        "text-amber-400"
                    );

                    star.classList.add(
                        "text-slate-300"
                    );

                }

            });

        });

    });

});

</script>


<?php

/* =========================================================
   FOOTER
========================================================= */

require_once "includes/footer.php";

?>