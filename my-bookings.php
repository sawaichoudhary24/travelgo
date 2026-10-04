<?php

require_once "config/database.php";

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$stmt = $pdo->prepare("
    SELECT
        bookings.*,
        packages.title AS package_title,
        packages.image AS package_image,
        destinations.name AS destination_name,
        destinations.location AS destination_location
    FROM bookings
    LEFT JOIN packages
        ON bookings.package_id = packages.id
    LEFT JOIN destinations
        ON packages.destination_id = destinations.id
    WHERE bookings.user_id = ?
    ORDER BY bookings.id DESC
");

$stmt->execute([$_SESSION['user_id']]);

$bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);

require_once "includes/header.php";
require_once "includes/navbar.php";

?>

<section class="py-14 px-4">

    <div class="max-w-7xl mx-auto">

        <div class="mb-10">

            <span class="text-sky-500 font-bold uppercase tracking-wider text-sm">
                Your Trips
            </span>

            <h1 class="text-3xl md:text-4xl font-extrabold text-slate-900 mt-2">
                My Bookings
            </h1>

            <p class="text-slate-500 mt-2">
                Manage and view all your TravelGo bookings.
            </p>

        </div>


        <?php if (empty($bookings)): ?>

            <div class="bg-white rounded-3xl border border-slate-200 p-12 text-center">

                <div class="w-20 h-20 bg-sky-50 text-sky-500 rounded-full flex items-center justify-center mx-auto mb-5">

                    <i class="fa-solid fa-suitcase-rolling text-3xl"></i>

                </div>

                <h2 class="text-2xl font-bold text-slate-900">
                    No Bookings Yet
                </h2>

                <p class="text-slate-500 mt-2 mb-6">
                    You haven't booked any trips yet.
                </p>

                <a
                    href="packages.php"
                    class="inline-flex items-center gap-2 px-6 py-3.5 bg-sky-500 hover:bg-sky-600 text-white rounded-xl font-bold"
                >
                    <i class="fa-solid fa-magnifying-glass"></i>
                    Explore Packages
                </a>

            </div>


        <?php else: ?>

            <div class="grid md:grid-cols-2 xl:grid-cols-3 gap-6">

                <?php foreach ($bookings as $booking): ?>

                    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">

                        <?php if (!empty($booking['package_image'])): ?>

                            <img
                                src="uploads/<?php echo htmlspecialchars($booking['package_image']); ?>"
                                alt="<?php echo htmlspecialchars($booking['package_title']); ?>"
                                class="w-full h-52 object-cover"
                            >

                        <?php else: ?>

                            <div class="w-full h-52 bg-slate-100 flex items-center justify-center">

                                <i class="fa-solid fa-image text-4xl text-slate-300"></i>

                            </div>

                        <?php endif; ?>


                        <div class="p-6">

                            <div class="flex items-center justify-between gap-3">

                                <span class="text-xs font-bold text-sky-600 bg-sky-50 px-3 py-1.5 rounded-full">

                                    #<?php echo $booking['id']; ?>

                                </span>


                                <?php

                                $status = $booking['status'];

                                $statusClass = "bg-orange-50 text-orange-600";

                                if ($status === "confirmed") {
                                    $statusClass = "bg-green-50 text-green-600";
                                }

                                if ($status === "cancelled") {
                                    $statusClass = "bg-red-50 text-red-600";
                                }

                                ?>

                                <span class="text-xs font-bold px-3 py-1.5 rounded-full <?php echo $statusClass; ?>">

                                    <?php echo ucfirst(htmlspecialchars($status)); ?>

                                </span>

                            </div>


                            <h2 class="text-xl font-extrabold text-slate-900 mt-4">

                                <?php echo htmlspecialchars($booking['package_title'] ?? 'Package'); ?>

                            </h2>


                            <p class="text-sm text-slate-500 mt-2">

                                <i class="fa-solid fa-location-dot text-sky-500 mr-1"></i>

                                <?php echo htmlspecialchars($booking['destination_name'] ?? 'Destination'); ?>

                            </p>


                            <div class="mt-5 space-y-3 text-sm">

                                <div class="flex justify-between">

                                    <span class="text-slate-500">
                                        Travel Date
                                    </span>

                                    <span class="font-semibold text-slate-800">
                                        <?php echo date("d M Y", strtotime($booking['booking_date'])); ?>
                                    </span>

                                </div>


                                <div class="flex justify-between">

                                    <span class="text-slate-500">
                                        Travelers
                                    </span>

                                    <span class="font-semibold text-slate-800">
                                        <?php echo (int)$booking['travelers']; ?>
                                    </span>

                                </div>


                                <div class="flex justify-between pt-3 border-t border-slate-200">

                                    <span class="font-bold text-slate-900">
                                        Total
                                    </span>

                                    <span class="font-extrabold text-sky-600">
                                        ₹<?php echo number_format((float)$booking['total_amount'], 2); ?>
                                    </span>

                                </div>

                            </div>

                        </div>

                    </div>

                <?php endforeach; ?>

            </div>

        <?php endif; ?>

    </div>

</section>

<?php require_once "includes/footer.php"; ?>