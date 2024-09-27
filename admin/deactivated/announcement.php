<?php

$database_table = "Announcement";
$rows_per_page = 5;
include $_SERVER['DOCUMENT_ROOT'] . "/mips/php/admin.php";
include $_SERVER['DOCUMENT_ROOT'] . "/mips/php/activated_pagination.php";

function getDeactivateAnnouncements($pdo, $start, $rows_per_page)
{
    $sql = "SELECT * FROM Announcement WHERE status = 1 LIMIT :start, :rows_per_page;";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':start', $start, PDO::PARAM_INT);
    $stmt->bindParam(':rows_per_page', $rows_per_page, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$all_announcements = getDeactivateAnnouncements($pdo, $start, $rows_per_page);

$pageTitle = "Announcement Management - MIPS";
include $_SERVER['DOCUMENT_ROOT'] . "/mips/components/admin_head.php"; ?>

<body id="<?php echo $id ?>">
    <?php include $_SERVER['DOCUMENT_ROOT'] . "/mips/components/admin_header.php"; ?>
    <div class="container">
        <?php include $_SERVER['DOCUMENT_ROOT'] . "/mips/components/admin_sidebar.php"; ?>
        <main class="gallery">
            <div class="wrapper">
                <div class="title">
                    <div class="left">
                        <h1>Deactivated Announcement</h1>
                    </div>
                    <div class="right">
                        <a href="/mips/admin/deactivated/"><i class="bi bi-arrow-90deg-up"></i>Deactivated Menu</a>
                    </div>
                </div>
                <?php if (!empty($all_announcements)) : ?>
                    <div class="box-container">
                        <?php foreach ($all_announcements as $announcement) : ?>
                            <div class="box" data-announcement-id="<?= htmlspecialchars($announcement['announcement_id']); ?>">
                                <div class="image-container">
                                    <img src="/mips/uploads/announcement/<?php echo htmlspecialchars($announcement['announcement_image_url']); ?>" alt="Image for <?php echo htmlspecialchars($announcement['announcement_title']); ?>">
                                </div>
                                <div class="actions">
                                    <form action="" method="POST" style="display:inline;" onsubmit="return showDeleteConfirmDialog(event);">
                                        <input type="hidden" name="announcement_id" value="<?= htmlspecialchars($announcement['announcement_id']); ?>">
                                        <input type="hidden" name="action" value="delete_announcement">
                                        <button type="submit" class="delete-announcement-btn"><i class="bi bi-x-square"></i></button>
                                    </form>
                                    <form action="" method="POST" style="display:inline;" onsubmit="return showRecoverConfirmDialog(event);">
                                        <input type="hidden" name="announcement_id" value="<?= htmlspecialchars($announcement['announcement_id']); ?>">
                                        <input type="hidden" name="action" value="recover_announcement">
                                        <button type="submit" class="recover-parent-btn"><i class="bi bi-arrow-clockwise"></i></button>
                                    </form>
                                </div>
                                <div class="info-container">
                                    <h3><?php echo htmlspecialchars($announcement['announcement_title']); ?></h3>
                                    <p><?php echo htmlspecialchars($announcement['announcement_message']); ?></p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else : ?>
                    <?php include $_SERVER['DOCUMENT_ROOT'] . "/mips/components/no_data_found.php"; ?>
                <?php endif; ?>
                <?php if (!empty($all_announcements)) : ?>
                    <?php include $_SERVER['DOCUMENT_ROOT'] . "/mips/components/pagination.php"; ?>
                <?php endif; ?>
            </div>
        </main>
    </div>
    <?php include $_SERVER['DOCUMENT_ROOT'] . "/mips/components/confirm_dialog.php"; ?>
    <script src="/mips/javascript/admin.js"></script>
</body>

</html>