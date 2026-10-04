<?php
require_once "config/database.php";
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['user_id'])) { header("Location: login.php"); exit; }

$userName = $_SESSION['user_name'] ?? 'Traveler';
$stmt = $pdo->query("SELECT packages.*, destinations.name AS destination_name FROM packages LEFT JOIN destinations ON packages.destination_id = destinations.id WHERE packages.status='active' ORDER BY packages.id DESC");
$packages = $stmt->fetchAll(PDO::FETCH_ASSOC);
require_once "includes/header.php";
require_once "includes/navbar.php";
?>

<section class="py-12 md:py-16 px-4 bg-slate-50 min-h-[70vh]">
  <div class="max-w-7xl mx-auto">
    <div class="bg-gradient-to-r from-sky-600 to-cyan-500 rounded-3xl p-7 md:p-10 text-white mb-10 shadow-lg">
      <p class="font-semibold text-sky-100 mb-2">Plan Your Trip</p>
      <h1 class="text-3xl md:text-4xl font-extrabold">Welcome, <?php echo htmlspecialchars($userName); ?> 👋</h1>
      <p class="mt-3 text-sky-50 max-w-2xl">Choose a package and start planning your next unforgettable journey.</p>
    </div>

    <div class="flex items-end justify-between gap-4 mb-6">
      <div><h2 class="text-2xl md:text-3xl font-extrabold text-slate-900">Choose Your Package</h2><p class="text-slate-500 mt-1">Packages available for your next trip.</p></div>
      <a href="profile.php" class="hidden sm:inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-white border border-slate-200 font-semibold text-slate-700 hover:border-sky-300"><i class="fa-solid fa-user"></i> My Profile</a>
    </div>

    <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
      <?php foreach ($packages as $package): ?>
      <article class="bg-white rounded-2xl overflow-hidden border border-slate-200 shadow-sm hover:shadow-lg transition">
        <div class="h-52 bg-slate-100 overflow-hidden">
          <?php if (!empty($package['image'])): ?><img src="uploads/<?php echo htmlspecialchars($package['image']); ?>" class="w-full h-full object-cover" alt="<?php echo htmlspecialchars($package['title']); ?>"><?php endif; ?>
        </div>
        <div class="p-5">
          <p class="text-sm text-sky-600 font-semibold"><i class="fa-solid fa-location-dot mr-1"></i><?php echo htmlspecialchars($package['destination_name'] ?? 'India'); ?></p>
          <h3 class="text-xl font-bold text-slate-900 mt-1"><?php echo htmlspecialchars($package['title']); ?></h3>
          <div class="flex items-center justify-between mt-5"><strong class="text-xl text-sky-600">₹<?php echo number_format((float)$package['price']); ?></strong><a href="book-package.php?id=<?php echo (int)$package['id']; ?>" class="px-4 py-2 rounded-xl bg-sky-600 text-white font-semibold hover:bg-sky-700">Book Now</a></div>
        </div>
      </article>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<?php require_once "includes/footer.php"; ?>
