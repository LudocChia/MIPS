<?php

$database_table = "announcement";
$rows_per_page = 5;
include $_SERVER['DOCUMENT_ROOT'] . "/mips/php/admin.php";
include $_SERVER['DOCUMENT_ROOT'] . "/mips/php/activate_pagination.php";

function getAnnouncements($pdo, $start, $rows_per_page)
{
    $sql = "SELECT * FROM Announcement WHERE status = 0 LIMIT :start, :rows_per_page;";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':start', $start, PDO::PARAM_INT);
    $stmt->bindParam(':rows_per_page', $rows_per_page, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$all_announcements = getAnnouncements($pdo, $start, $rows_per_page);

function generateAnnouncementID()
{
    return uniqid("AN");
}

function handleFileUpload($file, $existingImagePath = null)
{
    if ($file['error'] === UPLOAD_ERR_NO_FILE) {
        return $existingImagePath;
    }

    $allowedfileExtensions = ['jpg', 'jpeg', 'png'];
    $fileName = $file['name'];
    $fileTmpPath = $file['tmp_name'];
    $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

    if (in_array($fileExtension, $allowedfileExtensions)) {
        $newFileName = uniqid() . '.' . $fileExtension;
        $dest_path = $_SERVER['DOCUMENT_ROOT'] . '/mips/uploads/announcement/' . $newFileName;

        if (move_uploaded_file($fileTmpPath, $dest_path)) {
            if ($existingImagePath && file_exists($_SERVER['DOCUMENT_ROOT'] . '/mips/uploads/announcement/' . $existingImagePath)) {
                unlink($_SERVER['DOCUMENT_ROOT'] . '/mips/uploads/announcement/' . $existingImagePath);
            }
            return $newFileName;
        } else {
            echo "<script>alert('There was an error moving the uploaded file: $fileName');</script>";
            return $existingImagePath;
        }
    } else {
        echo "<script>alert('Upload failed. Allowed file types: " . implode(',', $allowedfileExtensions) . "');</script>";
        return $existingImagePath;
    }
}

if (isset($_POST["submit"])) {
    $announcementId = isset($_POST['announcement_id']) && !empty($_POST['announcement_id']) ? $_POST['announcement_id'] : generateAnnouncementID();
    $announcementTitle = $_POST["title"];
    $announcementMessage = $_POST["message"];

    if (!$announcementId && $_FILES["image"]["error"] === 4) {
        echo "<script>alert('Image Does Not Exist');</script>";
    } else {

        $oldImagePath = null;
        if (isset($_POST['announcement_id']) && !empty($_POST['announcement_id'])) {
            $stmt = $pdo->prepare("SELECT announcement_image_url FROM Announcement WHERE announcement_id = :announcement_id");
            $stmt->bindParam(':announcement_id', $announcementId);
            $stmt->execute();
            $oldImagePath = $stmt->fetchColumn();
        }

        $newImageName = handleFileUpload($_FILES['image'], $oldImagePath);

        if (isset($_POST['announcement_id']) && !empty($_POST['announcement_id'])) {

            $sql = "UPDATE Announcement SET announcement_title = :title, announcement_message = :message, admin_id = :admin_id";

            if ($newImageName) {
                $sql .= ", announcement_image_url = :image_url";
            }

            $sql .= " WHERE announcement_id = :announcement_id";

            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':title', $announcementTitle);
            $stmt->bindParam(':message', $announcementMessage);
            $stmt->bindParam(':announcement_id', $announcementId);
            $stmt->bindParam(':admin_id', $_SESSION['admin_id']);


            if ($newImageName) {
                $stmt->bindParam(':image_url', $newImageName);
            }
        } else {
            $sql = "INSERT INTO Announcement (announcement_id, announcement_title, announcement_message, announcement_image_url, admin_id, status) VALUES (:announcement_id, :title, :message, :image_url, :admin_id, 0)";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':announcement_id', $announcementId);
            $stmt->bindParam(':title', $announcementTitle);
            $stmt->bindParam(':message', $announcementMessage);
            $stmt->bindParam(':image_url', $newImageName);
            $stmt->bindParam(':admin_id', $_SESSION['admin_id']);
        }

        include $_SERVER['DOCUMENT_ROOT'] . "/mips/php/refresh_page.php";
    }
}

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
                        <h1>Announcement Management</h1>
                    </div>
                    <div class="right">
                        <button id="open-popup" class="btn btn-outline-primary"><i class="bi bi-plus-circle"></i>Add New Announcement</button>
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
                                    <form action="" method="POST" style="display:inline;" onsubmit="return showDeactivateConfirmDialog(event);">
                                        <input type="hidden" name="announcement_id" value="<?= htmlspecialchars($announcement['announcement_id']); ?>">
                                        <input type="hidden" name="action" value="deactivate_announcement">
                                        <button type="submit" class="delete-announcement-btn"><i class="bi bi-x-square"></i></button>
                                    </form>
                                    <button type="button" class="edit-announcement-btn" data-announcement-id="<?= htmlspecialchars($announcement['announcement_id']); ?>"><i class="bi bi-pencil-square"></i></button>
                                </div>
                                <div class="info-container">
                                    <div class="name-field">
                                        <h3><?php echo htmlspecialchars($announcement['announcement_title']); ?></h3>
                                    </div>
                                    <h4>Updated at: <?php echo htmlspecialchars($announcement['updated_at']); ?></h3>
                                        <h4>Created at: <?php echo htmlspecialchars($announcement['created_at']); ?></h3>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else : ?>
                    <?php include $_SERVER['DOCUMENT_ROOT'] . "/mips/components/no_data_found.php"; ?>
                <?php endif; ?>
            </div>
            <?php if (!empty($all_classes)) : ?>
                <?php include $_SERVER['DOCUMENT_ROOT'] . "/mips/components/pagination.php"; ?>
            <?php endif; ?>
        </main>
    </div>
    <dialog id="add-edit-data">
        <form method="post" enctype="multipart/form-data">
            <div class="title">
                <div class="left">
                    <h1>Add New Announcement</h1>
                </div>
                <div class="right">
                    <div class="actions"><button class="cancel"><i class="bi bi-x-circle"></i></button></div>
                </div>
            </div>
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
            <div class="controls">
                <button type="button" class="cancel">Cancel</button>
                <button type="reset" class="delete">Clear</button>
                <button type="submit" class="confirm" name="submit">Publish</button>
            </div>
        </form>
    </dialog>
    <?php include $_SERVER['DOCUMENT_ROOT'] . "/mips/components/confirm_dialog.php"; ?>
    <script src="/mips/javascript/common.js"></script>
    <script src="/mips/javascript/admin.js"></script>
    <script>
        document.querySelectorAll('.edit-announcement-btn').forEach(button => {
            button.addEventListener('click', function() {
                document.querySelector('.confirm').textContent = "Publish";
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

        document.querySelectorAll('#add-edit-data .cancel').forEach(button => {
            button.addEventListener('click', function() {
                document.querySelector('#add-edit-data h1').textContent = "Add New Announcement";
            });
        });
    </script>
</body>

</html>