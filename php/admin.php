<?php

session_start();

include $_SERVER['DOCUMENT_ROOT'] . "/mips/components/db_connect.php";

if (!isset($_SESSION['admin_id'])) {
    header('Location: /mips/admin/login.php');
    exit();
}

if ($_SESSION['admin_status'] == -1) {
    header('Location: /mips/new-password.php');
    exit();
}

function getStatusLabel($status)
{
    switch ($status) {
        case 0:
            return "Active";
        case -1:
            return "Unactivated";
        default:
            return "Unknown";
    }
}


$currentPage = $_SERVER['REQUEST_URI'];

$start = 0;
