<?php

session_start();

include $_SERVER['DOCUMENT_ROOT'] . "/mips/components/db_connect.php";
include $_SERVER['DOCUMENT_ROOT'] . "/mips/components/customer_login.php";

if (isset($_SESSION['user_status']) && $_SESSION['user_status'] == -1) {
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
