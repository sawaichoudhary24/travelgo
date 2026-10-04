<?php

require_once "config/database.php";

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$bookingId = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if ($bookingId <= 0) {
    header("Location: my-bookings.php");
    exit;
}

$stmt = $pdo->prepare("
    SELECT
        bookings.*,
        packages.title AS package_title,
        packages.image AS package_image,
        destinations.name AS destination_name
    FROM bookings
    LEFT JOIN packages
        ON bookings.package_id = packages.id
    LEFT JOIN destinations
        ON packages.destination_id = destinations.id
    WHERE bookings.id = ?
      AND bookings.user_id = ?
    LIMIT 1
");

$stmt->execute([
    $bookingId,
    $_SESSION['user_id']
]);

$booking = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$booking) {
    header("Location: my-bookings.php");
    exit;
}

require_once "includes/header.php";
require_once "includes/navbar.php";

?>

<section class="min-h-[75vh] flex items-center justify-center px-4 py-16">

    <div class="max-w-2xl w-full">

        <div class="bg-white rounded-3xl border border-slate-200 shadow-xl p-8 md:p-12 text-center">

            <div class="w-20 h-20 bg-green-100 text-green-600 rounded-full flex items-center justify-center mx-auto mb-6">

                <i class="fa-solid fa-check text-4xl"></i>

            </div>

            <h1 class="text-3xl md:text-4xl font-extrabold text-slate-900">
                Booking Submitted!
            </h1>

            <p class="text-slate-500 mt-3">
                Your trip booking has been successfully submitted.
            </p>


            <div class="mt-8 bg-slate-50 rounded-2xl p-6 text-left">

                <div class="flex justify-between gap-4 py-3 border-b border-slate-200">

                    <span class="text-slate-500">
                        Booking ID
                    </span>

                    <span class="font-bold text-slate-900">
                        #<?php echo $booking['id']; ?>
                    </span>

                </div>


                <div class="flex justify-between gap-4 py-3 border-b border-slate-200">

                    <span class="text-slate-500">
                        Package
                    </span>

                    <span class="font-bold text-slate-900 text-right">
                        <?php echo htmlspecialchars($booking['package_title']); ?>
                    </span>

                </div>


                <div class="flex justify-between gap-4 py-3 border-b border-slate-200">

                    <span class="text-slate-500">
                        Travel Date
                    </span>

                    <span class="font-bold text-slate-900">
                        <?php echo date("d M Y", strtotime($booking['booking_date'])); ?>
                    </span>

                </div>


                <div class="flex justify-between gap-4 py-3 border-b border-slate-200">

                    <span class="text-slate-500">
                        Travelers
                    </span>

                    <span class="font-bold text-slate-900">
                        <?php echo (int)$booking['travelers']; ?>
                    </span>

                </div>


                <div class="flex justify-between gap-4 py-3">

                    <span class="text-slate-500">
                        Total Amount
                    </span>

                    <span class="font-extrabold text-sky-600 text-lg">
                        ₹<?php echo number_format((float)$booking['total_amount'], 2); ?>
                    </span>

                </div>

            </div>


            <div class="mt-8 flex flex-col sm:flex-row gap-3">

                <a
                    href="my-bookings.php"
                    class="flex-1 px-5 py-3.5 rounded-xl bg-sky-500 hover:bg-sky-600 text-white font-bold transition"
                >
                    <i class="fa-solid fa-suitcase-rolling mr-2"></i>
                    My Bookings
                </a>

                <a
                    href="packages.php"
                    class="flex-1 px-5 py-3.5 rounded-xl border border-slate-300 hover:bg-slate-50 text-slate-700 font-bold transition"
                >
                    Browse More
                </a>

            </div>

        </div>

    </div>

</section>

<?php require_once "includes/footer.php"; ?>