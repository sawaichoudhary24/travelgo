<?php
/**
 * TravelGo user authentication guard.
 * Only the Home page, Login and Register pages are public.
 */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
