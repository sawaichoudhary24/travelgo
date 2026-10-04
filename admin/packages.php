<?php

require_once "../includes/auth.php";
require_once "../config/database.php";

$message = "";
$messageType = "success";

$editPackage = null;


/* =========================
   EDIT MODE
========================= */

if (isset($_GET['edit'])) {

    $editId = (int) $_GET['edit'];

    if ($editId >
 0) {

        $stmt = $pdo->prepare("
            SELECT *
            FROM packages
            WHERE id = ?
            LIMIT 1
        ");

        $stmt->execute([$editId]);

        $editPackage = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$editPackage) {
            header("Location: packages.php");
            exit;
        }
    }
}


/* =========================
   DELETE PACKAGE
========================= */

if (isset($_GET['delete'])) {

    $deleteId = (int) $_GET['delete'];

    if ($deleteId > 0) {

        /* Get image */

        $imageStmt = $pdo->prepare("
            SELECT image
            FROM packages
            WHERE id = ?
            LIMIT 1
        ");

        $imageStmt->execute([$deleteId]);

        $package = $imageStmt->fetch(PDO::FETCH_ASSOC);


        /* Delete package */

        $stmt = $pdo->prepare("
            DELETE FROM packages
            WHERE id = ?
        ");

        $stmt->execute([$deleteId]);


        /* Delete image */

        if (
            $package &&
            !empty($package['image'])
        ) {

            $imagePath = "../uploads/" . $package['image'];

            if (file_exists($imagePath)) {
                unlink($imagePath);
            }
        }


        header("Location: packages.php");
        exit;
    }
}


/* =========================
   ADD / UPDATE PACKAGE
========================= */

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $packageId = isset($_POST['package_id'])
        ? (int) $_POST['package_id']
        : 0;

    $destinationId = isset($_POST['destination_id'])
        ? (int) $_POST['destination_id']
        : 0;

    $title = trim($_POST['title'] ?? "");
    $slug = trim($_POST['slug'] ?? "");
    $duration = trim($_POST['duration'] ?? "");
    $price = trim($_POST['price'] ?? "");
    $description = trim($_POST['description'] ?? "");
    $status = $_POST['status'] ?? "active";


    /* =========================
       VALIDATION
    ========================= */

    if (
        empty($title) ||
        empty($slug) ||
        empty($duration) ||
        $price === ""
    ) {

        $message = "Please fill all required fields.";
        $messageType = "error";

    } elseif (!is_numeric($price) || (float)$price < 0) {

        $message = "Please enter a valid price.";
        $messageType = "error";

    } elseif (
        $destinationId > 0
    ) {

        /* Check destination */

        $destinationCheck = $pdo->prepare("
            SELECT id
            FROM destinations
            WHERE id = ?
            LIMIT 1
        ");

        $destinationCheck->execute([
            $destinationId
        ]);

        if (!$destinationCheck->fetch()) {

            $message = "Selected destination does not exist.";
            $messageType = "error";
        }
    }


    /* =========================
       IMAGE UPLOAD
    ========================= */

    $newImage = "";

    if ($messageType !== "error") {

        if (
            isset($_FILES['image']) &&
            $_FILES['image']['error'] === UPLOAD_ERR_OK
        ) {

            $allowedTypes = [
                'image/jpeg',
                'image/png',
                'image/webp'
            ];

            $fileType = mime_content_type(
                $_FILES['image']['tmp_name']
            );

            if (!in_array($fileType, $allowedTypes)) {

                $message = "Only JPG, JPEG, PNG and WEBP images are allowed.";
                $messageType = "error";

            } else {

                $extension = strtolower(
                    pathinfo(
                        $_FILES['image']['name'],
                        PATHINFO_EXTENSION
                    )
                );

                $newImage =
                    time() . "_" .
                    uniqid() .
                    "." .
                    $extension;

                $uploadPath =
                    "../uploads/" . $newImage;

                if (!move_uploaded_file(
                    $_FILES['image']['tmp_name'],
                    $uploadPath
                )) {

                    $message = "Image upload failed.";
                    $messageType = "error";

                    $newImage = "";
                }
            }
        }
    }


    /* =========================
       UPDATE
    ========================= */

    if (
        $messageType !== "error" &&
        $packageId > 0
    ) {

        /* Get old package */

        $oldStmt = $pdo->prepare("
            SELECT *
            FROM packages
            WHERE id = ?
            LIMIT 1
        ");

        $oldStmt->execute([
            $packageId
        ]);

        $oldPackage = $oldStmt->fetch(PDO::FETCH_ASSOC);


        if (!$oldPackage) {

            $message = "Package not found.";
            $messageType = "error";

        } else {

            /* Keep old image */

            $imageName = !empty($newImage)
                ? $newImage
                : $oldPackage['image'];


            $updateStmt = $pdo->prepare("
                UPDATE packages
                SET
                    destination_id = ?,
                    title = ?,
                    slug = ?,
                    description = ?,
                    duration = ?,
                    price = ?,
                    image = ?,
                    status = ?
                WHERE id = ?
            ");

            $updateStmt->execute([
                $destinationId > 0
                    ? $destinationId
                    : null,
                $title,
                $slug,
                $description,
                $duration,
                (float)$price,
                $imageName,
                $status,
                $packageId
            ]);


            /* Delete old image */

            if (
                !empty($newImage) &&
                !empty($oldPackage['image'])
            ) {

                $oldImagePath =
                    "../uploads/" .
                    $oldPackage['image'];

                if (file_exists($oldImagePath)) {
                    unlink($oldImagePath);
                }
            }


            header("Location: packages.php");
            exit;
        }
    }


    /* =========================
       ADD NEW PACKAGE
    ========================= */

    elseif (
        $messageType !== "error" &&
        $packageId <= 0
    ) {

        $imageName = $newImage;


        $insertStmt = $pdo->prepare("
            INSERT INTO packages
            (
                destination_id,
                title,
                slug,
                description,
                duration,
                price,
                image,
                status
            )
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");

        $insertStmt->execute([
            $destinationId > 0
                ? $destinationId
                : null,
            $title,
            $slug,
            $description,
            $duration,
            (float)$price,
            $imageName,
            $status
        ]);


        header("Location: packages.php");
        exit;
    }
}


/* =========================
   FETCH DESTINATIONS
========================= */

$destinationStmt = $pdo->query("
    SELECT *
    FROM destinations
    ORDER BY name ASC
");

$destinations =
    $destinationStmt->fetchAll(PDO::FETCH_ASSOC);


/* =========================
   FETCH PACKAGES
========================= */

$packageStmt = $pdo->query("
    SELECT
        packages.*,
        destinations.name AS destination_name,
        destinations.location AS destination_location
    FROM packages
    LEFT JOIN destinations
        ON packages.destination_id = destinations.id
    ORDER BY packages.id DESC
");

$packages =
    $packageStmt->fetchAll(PDO::FETCH_ASSOC);

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
        Packages - TravelGo Admin
    </title>


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



<!-- =========================
     MAIN
========================= -->

<main
    class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10"
>


    <!-- TITLE -->

    <div class="mb-8">

        <p
            class="text-sky-600 font-bold text-sm uppercase tracking-wider"
        >

            Travel Management

        </p>

        <h2
            class="text-3xl font-extrabold text-slate-900 mt-2"
        >

            Tour Packages

        </h2>

        <p class="text-slate-500 mt-2">

            Add, edit and manage your tour packages.

        </p>

    </div>



    <!-- MESSAGE -->

    <?php if (!empty($message)): ?>

        <div
            class="mb-6 p-4 rounded-xl
            <?php
            echo $messageType === 'error'
                ? 'bg-red-50 text-red-700 border border-red-200'
                : 'bg-green-50 text-green-700 border border-green-200';
            ?>"
        >

            <i
                class="fa-solid
                <?php
                echo $messageType === 'error'
                    ? 'fa-circle-exclamation'
                    : 'fa-circle-check';
                ?>
                mr-2"
            ></i>

            <?php
            echo htmlspecialchars($message);
            ?>

        </div>

    <?php endif; ?>



    <div class="grid lg:grid-cols-3 gap-8">


        <!-- =========================
             FORM
        ========================= -->

        <div>

            <div
                class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6"
            >

                <div class="flex items-center gap-3 mb-6">

                    <div
                        class="w-11 h-11 rounded-xl
                        <?php
                        echo $editPackage
                            ? 'bg-amber-100'
                            : 'bg-sky-100';
                        ?>
                        flex items-center justify-center"
                    >

                        <i
                            class="fa-solid
                            <?php
                            echo $editPackage
                                ? 'fa-pen'
                                : 'fa-plus';
                            ?>
                            <?php
                            echo $editPackage
                                ? 'text-amber-600'
                                : 'text-sky-600';
                            ?>"
                        ></i>

                    </div>


                    <div>

                        <h3
                            class="text-xl font-bold text-slate-900"
                        >

                            <?php
                            echo $editPackage
                                ? 'Edit Package'
                                : 'Add Package';
                            ?>

                        </h3>

                        <p class="text-sm text-slate-500">

                            <?php
                            echo $editPackage
                                ? 'Update package details'
                                : 'Create a new tour package';
                            ?>

                        </p>

                    </div>

                </div>



                <form
                    method="POST"
                    enctype="multipart/form-data"
                    class="space-y-5"
                >


                    <!-- Hidden ID -->

                    <?php if ($editPackage): ?>

                        <input
                            type="hidden"
                            name="package_id"
                            value="<?php echo (int)$editPackage['id']; ?>"
                        >

                    <?php endif; ?>



                    <!-- Destination -->

                    <div>

                        <label
                            class="block text-sm font-semibold text-slate-700 mb-2"
                        >

                            Destination

                        </label>


                        <select
                            name="destination_id"
                            class="w-full border border-slate-300 rounded-xl px-4 py-3 outline-none focus:ring-2 focus:ring-sky-500"
                        >

                            <option value="">

                                Select Destination

                            </option>


                            <?php foreach ($destinations as $destination): ?>

                                <option
                                    value="<?php echo (int)$destination['id']; ?>"
                                    <?php
                                    if (
                                        $editPackage &&
                                        (int)$editPackage['destination_id']
                                        === (int)$destination['id']
                                    ) {
                                        echo 'selected';
                                    }
                                    ?>
                                >

                                    <?php
                                    echo htmlspecialchars(
                                        $destination['name']
                                    );
                                    ?>

                                </option>

                            <?php endforeach; ?>

                        </select>

                    </div>



                    <!-- Title -->

                    <div>

                        <label
                            class="block text-sm font-semibold text-slate-700 mb-2"
                        >

                            Package Title
                            <span class="text-red-500">*</span>

                        </label>


                        <input
                            type="text"
                            name="title"
                            required
                            placeholder="Example: Rajasthan Royal Tour"
                            value="<?php
                            echo $editPackage
                                ? htmlspecialchars($editPackage['title'])
                                : '';
                            ?>"
                            class="w-full border border-slate-300 rounded-xl px-4 py-3 outline-none focus:ring-2 focus:ring-sky-500"
                        >

                    </div>



                    <!-- Slug -->

                    <div>

                        <label
                            class="block text-sm font-semibold text-slate-700 mb-2"
                        >

                            Slug
                            <span class="text-red-500">*</span>

                        </label>


                        <input
                            type="text"
                            name="slug"
                            required
                            placeholder="Example: rajasthan-royal-tour"
                            value="<?php
                            echo $editPackage
                                ? htmlspecialchars($editPackage['slug'])
                                : '';
                            ?>"
                            class="w-full border border-slate-300 rounded-xl px-4 py-3 outline-none focus:ring-2 focus:ring-sky-500"
                        >

                    </div>



                    <!-- Duration -->

                    <div>

                        <label
                            class="block text-sm font-semibold text-slate-700 mb-2"
                        >

                            Duration
                            <span class="text-red-500">*</span>

                        </label>


                        <input
                            type="text"
                            name="duration"
                            required
                            placeholder="Example: 5 Days / 4 Nights"
                            value="<?php
                            echo $editPackage
                                ? htmlspecialchars($editPackage['duration'])
                                : '';
                            ?>"
                            class="w-full border border-slate-300 rounded-xl px-4 py-3 outline-none focus:ring-2 focus:ring-sky-500"
                        >

                    </div>



                    <!-- Price -->

                    <div>

                        <label
                            class="block text-sm font-semibold text-slate-700 mb-2"
                        >

                            Price
                            <span class="text-red-500">*</span>

                        </label>


                        <div class="relative">

                            <span
                                class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-500"
                            >

                                ₹

                            </span>


                            <input
                                type="number"
                                name="price"
                                min="0"
                                step="0.01"
                                required
                                placeholder="12999"
                                value="<?php
                                echo $editPackage
                                    ? htmlspecialchars($editPackage['price'])
                                    : '';
                                ?>"
                                class="w-full border border-slate-300 rounded-xl pl-9 pr-4 py-3 outline-none focus:ring-2 focus:ring-sky-500"
                            >

                        </div>

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
                            placeholder="Write package description..."
                            class="w-full border border-slate-300 rounded-xl px-4 py-3 outline-none resize-none focus:ring-2 focus:ring-sky-500"
                        ><?php
                        echo $editPackage
                            ? htmlspecialchars($editPackage['description'])
                            : '';
                        ?></textarea>

                    </div>



                    <!-- Status -->

                    <div>

                        <label
                            class="block text-sm font-semibold text-slate-700 mb-2"
                        >

                            Status

                        </label>


                        <select
                            name="status"
                            class="w-full border border-slate-300 rounded-xl px-4 py-3 outline-none focus:ring-2 focus:ring-sky-500"
                        >

                            <option
                                value="active"
                                <?php
                                if (
                                    !$editPackage ||
                                    $editPackage['status'] === 'active'
                                ) {
                                    echo 'selected';
                                }
                                ?>
                            >

                                Active

                            </option>


                            <option
                                value="inactive"
                                <?php
                                if (
                                    $editPackage &&
                                    $editPackage['status'] === 'inactive'
                                ) {
                                    echo 'selected';
                                }
                                ?>
                            >

                                Inactive

                            </option>

                        </select>

                    </div>



                    <!-- Current Image -->

                    <?php if (
                        $editPackage &&
                        !empty($editPackage['image'])
                    ): ?>

                        <div>

                            <p
                                class="text-sm font-semibold text-slate-700 mb-2"
                            >

                                Current Image

                            </p>


                            <img
                                src="../uploads/<?php echo htmlspecialchars($editPackage['image']); ?>"
                                alt="Package Image"
                                class="w-full h-40 object-cover rounded-xl border border-slate-200"
                            >

                        </div>

                    <?php endif; ?>



                    <!-- Image -->

                    <div>

                        <label
                            class="block text-sm font-semibold text-slate-700 mb-2"
                        >

                            <?php
                            echo $editPackage
                                ? 'Change Image'
                                : 'Package Image';
                            ?>

                        </label>


                        <input
                            type="file"
                            name="image"
                            accept=".jpg,.jpeg,.png,.webp"
                            class="w-full border border-slate-300 rounded-xl px-4 py-3 bg-white"
                        >


                        <?php if ($editPackage): ?>

                            <p class="text-xs text-slate-500 mt-2">

                                Leave empty to keep the current image.

                            </p>

                        <?php endif; ?>

                    </div>



                    <!-- Buttons -->

                    <div class="flex gap-3">

                        <?php if ($editPackage): ?>

                            <a
                                href="packages.php"
                                class="w-1/3 border border-slate-300 text-slate-700 hover:bg-slate-50 py-3 rounded-xl font-semibold text-center transition"
                            >

                                Cancel

                            </a>


                            <button
                                type="submit"
                                class="w-2/3 bg-amber-500 hover:bg-amber-600 text-white py-3 rounded-xl font-bold transition"
                            >

                                <i class="fa-solid fa-save mr-2"></i>

                                Update Package

                            </button>

                        <?php else: ?>

                            <button
                                type="submit"
                                class="w-full bg-sky-500 hover:bg-sky-600 text-white py-3.5 rounded-xl font-bold transition"
                            >

                                <i class="fa-solid fa-plus mr-2"></i>

                                Add Package

                            </button>

                        <?php endif; ?>

                    </div>

                </form>

            </div>

        </div>



        <!-- =========================
             PACKAGE LIST
        ========================= -->

        <div class="lg:col-span-2">

            <div
                class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden"
            >

                <div
                    class="p-6 border-b border-slate-200"
                >

                    <h3
                        class="text-xl font-bold text-slate-900"
                    >

                        All Packages

                    </h3>

                    <p class="text-sm text-slate-500 mt-1">

                        Manage your tour packages.

                    </p>

                </div>



                <?php if (!empty($packages)): ?>

                    <div class="divide-y divide-slate-200">


                        <?php foreach ($packages as $package): ?>

                            <div class="p-5">

                                <div
                                    class="flex flex-col sm:flex-row gap-5"
                                >


                                    <!-- Image -->

                                    <div
                                        class="w-full sm:w-44 h-32 flex-shrink-0"
                                    >

                                        <?php if (
                                            !empty($package['image'])
                                        ): ?>

                                            <img
                                                src="../uploads/<?php echo htmlspecialchars($package['image']); ?>"
                                                alt="<?php echo htmlspecialchars($package['title']); ?>"
                                                class="w-full h-full object-cover rounded-xl"
                                            >

                                        <?php else: ?>

                                            <div
                                                class="w-full h-full rounded-xl bg-slate-100 flex items-center justify-center"
                                            >

                                                <i
                                                    class="fa-solid fa-image text-3xl text-slate-300"
                                                ></i>

                                            </div>

                                        <?php endif; ?>

                                    </div>



                                    <!-- Content -->

                                    <div
                                        class="flex-1 min-w-0"
                                    >


                                        <!-- Title -->

                                        <div
                                            class="flex flex-wrap items-start justify-between gap-3"
                                        >

                                            <div>

                                                <h4
                                                    class="text-xl font-bold text-slate-900"
                                                >

                                                    <?php
                                                    echo htmlspecialchars(
                                                        $package['title']
                                                    );
                                                    ?>

                                                </h4>


                                                <?php if (
                                                    !empty($package['destination_name'])
                                                ): ?>

                                                    <p
                                                        class="text-sm text-sky-600 font-semibold mt-1"
                                                    >

                                                        <i
                                                            class="fa-solid fa-location-dot mr-1"
                                                        ></i>

                                                        <?php
                                                        echo htmlspecialchars(
                                                            $package['destination_name']
                                                        );
                                                        ?>

                                                        <?php if (
                                                            !empty($package['destination_location'])
                                                        ): ?>

                                                            •
                                                            <?php
                                                            echo htmlspecialchars(
                                                                $package['destination_location']
                                                            );
                                                            ?>

                                                        <?php endif; ?>

                                                    </p>

                                                <?php endif; ?>

                                            </div>



                                            <!-- Status -->

                                            <?php if (
                                                $package['status'] === 'active'
                                            ): ?>

                                                <span
                                                    class="bg-green-50 text-green-600 px-3 py-1 rounded-full text-xs font-bold"
                                                >

                                                    Active

                                                </span>

                                            <?php else: ?>

                                                <span
                                                    class="bg-red-50 text-red-600 px-3 py-1 rounded-full text-xs font-bold"
                                                >

                                                    Inactive

                                                </span>

                                            <?php endif; ?>

                                        </div>



                                        <!-- Details -->

                                        <div
                                            class="flex flex-wrap gap-5 mt-3 text-sm text-slate-500"
                                        >

                                            <span>

                                                <i
                                                    class="fa-regular fa-clock text-sky-500 mr-1"
                                                ></i>

                                                <?php
                                                echo htmlspecialchars(
                                                    $package['duration']
                                                );
                                                ?>

                                            </span>


                                            <span>

                                                <i
                                                    class="fa-solid fa-indian-rupee-sign text-green-500 mr-1"
                                                ></i>

                                                <strong
                                                    class="text-slate-800"
                                                >

                                                    ₹<?php
                                                    echo number_format(
                                                        (float)$package['price'],
                                                        0
                                                    );
                                                    ?>

                                                </strong>

                                            </span>

                                        </div>



                                        <!-- Description -->

                                        <?php if (
                                            !empty($package['description'])
                                        ): ?>

                                            <p
                                                class="text-sm text-slate-600 mt-3 line-clamp-2"
                                            >

                                                <?php
                                                echo htmlspecialchars(
                                                    $package['description']
                                                );
                                                ?>

                                            </p>

                                        <?php endif; ?>



                                        <!-- Actions -->

                                        <div
                                            class="flex flex-wrap gap-3 mt-4"
                                        >


                                            <!-- Edit -->

                                            <a
                                                href="packages.php?edit=<?php echo (int)$package['id']; ?>"
                                                class="inline-flex items-center gap-2 bg-amber-50 text-amber-600 hover:bg-amber-100 px-4 py-2 rounded-lg text-sm font-semibold transition"
                                            >

                                                <i
                                                    class="fa-solid fa-pen"
                                                ></i>

                                                Edit

                                            </a>



                                            <!-- Delete -->

                                            <a
                                                href="packages.php?delete=<?php echo (int)$package['id']; ?>"
                                                onclick="return confirm('Are you sure you want to delete this package?');"
                                                class="inline-flex items-center gap-2 bg-red-50 text-red-600 hover:bg-red-100 px-4 py-2 rounded-lg text-sm font-semibold transition"
                                            >

                                                <i
                                                    class="fa-solid fa-trash"
                                                ></i>

                                                Delete

                                            </a>



                                            <!-- Itinerary -->

                                            <a
                                                href="itinerary.php"
                                                class="inline-flex items-center gap-2 bg-sky-50 text-sky-600 hover:bg-sky-100 px-4 py-2 rounded-lg text-sm font-semibold transition"
                                            >

                                                <i
                                                    class="fa-solid fa-route"
                                                ></i>

                                                Itinerary

                                            </a>

                                        </div>

                                    </div>

                                </div>

                            </div>

                        <?php endforeach; ?>

                    </div>

                <?php else: ?>

                    <div class="p-12 text-center">

                        <div
                            class="w-16 h-16 rounded-full bg-slate-100 flex items-center justify-center mx-auto"
                        >

                            <i
                                class="fa-solid fa-suitcase-rolling text-2xl text-slate-400"
                            ></i>

                        </div>


                        <h3
                            class="text-xl font-bold text-slate-900 mt-5"
                        >

                            No Packages Found

                        </h3>


                        <p class="text-slate-500 mt-2">

                            Add your first tour package using the form.

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