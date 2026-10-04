<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$isLoggedIn = isset($_SESSION['user_id']);
$isAdmin = isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';

$userName = $_SESSION['user_name'] ?? 'User';

?>

<!-- =====================================================
     TRAVELGO NAVBAR
===================================================== -->

<header class="travelgo-header">

    <div class="travelgo-container">

        <!-- ================= TOP NAV ================= -->

        <div class="travelgo-nav">

            <!-- ================= LOGO ================= -->

            <a href="index.php" class="travelgo-logo">

                <div class="travelgo-logo-icon">
                    <i class="fa-solid fa-plane-departure"></i>
                </div>

                <div class="travelgo-logo-text">

                    <div class="travelgo-brand">
                        <span>Travel</span><strong>Go</strong>
                    </div>

                    <div class="travelgo-tagline">
                        TOURS & TRAVELS
                    </div>

                </div>

            </a>


            <!-- ================= DESKTOP MENU ================= -->

            <nav class="travelgo-desktop-menu">

                <a href="index.php" class="travelgo-menu-link">
                    Home
                </a>

                <a href="destinations.php" class="travelgo-menu-link">
                    Destinations
                </a>

                <a href="packages.php" class="travelgo-menu-link">
                    Packages
                </a>

                <a href="about.php" class="travelgo-menu-link">
                    About
                </a>

                <a href="gallery.php" class="travelgo-menu-link">
                    Gallery
                </a>

                <a href="blog.php" class="travelgo-menu-link">
                    Blog
                </a>

                <a href="contact.php" class="travelgo-menu-link">
                    Contact
                </a>

            </nav>


            <!-- ================= RIGHT SIDE ================= -->

            <div class="travelgo-right">

                <?php if ($isLoggedIn): ?>

                    <!-- USER DROPDOWN -->
                    <div class="travelgo-account" id="travelgoAccount">

                        <button type="button"
                                class="travelgo-account-btn"
                                id="travelgoAccountBtn"
                                aria-expanded="false"
                                aria-haspopup="true">

                            <span class="travelgo-user-icon">
                                <i class="fa-solid fa-user"></i>
                            </span>

                            <span class="travelgo-user-text">
                                <span>Hi,</span>
                                <strong><?php echo htmlspecialchars($userName); ?></strong>
                            </span>

                            <i class="fa-solid fa-chevron-down travelgo-account-chevron"></i>

                        </button>

                        <div class="travelgo-account-menu" id="travelgoAccountMenu">

                            <div class="travelgo-account-heading">
                                <div class="travelgo-account-avatar">
                                    <i class="fa-solid fa-user"></i>
                                </div>
                                <div>
                                    <small>Logged in as</small>
                                    <strong><?php echo htmlspecialchars($userName); ?></strong>
                                </div>
                            </div>

                            <div class="travelgo-account-divider"></div>

                            <?php if ($isAdmin): ?>
                                <a href="admin/dashboard.php" class="travelgo-dropdown-link">
                                    <i class="fa-solid fa-gauge-high"></i>
                                    <span>Admin Dashboard</span>
                                </a>
                            <?php endif; ?>

                            <div class="travelgo-account-divider"></div>

                            <a href="<?php echo $isAdmin ? 'admin/logout.php' : 'logout.php'; ?>"
                               class="travelgo-dropdown-link travelgo-dropdown-logout">
                                <i class="fa-solid fa-right-from-bracket"></i>
                                <span>Logout</span>
                            </a>

                        </div>

                    </div>

                <?php else: ?>

                    <!-- LOGIN -->
                    <a href="login.php" class="travelgo-login">Login</a>

                    <!-- REGISTER -->
                    <a href="register.php" class="travelgo-register">Register</a>

                <?php endif; ?>

                <!-- ================= PLAN YOUR TRIP ================= -->
                <a href="packages.php" class="travelgo-plan">
                    <i class="fa-solid fa-plane-departure"></i>
                    <span>Plan Your Trip</span>
                    <i class="fa-solid fa-arrow-right travelgo-plan-arrow"></i>
                </a>

            </div>

            <!-- ================= MOBILE BUTTON ================= -->

            <button
                type="button"
                id="mobileMenuBtn"
                class="travelgo-mobile-btn"
                aria-label="Open Menu">

                <i id="mobileMenuIcon"
                   class="fa-solid fa-bars"></i>

            </button>

        </div>


        <!-- =====================================================
             MOBILE MENU
        ===================================================== -->

        <div id="mobileMenu"
             class="travelgo-mobile-menu">

            <div class="travelgo-mobile-inner">


                <!-- HOME -->

                <a href="index.php"
                   class="travelgo-mobile-link">

                   

                    <span>Home</span>

                </a>


                <!-- DESTINATIONS -->

                <a href="destinations.php"
                   class="travelgo-mobile-link">

                  

                    <span>Destinations</span>

                </a>


                <!-- PACKAGES -->

                <a href="packages.php"
                   class="travelgo-mobile-link">

                    

                    <span>Packages</span>

                </a>


                <!-- ABOUT -->

                <a href="about.php"
                   class="travelgo-mobile-link">

                    <span>About</span>

                </a>


                <!-- GALLERY -->

                <a href="gallery.php"
                   class="travelgo-mobile-link">

            

                    <span>Gallery</span>

                </a>


                <!-- BLOG -->

                <a href="blog.php"
                   class="travelgo-mobile-link">

                    
                    <span>Blog</span>

                </a>


                <!-- CONTACT -->

                <a href="contact.php"
                   class="travelgo-mobile-link">

                 
                    <span>Contact</span>

                </a>


                <?php if ($isLoggedIn): ?>

                    <?php if ($isAdmin): ?>

                        <!-- ADMIN -->

                        <a href="admin/dashboard.php"
                           class="travelgo-mobile-link">

                            <i class="fa-solid fa-gauge-high"></i>

                            <span>Admin Dashboard</span>

                        </a>


                        <!-- LOGOUT -->

                        <a href="admin/logout.php"
                           class="travelgo-mobile-link travelgo-mobile-danger">

                            <i class="fa-solid fa-right-from-bracket"></i>

                            <span>Logout</span>

                        </a>


                    <?php else: ?>


                        <!-- LOGOUT -->

                        <a href="logout.php"
                           class="travelgo-mobile-link travelgo-mobile-danger">

                            <i class="fa-solid fa-right-from-bracket"></i>

                            <span>Logout</span>

                        </a>

                    <?php endif; ?>


                <?php else: ?>

                    <!-- LOGIN -->

                    <a href="login.php"
                       class="travelgo-mobile-link">

                        <i class="fa-solid fa-right-to-bracket"></i>

                        <span>Login</span>

                    </a>


                    <!-- REGISTER -->

                    <a href="register.php"
                       class="travelgo-mobile-link">

                        <i class="fa-solid fa-user-plus"></i>

                        <span>Register</span>

                    </a>

                <?php endif; ?>


                <!-- PLAN YOUR TRIP -->

                <a href="packages.php"
                   class="travelgo-mobile-plan">

                    <i class="fa-solid fa-plane-departure"></i>

                    <span>Plan Your Trip</span>

                    <i class="fa-solid fa-arrow-right"></i>

                </a>


            </div>

        </div>

    </div>

</header>


<!-- =====================================================
     NAVBAR CSS
===================================================== -->

<style>

/* =====================================================
   GLOBAL SCROLL FIX
===================================================== */

html,
body {
    width: 100%;
    max-width: 100%;
    overflow-x: hidden !important;
}


/* =====================================================
   HEADER
===================================================== */

.travelgo-header {

    width: 100%;

    max-width: 100%;

    background: rgba(255, 255, 255, 0.97);

    backdrop-filter: blur(12px);

    -webkit-backdrop-filter: blur(12px);

    border-bottom: 1px solid #e8eef5;

    box-shadow:
        0 4px 20px rgba(15, 23, 42, 0.06);

    position: sticky;

    top: 0;

    z-index: 9999;

    overflow: visible;

}


/* =====================================================
   CONTAINER
===================================================== */

.travelgo-container {

    width: 100%;

    max-width: 1380px;

    margin: 0 auto;

    padding: 0 24px;

}


/* =====================================================
   NAV
===================================================== */

.travelgo-nav {

    min-height: 82px;

    display: flex;

    align-items: center;

    justify-content: space-between;

    gap: 24px;

}


/* =====================================================
   LOGO
===================================================== */

.travelgo-logo {

    display: flex;

    align-items: center;

    gap: 12px;

    flex-shrink: 0;

    text-decoration: none;

}


.travelgo-logo-icon {

    width: 50px;

    height: 50px;

    border-radius: 15px;

    display: flex;

    align-items: center;

    justify-content: center;

    background:
        linear-gradient(
            135deg,
            #38bdf8,
            #0284c7
        );

    color: white;

    font-size: 21px;

    box-shadow:
        0 8px 18px rgba(14, 165, 233, 0.25);

    transition: 0.3s ease;

}


.travelgo-logo:hover .travelgo-logo-icon {

    transform: translateY(-2px) rotate(-3deg);

}


.travelgo-logo-text {

    line-height: 1;

}


.travelgo-brand {

    font-size: 26px;

    font-weight: 800;

    letter-spacing: -0.7px;

    white-space: nowrap;

}


.travelgo-brand span {

    color: #172033;

}


.travelgo-brand strong {

    color: #0ea5e9;

}


.travelgo-tagline {

    margin-top: 6px;

    font-size: 9px;

    font-weight: 700;

    letter-spacing: 2.2px;

    color: #94a3b8;

}


/* =====================================================
   DESKTOP MENU
===================================================== */

.travelgo-desktop-menu {

    display: flex;

    align-items: center;

    justify-content: center;

    gap: 25px;

    flex: 1;

}


.travelgo-menu-link {

    position: relative;

    padding: 8px 0;

    color: #334155;

    font-size: 15px;

    font-weight: 600;

    white-space: nowrap;

    text-decoration: none;

    transition: 0.25s ease;

}


.travelgo-menu-link:hover {

    color: #0ea5e9;

}


.travelgo-menu-link::after {

    content: "";

    position: absolute;

    left: 0;

    bottom: 0;

    width: 0;

    height: 2px;

    border-radius: 10px;

    background: #0ea5e9;

    transition: width 0.25s ease;

}


.travelgo-menu-link:hover::after {

    width: 100%;

}


/* =====================================================
   RIGHT SIDE
===================================================== */

.travelgo-right {

    display: flex;

    align-items: center;

    gap: 7px;

    flex-shrink: 0;

}


/* =====================================================
   USER
===================================================== */

.travelgo-user {

    display: flex;

    align-items: center;

    gap: 9px;

    padding: 5px 8px;

}


.travelgo-user-icon {

    width: 40px;

    height: 40px;

    border-radius: 50%;

    display: flex;

    align-items: center;

    justify-content: center;

    background: #e0f2fe;

    color: #0284c7;

    font-size: 15px;

}


.travelgo-user-text {

    display: flex;

    flex-direction: column;

    line-height: 1.15;

    min-width: 0;

}


.travelgo-user-text span {

    color: #64748b;

    font-size: 11px;

}


.travelgo-user-text strong {

    color: #1e293b;

    font-size: 13px;

    font-weight: 700;

    max-width: 105px;

    white-space: nowrap;

    overflow: hidden;

    text-overflow: ellipsis;

}


/* =====================================================
   ACTION BUTTONS
===================================================== */

.travelgo-action {

    display: flex;

    align-items: center;

    gap: 7px;

    padding: 9px 10px;

    border-radius: 11px;

    color: #334155;

    font-size: 13px;

    font-weight: 600;

    text-decoration: none;

    white-space: nowrap;

    transition: 0.25s ease;

}


.travelgo-action i {

    color: #0ea5e9;

}


.travelgo-action:hover {

    color: #0284c7;

    background: #f0f9ff;

}


/* =====================================================
   LOGIN
===================================================== */

.travelgo-login {

    color: #475569;

    font-size: 14px;

    font-weight: 600;

    text-decoration: none;

    padding: 9px 8px;

}


.travelgo-login:hover {

    color: #0ea5e9;

}


/* =====================================================
   REGISTER
===================================================== */

.travelgo-register {

    color: #0284c7;

    background: #e0f2fe;

    border-radius: 10px;

    padding: 9px 12px;

    font-size: 13px;

    font-weight: 700;

    text-decoration: none;

}


.travelgo-register:hover {

    background: #bae6fd;

}


/* =====================================================
   LOGOUT
===================================================== */

.travelgo-logout {

    width: 40px;

    height: 40px;

    display: flex;

    align-items: center;

    justify-content: center;

    border-radius: 11px;

    background: #fff1f2;

    color: #ef4444;

    text-decoration: none;

    transition: 0.25s ease;

}


.travelgo-logout:hover {

    background: #fee2e2;

    transform: translateY(-2px);

}


/* =====================================================
   PLAN BUTTON
===================================================== */

.travelgo-plan {

    display: flex;

    align-items: center;

    justify-content: center;

    gap: 9px;

    min-height: 50px;

    padding: 0 19px;

    border-radius: 15px;

    color: white;

    background:
        linear-gradient(
            135deg,
            #0ea5e9,
            #0284c7
        );

    font-size: 14px;

    font-weight: 800;

    text-decoration: none;

    white-space: nowrap;

    box-shadow:
        0 9px 22px rgba(14, 165, 233, 0.22);

    transition: 0.3s ease;

}


.travelgo-plan:hover {

    color: white;

    transform: translateY(-2px);

    box-shadow:
        0 13px 28px rgba(14, 165, 233, 0.30);

}


.travelgo-plan-arrow {

    font-size: 12px;

    transition: 0.3s ease;

}


.travelgo-plan:hover .travelgo-plan-arrow {

    transform: translateX(3px);

}


/* =====================================================
   ACCOUNT DROPDOWN
===================================================== */

.travelgo-account {
    position: relative;
    flex-shrink: 0;
}

.travelgo-account-btn {
    display: flex;
    align-items: center;
    gap: 9px;
    min-height: 50px;
    padding: 4px 9px 4px 4px;
    border: 0;
    border-radius: 14px;
    background: transparent;
    color: #334155;
    cursor: pointer;
    font-family: inherit;
    transition: 0.25s ease;
}

.travelgo-account-btn:hover,
.travelgo-account.open .travelgo-account-btn {
    background: #f0f9ff;
}

.travelgo-account-chevron {
    margin-left: 2px;
    color: #64748b;
    font-size: 10px;
    transition: transform 0.25s ease;
}

.travelgo-account.open .travelgo-account-chevron {
    transform: rotate(180deg);
}

.travelgo-account-menu {
    position: absolute;
    top: calc(100% + 10px);
    right: 0;
    width: 245px;
    padding: 9px;
    border: 1px solid #e2e8f0;
    border-radius: 16px;
    background: #fff;
    box-shadow: 0 18px 45px rgba(15, 23, 42, 0.16);
    opacity: 0;
    visibility: hidden;
    transform: translateY(-7px);
    pointer-events: none;
    transition: 0.2s ease;
    z-index: 9999;
}

.travelgo-account.open .travelgo-account-menu {
    opacity: 1;
    visibility: visible;
    transform: translateY(0);
    pointer-events: auto;
}

.travelgo-account-heading {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 8px 8px 10px;
}

.travelgo-account-heading small {
    display: block;
    color: #94a3b8;
    font-size: 10px;
    margin-bottom: 2px;
}

.travelgo-account-heading strong {
    display: block;
    max-width: 175px;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
    color: #0f172a;
    font-size: 13px;
}

.travelgo-account-avatar {
    width: 38px;
    height: 38px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #e0f2fe;
    color: #0284c7;
    flex-shrink: 0;
}

.travelgo-account-divider {
    height: 1px;
    margin: 5px 2px;
    background: #e2e8f0;
}

.travelgo-dropdown-link {
    display: flex;
    align-items: center;
    gap: 11px;
    width: 100%;
    padding: 11px 10px;
    border-radius: 10px;
    color: #334155;
    text-decoration: none;
    font-size: 13px;
    font-weight: 600;
    transition: 0.2s ease;
}

.travelgo-dropdown-link i {
    width: 18px;
    text-align: center;
    color: #0284c7;
}

.travelgo-dropdown-link:hover {
    background: #f0f9ff;
    color: #0284c7;
}

.travelgo-dropdown-logout {
    color: #ef4444;
}

.travelgo-dropdown-logout i {
    color: #ef4444;
}

.travelgo-dropdown-logout:hover {
    background: #fff1f2;
    color: #dc2626;
}

/* =====================================================
   MOBILE BUTTON
===================================================== */

.travelgo-mobile-btn {

    display: none;

    width: 44px;

    height: 44px;

    border: 0;

    border-radius: 12px;

    background: #f1f5f9;

    color: #334155;

    font-size: 18px;

    cursor: pointer;

    transition: 0.25s ease;

}


.travelgo-mobile-btn:hover {

    background: #e0f2fe;

    color: #0284c7;

}


/* =====================================================
   MOBILE MENU
===================================================== */

.travelgo-mobile-menu {

    display: none;

    width: 100%;

    padding-bottom: 18px;

}


.travelgo-mobile-inner {

    width: 100%;

    padding: 10px;

    background: #f8fafc;

    border: 1px solid #e2e8f0;

    border-radius: 18px;

}


.travelgo-mobile-link {

    width: 100%;

    display: flex;

    align-items: center;

    gap: 13px;

    padding: 13px 14px;

    border-radius: 12px;

    color: #334155;

    font-size: 15px;

    font-weight: 600;

    text-decoration: none;

    transition: 0.2s ease;

}


.travelgo-mobile-link i {

    width: 20px;

    text-align: center;

    color: #0ea5e9;

}


.travelgo-mobile-link:hover {

    background: white;

    color: #0284c7;

}


.travelgo-mobile-danger {

    color: #ef4444 !important;

}


.travelgo-mobile-danger i {

    color: #ef4444 !important;

}


/* =====================================================
   MOBILE PLAN
===================================================== */

.travelgo-mobile-plan {

    width: 100%;

    margin-top: 8px;

    padding: 14px;

    display: flex;

    align-items: center;

    justify-content: center;

    gap: 10px;

    border-radius: 13px;

    color: white;

    background:
        linear-gradient(
            135deg,
            #0ea5e9,
            #0284c7
        );

    font-size: 14px;

    font-weight: 800;

    text-decoration: none;

}


/* =====================================================
   TABLET / SMALL LAPTOP
===================================================== */

@media (max-width: 1535px) {

    .travelgo-desktop-menu,
    .travelgo-right {

        display: none;

    }


    .travelgo-mobile-btn {

        display: flex;

        align-items: center;

        justify-content: center;

    }

}


/* =====================================================
   TABLET
===================================================== */

@media (min-width: 769px) and (max-width: 1535px) {

    .travelgo-container {

        padding-left: 30px;

        padding-right: 30px;

    }

}


/* =====================================================
   MOBILE
===================================================== */

@media (max-width: 768px) {

    .travelgo-container {

        padding-left: 16px;

        padding-right: 16px;

    }


    .travelgo-nav {

        min-height: 72px;

    }


    .travelgo-logo-icon {

        width: 44px;

        height: 44px;

        border-radius: 13px;

        font-size: 18px;

    }


    .travelgo-brand {

        font-size: 22px;

    }


    .travelgo-tagline {

        font-size: 7px;

        letter-spacing: 1.7px;

        margin-top: 5px;

    }

}


/* =====================================================
   VERY SMALL MOBILE
===================================================== */

@media (max-width: 420px) {

    .travelgo-logo {

        gap: 9px;

    }


    .travelgo-logo-icon {

        width: 42px;

        height: 42px;

    }


    .travelgo-brand {

        font-size: 20px;

    }


    .travelgo-tagline {

        font-size: 6px;

        letter-spacing: 1.3px;

    }


    .travelgo-mobile-btn {

        width: 42px;

        height: 42px;

    }

}

</style>


<!-- =====================================================
     MOBILE MENU JAVASCRIPT
===================================================== -->

<script>

document.addEventListener("DOMContentLoaded", function () {

    const menuButton = document.getElementById("mobileMenuBtn");

    const mobileMenu = document.getElementById("mobileMenu");

    const menuIcon = document.getElementById("mobileMenuIcon");


    if (menuButton && mobileMenu && menuIcon) {

        menuButton.addEventListener("click", function () {

            const isOpen =
                mobileMenu.style.display === "block";


            if (isOpen) {

                mobileMenu.style.display = "none";

                menuIcon.classList.remove("fa-xmark");

                menuIcon.classList.add("fa-bars");

            } else {

                mobileMenu.style.display = "block";

                menuIcon.classList.remove("fa-bars");

                menuIcon.classList.add("fa-xmark");

            }

        });


        /* Close mobile menu after clicking link */

        const mobileLinks =
            mobileMenu.querySelectorAll("a");


        mobileLinks.forEach(function (link) {

            link.addEventListener("click", function () {

                mobileMenu.style.display = "none";

                menuIcon.classList.remove("fa-xmark");

                menuIcon.classList.add("fa-bars");

            });

        });

    }

});

</script>

<script>
document.addEventListener("DOMContentLoaded", function () {
    const account = document.getElementById("travelgoAccount");
    const accountBtn = document.getElementById("travelgoAccountBtn");

    if (!account || !accountBtn) return;

    accountBtn.addEventListener("click", function (event) {
        event.stopPropagation();
        const open = account.classList.toggle("open");
        accountBtn.setAttribute("aria-expanded", open ? "true" : "false");
    });

    document.addEventListener("click", function (event) {
        if (!account.contains(event.target)) {
            account.classList.remove("open");
            accountBtn.setAttribute("aria-expanded", "false");
        }
    });

    document.addEventListener("keydown", function (event) {
        if (event.key === "Escape") {
            account.classList.remove("open");
            accountBtn.setAttribute("aria-expanded", "false");
        }
    });
});
</script>
