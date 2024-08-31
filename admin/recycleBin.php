<?php

// $database_table = "admin";
// $rows_per_page = 12;
include $_SERVER['DOCUMENT_ROOT'] . "/mips/php/admin.php";

$pageTitle = "Admin Management - MIPS";
include $_SERVER['DOCUMENT_ROOT'] . "/mips/components/admin_head.php"; ?>

<body>
    <?php include $_SERVER['DOCUMENT_ROOT'] . "/mips/components/admin_header.php"; ?>
    <div class="container">
        <?php include $_SERVER['DOCUMENT_ROOT'] . "/mips/components/admin_sidebar.php"; ?>
        <main class="announcement">
            <div class="wrapper">
                <div class="title">
                    <div class="left">
                        <h1>Recycle Bin</h1>
                    </div>
                </div>
                <div class="box-container">
                    <div class="box">
                        <div class="image-container">
                            <a href="/mips/admin/recycleBin/announcement.php"><img src="images/" alt="Recycle Bin"></a>
                        </div>
                        <div class="actions">
                            <form action="" method="POST" style="display:inline;" onsubmit="return showDeactivateConfirmDialog(event);">
                                <input type="hidden" name="announcement_id" value="<?= htmlspecialchars($announcement['announcement_id']); ?>">
                                <input type="hidden" name="action" value="deactivate_announcement">
                                <button type="submit" class="delete-announcement-btn"><i class="bi bi-x-square"></i></button>
                            </form>
                            <button type="button" class="edit-announcement-btn" data-announcement-id="<?= htmlspecialchars($announcement['announcement_id']); ?>"><i class="bi bi-pencil-square"></i></button>
                        </div>
                        <div class="txt">
                            <h3>Announcement</h3>
                        </div>
                    </div>
                    <div class="box">
                        <div class="image-container">
                            <a href="/mips/admin/recycleBin/order.php"><img src="images/" alt="Recycle Bin"></a>
                        </div>
                        <div class="actions">
                            <form action="" method="POST" style="display:inline;" onsubmit="return showDeactivateConfirmDialog(event);">
                                <input type="hidden" name="announcement_id" value="<?= htmlspecialchars($announcement['announcement_id']); ?>">
                                <input type="hidden" name="action" value="deactivate_announcement">
                                <button type="submit" class="delete-announcement-btn"><i class="bi bi-x-square"></i></button>
                            </form>
                            <button type="button" class="edit-announcement-btn" data-announcement-id="<?= htmlspecialchars($announcement['announcement_id']); ?>"><i class="bi bi-pencil-square"></i></button>
                        </div>
                        <div class="txt">
                            <h3>Order</h3>
                        </div>
                    </div>
                    <div class="box">
                        <div class="image-container">
                            <a href="/mips/admin/recycleBin/parent.php"><img src="images/" alt="Recycle Bin"></a>
                        </div>
                        <div class="actions">
                            <form action="" method="POST" style="display:inline;" onsubmit="return showDeactivateConfirmDialog(event);">
                                <input type="hidden" name="announcement_id" value="<?= htmlspecialchars($announcement['announcement_id']); ?>">
                                <input type="hidden" name="action" value="deactivate_announcement">
                                <button type="submit" class="delete-announcement-btn"><i class="bi bi-x-square"></i></button>
                            </form>
                            <button type="button" class="edit-announcement-btn" data-announcement-id="<?= htmlspecialchars($announcement['announcement_id']); ?>"><i class="bi bi-pencil-square"></i></button>
                        </div>
                        <div class="txt">
                            <h3>Parent</h3>
                        </div>
                    </div>
                </div>
                <!-- </?php include $_SERVER['DOCUMENT_ROOT'] . "/mips/components/pagination.php"; ?> -->
            </div>
        </main>
    </div>
    <dialog id="add-edit-data">
        <div class="title">
            <div class="left">
                <h1>Add New Admin</h1>
            </div>
            <div class="right">
                <button class="cancel"><i class="bi bi-x-lg"></i></button>
            </div>
        </div>
        <form action="" method="post">
            <input type="hidden" name="admin_id" value="">
            <div class="input-container">
                <h2>Admin Name<sup>*</sup></h2>
                <div class="input-field">
                    <input type="text" name="name" value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>" required>
                </div>
                <p>Please enter the admin's full name.</p>
            </div>
            <div class="input-container">
                <h2>Admin Email<sup>*</sup></h2>
                <div class="input-field">
                    <input type="email" name="email" value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" required>
                </div>
                <p>Please enter the admin's email address.</p>
            </div>
            <div class="input-container">
                <h2>Password<sup>*</sup></h2>
                <div class="input-field">
                    <input type="password" name="password" required>
                </div>
                <p>Please enter a secure password.</p>
            </div>
            <div class="input-container">
                <h2>Confirm Password<sup>*</sup></h2>
                <div class="input-field">
                    <input type="password" name="confirm_password" required>
                </div>
                <p>Please confirm the password.</p>
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
        document.querySelectorAll('.edit-admin-btn').forEach(button => {
            button.addEventListener('click', function() {
                const adminId = this.dataset.adminId;
                fetch(`/mips/admin/ajax.php?action=get_admin`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded'
                        },
                        body: `admin_id=${encodeURIComponent(adminId)}`
                    })
                    .then(response => response.json())
                    .then(admin => {
                        if (admin.error) {
                            alert(admin.error);
                        } else {
                            document.querySelector('#add-edit-data [name="name"]').value = admin.admin_name;
                            document.querySelector('#add-edit-data [name="email"]').value = admin.admin_email;
                            document.querySelector('#add-edit-data [name="admin_id"]').value = admin.admin_id;

                            document.querySelector('#add-edit-data h1').textContent = "Edit Admin";
                            document.getElementById('add-edit-data').showModal();
                        }
                    })
                    .catch(error => {
                        console.error('Error fetching admin data:', error);
                        alert('Failed to load admin data.');
                    });
            });
        });
    </script>
</body>

</html>