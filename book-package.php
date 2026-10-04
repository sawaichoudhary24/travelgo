<?php

require_once "config/database.php";

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/* User login check */
if (!isset($_SESSION['user_id'])) {

    header("Location: login.php");
    exit;
}

/* Package ID */
$packageId = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if ($packageId <= 0) {
    header("Location: packages.php");
    exit;
}

/* Get package */
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

$stmt->execute([$packageId]);

$package = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$package) {
    header("Location: packages.php");
    exit;
}

$error = "";

/* Booking submit */
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $bookingDate = $_POST["booking_date"] ?? "";
    $travelers = (int) ($_POST["travelers"] ?? 1);

    if ($bookingDate === "") {

        $error = "Please select your travel date.";

    } elseif ($travelers < 1 || $travelers > 20) {

        $error = "Travelers must be between 1 and 20.";

    } elseif ($bookingDate < date("Y-m-d")) {

        $error = "Please select a future travel date.";

    } else {

        $price = (float) $package["price"];

        $totalAmount = $price * $travelers;

        $insertStmt = $pdo->prepare("
            INSERT INTO bookings
            (
                user_id,
                package_id,
                booking_date,
                travelers,
                total_amount,
                status
            )
            VALUES (?, ?, ?, ?, ?, 'pending')
        ");

        $insertStmt->execute([
            $_SESSION["user_id"],
            $packageId,
            $bookingDate,
            $travelers,
            $totalAmount
        ]);

        $bookingId = $pdo->lastInsertId();

        header("Location: booking-success.php?id=" . $bookingId);
        exit;
    }
}

require_once "includes/header.php";
require_once "includes/navbar.php";

?>

<section class="py-14 px-4">

    <div class="max-w-6xl mx-auto">

        <!-- Header -->

        <div class="mb-10">

            <a
                href="package-details.php?id=<?php echo $packageId; ?>"
                class="inline-flex items-center gap-2 text-sky-600 font-semibold mb-4"
            >
                <i class="fa-solid fa-arrow-left"></i>
                Back to Package
            </a>

            <h1 class="text-3xl md:text-4xl font-extrabold text-slate-900">
                Book Your Trip
            </h1>

            <p class="text-slate-500 mt-2">
                Complete the form below to reserve your package.
            </p>

        </div>


        <div class="grid lg:grid-cols-3 gap-8">


            <!-- ================= FORM ================= -->

            <div class="lg:col-span-2">

                <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6 md:p-8">

                    <h2 class="text-xl font-bold text-slate-900 mb-6">
                        Booking Information
                    </h2>


                    <?php if ($error): ?>

                        <div class="mb-6 p-4 rounded-xl bg-red-50 border border-red-200 text-red-700">

                            <i class="fa-solid fa-circle-exclamation mr-2"></i>

                            <?php echo htmlspecialchars($error); ?>

                        </div>

                    <?php endif; ?>


                    <form method="POST" id="bookingForm" class="space-y-6">


                        <!-- Name -->

                        <div>

                            <label class="block text-sm font-bold text-slate-700 mb-2">
                                Full Name
                            </label>

                            <div class="relative">

                                <i class="fa-solid fa-user absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>

                                <input
                                    type="text"
                                    value="<?php echo htmlspecialchars($_SESSION["user_name"]); ?>"
                                    readonly
                                    class="w-full pl-11 pr-4 py-3.5 rounded-xl border border-slate-300 bg-slate-50 text-slate-600 outline-none"
                                >

                            </div>

                        </div>


                        <!-- Email -->

                        <div>

                            <label class="block text-sm font-bold text-slate-700 mb-2">
                                Email
                            </label>

                            <div class="relative">

                                <i class="fa-solid fa-envelope absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>

                                <input
                                    type="email"
                                    value="<?php echo htmlspecialchars($_SESSION["user_email"]); ?>"
                                    readonly
                                    class="w-full pl-11 pr-4 py-3.5 rounded-xl border border-slate-300 bg-slate-50 text-slate-600 outline-none"
                                >

                            </div>

                        </div>


                        <!-- Date + Travelers -->

                        <div class="grid md:grid-cols-2 gap-5">


                            <!-- Date -->

                            <div>

                                <label class="block text-sm font-bold text-slate-700 mb-2">
                                    Travel Date
                                </label>

                                <div class="relative">

                                    <i class="fa-solid fa-calendar-days absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>

                                    <input
                                        type="date"
                                        name="booking_date"
                                        min="<?php echo date('Y-m-d'); ?>"
                                        value="<?php echo htmlspecialchars($_POST['booking_date'] ?? ''); ?>"
                                        required
                                        class="w-full pl-11 pr-4 py-3.5 rounded-xl border border-slate-300 focus:border-sky-500 focus:ring-2 focus:ring-sky-100 outline-none"
                                    >

                                </div>

                            </div>


                            <!-- Travelers -->

                            <div>

                                <label class="block text-sm font-bold text-slate-700 mb-2">
                                    Number of Travelers
                                </label>

                                <div class="relative">

                                    <i class="fa-solid fa-users absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>

                                    <input
                                        type="number"
                                        name="travelers"
                                        id="travelers"
                                        min="1"
                                        max="20"
                                        value="<?php echo (int) ($_POST['travelers'] ?? 1); ?>"
                                        required
                                        class="w-full pl-11 pr-4 py-3.5 rounded-xl border border-slate-300 focus:border-sky-500 focus:ring-2 focus:ring-sky-100 outline-none"
                                    >

                                </div>

                            </div>

                        </div>


                        <!-- Total -->

                        <div class="bg-sky-50 border border-sky-100 rounded-2xl p-5">

                            <div class="flex items-center justify-between">

                                <span class="text-slate-600 font-medium">
                                    Total Amount
                                </span>

                                <span
                                    id="totalAmount"
                                    class="text-2xl font-extrabold text-sky-600"
                                >
                                    ₹<?php echo number_format((float)$package["price"], 2); ?>
                                </span>

                            </div>

                        </div>


                        <!-- Submit -->

                        <button
                            type="submit"
                            class="w-full py-4 rounded-xl bg-sky-500 hover:bg-sky-600 text-white font-bold text-lg transition"
                        >

                            <i class="fa-solid fa-calendar-check mr-2"></i>

                            Confirm Booking

                        </button>

                    </form>

                </div>

            </div>


            <!-- ================= PACKAGE SUMMARY ================= -->

            <div>

                <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden sticky top-24">

                    <?php if (!empty($package["image"])): ?>

                        <img
                            src="uploads/<?php echo htmlspecialchars($package["image"]); ?>"
                            alt="<?php echo htmlspecialchars($package["title"]); ?>"
                            class="w-full h-56 object-cover"
                        >

                    <?php else: ?>

                        <div class="w-full h-56 bg-slate-100 flex items-center justify-center">

                            <i class="fa-solid fa-image text-4xl text-slate-300"></i>

                        </div>

                    <?php endif; ?>


                    <div class="p-6">

                        <span class="inline-flex items-center gap-2 text-sm font-semibold text-sky-600 bg-sky-50 px-3 py-1.5 rounded-full">

                            <i class="fa-solid fa-location-dot"></i>

                            <?php echo htmlspecialchars($package["destination_name"] ?? "Travel Destination"); ?>

                        </span>


                        <h2 class="text-xl font-extrabold text-slate-900 mt-4">

                            <?php echo htmlspecialchars($package["title"]); ?>

                        </h2>


                        <div class="mt-5 space-y-4">


                            <div class="flex items-center justify-between">

                                <span class="text-slate-500">
                                    Price / Person
                                </span>

                                <span class="font-bold text-slate-900">
                                    ₹<?php echo number_format((float)$package["price"], 2); ?>
                                </span>

                            </div>


                            <div class="flex items-center justify-between">

                                <span class="text-slate-500">
                                    Duration
                                </span>

                                <span class="font-bold text-slate-900">

                                    <?php echo htmlspecialchars($package["duration"] ?? "N/A"); ?>

                                </span>

                            </div>


                            <div class="border-t border-slate-200 pt-4 flex items-center justify-between">

                                <span class="font-bold text-slate-900">
                                    Booking Status
                                </span>

                                <span class="text-orange-600 font-bold">
                                    Pending
                                </span>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

</section>


<script>

const travelersInput = document.getElementById("travelers");
const totalAmount = document.getElementById("totalAmount");

const pricePerPerson = <?php echo json_encode((float)$package["price"]); ?>;

function updateTotal() {

    let travelers = parseInt(travelersInput.value) || 1;

    if (travelers < 1) {
        travelers = 1;
    }

    const total = pricePerPerson * travelers;

    totalAmount.textContent =
        "₹" + total.toLocaleString("en-IN", {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
}

travelersInput.addEventListener("input", updateTotal);

updateTotal();

</script>


<?php require_once "includes/footer.php"; ?>