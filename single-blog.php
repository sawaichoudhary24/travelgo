<?php

require_once "config/database.php";
require_once "includes/user-auth.php";

/* =========================
   GET BLOG ID
========================= */

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if ($id <= 0) {
    header("Location: blog.php");
    exit;
}


/* =========================
   FETCH BLOG
========================= */

$stmt = $pdo->prepare("
    SELECT *
    FROM blogs
    WHERE id = ?
      AND status = 'published'
    LIMIT 1
");

$stmt->execute([$id]);

$blog = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$blog) {
    header("Location: blog.php");
    exit;
}


/* =========================
   RELATED BLOGS
========================= */

$relatedStmt = $pdo->prepare("
    SELECT *
    FROM blogs
    WHERE status = 'published'
      AND id != ?
    ORDER BY id DESC
    LIMIT 3
");

$relatedStmt->execute([$id]);

$relatedBlogs = $relatedStmt->fetchAll(PDO::FETCH_ASSOC);


/* =========================
   HEADER + NAVBAR
========================= */

require_once "includes/header.php";
require_once "includes/navbar.php";

?>

<!-- =========================
     BLOG HERO
========================= -->

<section class="bg-slate-900 text-white">

    <div class="max-w-6xl mx-auto px-6 py-16">

        <!-- Breadcrumb -->

        <div class="mb-6">

            <a
                href="blog.php"
                class="inline-flex items-center gap-2 text-sky-400 hover:text-sky-300 font-semibold"
            >
                <i class="fa-solid fa-arrow-left"></i>
                Back to Blog
            </a>

        </div>


        <!-- Category / Label -->

        <div class="mb-5">

            <span
                class="inline-block px-4 py-2 rounded-full bg-sky-500/20 text-sky-300 text-sm font-semibold"
            >
                TravelGo Travel Guide
            </span>

        </div>


        <!-- Title -->

        <h1
            class="text-3xl md:text-5xl font-extrabold leading-tight max-w-4xl"
        >
            <?php echo htmlspecialchars($blog['title']); ?>
        </h1>


        <!-- Meta -->

        <div
            class="flex flex-wrap items-center gap-5 mt-6 text-slate-300"
        >

            <div class="flex items-center gap-2">

                <i class="fa-regular fa-calendar"></i>

                <?php
                echo date(
                    "d M Y",
                    strtotime($blog['created_at'])
                );
                ?>

            </div>


            <?php if (!empty($blog['author'])): ?>

                <div class="flex items-center gap-2">

                    <i class="fa-regular fa-user"></i>

                    <?php
                    echo htmlspecialchars($blog['author']);
                    ?>

                </div>

            <?php endif; ?>

        </div>

    </div>

</section>



<!-- =========================
     BLOG CONTENT
========================= -->

<section class="py-16 bg-slate-50">

    <div class="max-w-6xl mx-auto px-6">

        <div class="grid lg:grid-cols-3 gap-10">


            <!-- =========================
                 MAIN BLOG
            ========================= -->

            <article class="lg:col-span-2">

                <div class="bg-white rounded-3xl shadow-sm overflow-hidden">


                    <!-- Featured Image -->

                    <?php if (!empty($blog['image'])): ?>

                        <div class="w-full bg-slate-100">

                            <img
                                src="uploads/<?php echo htmlspecialchars($blog['image']); ?>"
                                alt="<?php echo htmlspecialchars($blog['title']); ?>"
                                class="w-full h-[280px] md:h-[450px] object-cover"
                            >

                        </div>

                    <?php endif; ?>


                    <!-- Content -->

                    <div class="p-6 md:p-10">

                        <div
                            class="text-slate-700 text-base md:text-lg leading-8"
                        >

                            <?php
                            echo nl2br(
                                htmlspecialchars($blog['content'])
                            );
                            ?>

                        </div>

                    </div>

                </div>


                <!-- Back Button -->

                <div class="mt-8">

                    <a
                        href="blog.php"
                        class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-semibold transition"
                    >

                        <i class="fa-solid fa-arrow-left"></i>

                        Back to All Blogs

                    </a>

                </div>

            </article>



            <!-- =========================
                 SIDEBAR
            ========================= -->

            <aside>

                <div
                    class="bg-white rounded-3xl shadow-sm p-6 sticky top-6"
                >

                    <h2
                        class="text-xl font-bold text-slate-900 mb-6"
                    >
                        Explore TravelGo
                    </h2>


                    <!-- Packages -->

                    <a
                        href="packages.php"
                        class="group flex items-center gap-4 p-4 rounded-2xl bg-sky-50 hover:bg-sky-100 transition mb-4"
                    >

                        <div
                            class="w-12 h-12 rounded-xl bg-sky-500 text-white flex items-center justify-center"
                        >

                            <i class="fa-solid fa-suitcase-rolling"></i>

                        </div>


                        <div>

                            <h3 class="font-bold text-slate-900">
                                Tour Packages
                            </h3>

                            <p class="text-sm text-slate-500">
                                Explore our packages
                            </p>

                        </div>

                    </a>



                    <!-- Destinations -->

                    <a
                        href="destinations.php"
                        class="group flex items-center gap-4 p-4 rounded-2xl bg-emerald-50 hover:bg-emerald-100 transition mb-4"
                    >

                        <div
                            class="w-12 h-12 rounded-xl bg-emerald-500 text-white flex items-center justify-center"
                        >

                            <i class="fa-solid fa-location-dot"></i>

                        </div>


                        <div>

                            <h3 class="font-bold text-slate-900">
                                Destinations
                            </h3>

                            <p class="text-sm text-slate-500">
                                Discover new places
                            </p>

                        </div>

                    </a>



                    <!-- Contact -->

                    <a
                        href="contact.php"
                        class="group flex items-center gap-4 p-4 rounded-2xl bg-orange-50 hover:bg-orange-100 transition"
                    >

                        <div
                            class="w-12 h-12 rounded-xl bg-orange-500 text-white flex items-center justify-center"
                        >

                            <i class="fa-solid fa-headset"></i>

                        </div>


                        <div>

                            <h3 class="font-bold text-slate-900">
                                Contact Us
                            </h3>

                            <p class="text-sm text-slate-500">
                                Plan your next trip
                            </p>

                        </div>

                    </a>

                </div>

            </aside>

        </div>

    </div>

</section>



<!-- =========================
     RELATED BLOGS
========================= -->

<?php if (!empty($relatedBlogs)): ?>

<section class="py-16 bg-white">

    <div class="max-w-6xl mx-auto px-6">


        <div class="text-center mb-10">

            <span
                class="text-sky-500 font-semibold uppercase tracking-wider text-sm"
            >
                Keep Exploring
            </span>

            <h2
                class="text-3xl md:text-4xl font-extrabold text-slate-900 mt-2"
            >
                Related Travel Blogs
            </h2>

            <p class="text-slate-500 mt-3">
                More travel inspiration for your next adventure.
            </p>

        </div>



        <div class="grid md:grid-cols-3 gap-8">

            <?php foreach ($relatedBlogs as $related): ?>

                <article
                    class="bg-slate-50 rounded-2xl overflow-hidden border border-slate-200 hover:shadow-xl transition"
                >

                    <!-- Image -->

                    <?php if (!empty($related['image'])): ?>

                        <img
                            src="uploads/<?php echo htmlspecialchars($related['image']); ?>"
                            alt="<?php echo htmlspecialchars($related['title']); ?>"
                            class="w-full h-52 object-cover"
                        >

                    <?php else: ?>

                        <div
                            class="w-full h-52 bg-slate-200 flex items-center justify-center"
                        >

                            <i
                                class="fa-solid fa-image text-4xl text-slate-400"
                            ></i>

                        </div>

                    <?php endif; ?>


                    <!-- Card Content -->

                    <div class="p-6">

                        <div
                            class="flex items-center gap-2 text-sm text-slate-500 mb-3"
                        >

                            <i class="fa-regular fa-calendar"></i>

                            <?php
                            echo date(
                                "d M Y",
                                strtotime($related['created_at'])
                            );
                            ?>

                        </div>


                        <h3
                            class="text-xl font-bold text-slate-900 leading-snug"
                        >

                            <?php
                            echo htmlspecialchars($related['title']);
                            ?>

                        </h3>


                        <p class="text-slate-600 mt-3 line-clamp-3">

                            <?php

                            $excerpt = strip_tags(
                                $related['content']
                            );

                            echo htmlspecialchars(
                                mb_substr($excerpt, 0, 120)
                            );

                            ?>

                            <?php if (mb_strlen($excerpt) > 120): ?>
                                ...
                            <?php endif; ?>

                        </p>


                        <a
                            href="single-blog.php?id=<?php echo (int)$related['id']; ?>"
                            class="inline-flex items-center gap-2 mt-5 text-sky-600 hover:text-sky-700 font-bold"
                        >

                            Read More

                            <i class="fa-solid fa-arrow-right"></i>

                        </a>

                    </div>

                </article>

            <?php endforeach; ?>

        </div>

    </div>

</section>

<?php endif; ?>



<!-- =========================
     CTA
========================= -->

<section class="py-16 bg-sky-500">

    <div class="max-w-5xl mx-auto px-6 text-center text-white">

        <div
            class="w-16 h-16 mx-auto rounded-2xl bg-white/20 flex items-center justify-center mb-6"
        >

            <i class="fa-solid fa-plane-departure text-2xl"></i>

        </div>


        <h2
            class="text-3xl md:text-4xl font-extrabold"
        >
            Ready for Your Next Adventure?
        </h2>


        <p class="mt-4 text-sky-100 text-lg">
            Explore our destinations and find the perfect tour package.
        </p>


        <div
            class="flex flex-col sm:flex-row justify-center gap-4 mt-8"
        >

            <a
                href="packages.php"
                class="px-7 py-3 rounded-xl bg-white text-sky-600 hover:bg-slate-100 font-bold transition"
            >
                Explore Packages
            </a>


            <a
                href="contact.php"
                class="px-7 py-3 rounded-xl border-2 border-white text-white hover:bg-white hover:text-sky-600 font-bold transition"
            >
                Contact Us
            </a>

        </div>

    </div>

</section>



<?php

require_once "includes/footer.php";

?>