
<?php

require_once "config/database.php";
require_once "includes/user-auth.php";

/*
|--------------------------------------------------------------------------
| Filters + Pagination
|--------------------------------------------------------------------------
*/

$perPage = 6;
$currentPage = max(1, (int)($_GET['page'] ?? 1));

$minPrice = isset($_GET['min_price']) && $_GET['min_price'] !== '' ? (float)$_GET['min_price'] : 0;
$maxPrice = isset($_GET['max_price']) && $_GET['max_price'] !== '' ? (float)$_GET['max_price'] : 1000000;
$durationFilter = trim($_GET['duration'] ?? '');
$destinationFilter = trim($_GET['destination'] ?? '');

$where = ["packages.status = 'active'"];
$params = [];

if ($minPrice > 0) {
    $where[] = 'packages.price >= :min_price';
    $params[':min_price'] = $minPrice;
}

if ($maxPrice < 1000000) {
    $where[] = 'packages.price <= :max_price';
    $params[':max_price'] = $maxPrice;
}

if ($durationFilter !== '') {
    $where[] = 'packages.duration = :duration';
    $params[':duration'] = $durationFilter;
}

if ($destinationFilter !== '') {
    $where[] = 'destinations.id = :destination';
    $params[':destination'] = (int)$destinationFilter;
}

$whereSql = implode(' AND ', $where);

// Filter options
$durationOptions = $pdo->query("
    SELECT DISTINCT duration
    FROM packages
    WHERE status = 'active' AND duration IS NOT NULL AND duration <> ''
    ORDER BY duration
")->fetchAll(PDO::FETCH_COLUMN);

$destinationOptions = $pdo->query("
    SELECT id, name
    FROM destinations
    ORDER BY name
")->fetchAll(PDO::FETCH_ASSOC);

$totalStmt = $pdo->prepare("
    SELECT COUNT(*)
    FROM packages
    LEFT JOIN destinations ON packages.destination_id = destinations.id
    WHERE $whereSql
");
foreach ($params as $key => $value) {
    $totalStmt->bindValue($key, $value, is_int($value) ? PDO::PARAM_INT : PDO::PARAM_STR);
}
$totalStmt->execute();
$totalPackages = (int)$totalStmt->fetchColumn();
$totalPages = max(1, (int)ceil($totalPackages / $perPage));
$currentPage = min($currentPage, $totalPages);
$offset = ($currentPage - 1) * $perPage;

$stmt = $pdo->prepare("
    SELECT
        packages.*,
        destinations.name AS destination_name,
        destinations.location AS destination_location
    FROM packages
    LEFT JOIN destinations ON packages.destination_id = destinations.id
    WHERE $whereSql
    ORDER BY packages.id DESC
    LIMIT :limit OFFSET :offset
");
foreach ($params as $key => $value) {
    $stmt->bindValue($key, $value, is_int($value) ? PDO::PARAM_INT : PDO::PARAM_STR);
}
$stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$packages = $stmt->fetchAll(PDO::FETCH_ASSOC);

$filterQuery = function($page = null) use ($minPrice, $maxPrice, $durationFilter, $destinationFilter, $currentPage) {
    $query = [];
    if ($minPrice > 0) $query['min_price'] = $minPrice;
    if ($maxPrice < 1000000) $query['max_price'] = $maxPrice;
    if ($durationFilter !== '') $query['duration'] = $durationFilter;
    if ($destinationFilter !== '') $query['destination'] = $destinationFilter;
    $query['page'] = $page ?? $currentPage;
    return '?' . http_build_query($query);
};

/*
|--------------------------------------------------------------------------
| Header + Navbar
|--------------------------------------------------------------------------
*/

require_once "includes/header.php";
require_once "includes/navbar.php";

?>

<!-- =========================================================
     PAGE HERO
========================================================= -->

<section class=" py-20 h-[450px] bg-[url('assets/images/p1.png')]   bg-cover bg-center">

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">

        <p class="text-sky-400 font-semibold uppercase tracking-widest text-sm">
            Travel With TravelGo
        </p>

        <h1 class="text-4xl md:text-5xl font-extrabold text-white mt-3">
            Explore Our Tour Packages
        </h1>

        <p class="text-slate-300 max-w-2xl mx-auto mt-5 leading-7">
            Choose from our carefully designed tour packages
            and make your next journey unforgettable.
        </p>

    </div>

</section>


<!-- =========================================================
     PACKAGES
========================================================= -->

<section class="py-12 md:py-16 bg-slate-50">

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <div class="flex flex-col lg:flex-row items-start gap-7 lg:gap-8">

            <!-- =====================================================
                 LARGE FILTER SIDEBAR
            ====================================================== -->
            <aside class="w-full lg:w-[320px] xl:w-[340px] shrink-0 bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">

                <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between">
                    <div>
                        <h2 class="text-xl font-extrabold text-slate-900">Filter</h2>
                        <p class="text-xs text-slate-400 mt-1">Find your perfect trip</p>
                    </div>
                    <i class="fa-solid fa-sliders text-sky-500 text-lg"></i>
                </div>

                <form method="get" class="p-6">

                    <!-- Price -->
                    <div class="mb-7">
                        <label class="block text-sm font-bold text-slate-800 mb-3">Price Range</label>
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <span class="text-[11px] text-slate-400">Min Price</span>
                                <div class="relative mt-1">
                                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm">₹</span>
                                    <input type="number" name="min_price" min="0" value="<?php echo $minPrice > 0 ? htmlspecialchars((string)$minPrice) : ''; ?>" placeholder="0" class="w-full pl-7 pr-3 py-2.5 rounded-lg border border-slate-200 text-sm outline-none focus:border-sky-400 focus:ring-2 focus:ring-sky-100">
                                </div>
                            </div>
                            <div>
                                <span class="text-[11px] text-slate-400">Max Price</span>
                                <div class="relative mt-1">
                                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm">₹</span>
                                    <input type="number" name="max_price" min="0" value="<?php echo $maxPrice < 1000000 ? htmlspecialchars((string)$maxPrice) : ''; ?>" placeholder="1000000" class="w-full pl-7 pr-3 py-2.5 rounded-lg border border-slate-200 text-sm outline-none focus:border-sky-400 focus:ring-2 focus:ring-sky-100">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Duration -->
                    <div class="mb-7">
                        <label class="block text-sm font-bold text-slate-800 mb-3">Duration</label>
                        <div class="space-y-2.5">
                            <label class="flex items-center gap-3 cursor-pointer text-sm text-slate-600">
                                <input type="radio" name="duration" value="" <?php echo $durationFilter === '' ? 'checked' : ''; ?> class="accent-sky-500 w-4 h-4">
                                <span>All Durations</span>
                            </label>
                            <?php foreach ($durationOptions as $duration): ?>
                                <label class="flex items-center gap-3 cursor-pointer text-sm text-slate-600">
                                    <input type="radio" name="duration" value="<?php echo htmlspecialchars($duration); ?>" <?php echo $durationFilter === $duration ? 'checked' : ''; ?> class="accent-sky-500 w-4 h-4">
                                    <span><?php echo htmlspecialchars($duration); ?></span>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- Destination -->
                    <div class="mb-7">
                        <label for="destination" class="block text-sm font-bold text-slate-800 mb-3">Destination</label>
                        <div class="relative">
                            <i class="fa-solid fa-location-dot absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                            <select id="destination" name="destination" class="w-full appearance-none pl-9 pr-9 py-3 rounded-lg border border-slate-200 text-sm text-slate-600 bg-white outline-none focus:border-sky-400 focus:ring-2 focus:ring-sky-100">
                                <option value="">All Destinations</option>
                                <?php foreach ($destinationOptions as $destination): ?>
                                    <option value="<?php echo (int)$destination['id']; ?>" <?php echo $destinationFilter === (string)$destination['id'] ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($destination['name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <i class="fa-solid fa-chevron-down absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs pointer-events-none"></i>
                        </div>
                    </div>

                    <button type="submit" class="w-full py-3 rounded-xl bg-sky-500 hover:bg-sky-600 text-white font-bold transition shadow-sm">
                        <i class="fa-solid fa-filter mr-2"></i>Apply Filter
                    </button>

                    <?php if ($minPrice > 0 || $maxPrice < 1000000 || $durationFilter !== '' || $destinationFilter !== ''): ?>
                        <a href="packages.php" class="block text-center text-sm font-semibold text-slate-500 hover:text-sky-500 mt-4">Clear All Filters</a>
                    <?php endif; ?>

                </form>
            </aside>


            <!-- =====================================================
                 PACKAGE RESULTS
            ====================================================== -->
            <div class="flex-1 min-w-0 w-full">

                <div class="flex items-center justify-between mb-5">
                    <div>
                        <h2 class="text-xl md:text-2xl font-extrabold text-slate-900">Tour Packages</h2>
                        <p class="text-sm text-slate-500 mt-1">Showing <?php echo count($packages); ?> of <?php echo $totalPackages; ?> packages</p>
                    </div>
                </div>

                <?php if (count($packages) > 0): ?>

                    <div class="space-y-5">

                        <?php foreach ($packages as $package): ?>

                            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm hover:shadow-md transition overflow-hidden group">
                                <div class="flex flex-col sm:flex-row min-h-[205px]">

                                    <!-- Image -->
                                    <div class="relative w-full sm:w-[230px] lg:w-[250px] shrink-0 h-[205px] sm:h-auto overflow-hidden bg-slate-100">
                                        <?php if (!empty($package['image'])): ?>
                                            <img src="uploads/<?php echo htmlspecialchars($package['image']); ?>" alt="<?php echo htmlspecialchars($package['title']); ?>" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                                        <?php else: ?>
                                            <div class="w-full h-full flex items-center justify-center bg-sky-50">
                                                <i class="fa-solid fa-suitcase-rolling text-5xl text-sky-400"></i>
                                            </div>
                                        <?php endif; ?>
                                    </div>

                                    <!-- Content -->
                                    <div class="flex-1 p-5 md:p-6 flex flex-col justify-between min-w-0">
                                        <div>
                                            <div class="flex flex-wrap items-start justify-between gap-3">
                                                <div>
                                                    <?php if (!empty($package['destination_name'])): ?>
                                                        <div class="flex items-center gap-2 text-xs font-semibold text-sky-500 mb-2">
                                                            <i class="fa-solid fa-location-dot"></i>
                                                            <span><?php echo htmlspecialchars($package['destination_name']); ?></span>
                                                        </div>
                                                    <?php endif; ?>

                                                    <h3 class="text-xl md:text-2xl font-extrabold text-slate-900 group-hover:text-sky-500 transition">
                                                        <?php echo htmlspecialchars($package['title']); ?>
                                                    </h3>
                                                </div>

                                                <div class="text-right shrink-0">
                                                    <p class="text-xs text-slate-400">Starting from</p>
                                                    <p class="text-xl font-extrabold text-sky-600">₹<?php echo number_format($package['price'], 0); ?></p>
                                                </div>
                                            </div>

                                            <div class="flex flex-wrap items-center gap-x-5 gap-y-2 mt-3 text-sm text-slate-500">
                                                <?php if (!empty($package['duration'])): ?>
                                                    <span class="inline-flex items-center gap-2">
                                                        <i class="fa-regular fa-clock text-sky-500"></i>
                                                        <?php echo htmlspecialchars($package['duration']); ?>
                                                    </span>
                                                <?php endif; ?>
                                                <span class="inline-flex items-center gap-1 text-amber-400">
                                                    <i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i>
                                                    <b class="text-slate-500 ml-1">5.0</b>
                                                </span>
                                            </div>

                                            <?php if (!empty($package['description'])): ?>
                                                <p class="text-sm leading-6 text-slate-500 mt-4 line-clamp-2">
                                                    <?php echo htmlspecialchars($package['description']); ?>
                                                </p>
                                            <?php else: ?>
                                                <p class="text-sm leading-6 text-slate-500 mt-4 line-clamp-2">Discover amazing places and unforgettable experiences with TravelGo.</p>
                                            <?php endif; ?>
                                        </div>

                                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mt-5 pt-4 border-t border-slate-100">
                                            <span class="text-xs text-slate-400 hidden sm:block">TravelGo • Best experience</span>

                                            <div class="flex flex-col xs:flex-row sm:flex-row gap-2 w-full sm:w-auto sm:ml-auto">
                                                <a href="package-details.php?id=<?php echo (int)$package['id']; ?>" class="inline-flex items-center justify-center gap-2 border border-sky-500 text-sky-600 hover:bg-sky-50 px-5 py-2.5 rounded-lg text-sm font-bold transition">
                                                    View Details <i class="fa-solid fa-arrow-right text-xs"></i>
                                                </a>

                                                <a href="book-package.php?id=<?php echo (int)$package['id']; ?>" class="inline-flex items-center justify-center gap-2 bg-sky-500 hover:bg-sky-600 text-white px-5 py-2.5 rounded-lg text-sm font-bold transition shadow-sm">
                                                    <i class="fa-solid fa-calendar-check text-xs"></i>
                                                    Book Now
                                                </a>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>

                        <?php endforeach; ?>

                    </div>

                    <!-- Pagination -->
                    <?php if ($totalPages > 1): ?>
                        <div class="mt-8 flex flex-wrap items-center justify-center gap-2" aria-label="Tour Packages pagination">
                            <?php if ($currentPage > 1): ?>
                                <a href="<?php echo htmlspecialchars($filterQuery($currentPage - 1)); ?>" class="w-10 h-10 flex items-center justify-center rounded-lg border border-slate-200 bg-white text-slate-600 hover:bg-slate-100 transition"><i class="fa-solid fa-chevron-left text-xs"></i></a>
                            <?php endif; ?>

                            <?php for ($page = 1; $page <= $totalPages; $page++): ?>
                                <a href="<?php echo htmlspecialchars($filterQuery($page)); ?>" class="w-10 h-10 flex items-center justify-center rounded-lg font-bold text-sm transition <?php echo $page === $currentPage ? 'bg-sky-500 text-white shadow-md' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-100'; ?>"><?php echo $page; ?></a>
                            <?php endfor; ?>

                            <?php if ($currentPage < $totalPages): ?>
                                <a href="<?php echo htmlspecialchars($filterQuery($currentPage + 1)); ?>" class="w-10 h-10 flex items-center justify-center rounded-lg border border-slate-200 bg-white text-slate-600 hover:bg-slate-100 transition"><i class="fa-solid fa-chevron-right text-xs"></i></a>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>

                <?php else: ?>
                    <div class="bg-white rounded-2xl border border-slate-200 p-10 md:p-14 text-center">
                        <div class="w-16 h-16 mx-auto rounded-full bg-sky-50 flex items-center justify-center">
                            <i class="fa-solid fa-filter-circle-xmark text-2xl text-sky-500"></i>
                        </div>
                        <h2 class="text-2xl font-bold text-slate-900 mt-5">No Packages Found</h2>
                        <p class="text-slate-500 mt-2">Try changing your filters to see more tour packages.</p>
                        <a href="packages.php" class="inline-flex items-center gap-2 mt-5 bg-sky-500 hover:bg-sky-600 text-white px-6 py-3 rounded-xl font-semibold transition">Clear Filters</a>
                    </div>
                <?php endif; ?>

            </div>
        </div>

    </div>

</section>


<!-- =========================================================
     CTA
========================================================= -->

<section class="py-16 bg-sky-500">

    <div
        class="max-w-5xl mx-auto
               px-4 text-center"
    >

        <h2
            class="text-3xl md:text-4xl
                   font-extrabold text-white"
        >
            Can't Find What You're Looking For?
        </h2>


        <p
            class="text-sky-100
                   max-w-2xl mx-auto mt-4"
        >
            Tell us about your dream trip and we'll help
            you plan the perfect journey.
        </p>


        <a
            href="contact.php"
            class="inline-flex
                   items-center gap-2
                   mt-7 bg-white
                   hover:bg-slate-100
                   text-sky-600
                   px-7 py-3.5
                   rounded-xl
                   font-bold transition"
        >

            Plan My Trip

            <i class="fa-solid fa-arrow-right"></i>

        </a>

    </div>

</section>


<?php

require_once "includes/footer.php";

?>
