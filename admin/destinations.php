<?php

require_once "../includes/auth.php";
require_once "../config/database.php";

// Store destination-specific highlight/gallery images as JSON.
try {
    $pdo->exec("ALTER TABLE destinations ADD COLUMN gallery_images TEXT NULL");
} catch (PDOException $e) {
    // Column already exists (or DB user does not have ALTER permission).
}

$message = "";
$messageType = "success";

/* =========================
   EDIT MODE
========================= */

$editDestination = null;

if (isset($_GET['edit'])) {

    $editId = (int) $_GET['edit'];

    if ($editId >
 0) {

        $stmt = $pdo->prepare("
            SELECT *
            FROM destinations
            WHERE id = ?
            LIMIT 1
        ");

        $stmt->execute([$editId]);

        $editDestination = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$editDestination) {
            header("Location: destinations.php");
            exit;
        }
    }
}


/* =========================
   DELETE DESTINATION
========================= */

if (isset($_GET['delete'])) {

    $deleteId = (int) $_GET['delete'];

    if ($deleteId > 0) {

        /* Get image before deleting */

        $imageStmt = $pdo->prepare("
            SELECT image, gallery_images
            FROM destinations
            WHERE id = ?
            LIMIT 1
        ");

        $imageStmt->execute([$deleteId]);

        $destination = $imageStmt->fetch(PDO::FETCH_ASSOC);


        /* Delete destination */

        $stmt = $pdo->prepare("
            DELETE FROM destinations
            WHERE id = ?
        ");

        $stmt->execute([$deleteId]);


        /* Delete image */

        if (
            $destination &&
            !empty($destination['image'])
        ) {

            $imagePath = "../uploads/" . $destination['image'];

            if (file_exists($imagePath)) {
                unlink($imagePath);
            }
        }

        // Remove this destination's private detail/gallery images too.
        if ($destination && !empty($destination['gallery_images'])) {
            $galleryImages = json_decode($destination['gallery_images'], true);
            if (is_array($galleryImages)) {
                foreach ($galleryImages as $galleryImage) {
                    $galleryPath = "../uploads/" . basename($galleryImage);
                    if (file_exists($galleryPath)) { unlink($galleryPath); }
                }
            }
        }


        header("Location: destinations.php");
        exit;
    }
}


/* =========================
   ADD / UPDATE
========================= */

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $destinationId = isset($_POST['destination_id'])
        ? (int) $_POST['destination_id']
        : 0;

    $name = trim($_POST['name'] ?? "");
    $slug = trim($_POST['slug'] ?? "");
    $location = trim($_POST['location'] ?? "");
    $description = trim($_POST['description'] ?? "");

    $oldGalleryImages = [];
    if ($destinationId > 0) {
        $galleryStmt = $pdo->prepare("SELECT gallery_images FROM destinations WHERE id = ? LIMIT 1");
        $galleryStmt->execute([$destinationId]);
        $galleryRaw = $galleryStmt->fetchColumn();
        if ($galleryRaw) {
            $decoded = json_decode($galleryRaw, true);
            if (is_array($decoded)) { $oldGalleryImages = array_values(array_filter($decoded)); }
        }
    }


    /* =========================
       VALIDATION
    ========================= */

    if (
        empty($name) ||
        empty($slug) ||
        empty($location)
    ) {

        $message = "Please fill all required fields.";
        $messageType = "error";

    } else {


        /* =========================
           IMAGE UPLOAD
        ========================= */

        $newImage = "";

        if (
            isset($_FILES['image']) &&
            $_FILES['image']['error'] === UPLOAD_ERR_OK
        ) {

            $allowedTypes = [
                'image/jpeg',
                'image/jpg',
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

                $newImage = time() . "_" . uniqid() . "." . $extension;

                $uploadPath = "../uploads/" . $newImage;

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


        /* =========================
           DESTINATION-SPECIFIC GALLERY IMAGES
           Up to 7 images can be uploaded for each destination.
        ========================= */

        $newGalleryImages = [];
        if (isset($_FILES['gallery_images']) && is_array($_FILES['gallery_images']['name'])) {
            $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp'];
            $total = count($_FILES['gallery_images']['name']);

            if ($total > 7) {
                $message = "You can upload maximum 7 additional destination images.";
                $messageType = "error";
            } else {
                for ($i = 0; $i < $total; $i++) {
                    if ($_FILES['gallery_images']['error'][$i] === UPLOAD_ERR_NO_FILE) continue;
                    if ($_FILES['gallery_images']['error'][$i] !== UPLOAD_ERR_OK) {
                        $message = "One of the gallery images could not be uploaded.";
                        $messageType = "error";
                        break;
                    }

                    $fileType = mime_content_type($_FILES['gallery_images']['tmp_name'][$i]);
                    if (!in_array($fileType, $allowedTypes, true)) {
                        $message = "Gallery images must be JPG, JPEG, PNG or WEBP.";
                        $messageType = "error";
                        break;
                    }

                    $extension = strtolower(pathinfo($_FILES['gallery_images']['name'][$i], PATHINFO_EXTENSION));
                    $galleryName = time() . "_" . uniqid() . "_gallery." . $extension;
                    $galleryPath = "../uploads/" . $galleryName;

                    if (!move_uploaded_file($_FILES['gallery_images']['tmp_name'][$i], $galleryPath)) {
                        $message = "One of the gallery images could not be saved.";
                        $messageType = "error";
                        break;
                    }
                    $newGalleryImages[] = $galleryName;
                }
            }
        }

        /* Continue only if no upload error */

        if ($messageType !== "error") {


            /* =========================
               UPDATE
            ========================= */

            if ($destinationId > 0) {

                /* Get old destination */

                $oldStmt = $pdo->prepare("
                    SELECT *
                    FROM destinations
                    WHERE id = ?
                    LIMIT 1
                ");

                $oldStmt->execute([$destinationId]);

                $oldDestination = $oldStmt->fetch(PDO::FETCH_ASSOC);


                if (!$oldDestination) {

                    $message = "Destination not found.";
                    $messageType = "error";

                } else {

                    /* Keep old image if new image not uploaded */

                    $imageName = !empty($newImage)
                        ? $newImage
                        : $oldDestination['image'];

                    $existingGallery = [];
                    if (!empty($oldDestination['gallery_images'])) {
                        $decoded = json_decode($oldDestination['gallery_images'], true);
                        if (is_array($decoded)) { $existingGallery = array_values(array_filter($decoded)); }
                    }
                    $galleryImages = !empty($newGalleryImages) ? $newGalleryImages : $existingGallery;

                    $updateStmt = $pdo->prepare("
                        UPDATE destinations
                        SET
                            name = ?,
                            slug = ?,
                            location = ?,
                            description = ?,
                            image = ?,
                            gallery_images = ?
                        WHERE id = ?
                    ");

                    $updateStmt->execute([
                        $name,
                        $slug,
                        $location,
                        $description,
                        $imageName,
                        json_encode($galleryImages),
                        $destinationId
                    ]);


                    /* Delete old image if new image uploaded */

                    if (
                        !empty($newImage) &&
                        !empty($oldDestination['image'])
                    ) {

                        $oldImagePath =
                            "../uploads/" .
                            $oldDestination['image'];

                        if (file_exists($oldImagePath)) {
                            unlink($oldImagePath);
                        }
                    }

                    if (!empty($newGalleryImages) && !empty($existingGallery)) {
                        foreach ($existingGallery as $oldGallery) {
                            $oldGalleryPath = "../uploads/" . $oldGallery;
                            if (file_exists($oldGalleryPath)) { unlink($oldGalleryPath); }
                        }
                    }


                    header("Location: destinations.php");
                    exit;
                }
            }


            /* =========================
               ADD NEW
            ========================= */

            else {

                $imageName = $newImage;


                $insertStmt = $pdo->prepare("
                    INSERT INTO destinations
                    (
                        name,
                        slug,
                        location,
                        description,
                        image,
                        gallery_images
                    )
                    VALUES (?, ?, ?, ?, ?, ?)
                ");

                $insertStmt->execute([
                    $name,
                    $slug,
                    $location,
                    $description,
                    $imageName,
                    json_encode($newGalleryImages)
                ]);


                header("Location: destinations.php");
                exit;
            }
        }
    }
}


/* =========================
   FETCH DESTINATIONS
========================= */

$stmt = $pdo->query("
    SELECT *
    FROM destinations
    ORDER BY id DESC
");

$destinations = $stmt->fetchAll(PDO::FETCH_ASSOC);

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
        Destinations - TravelGo Admin
    </title>


    <!-- Tailwind -->

    <script src="https://cdn.tailwindcss.com"></script>


    <!-- Font Awesome -->

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

<main
    class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10"
>


    <!-- Page Heading -->

    <div class="mb-8">

        <p
            class="text-sky-600 font-bold text-sm uppercase tracking-wider"
        >

            Travel Management

        </p>


        <h2
            class="text-3xl font-extrabold text-slate-900 mt-2"
        >

            Destinations

        </h2>


        <p class="text-slate-500 mt-2">

            Add, edit and manage your travel destinations.

        </p>

    </div>



    <!-- =========================
         MESSAGE
    ========================= -->

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



    <!-- =========================
         CONTENT GRID
    ========================= -->

    <div class="grid lg:grid-cols-3 gap-8">


        <!-- =========================
             FORM
        ========================= -->

        <div>

            <div
                class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6"
            >


                <!-- Form Heading -->

                <div class="flex items-center gap-3 mb-6">

                    <div
                        class="w-11 h-11 rounded-xl
                        <?php
                        echo $editDestination
                            ? 'bg-amber-100'
                            : 'bg-sky-100';
                        ?>
                        flex items-center justify-center"
                    >

                        <i
                            class="fa-solid
                            <?php
                            echo $editDestination
                                ? 'fa-pen'
                                : 'fa-plus';
                            ?>
                            <?php
                            echo $editDestination
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
                            echo $editDestination
                                ? 'Edit Destination'
                                : 'Add Destination';
                            ?>

                        </h3>


                        <p class="text-sm text-slate-500">

                            <?php
                            echo $editDestination
                                ? 'Update destination details'
                                : 'Create a new destination';
                            ?>

                        </p>

                    </div>

                </div>



                <!-- FORM -->

                <form
                    method="POST"
                    enctype="multipart/form-data"
                    class="space-y-5"
                >


                    <!-- Hidden ID -->

                    <?php if ($editDestination): ?>

                        <input
                            type="hidden"
                            name="destination_id"
                            value="<?php echo (int)$editDestination['id']; ?>"
                        >

                    <?php endif; ?>



                    <!-- Name -->

                    <div>

                        <label
                            class="block text-sm font-semibold text-slate-700 mb-2"
                        >

                            Destination Name
                            <span class="text-red-500">*</span>

                        </label>


                        <input
                            type="text"
                            name="name"
                            required
                            placeholder="Example: Jaipur"
                            value="<?php
                            echo $editDestination
                                ? htmlspecialchars($editDestination['name'])
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
                            placeholder="Example: jaipur"
                            value="<?php
                            echo $editDestination
                                ? htmlspecialchars($editDestination['slug'])
                                : '';
                            ?>"
                            class="w-full border border-slate-300 rounded-xl px-4 py-3 outline-none focus:ring-2 focus:ring-sky-500"
                        >

                    </div>



                    <!-- Location -->

                    <div>

                        <label
                            class="block text-sm font-semibold text-slate-700 mb-2"
                        >

                            Location
                            <span class="text-red-500">*</span>

                        </label>


                        <input
                            type="text"
                            name="location"
                            required
                            placeholder="Example: Rajasthan, India"
                            value="<?php
                            echo $editDestination
                                ? htmlspecialchars($editDestination['location'])
                                : '';
                            ?>"
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
                            placeholder="Write destination description..."
                            class="w-full border border-slate-300 rounded-xl px-4 py-3 outline-none resize-none focus:ring-2 focus:ring-sky-500"
                        ><?php
                        echo $editDestination
                            ? htmlspecialchars($editDestination['description'])
                            : '';
                        ?></textarea>

                    </div>



                    <!-- Current Image -->

                    <?php if (
                        $editDestination &&
                        !empty($editDestination['image'])
                    ): ?>

                        <div>

                            <p
                                class="text-sm font-semibold text-slate-700 mb-2"
                            >

                                Current Image

                            </p>


                            <img
                                src="../uploads/<?php echo htmlspecialchars($editDestination['image']); ?>"
                                alt="Current Destination"
                                class="w-full h-40 object-cover rounded-xl border border-slate-200"
                            >

                        </div>

                    <?php endif; ?>



                    <!-- Destination-specific Images -->
                    <?php
                    $currentGallery = [];
                    if ($editDestination && !empty($editDestination['gallery_images'])) {
                        $decodedGallery = json_decode($editDestination['gallery_images'], true);
                        if (is_array($decodedGallery)) { $currentGallery = array_values(array_filter($decodedGallery)); }
                    }
                    ?>
                    <?php if (!empty($currentGallery)): ?>
                        <div>
                            <p class="text-sm font-semibold text-slate-700 mb-2">Current Detail Images</p>
                            <div class="grid grid-cols-3 gap-2">
                                <?php foreach ($currentGallery as $galleryImage): ?>
                                    <img src="../uploads/<?php echo htmlspecialchars($galleryImage); ?>" alt="Destination detail image" class="w-full h-20 object-cover rounded-lg border border-slate-200">
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">
                            Detail / Highlight Images
                        </label>
                        <input type="file" name="gallery_images[]" multiple accept=".jpg,.jpeg,.png,.webp" class="w-full border border-slate-300 rounded-xl px-4 py-3 bg-white">
                        <p class="text-xs text-slate-500 mt-2">Upload up to 7 different images for this destination. These images are used separately in Highlights and Photo Gallery.</p>
                        <?php if ($editDestination && !empty($currentGallery)): ?>
                            <p class="text-xs text-amber-600 mt-1">Uploading new detail images will replace the current detail-image set.</p>
                        <?php endif; ?>
                    </div>

                    <!-- Image -->

                    <div>

                        <label
                            class="block text-sm font-semibold text-slate-700 mb-2"
                        >

                            <?php
                            echo $editDestination
                                ? 'Change Image'
                                : 'Destination Image';
                            ?>

                            <?php if (!$editDestination): ?>

                                <span class="text-red-500">*</span>

                            <?php endif; ?>

                        </label>


                        <input
                            type="file"
                            name="image"
                            accept=".jpg,.jpeg,.png,.webp"
                            class="w-full border border-slate-300 rounded-xl px-4 py-3 bg-white"
                        >


                        <?php if ($editDestination): ?>

                            <p class="text-xs text-slate-500 mt-2">

                                Leave empty to keep the current image.

                            </p>

                        <?php endif; ?>

                    </div>



                    <!-- Buttons -->

                    <div class="flex gap-3">


                        <?php if ($editDestination): ?>

                            <a
                                href="destinations.php"
                                class="w-1/3 border border-slate-300 text-slate-700 hover:bg-slate-50 py-3 rounded-xl font-semibold text-center transition"
                            >

                                Cancel

                            </a>


                            <button
                                type="submit"
                                class="w-2/3 bg-amber-500 hover:bg-amber-600 text-white py-3 rounded-xl font-bold transition"
                            >

                                <i class="fa-solid fa-save mr-2"></i>

                                Update Destination

                            </button>

                        <?php else: ?>

                            <button
                                type="submit"
                                class="w-full bg-sky-500 hover:bg-sky-600 text-white py-3.5 rounded-xl font-bold transition"
                            >

                                <i class="fa-solid fa-plus mr-2"></i>

                                Add Destination

                            </button>

                        <?php endif; ?>

                    </div>

                </form>

            </div>

        </div>



        <!-- =========================
             DESTINATION LIST
        ========================= -->

        <div class="lg:col-span-2">

            <div
                class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden"
            >


                <!-- List Heading -->

                <div
                    class="p-6 border-b border-slate-200"
                >

                    <h3
                        class="text-xl font-bold text-slate-900"
                    >

                        All Destinations

                    </h3>


                    <p class="text-sm text-slate-500 mt-1">

                        Manage your available destinations.

                    </p>

                </div>



                <?php if (!empty($destinations)): ?>

                    <div class="divide-y divide-slate-200">


                        <?php foreach ($destinations as $destination): ?>

                            <div class="p-5">


                                <div
                                    class="flex flex-col sm:flex-row gap-5"
                                >


                                    <!-- Image -->

                                    <div
                                        class="w-full sm:w-40 h-32 flex-shrink-0"
                                    >

                                        <?php if (
                                            !empty($destination['image'])
                                        ): ?>

                                            <img
                                                src="../uploads/<?php echo htmlspecialchars($destination['image']); ?>"
                                                alt="<?php echo htmlspecialchars($destination['name']); ?>"
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

                                    <div class="flex-1 min-w-0">


                                        <!-- Title -->

                                        <h4
                                            class="text-xl font-bold text-slate-900"
                                        >

                                            <?php
                                            echo htmlspecialchars(
                                                $destination['name']
                                            );
                                            ?>

                                        </h4>



                                        <!-- Location -->

                                        <p
                                            class="text-sm text-slate-500 mt-2"
                                        >

                                            <i
                                                class="fa-solid fa-location-dot text-sky-500 mr-1"
                                            ></i>

                                            <?php
                                            echo htmlspecialchars(
                                                $destination['location']
                                            );
                                            ?>

                                        </p>



                                        <!-- Slug -->

                                        <p
                                            class="text-xs text-slate-400 mt-2"
                                        >

                                            Slug:

                                            <?php
                                            echo htmlspecialchars(
                                                $destination['slug']
                                            );
                                            ?>

                                        </p>



                                        <!-- Description -->

                                        <?php if (
                                            !empty($destination['description'])
                                        ): ?>

                                            <p
                                                class="text-sm text-slate-600 mt-3 line-clamp-2"
                                            >

                                                <?php
                                                echo htmlspecialchars(
                                                    $destination['description']
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
                                                href="destinations.php?edit=<?php echo (int)$destination['id']; ?>"
                                                class="inline-flex items-center gap-2 bg-amber-50 text-amber-600 hover:bg-amber-100 px-4 py-2 rounded-lg text-sm font-semibold transition"
                                            >

                                                <i
                                                    class="fa-solid fa-pen"
                                                ></i>

                                                Edit

                                            </a>



                                            <!-- Delete -->

                                            <a
                                                href="destinations.php?delete=<?php echo (int)$destination['id']; ?>"
                                                onclick="return confirm('Are you sure you want to delete this destination?');"
                                                class="inline-flex items-center gap-2 bg-red-50 text-red-600 hover:bg-red-100 px-4 py-2 rounded-lg text-sm font-semibold transition"
                                            >

                                                <i
                                                    class="fa-solid fa-trash"
                                                ></i>

                                                Delete

                                            </a>

                                        </div>

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
                                class="fa-solid fa-location-dot text-2xl text-slate-400"
                            ></i>

                        </div>


                        <h3
                            class="text-xl font-bold text-slate-900 mt-5"
                        >

                            No Destinations Found

                        </h3>


                        <p class="text-slate-500 mt-2">

                            Add your first destination using the form.

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