<?php

require_once "config/database.php";
require_once "includes/user-auth.php";

$stmt = $pdo->query("
    SELECT *
    FROM blogs
    WHERE status = 'published'
    ORDER BY id DESC
");

$blogs = $stmt->fetchAll(PDO::FETCH_ASSOC);

require_once "includes/header.php";
require_once "includes/navbar.php";

?>

<!-- =========================
     HERO
========================= -->

<section class=" py-20 h-[400px] bg-[url('assets/images/b1.png')]   bg-cover bg-center text-white ">

    <div class="max-w-7xl mx-auto px-4 text-center">

        <p class="text-sky-400 font-semibold uppercase tracking-wider mb-3">
            TravelGo Blog
        </p>

        <h1 class="text-4xl md:text-5xl font-extrabold mb-5">
            Travel Stories & Tips
        </h1>

        <p class="max-w-2xl mx-auto text-slate-300 text-lg">
            Discover amazing destinations, travel tips,
            guides and inspiring travel stories.
        </p>

    </div>

</section>


<!-- =========================
     BLOG SECTION
========================= -->

<section class="py-16">

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">


        <?php if (empty($blogs)): ?>

            <div class="text-center py-16">

                <div class="w-20 h-20 mx-auto mb-5
                            rounded-full bg-slate-100
                            flex items-center justify-center">

                    <i class="fa-solid fa-newspaper
                              text-3xl text-slate-400"></i>

                </div>

                <h2 class="text-2xl font-bold">
                    No blogs available
                </h2>

                <p class="text-slate-500 mt-2">
                    New travel stories will be published soon.
                </p>

            </div>


        <?php else: ?>


            <!-- BLOG GRID -->

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3
                        gap-8">


                <?php foreach ($blogs as $blog): ?>

                    <article
                        class="bg-white rounded-2xl
                               overflow-hidden
                               border border-slate-200
                               shadow-sm
                               hover:shadow-xl
                               transition
                               duration-300">


                        <!-- IMAGE -->

                        <a href="single-blog.php?id=<?php echo (int)$blog['id']; ?>">

                            <?php if (!empty($blog['image'])): ?>

                                <img
                                    src="uploads/<?php echo htmlspecialchars($blog['image']); ?>"
                                    alt="<?php echo htmlspecialchars($blog['title']); ?>"
                                    class="w-full h-56 object-cover
                                           hover:scale-105
                                           transition duration-500">

                            <?php else: ?>

                                <div class="w-full h-56
                                            bg-slate-200
                                            flex items-center
                                            justify-center">

                                    <i class="fa-solid fa-image
                                              text-4xl text-slate-400"></i>

                                </div>

                            <?php endif; ?>

                        </a>


                        <!-- CONTENT -->

                        <div class="p-6">


                            <!-- DATE / AUTHOR -->

                            <div class="flex items-center
                                        gap-4
                                        text-xs
                                        text-slate-500
                                        mb-3">

                                <span>

                                    <i class="fa-regular fa-calendar
                                              mr-1"></i>

                                    <?php
                                    echo date(
                                        "d M Y",
                                        strtotime($blog['created_at'])
                                    );
                                    ?>

                                </span>


                                <span>

                                    <i class="fa-solid fa-user
                                              mr-1"></i>

                                    <?php
                                    echo !empty($blog['author'])
                                        ? htmlspecialchars($blog['author'])
                                        : "TravelGo";
                                    ?>

                                </span>

                            </div>


                            <!-- TITLE -->

                            <h2 class="text-xl font-bold
                                       text-slate-900
                                       leading-snug
                                       hover:text-sky-500
                                       transition">

                                <a href="single-blog.php?id=<?php echo (int)$blog['id']; ?>">

                                    <?php
                                    echo htmlspecialchars(
                                        $blog['title']
                                    );
                                    ?>

                                </a>

                            </h2>


                            <!-- EXCERPT -->

                            <p class="text-slate-500
                                      text-sm
                                      leading-6
                                      mt-3">

                                <?php

                                $excerpt = strip_tags(
                                    $blog['content']
                                );

                                echo htmlspecialchars(
                                    mb_substr($excerpt, 0, 130)
                                );

                                if (mb_strlen($excerpt) > 130) {
                                    echo "...";
                                }

                                ?>

                            </p>


                            <!-- READ MORE -->

                            <div class="mt-5">

                                <a
                                    href="single-blog.php?id=<?php echo (int)$blog['id']; ?>"
                                    class="inline-flex
                                           items-center
                                           gap-2
                                           text-sky-600
                                           font-bold
                                           hover:text-sky-700">

                                    Read More

                                    <i class="fa-solid fa-arrow-right
                                              text-sm"></i>

                                </a>

                            </div>

                        </div>

                    </article>

                <?php endforeach; ?>

            </div>


        <?php endif; ?>

    </div>

</section>


<!-- =========================
     CTA
========================= -->

<section class="py-16 bg-sky-500">

    <div class="max-w-4xl mx-auto px-4 text-center text-white">

        <h2 class="text-3xl md:text-4xl font-extrabold">

            Ready for Your Next Adventure?

        </h2>

        <p class="mt-3 text-sky-100">

            Explore our tour packages and start planning
            your perfect trip.

        </p>

        <a
            href="packages.php"
            class="inline-flex items-center gap-2
                   mt-7
                   px-7 py-3
                   rounded-xl
                   bg-white
                   text-sky-600
                   font-bold
                   hover:bg-slate-100
                   transition">

            <i class="fa-solid fa-suitcase-rolling"></i>

            Explore Packages

        </a>

    </div>

</section>


<?php require_once "includes/footer.php"; ?>