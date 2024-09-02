<?php

session_start();

include $_SERVER['DOCUMENT_ROOT'] . "/mips/components/db_connect.php";

if (!isset($_SESSION['admin_id'])) {
    header('Location: /mips/admin/login.php');
    exit();
}

if ($_SESSION['admin_status'] == -1) {
    header('Location: /mips/admin/new-password.php');
    exit();
}

$currentPage = basename($_SERVER['PHP_SELF']);

$start = 0;
