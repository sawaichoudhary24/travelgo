<?php

require_once "config/database.php";
require_once "includes/user-auth.php";

/* =========================
   FETCH DESTINATION IMAGES
========================= */

$destinationStmt = $pdo->query("
    SELECT id, name, image
    FROM destinations
    WHERE image IS NOT NULL
      AND image != ''
    ORDER BY id DESC
");

$destinations = $destinationStmt->fetchAll(PDO::FETCH_ASSOC);


/* =========================
   FETCH PACKAGE IMAGES
========================= */

$packageStmt = $pdo->query("
    SELECT id, title, image
    FROM packages
    WHERE image IS NOT NULL
      AND image != ''
      AND status = 'active'
    ORDER BY id DESC
");

$packages = $packageStmt->fetchAll(PDO::FETCH_ASSOC);


/* =========================
   CREATE GALLERY ITEMS
========================= */

$galleryItems = [];

/* Destinations */
foreach ($destinations as $destination) {

    $galleryItems[] = [
        "type"  => "destination",
        "title" => $destination["name"],
        "image" => $destination["image"]
    ];
}


/* Packages */
foreach ($packages as $package) {

    $galleryItems[] = [
        "type"  => "package",
        "title" => $package["title"],
        "image" => $package["image"]
    ];
}


require_once "includes/header.php";
require_once "includes/navbar.php";

?>

<!-- =========================
     HERO SECTION
========================= -->

<section class="relative bg-slate-900 text-white">

    <div class="absolute inset-0 bg-gradient-to-r from-slate-950/95 via-slate-900/80 to-sky-900/70"></div>

    <div class="relative max-w-7xl mx-auto px-6 py-24 text-center">

        <span class="inline-block px-4 py-2 rounded-full bg-sky-500/20 text-sky-300 text-sm font-semibold mb-5">
            TravelGo Gallery
        </span>

        <h1 class="text-4xl md:text-5xl lg:text-6xl font-extrabold mb-6">
            Explore Our Travel Gallery
        </h1>

        <p class="max-w-2xl mx-auto text-slate-300 text-lg leading-8">
            Get inspired by beautiful destinations and unforgettable travel experiences with TravelGo.
        </p>

    </div>

</section>


<!-- =========================
     GALLERY SECTION
========================= -->

<section class="py-20 bg-slate-50">

    <div class="max-w-7xl mx-auto px-6">

        <!-- Heading -->

        <div class="text-center mb-12">

            <span class="text-sky-500 font-bold uppercase tracking-wider text-sm">
                Memories & Moments
            </span>

            <h2 class="text-3xl md:text-4xl font-extrabold text-slate-900 mt-3">
                Discover Beautiful Places
            </h2>

            <p class="text-slate-600 max-w-2xl mx-auto mt-4">
                Explore some of the destinations and tour packages available through TravelGo.
            </p>

        </div>


        <!-- =========================
             FILTER BUTTONS
        ========================= -->

        <div class="flex flex-wrap justify-center gap-3 mb-10">

            <button
                type="button"
                class="gallery-filter active-filter px-6 py-3 rounded-full bg-sky-500 text-white font-semibold transition"
                data-filter="all"
            >
                All
            </button>

            <button
                type="button"
                class="gallery-filter px-6 py-3 rounded-full bg-white text-slate-700 font-semibold border border-slate-200 hover:bg-sky-50 hover:text-sky-600 transition"
                data-filter="destination"
            >
                Destinations
            </button>

            <button
                type="button"
                class="gallery-filter px-6 py-3 rounded-full bg-white text-slate-700 font-semibold border border-slate-200 hover:bg-sky-50 hover:text-sky-600 transition"
                data-filter="package"
            >
                Packages
            </button>

        </div>


        <!-- =========================
             GALLERY GRID
        ========================= -->

        <?php if (!empty($galleryItems)): ?>

            <div
                id="galleryGrid"
                class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6"
            >

                <?php foreach ($galleryItems as $index => $item): ?>

                    <div
                        class="gallery-item group relative overflow-hidden rounded-2xl bg-white shadow-sm hover:shadow-xl transition duration-300"
                        data-category="<?php echo htmlspecialchars($item["type"]); ?>"
                    >

                        <!-- Image -->

                        <div class="relative h-72 overflow-hidden">

                            <img
                                src="uploads/<?php echo htmlspecialchars($item["image"]); ?>"
                                alt="<?php echo htmlspecialchars($item["title"]); ?>"
                                class="gallery-image w-full h-full object-cover group-hover:scale-110 transition duration-700"
                                data-index="<?php echo $index; ?>"
                                data-title="<?php echo htmlspecialchars($item["title"]); ?>"
                            >


                            <!-- Overlay -->

                            <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent opacity-0 group-hover:opacity-100 transition duration-300">

                                <div class="absolute bottom-0 left-0 right-0 p-6 text-white">

                                    <div class="flex items-center gap-2 mb-2">

                                        <?php if ($item["type"] === "destination"): ?>

                                            <span class="text-xs uppercase tracking-wider bg-sky-500 px-3 py-1 rounded-full">
                                                Destination
                                            </span>

                                        <?php else: ?>

                                            <span class="text-xs uppercase tracking-wider bg-emerald-500 px-3 py-1 rounded-full">
                                                Package
                                            </span>

                                        <?php endif; ?>

                                    </div>

                                    <h3 class="text-xl font-bold">
                                        <?php echo htmlspecialchars($item["title"]); ?>
                                    </h3>

                                </div>


                                <!-- View Button -->

                                <button
                                    type="button"
                                    class="open-lightbox absolute top-5 right-5 w-11 h-11 rounded-full bg-white/90 text-slate-800 flex items-center justify-center hover:bg-white transition"
                                    data-index="<?php echo $index; ?>"
                                >
                                    <i class="fa-solid fa-magnifying-glass-plus"></i>
                                </button>

                            </div>

                        </div>

                    </div>

                <?php endforeach; ?>

            </div>

        <?php else: ?>

            <!-- Empty State -->

            <div class="text-center bg-white rounded-3xl p-12 shadow-sm">

                <div class="w-20 h-20 mx-auto rounded-full bg-sky-100 text-sky-500 flex items-center justify-center text-3xl mb-5">
                    <i class="fa-solid fa-images"></i>
                </div>

                <h3 class="text-2xl font-bold text-slate-900 mb-3">
                    No Gallery Images Yet
                </h3>

                <p class="text-slate-600 mb-6">
                    Add destination or package images from the admin panel and they will automatically appear here.
                </p>

                <a
                    href="destinations.php"
                    class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-sky-500 hover:bg-sky-600 text-white font-semibold transition"
                >
                    <i class="fa-solid fa-location-dot"></i>
                    Explore Destinations
                </a>

            </div>

        <?php endif; ?>

    </div>

</section>


<!-- =========================
     LIGHTBOX
========================= -->

<div
    id="lightbox"
    class="fixed inset-0 z-[9999] hidden bg-black/90 items-center justify-center p-5"
>

    <!-- Close -->

    <button
        id="closeLightbox"
        type="button"
        class="absolute top-5 right-5 w-12 h-12 rounded-full bg-white/10 hover:bg-white/20 text-white flex items-center justify-center text-xl transition"
    >
        <i class="fa-solid fa-xmark"></i>
    </button>


    <!-- Previous -->

    <button
        id="prevImage"
        type="button"
        class="absolute left-4 md:left-8 top-1/2 -translate-y-1/2 w-12 h-12 rounded-full bg-white/10 hover:bg-white/20 text-white flex items-center justify-center transition"
    >
        <i class="fa-solid fa-chevron-left"></i>
    </button>


    <!-- Image Area -->

    <div class="max-w-5xl w-full text-center">

        <img
            id="lightboxImage"
            src=""
            alt=""
            class="max-h-[75vh] w-auto max-w-full mx-auto rounded-xl object-contain shadow-2xl"
        >

        <h3
            id="lightboxTitle"
            class="text-white text-xl md:text-2xl font-bold mt-5"
        ></h3>

    </div>


    <!-- Next -->

    <button
        id="nextImage"
        type="button"
        class="absolute right-4 md:right-8 top-1/2 -translate-y-1/2 w-12 h-12 rounded-full bg-white/10 hover:bg-white/20 text-white flex items-center justify-center transition"
    >
        <i class="fa-solid fa-chevron-right"></i>
    </button>

</div>


<!-- =========================
     CTA
========================= -->

<section class="py-20 bg-white">

    <div class="max-w-5xl mx-auto px-6">

        <div class="rounded-3xl bg-gradient-to-r from-sky-500 to-blue-600 p-10 md:p-14 text-center text-white shadow-xl">

            <div class="w-16 h-16 mx-auto rounded-2xl bg-white/20 flex items-center justify-center text-2xl mb-6">
                <i class="fa-solid fa-plane-departure"></i>
            </div>

            <h2 class="text-3xl md:text-4xl font-extrabold mb-4">
                Ready to Create Your Own Memories?
            </h2>

            <p class="text-sky-100 max-w-2xl mx-auto mb-8">
                Choose your favorite destination and start planning your next unforgettable journey with TravelGo.
            </p>

            <div class="flex flex-col sm:flex-row justify-center gap-4">

                <a
                    href="destinations.php"
                    class="inline-flex items-center justify-center gap-2 px-7 py-3.5 rounded-xl bg-white text-sky-600 font-bold hover:bg-slate-100 transition"
                >
                    <i class="fa-solid fa-location-dot"></i>
                    Explore Destinations
                </a>

                <a
                    href="packages.php"
                    class="inline-flex items-center justify-center gap-2 px-7 py-3.5 rounded-xl border border-white/40 text-white font-bold hover:bg-white/10 transition"
                >
                    <i class="fa-solid fa-suitcase-rolling"></i>
                    View Packages
                </a>

            </div>

        </div>

    </div>

</section>


<!-- =========================
     JAVASCRIPT
========================= -->

<script>

document.addEventListener("DOMContentLoaded", function () {

    /* =========================
       FILTER
    ========================= */

    const filterButtons = document.querySelectorAll(".gallery-filter");
    const galleryItems = document.querySelectorAll(".gallery-item");

    filterButtons.forEach(function (button) {

        button.addEventListener("click", function () {

            const filter = this.dataset.filter;


            /* Remove active style */

            filterButtons.forEach(function (btn) {

                btn.classList.remove(
                    "active-filter",
                    "bg-sky-500",
                    "text-white"
                );

                btn.classList.add(
                    "bg-white",
                    "text-slate-700"
                );

            });


            /* Add active style */

            this.classList.add(
                "active-filter",
                "bg-sky-500",
                "text-white"
            );

            this.classList.remove(
                "bg-white",
                "text-slate-700"
            );


            /* Filter items */

            galleryItems.forEach(function (item) {

                const category = item.dataset.category;

                if (filter === "all" || category === filter) {

                    item.classList.remove("hidden");

                } else {

                    item.classList.add("hidden");

                }

            });

        });

    });


    /* =========================
       LIGHTBOX
    ========================= */

    const images = document.querySelectorAll(".gallery-image");

    const lightbox = document.getElementById("lightbox");
    const lightboxImage = document.getElementById("lightboxImage");
    const lightboxTitle = document.getElementById("lightboxTitle");

    const closeLightbox = document.getElementById("closeLightbox");
    const nextImage = document.getElementById("nextImage");
    const prevImage = document.getElementById("prevImage");

    let currentIndex = 0;


    function showImage(index) {

        if (!images.length) {
            return;
        }

        if (index < 0) {
            index = images.length - 1;
        }

        if (index >= images.length) {
            index = 0;
        }

        currentIndex = index;

        const image = images[currentIndex];

        lightboxImage.src = image.src;
        lightboxImage.alt = image.alt;
        lightboxTitle.textContent = image.dataset.title || "";

        lightbox.classList.remove("hidden");
        lightbox.classList.add("flex");

        document.body.classList.add("overflow-hidden");
    }


    function closeBox() {

        lightbox.classList.add("hidden");
        lightbox.classList.remove("flex");

        document.body.classList.remove("overflow-hidden");
    }


    /* Open buttons */

    document.querySelectorAll(".open-lightbox").forEach(function (button) {

        button.addEventListener("click", function () {

            const index = parseInt(this.dataset.index);

            showImage(index);

        });

    });


    /* Image click */

    images.forEach(function (image) {

        image.addEventListener("click", function () {

            const index = parseInt(this.dataset.index);

            showImage(index);

        });

    });


    /* Next */

    nextImage.addEventListener("click", function (event) {

        event.stopPropagation();

        showImage(currentIndex + 1);

    });


    /* Previous */

    prevImage.addEventListener("click", function (event) {

        event.stopPropagation();

        showImage(currentIndex - 1);

    });


    /* Close */

    closeLightbox.addEventListener("click", function () {

        closeBox();

    });


    /* Click outside */

    lightbox.addEventListener("click", function (event) {

        if (event.target === lightbox) {

            closeBox();

        }

    });


    /* Keyboard */

    document.addEventListener("keydown", function (event) {

        if (lightbox.classList.contains("hidden")) {
            return;
        }

        if (event.key === "Escape") {

            closeBox();

        }

        if (event.key === "ArrowRight") {

            showImage(currentIndex + 1);

        }

        if (event.key === "ArrowLeft") {

            showImage(currentIndex - 1);

        }

    });

});

</script>


<?php require_once "includes/footer.php"; ?>