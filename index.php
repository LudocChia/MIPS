<?php

$pageTitle = "Home - MIPS";
$currentPage = basename($_SERVER['PHP_SELF']);
include $_SERVER['DOCUMENT_ROOT'] . "/mips/php/customer.php";

function getAnnouncements($pdo)
{
    $sql = "SELECT * FROM Announcement WHERE status = 0";
    $stml = $pdo->prepare($sql);
    $stml->execute();
    return $stml->fetchAll(PDO::FETCH_ASSOC);
}

$announcements = getAnnouncements($pdo);
?>

<body>
    <?php include $_SERVER['DOCUMENT_ROOT'] . "/mips/components/customer_header.php"; ?>
    <section class="banner">
        <div class="container">
            <div class="wrapper">
                <div class="slider">
                    <div class="list">
                        <?php foreach ($announcements as $announcement) { ?>
                            <div class='item'>
                                <img src="/mips/uploads/announcement/<?php echo htmlspecialchars($announcement['announcement_image_url']); ?>" alt="<?php echo htmlspecialchars($announcement['announcement_title']); ?>" />
                            </div>
                        <?php } ?>
                    </div>
                    <div class="buttons">
                        <?php if (count($announcements) > 1) { ?>
                            <button id="prev"><i class="bi bi-chevron-left"></i></button>
                            <button id="next"><i class="bi bi-chevron-right"></i></button>
                        <?php } ?>
                    </div>
                    <ul class="dots">
                        <li class="active"></li>
                        <?php for ($i = 1; $i < count($announcements); $i++) { ?>
                            <li></li>
                        <?php } ?>
                    </ul>
                </div>
            </div>
        </div>
    </section>
    <?php include $_SERVER['DOCUMENT_ROOT'] . "/mips/components/customer_footer.php"; ?>
    <script src="/mips/javascript/common.js"></script>
    <script src="/mips/javascript/customer.js"></script>
</body>

</html>