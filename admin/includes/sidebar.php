<?php
$currentAdminPage = basename($_SERVER['PHP_SELF']);
?>

<aside class="tg-admin-sidebar" id="tgAdminSidebar">
  <div class="tg-admin-brand">
    <a href="dashboard.php">
      <span class="tg-admin-brand-icon"><i class="fa-solid fa-plane-departure"></i></span>
      <span><b>TravelGo</b><small>ADMIN PANEL</small></span>
    </a>
  </div>
  <nav class="tg-admin-nav">
    <a class="<?= $currentAdminPage==='dashboard.php'?'active':'' ?>" href="dashboard.php"><i class="fa-solid fa-grid-2"></i><span>Dashboard</span></a>
    <a class="<?= $currentAdminPage==='packages.php'?'active':'' ?>" href="packages.php"><i class="fa-solid fa-suitcase-rolling"></i><span>Packages</span></a>
    <a class="<?= $currentAdminPage==='destinations.php'?'active':'' ?>" href="destinations.php"><i class="fa-solid fa-location-dot"></i><span>Destinations</span></a>
    <a class="<?= $currentAdminPage==='bookings.php'?'active':'' ?>" href="bookings.php"><i class="fa-solid fa-calendar-check"></i><span>Bookings</span></a>
    <a class="<?= $currentAdminPage==='users.php'?'active':'' ?>" href="users.php"><i class="fa-solid fa-users"></i><span>Users</span></a>
    <a class="<?= $currentAdminPage==='reviews.php'?'active':'' ?>" href="reviews.php"><i class="fa-solid fa-star"></i><span>Reviews</span></a>
    <a class="<?= $currentAdminPage==='blogs.php'?'active':'' ?>" href="blogs.php"><i class="fa-solid fa-newspaper"></i><span>Blogs</span></a>
    <a class="<?= $currentAdminPage==='contacts.php'?'active':'' ?>" href="contacts.php"><i class="fa-solid fa-envelope"></i><span>Contacts</span></a>
    <a class="<?= $currentAdminPage==='itinerary.php'?'active':'' ?>" href="itinerary.php"><i class="fa-solid fa-route"></i><span>Itinerary</span></a>
  </nav>
</aside>
<button type="button" class="tg-admin-menu-btn" id="tgAdminMenuBtn"><i class="fa-solid fa-bars"></i></button>
<div class="tg-admin-overlay" id="tgAdminOverlay"></div>
<script>
(function(){const b=document.getElementById('tgAdminMenuBtn'),s=document.getElementById('tgAdminSidebar'),o=document.getElementById('tgAdminOverlay');if(b){b.onclick=()=>s.classList.toggle('open')}if(o){o.onclick=()=>s.classList.remove('open')}})();
</script>
