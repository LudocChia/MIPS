<?php

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
