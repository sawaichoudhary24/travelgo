<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


/* =========================
   ADMIN LOGIN CHECK
========================= */

if (
    !isset($_SESSION['user_id']) ||
    !isset($_SESSION['user_role']) ||
    $_SESSION['user_role'] !== 'admin'
) {

    header("Location: index.php");
    exit;
}