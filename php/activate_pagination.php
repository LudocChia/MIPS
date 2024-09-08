<?php

function getPageCount($pdo, $rows_per_page, $database_table)
{
    $sql = "SELECT COUNT(*) AS count FROM $database_table WHERE status = 0";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    $total_rows = $result['count'];
    $total_pages = ceil($total_rows / $rows_per_page);

    return $total_pages;
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

?>

<head>
    <link rel="icon" type="image/x-icon" href="/mips/images/MIPS_icon.png">
</head>

<body data-page-count="<?php echo $pageCount; ?>" data-current-page="<?php echo $currentPage; ?>"></body>