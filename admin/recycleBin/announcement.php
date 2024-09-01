<?php

$database_table = "Announcement";
$rows_per_page = 5;
include $_SERVER['DOCUMENT_ROOT'] . "/mips/php/admin.php";
include $_SERVER['DOCUMENT_ROOT'] . "/mips/php/activate_pagination.php";

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
        <main class="announcement">
            <div class="wrapper">
                <div class="title">
                    <div class="left">
                        <h1>Announcement Recycle Bin</h1>
                    </div>
                    <div class="right">
                        <a href="/mips/admin/recycleBin.php"><i class="bi bi-arrow-return-left"></i>Recycle Bin Menu</a>
                    </div>
                </div>
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
                            <div class="txt">
                                <h3><?php echo htmlspecialchars($announcement['announcement_title']); ?></h3>
                                <p><?php echo htmlspecialchars($announcement['announcement_message']); ?></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <?php include $_SERVER['DOCUMENT_ROOT'] . "/mips/components/pagination.php"; ?>
            </div>
        </main>
    </div>
    <dialog id="add-edit-data">
        <div class="title">
            <div class="left">
                <h1>Add New Announcement</h1>
            </div>
            <div class="right">
                <button class="cancel"><i class="bi bi-x-circle"></i></button>
            </div>
        </div>
        <form action="" method="post" enctype="multipart/form-data">
            <input type="hidden" name="announcement_id" value="">
            <div class="input-container">
                <h2>Announcement Title<sup>*</sup></h2>
                <div class="input-field">
                    <input type="text" name="title" value="<?php echo isset($_POST['title']) ? htmlspecialchars($_POST['title']) : ''; ?>">
                </div>
                <p>Please enter the announcement title.</p>
            </div>
            <div class="input-container">
                <h2>Announcement Message</h2>
                <div class="input-field">
                    <textarea name="message"><?php echo isset($_POST['message']) ? htmlspecialchars($_POST['message']) : ''; ?></textarea>
                </div>
                <p>Please enter the announcement message.</p>
            </div>
            <div class="input-container">
                <h2>Announcement Image<sup>*</sup></h2>
                <div class="input-field">
                    <input type="file" name="image" id="image" accept=".jpg, .jpeg, .png">
                </div>
                <p>Please upload an image for the announcement.</p>
            </div>
            <div class="input-container controls">
                <button type="button" class="cancel">Cancel</button>
                <button type="reset">Clear</button>
                <button type="submit" name="submit">Publish</button>
            </div>
        </form>
    </dialog>
    <?php include $_SERVER['DOCUMENT_ROOT'] . "/mips/components/confirm_dialog.php"; ?>
    <script src="/mips/javascript/admin.js"></script>
    <script>
        document.querySelectorAll('.edit-announcement-btn').forEach(button => {
            button.addEventListener('click', function() {
                const announcementId = this.dataset.announcementId;
                fetch(`/mips/admin/ajax.php?action=get_announcement&announcement_id=${announcementId}`)
                    .then(response => response.json())
                    .then(announcement => {
                        if (announcement.error) {
                            alert(announcement.error);
                        } else {
                            document.querySelector('#add-edit-data [name="announcement_id"]').value = announcement.announcement_id;
                            document.querySelector('#add-edit-data [name="title"]').value = announcement.announcement_title;
                            document.querySelector('#add-edit-data [name="message"]').value = announcement.announcement_message;
                            document.querySelector('#add-edit-data h1').textContent = "Edit Announcement";
                            document.getElementById('add-edit-data').showModal();
                        }
                    })
                    .catch(error => {
                        console.error('Error fetching announcement data:', error);
                        alert('Failed to load announcement data.');
                    });
            });
        });
    </script>
</body>

</html>