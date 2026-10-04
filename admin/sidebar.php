<?php
$currentAdminPage = basename($_SERVER['PHP_SELF']);
$adminUserName = $_SESSION['user_name'] ?? 'Admin';
?>
<div class="tg-admin-overlay" id="tgAdminOverlay"></div>
<aside class="tg-admin-sidebar" id="tgAdminSidebar">
  <div class="tg-admin-brand">
    <a href="dashboard.php" class="tg-admin-brand-link">
      <span class="tg-admin-brand-icon"><i class="fa-solid fa-plane-departure"></i></span>
      <span><b>Travel<span>Go</span></b><small>ADMIN PANEL</small></span>
    </a>
    <button class="tg-admin-close" id="tgAdminClose" type="button" aria-label="Close sidebar"><i class="fa-solid fa-xmark"></i></button>
  </div>
  <div class="tg-admin-profile">
    <div class="tg-admin-avatar"><i class="fa-solid fa-user"></i></div>
    <div><strong><?php echo htmlspecialchars($adminUserName); ?></strong><small>Administrator</small></div>
  </div>
  <nav class="tg-admin-nav">
    <p class="tg-admin-label">MAIN MENU</p>
    <?php
    $items = [
      ['dashboard.php','fa-gauge-high','Dashboard'],
      ['packages.php','fa-suitcase-rolling','Packages'],
      ['destinations.php','fa-location-dot','Destinations'],
      ['bookings.php','fa-calendar-check','Bookings'],
      ['users.php','fa-users','Users'],
      ['reviews.php','fa-star','Reviews'],
      ['blogs.php','fa-newspaper','Blogs'],
      ['itinerary.php','fa-route','Itinerary'],
      ['contacts.php','fa-envelope','Contacts'],
    ];
    foreach ($items as $item):
      $active = $currentAdminPage === $item[0] ? ' active' : '';
    ?>
      <a href="<?php echo $item[0]; ?>" class="tg-admin-nav-link<?php echo $active; ?>">
        <i class="fa-solid <?php echo $item[1]; ?>"></i><span><?php echo $item[2]; ?></span>
      </a>
    <?php endforeach; ?>
  </nav>
  <div class="tg-admin-sidebar-footer">
    <a href="../index.php" target="_blank" rel="noopener noreferrer" class="tg-admin-bottom-btn">
      <i class="fa-solid fa-globe"></i>
      <span>View Website</span>
    </a>
    <a href="logout.php" class="tg-admin-bottom-btn tg-admin-logout">
      <i class="fa-solid fa-right-from-bracket"></i>
      <span>Logout</span>
    </a>
  </div>
</aside>
<button class="tg-admin-mobile-btn" id="tgAdminMobileBtn" type="button" aria-label="Open admin menu"><i class="fa-solid fa-bars"></i></button>
