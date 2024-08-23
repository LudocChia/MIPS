<?php

session_start();

include $_SERVER['DOCUMENT_ROOT'] . "/mips/components/db_connect.php";

if (!isset($_SESSION['admin_id'])) {
    header('Location: /mips/admin/login.php');
    exit();
}

$currentPage = basename($_SERVER['PHP_SELF']);

$start = 0;

function getPageCount($pdo, $rows_per_page, $database_table)
{
    $sql = "SELECT COUNT(*) AS count FROM $database_table WHERE is_deleted = 0";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return ceil($result['count'] / $rows_per_page);
}

$pageCount = getPageCount($pdo, $rows_per_page, $database_table);

if (isset($_GET['page-nr'])) {
    $page = $_GET['page-nr'] - 1;
    $start = $page * $rows_per_page;
}

if (isset($_GET['page-nr'])) {
    $id = $_GET['page-nr'];
} else {
    $id = 1;
}
