<?php

require_once "../includes/auth.php";
?>
<?php

require_once "../config/database.php";

$message = "";
$messageType = "success";


/* =========================
   DELETE ITINERARY
========================= */

if (isset($_GET['delete'])) {

    $deleteId = (int) $_GET['delete'];

    if ($deleteId >
 0) {

        $stmt = $pdo->prepare("
            DELETE FROM package_itinerary
            WHERE id = ?
        ");

        $stmt->execute([$deleteId]);

        header("Location: itinerary.php");
        exit;
    }
}


/* =========================
   ADD ITINERARY
========================= */

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $packageId = isset($_POST['package_id'])
        ? (int) $_POST['package_id']
        : 0;

    $dayNumber = isset($_POST['day_number'])
        ? (int) $_POST['day_number']
        : 0;

    $title = trim($_POST['title'] ?? "");

    $description = trim($_POST['description'] ?? "");


    if ($packageId <= 0 || $dayNumber <= 0 || empty($title)) {

        $message = "Please fill all required fields.";
        $messageType = "error";

    } else {

        /* Check package */

        $checkPackage = $pdo->prepare("
            SELECT id
            FROM packages
            WHERE id = ?
            LIMIT 1
        ");

        $checkPackage->execute([$packageId]);

        if (!$checkPackage->fetch()) {

            $message = "Selected package does not exist.";
            $messageType = "error";

        } else {

            /* Insert itinerary */

            $stmt = $pdo->prepare("
                INSERT INTO package_itinerary
                (
                    package_id,
                    day_number,
                    title,
                    description
                )
                VALUES (?, ?, ?, ?)
            ");

            $stmt->execute([
                $packageId,
                $dayNumber,
                $title,
                $description
            ]);

            $message = "Itinerary added successfully.";
            $messageType = "success";
        }
    }
}


/* =========================
   FETCH PACKAGES
========================= */

$packageStmt = $pdo->query("
    SELECT
        id,
        title,
        duration
    FROM packages
    ORDER BY title ASC
");

$packages = $packageStmt->fetchAll(PDO::FETCH_ASSOC);


/* =========================
   FETCH ITINERARY
========================= */

$itineraryStmt = $pdo->query("
    SELECT
        package_itinerary.*,
        packages.title AS package_title
    FROM package_itinerary
    LEFT JOIN packages
        ON package_itinerary.package_id = packages.id
    ORDER BY
        packages.title ASC,
        package_itinerary.day_number ASC
");

$itineraries = $itineraryStmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Manage Itinerary - TravelGo Admin</title>

    <script src="https://cdn.tailwindcss.com"></script>

    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
    >

    <link rel="stylesheet" href="../assets/css/admin.css">
</head>


<body class="bg-slate-100 text-slate-800 tg-admin-layout">

<?php include_once "sidebar.php"; ?>


<!-- =========================
     HEADER
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

<main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">


    <!-- PAGE TITLE -->

    <div class="mb-8">

        <p
            class="text-sky-600 font-bold text-sm uppercase tracking-wider"
        >

            Package Management

        </p>

        <h2 class="text-3xl font-extrabold text-slate-900 mt-2">

            Manage Itinerary

        </h2>

        <p class="text-slate-500 mt-2">

            Add day-by-day itinerary for your tour packages.

        </p>

    </div>



    <!-- =========================
         MESSAGE
    ========================= -->

    <?php if (!empty($message)): ?>

        <div
            class="mb-6 p-4 rounded-xl
            <?php echo $messageType === 'error'
                ? 'bg-red-50 text-red-700 border border-red-200'
                : 'bg-green-50 text-green-700 border border-green-200'; ?>"
        >

            <i
                class="fa-solid
                <?php echo $messageType === 'error'
                    ? 'fa-circle-exclamation'
                    : 'fa-circle-check'; ?>
                mr-2"
            ></i>

            <?php echo htmlspecialchars($message); ?>

        </div>

    <?php endif; ?>



    <div class="grid lg:grid-cols-3 gap-8">


        <!-- =========================
             ADD ITINERARY FORM
        ========================= -->

        <div class="lg:col-span-1">

            <div
                class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6"
            >

                <div class="flex items-center gap-3 mb-6">

                    <div
                        class="w-11 h-11 rounded-xl bg-sky-100 flex items-center justify-center"
                    >

                        <i class="fa-solid fa-plus text-sky-600"></i>

                    </div>

                    <div>

                        <h3 class="text-xl font-bold text-slate-900">

                            Add Itinerary

                        </h3>

                        <p class="text-sm text-slate-500">

                            Add a new tour day

                        </p>

                    </div>

                </div>


                <form
                    method="POST"
                    class="space-y-5"
                >


                    <!-- Package -->

                    <div>

                        <label
                            class="block text-sm font-semibold text-slate-700 mb-2"
                        >

                            Select Package

                            <span class="text-red-500">*</span>

                        </label>

                        <select
                            name="package_id"
                            required
                            class="w-full border border-slate-300 rounded-xl px-4 py-3 outline-none focus:ring-2 focus:ring-sky-500"
                        >

                            <option value="">

                                Select Package

                            </option>


                            <?php foreach ($packages as $package): ?>

                                <option
                                    value="<?php echo (int)$package['id']; ?>"
                                >

                                    <?php
                                    echo htmlspecialchars($package['title']);

                                    if (!empty($package['duration'])) {
                                        echo " (" .
                                            htmlspecialchars($package['duration'])
                                            . ")";
                                    }
                                    ?>

                                </option>

                            <?php endforeach; ?>

                        </select>

                    </div>



                    <!-- Day Number -->

                    <div>

                        <label
                            class="block text-sm font-semibold text-slate-700 mb-2"
                        >

                            Day Number

                            <span class="text-red-500">*</span>

                        </label>

                        <input
                            type="number"
                            name="day_number"
                            min="1"
                            placeholder="Example: 1"
                            required
                            class="w-full border border-slate-300 rounded-xl px-4 py-3 outline-none focus:ring-2 focus:ring-sky-500"
                        >

                    </div>



                    <!-- Title -->

                    <div>

                        <label
                            class="block text-sm font-semibold text-slate-700 mb-2"
                        >

                            Day Title

                            <span class="text-red-500">*</span>

                        </label>

                        <input
                            type="text"
                            name="title"
                            placeholder="Example: Jaipur Sightseeing"
                            required
                            class="w-full border border-slate-300 rounded-xl px-4 py-3 outline-none focus:ring-2 focus:ring-sky-500"
                        >

                    </div>



                    <!-- Description -->

                    <div>

                        <label
                            class="block text-sm font-semibold text-slate-700 mb-2"
                        >

                            Description

                        </label>

                        <textarea
                            name="description"
                            rows="5"
                            placeholder="Describe activities, places and plan for this day..."
                            class="w-full border border-slate-300 rounded-xl px-4 py-3 outline-none resize-none focus:ring-2 focus:ring-sky-500"
                        ></textarea>

                    </div>



                    <!-- Submit -->

                    <button
                        type="submit"
                        class="w-full bg-sky-500 hover:bg-sky-600 text-white py-3.5 rounded-xl font-bold transition"
                    >

                        <i class="fa-solid fa-plus mr-2"></i>

                        Add Itinerary

                    </button>

                </form>

            </div>

        </div>



        <!-- =========================
             ITINERARY LIST
        ========================= -->

        <div class="lg:col-span-2">

            <div
                class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden"
            >


                <!-- Heading -->

                <div
                    class="p-6 border-b border-slate-200"
                >

                    <h3 class="text-xl font-bold text-slate-900">

                        All Itineraries

                    </h3>

                    <p class="text-sm text-slate-500 mt-1">

                        Manage package day plans

                    </p>

                </div>



                <?php if (!empty($itineraries)): ?>

                    <div class="divide-y divide-slate-200">

                        <?php foreach ($itineraries as $item): ?>

                            <div class="p-6">

                                <div class="flex items-start gap-4">


                                    <!-- Day -->

                                    <div
                                        class="w-12 h-12 rounded-xl bg-sky-500 text-white flex items-center justify-center font-bold flex-shrink-0"
                                    >

                                        <?php
                                        echo (int)$item['day_number'];
                                        ?>

                                    </div>


                                    <!-- Content -->

                                    <div class="flex-1 min-w-0">

                                        <div
                                            class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-3"
                                        >

                                            <div>

                                                <p
                                                    class="text-xs uppercase tracking-wider font-bold text-sky-600"
                                                >

                                                    <?php
                                                    echo htmlspecialchars(
                                                        $item['package_title']
                                                    );
                                                    ?>

                                                </p>

                                                <h4
                                                    class="text-lg font-bold text-slate-900 mt-1"
                                                >

                                                    Day
                                                    <?php
                                                    echo (int)$item['day_number'];
                                                    ?>:

                                                    <?php
                                                    echo htmlspecialchars(
                                                        $item['title']
                                                    );
                                                    ?>

                                                </h4>

                                            </div>


                                            <!-- Delete -->

                                            <a
                                                href="itinerary.php?delete=<?php echo (int)$item['id']; ?>"
                                                onclick="return confirm('Are you sure you want to delete this itinerary?');"
                                                class="text-red-500 hover:text-red-700 font-semibold text-sm"
                                            >

                                                <i class="fa-solid fa-trash mr-1"></i>

                                                Delete

                                            </a>

                                        </div>


                                        <?php if (!empty($item['description'])): ?>

                                            <p
                                                class="text-slate-600 leading-7 mt-3"
                                            >

                                                <?php
                                                echo nl2br(
                                                    htmlspecialchars(
                                                        $item['description']
                                                    )
                                                );
                                                ?>

                                            </p>

                                        <?php endif; ?>

                                    </div>

                                </div>

                            </div>

                        <?php endforeach; ?>

                    </div>

                <?php else: ?>

                    <!-- Empty -->

                    <div class="p-12 text-center">

                        <div
                            class="w-16 h-16 rounded-full bg-slate-100 flex items-center justify-center mx-auto"
                        >

                            <i
                                class="fa-solid fa-route text-2xl text-slate-400"
                            ></i>

                        </div>

                        <h3
                            class="text-xl font-bold text-slate-900 mt-5"
                        >

                            No Itinerary Added

                        </h3>

                        <p class="text-slate-500 mt-2">

                            Add your first package itinerary using the form.

                        </p>

                    </div>

                <?php endif; ?>

            </div>

        </div>

    </div>

</main>


<script src="../assets/js/admin.js"></script>
</body>

</html>
```
