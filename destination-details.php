<?php
require_once "config/database.php";
require_once "includes/user-auth.php";

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
if ($id <= 0) {
    header("Location: destinations.php");
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM destinations WHERE id = ? LIMIT 1");
$stmt->execute([$id]);
$destination = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$destination) {
    header("Location: destinations.php");
    exit;
}

$packageStmt = $pdo->prepare("SELECT * FROM packages WHERE destination_id = ? AND status = 'active' ORDER BY id DESC");
$packageStmt->execute([$id]);
$packages = $packageStmt->fetchAll(PDO::FETCH_ASSOC);

require_once "includes/header.php";
require_once "includes/navbar.php";

$name = trim($destination['name'] ?? 'Destination');
$location = trim($destination['location'] ?? 'India');
$description = trim($destination['description'] ?? '');


$availableImages = [];

if (!empty($destination['gallery_images'])) {
    $decodedGallery = json_decode($destination['gallery_images'], true);
    if (is_array($decodedGallery)) {
        foreach ($decodedGallery as $galleryImage) {
            if (!empty($galleryImage) && !in_array($galleryImage, $availableImages, true)) {
                $availableImages[] = $galleryImage;
            }
        }
    }
}

// Package images are a useful secondary source when fewer detail images were uploaded.
foreach ($packages as $p) {
    if (!empty($p['image']) && !in_array($p['image'], $availableImages, true)) {
        $availableImages[] = $p['image'];
    }
}

// Finally use the main destination image. Never scan the global uploads folder.
if (!empty($destination['image']) && !in_array($destination['image'], $availableImages, true)) {
    $availableImages[] = $destination['image'];
}

?>



<main class="min-h-screen bg-white text-slate-900">
   
 <!-- HERO -->
<section class="relative h-[400px] overflow-hidden bg-slate-950 max-[640px]:h-[360px]">

    <?php if (!empty($destination['image'])): ?>
        <img
            src="uploads/<?php echo htmlspecialchars($destination['image']); ?>"
            alt="<?php echo htmlspecialchars($name); ?>"
            class="absolute inset-0 h-full w-full object-cover"
        >
    <?php else: ?>
        <div class="absolute inset-0 bg-slate-700"></div>
    <?php endif; ?>

    <!-- Image Overlay -->
    <div class="absolute inset-0 bg-gradient-to-r from-slate-950/75 via-slate-950/35 to-transparent"></div>

    <!-- Hero Content -->
    <div class="relative z-10 mx-auto flex h-[400px] w-[min(1220px,calc(100%-32px))] items-center max-[640px]:h-[360px] max-[640px]:w-[calc(100%-24px)]">

        <div class="max-w-[650px]">

            <div class="mb-4 inline-flex items-center gap-2 rounded-full bg-sky-500/20 px-5 py-2 text-[11px] font-bold uppercase tracking-wide text-sky-300 backdrop-blur-sm">
                <i class="fa-solid fa-compass"></i>
                Explore Destination
            </div>

            <h1 class="m-0 text-5xl font-extrabold leading-[1.05] text-white max-[640px]:text-4xl">
                <?php echo htmlspecialchars($name); ?>
            </h1>

            <div class="mt-5 flex items-center gap-2 text-sm font-medium text-slate-200">
                <i class="fa-solid fa-location-dot text-sky-400"></i>
                <?php echo htmlspecialchars($location); ?>
            </div>

        </div>

    </div>
</section>

    <!-- HIGHLIGHTS + DISCOVER -->
    <section class="py-[30px] pb-7">
        <div class="w-[min(1220px,calc(100%-32px))] mx-auto max-[640px]:w-[calc(100%-24px)] grid grid-cols-[1.02fr_.98fr] gap-[34px] items-start max-[900px]:grid-cols-1">
            <div>
                <div class="text-[#2688a8] text-[9px] font-bold uppercase tracking-wider">Interactive &amp; Experiences</div>
                <h2 class="text-[22px] leading-tight font-extrabold my-1 text-slate-900">Highlights &amp; Experiences</h2>
                <div class="grid grid-cols-3 gap-[7px] mt-[9px] max-[640px]:grid-cols-1">
                    <?php for ($i = 0; $i < 6; $i++):
                        $img = $availableImages[$i % max(1, count($availableImages))] ?? '';
                    ?>
                        <div class="relative  overflow-hidden rounded-[7px] bg-blue-100 max-[640px]:h-[180px]">
                            <?php if ($img): ?><img src="uploads/<?php echo htmlspecialchars($img); ?>" alt="<?php echo htmlspecialchars($name); ?>"><?php endif; ?>
                        </div>
                    <?php endfor; ?>
                </div>

<div class="grid grid-cols-3 gap-[9px] mt-3 max-[640px]:grid-cols-1">

    <!-- Location -->
    <div class="border border-sky-200 rounded-[10px] p-[10px] min-h-[72px] bg-sky-50">
        <div class="text-sky-600 text-[20px] mb-1">
            <i class="fa-solid fa-location-dot"></i>
        </div>
        <h3 class="text-slate-900 text-[20px] leading-tight">Location</h3>
        <p class="text-slate-700 text-[18px] leading-tight mt-1">
            <?php echo htmlspecialchars($location); ?>
        </p>
    </div>

    <!-- Tour Packages -->
    <div class="border border-emerald-200 rounded-[10px] p-[10px] min-h-[72px] bg-emerald-50">
        <div class="text-emerald-600 text-[20px] mb-1">
            <i class="fa-solid fa-suitcase-rolling"></i>
        </div>
        <h3 class="text-slate-900 text-[20px] leading-tight">Tour Packages</h3>
        <p class="text-slate-700 text-[18px] leading-tight mt-1">
            <?php echo count($packages); ?> Available
        </p>
    </div>

    <!-- Travel With Us -->
    <div class="border border-orange-200 rounded-[10px] p-[10px] min-h-[72px] bg-orange-50">
        <div class="text-orange-600 text-[20px] mb-1">
            <i class="fa-solid fa-shield-heart"></i>
        </div>
        <h3 class="text-slate-900 text-[20px] leading-tight">Travel With Us</h3>
        <p class="text-slate-700 text-[18px] leading-tight mt-1">
            Safe &amp; Comfortable
        </p>
    </div>

</div>
            </div>

            <div>
                <div class="text-[#2688a8] text-[9px] font-bold uppercase tracking-wider">About This Destination</div>
                <h2 class="text-[22px] leading-tight font-extrabold my-1 text-slate-900">Discover <?php echo htmlspecialchars($name); ?></h2>
                <p class="text-[11px] leading-[1.65] text-slate-700 m-0">
                    <?php echo $description ? nl2br(htmlspecialchars($description)) : 'Discover the beauty, culture and unforgettable experiences of this amazing destination with TravelGo.'; ?>
                </p>
                <div class="mt-6 w-full">
                    <div class="flex w-full gap-2">
                        <input id="destinationMapSearch" type="text" value="<?php echo htmlspecialchars($location); ?>"
                               class="min-w-0 flex-1 h-12 rounded-lg border border-slate-300 bg-white px-4 text-sm text-slate-800 outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-100"
                               placeholder="Search any place..." aria-label="Search any place">
                        <button type="button" id="destinationMapFind"
                                class="h-12 shrink-0 rounded-lg bg-sky-500 px-5 text-sm font-bold text-white shadow-sm transition hover:bg-sky-600">
                            <i class="fa-solid fa-magnifying-glass mr-1"></i>Find
                        </button>
                    </div>

                    <div class="mt-3 h-[430px] w-full overflow-hidden rounded-lg border border-slate-200 bg-slate-100 shadow-sm max-[900px]:h-[390px] max-[640px]:h-[330px]">
                        <iframe id="destinationGoogleMap" loading="lazy" title="Interactive map of <?php echo htmlspecialchars($name); ?>"
                                class="h-full w-full border-0"
                                src="https://www.google.com/maps?q=<?php echo rawurlencode($location); ?>&output=embed"
                                allowfullscreen></iframe>
                    </div>
                </div>

                <script>
                (function () {
                    const input = document.getElementById('destinationMapSearch');
                    const button = document.getElementById('destinationMapFind');
                    const map = document.getElementById('destinationGoogleMap');
                    if (!input || !button || !map) return;

                    function findPlace() {
                        const place = input.value.trim();
                        if (!place) return;
                        map.src = 'https://www.google.com/maps?q=' + encodeURIComponent(place) + '&output=embed';
                    }

                    button.addEventListener('click', findPlace);
                    input.addEventListener('keydown', function (event) {
                        if (event.key === 'Enter') {
                            event.preventDefault();
                            findPlace();
                        }
                    });
                })();
                </script>
            </div>
        </div>
    </section>
</main>

<?php require_once "includes/footer.php"; ?>
