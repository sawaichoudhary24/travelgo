
<?php

require_once "config/database.php";
require_once "includes/user-auth.php";

/*
|--------------------------------------------------------------------------
| Fetch Destinations
|--------------------------------------------------------------------------
*/

$perPage = 6;
$currentPage = max(1, (int)($_GET['page'] ?? 1));

$totalStmt = $pdo->query("SELECT COUNT(*) FROM destinations");
$totalDestinations = (int)$totalStmt->fetchColumn();

$totalPages = max(1, (int)ceil($totalDestinations / $perPage));
$currentPage = min($currentPage, $totalPages);

$offset = ($currentPage - 1) * $perPage;

$stmt = $pdo->prepare("
    SELECT
        d.*,

        (
            SELECT p.id
            FROM packages p
            WHERE p.destination_id = d.id
              AND p.status = 'active'
            ORDER BY p.price ASC, p.id DESC
            LIMIT 1
        ) AS package_id,

        (
            SELECT p.price
            FROM packages p
            WHERE p.destination_id = d.id
              AND p.status = 'active'
            ORDER BY p.price ASC, p.id DESC
            LIMIT 1
        ) AS package_price,

        (
            SELECT p.duration
            FROM packages p
            WHERE p.destination_id = d.id
              AND p.status = 'active'
            ORDER BY p.price ASC, p.id DESC
            LIMIT 1
        ) AS package_duration

    FROM destinations d
    ORDER BY d.id DESC
    LIMIT :limit OFFSET :offset
");

$stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();

$destinations = $stmt->fetchAll(PDO::FETCH_ASSOC);


/*
|--------------------------------------------------------------------------
| Header + Navbar
|--------------------------------------------------------------------------
*/

require_once "includes/header.php";
require_once "includes/navbar.php";

?>


<!-- =========================================================
     HERO
========================================================= -->

<section
    class="relative h-[400px] overflow-hidden
           bg-[url('assets/images/d1.png')]
           bg-cover bg-center
           py-20"
>

    <!-- Background Overlay -->
    <div
        class="absolute inset-0
               bg-gradient-to-r
               from-slate-950/90
               via-slate-900/70
               to-sky-900/50"
    ></div>


    <!-- Hero Content -->
    <div
        class="relative z-10
               max-w-7xl mx-auto
               px-4 sm:px-6 lg:px-8
               text-center"
    >

        <p
            class="text-sky-400
                   font-semibold
                   uppercase
                   tracking-widest
                   text-sm"
        >
            Explore The World
        </p>


        <h1
            class="text-4xl
                   md:text-5xl
                   font-extrabold
                   text-white
                   mt-3"
        >
            Popular Destinations
        </h1>


        <p
            class="text-slate-300
                   max-w-2xl
                   mx-auto
                   mt-5
                   leading-7"
        >
            Discover beautiful places, amazing experiences and
            unforgettable journeys with TravelGo.
        </p>

    </div>

</section>


<!-- =========================================================
     DESTINATIONS
========================================================= -->

<section class="py-16 bg-slate-50">

    <div
        class="max-w-7xl
               mx-auto
               px-4
               sm:px-6
               lg:px-8"
    >

        <?php if (count($destinations) > 0): ?>


            <!-- Destination Grid -->

            <div
                class="grid
                       grid-cols-1
                       sm:grid-cols-2
                       lg:grid-cols-3
                       gap-6
                       xl:gap-7"
            >

                <?php foreach ($destinations as $destination): ?>

                    <?php

                    $cardName = trim(
                        $destination['name'] ?? 'Destination'
                    );

                    $cardLocation = trim(
                        $destination['location'] ?? ''
                    );

                    $description = trim(
                        strip_tags(
                            $destination['description'] ?? ''
                        )
                    );

                    ?>


                    <!-- =================================================
                         DESTINATION CARD
                    ================================================== -->

                    <article
                        class="group
                               relative
                               w-full
                               max-w-[380px]
                               h-[410px]
                               
                               justify-self-center
                               overflow-hidden
                               bg-white
                               border
                               border-[#e8edf2]
                               rounded-t-none
                               rounded-b-[20px]
                               shadow-[0_5px_18px_rgba(15,23,42,0.07)]
                               transition-all
                               duration-300
                               ease-in-out
                               hover:-translate-y-[5px]
                               hover:shadow-[0_16px_34px_rgba(15,23,42,0.13)]"
                    >


                        <!-- Image -->

                        <div
                            class="relative
                                   h-[210px]
                                   min-[481px]:h-[220px]
                                   mx-[10px]
                                   mt-[10px]
                                   overflow-hidden
                                   rounded-t-none
                                   rounded-b-none
                                   bg-gray-200"
                        >

                            <?php if (!empty($destination['image'])): ?>

                                <img
                                    src="uploads/<?php echo htmlspecialchars($destination['image']); ?>"
                                    alt="<?php echo htmlspecialchars($cardName); ?>"
                                    loading="lazy"
                                    class="block
                                           w-full
                                           h-full
                                           object-cover
                                           transition-transform
                                           duration-[550ms]
                                           ease-in-out
                                           group-hover:scale-[1.06]"
                                >

                            <?php else: ?>


                                <!-- Placeholder -->

                                <div
                                    class="flex
                                           items-center
                                           justify-center
                                           w-full
                                           h-full
                                           text-[#2490c9]
                                           text-[44px]"
                                           
                                >

                                    <i
                                        class="fa-solid fa-location-dot"
                                    ></i>

                                </div>

                            <?php endif; ?>


                            <!-- Image Overlay -->

                            <div
                                class="absolute
                                       inset-0
                                       bg-gradient-to-b
                                       from-black/[0.03]
                                       to-black/[0.16]
                                       pointer-events-none"
                            ></div>

                        </div>


                        <!-- Card Body -->

                        <div
                            class="px-[13px]
                                   pt-[14px]
                                   pb-[15px]
                                   min-[481px]:px-[14px]
                                   min-[481px]:pt-[15px]"
                        >


                            <!-- Label -->

                            <div class="text-slate-400 text-[11px] leading-none font-medium uppercase tracking-[0.14em]">
                                DESTINATION
                            </div>


                            <!-- Name + Duration -->

                            <div
                                class="mt-[18px]
                                       flex
                                       items-center
                                       justify-between
                                       gap-3
                                       min-w-0"
                                       
                                       >

                                <!-- Destination Name -->
                                <h2
                                    class="group/title
                                           min-w-0
                                           m-0
                                           text-[#111827]
                                           text-[18px]
                                           leading-[1.15]
                                           font-medium
                                           tracking-[-0.02em]
                                           break-words
                                           cursor-pointer
                                           transition-all
                                           duration-300
                                           ease-out
                                           hover:text-[#2385bd]
                                           hover:translate-x-[3px]"
                                >
                                    <?php echo htmlspecialchars($cardName); ?>
                                </h2>

                                <!-- Duration on right -->
                                <div
                                    class="shrink-0
                                           flex
                                           items-center
                                           justify-end
                                           text-slate-700
                                           text-[14px]
                                           min-[481px]:text-[16px]
                                           leading-tight
                                           font-medium
                                           whitespace-nowrap"
                                >
                                    <?php if (!empty($destination['package_duration'])): ?>
                                        <?php echo htmlspecialchars($destination['package_duration']); ?>
                                    <?php else: ?>
                                        Flexible Trip
                                    <?php endif; ?>
                                </div>

                            </div>


                            <!-- Footer -->

                            <div
                                class="flex
                                       items-center
                                       justify-between
                                       gap-[12px]
                                       mt-[15px]"
                            >


                                <!-- Price -->

                                <div
                                    class="flex
                                           items-baseline
                                           gap-[5px]
                                           text-[#111]
                                           whitespace-nowrap"
                                >

                                    <?php if ($destination['package_price'] !== null): ?>

                                        <strong
                                            class="text-[22px]
                                                   min-[481px]:text-[25px]
                                                   leading-none
                                                   font-bold"
                                        >
                                            ₹<?php
                                            echo number_format(
                                                (float)$destination['package_price'],
                                                0
                                            );
                                            ?>
                                        </strong>

                                        <span class="text-[15px]">
                                            / person
                                        </span>

                                    <?php else: ?>

                                        <strong
                                            class="text-[22px]
                                                   min-[481px]:text-[25px]
                                                   leading-none
                                                   font-bold"
                                        >
                                            Explore
                                        </strong>

                                    <?php endif; ?>

                                </div>


                                <!-- View Details -->

                                <a
                                    href="destination-details.php?id=<?php echo (int)$destination['id']; ?>"
                                    aria-label="View details for <?php echo htmlspecialchars($cardName); ?>"
                                    class="inline-flex
                                           items-center
                                           justify-center
                                           min-h-[38px]
                                           px-[15px]
                                           min-[481px]:px-[18px]
                                           rounded-[22px]
                                           bg-[#2385bd]
                                           hover:bg-[#176e9f]
                                           text-white
                                           text-[13px]
                                           min-[481px]:text-[14px]
                                           font-semibold
                                           tracking-[0.01em]
                                           no-underline
                                           shadow-[0_5px_12px_rgba(35,133,189,0.2)]
                                           transition-all
                                           duration-200
                                           hover:-translate-y-px"
                                >
                                    VIEW DETAILS
                                </a>

                            </div>

                        </div>

                    </article>

                <?php endforeach; ?>

            </div>



            <?php if ($totalPages > 1): ?>

                <div
                    class="mt-10
                           flex
                           flex-wrap
                           items-center
                           justify-center
                           gap-2"
                    aria-label="Destinations pagination"
                >


                    <!-- Previous -->

                    <?php if ($currentPage > 1): ?>

                        <a
                            href="?page=<?php echo $currentPage - 1; ?>"
                            class="px-4
                                   py-2
                                   rounded-lg
                                   border
                                   border-slate-200
                                   bg-white
                                   text-slate-700
                                   hover:bg-slate-100
                                   transition"
                        >

                            <i
                                class="fa-solid
                                       fa-chevron-left
                                       mr-1"
                            ></i>

                            Previous

                        </a>

                    <?php endif; ?>


                    <!-- Page Numbers -->

                    <?php for ($page = 1; $page <= $totalPages; $page++): ?>

                        <a
                            href="?page=<?php echo $page; ?>"
                            class="w-10
                                   h-10
                                   flex
                                   items-center
                                   justify-center
                                   rounded-lg
                                   font-semibold
                                   transition
                                   <?php echo $page === $currentPage
                                       ? 'bg-sky-500 text-white shadow-md'
                                       : 'bg-white text-slate-700 border border-slate-200 hover:bg-slate-100';
                                   ?>"
                        >
                            <?php echo $page; ?>
                        </a>

                    <?php endfor; ?>


                    <!-- Next -->

                    <?php if ($currentPage < $totalPages): ?>

                        <a
                            href="?page=<?php echo $currentPage + 1; ?>"
                            class="px-4
                                   py-2
                                   rounded-lg
                                   border
                                   border-slate-200
                                   bg-white
                                   text-slate-700
                                   hover:bg-slate-100
                                   transition"
                        >

                            Next

                            <i
                                class="fa-solid
                                       fa-chevron-right
                                       ml-1"
                            ></i>

                        </a>

                    <?php endif; ?>

                </div>

            <?php endif; ?>


        <?php else: ?>


            <!-- =====================================================
                 EMPTY STATE
            ====================================================== -->

            <div
                class="bg-white
                       rounded-2xl
                       border
                       border-slate-200
                       p-12
                       text-center"
            >

                <div
                    class="w-20
                           h-20
                           mx-auto
                           rounded-full
                           bg-sky-50
                           flex
                           items-center
                           justify-center"
                >

                    <i
                        class="fa-solid
                               fa-map-location-dot
                               text-3xl
                               text-sky-500"
                    ></i>

                </div>


                <h2
                    class="text-2xl
                           font-bold
                           text-slate-900
                           mt-6"
                >
                    No Destinations Available
                </h2>


                <p
                    class="text-slate-500
                           mt-3"
                >
                    Destinations will appear here once they are added
                    from the admin panel.
                </p>


                <a
                    href="index.php"
                    class="inline-block
                           mt-6
                           bg-sky-500
                           hover:bg-sky-600
                           text-white
                           px-6
                           py-3
                           rounded-xl
                           font-semibold
                           transition"
                >
                    Back to Home
                </a>

            </div>


        <?php endif; ?>

    </div>

</section>


<!-- =========================================================
     CTA
========================================================= -->

<section
    class="py-16
           bg-sky-500"
>

    <div
        class="max-w-5xl
               mx-auto
               px-4
               text-center"
    >

        <h2
            class="text-3xl
                   md:text-4xl
                   font-extrabold
                   text-white"
        >
            Ready to Explore?
        </h2>


        <p
            class="text-sky-100
                   mt-4
                   max-w-2xl
                   mx-auto"
        >
            Find your perfect destination and start planning
            your next unforgettable journey.
        </p>


        <a
            href="packages.php"
            class="inline-flex
                   items-center
                   gap-2
                   mt-7
                   bg-white
                   text-sky-600
                   hover:bg-slate-100
                   px-7
                   py-3.5
                   rounded-xl
                   font-bold
                   transition"
        >

            View Tour Packages

            <i
                class="fa-solid
                       fa-arrow-right"
            ></i>

        </a>

    </div>

</section>


<?php

require_once "includes/footer.php";

?>

