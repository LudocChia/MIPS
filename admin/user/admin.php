<?php

$database_table = "admin";
$rows_per_page = 12;
include $_SERVER['DOCUMENT_ROOT'] . "/mips/php/admin.php";
include $_SERVER['DOCUMENT_ROOT'] . "/mips/php/activate_pagination.php";

function getAllAdmins($pdo, $start, $rows_per_page)
{
    $sql = "SELECT * FROM Admin WHERE status IN (-1, 0) ORDER BY created_at DESC LIMIT :start, :rows_per_page";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':start', $start, PDO::PARAM_INT);
    $stmt->bindParam(':rows_per_page', $rows_per_page, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$all_admins = getAllAdmins($pdo, $start, $rows_per_page);

function generateAdminId()
{
    return uniqid("AD");
}

$pageTitle = "Admin Management - MIPS";
include $_SERVER['DOCUMENT_ROOT'] . "/mips/components/admin_head.php";
?>

<body id="<?php echo $id ?>">
    <?php include $_SERVER['DOCUMENT_ROOT'] . "/mips/components/admin_header.php"; ?>
    <div class="container">
        <?php include $_SERVER['DOCUMENT_ROOT'] . "/mips/components/admin_sidebar.php"; ?>
        <main class="admin">
            <div class="wrapper">
                <div class="title">
                    <div class="left">
                        <h1>Admin Management</h1>
                    </div>
                    <div class="right">
                        <button class="btn btn-outline-primary" id="open-popup"><i class="bi bi-person-fill-add"></i>Add New Admin</button>
                    </div>
                </div>
                <div class="table-body">
                    <?php if (!empty($all_admins)) : ?>
                        <table>
                            <thead>
                                <tr>
                                    <th>
                                        <h3>Admin ID</h3>
                                    </th>
                                    <th>
                                        <h3>Admin Name</h3>
                                    </th>
                                    <th>
                                        <h3>Admin Email</h3>
                                    </th>
                                    <th>
                                        <h3>Admin Register Date</h3>
                                    </th>
                                    <th>
                                        <h3>Status</h3>
                                    </th>
                                    <th>
                                        <h3>Actions</h3>
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($all_admins as $admin) { ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($admin['admin_id']); ?></td>
                                        <td><?php echo htmlspecialchars($admin['admin_name']); ?></td>
                                        <td><?php echo htmlspecialchars($admin['admin_email']); ?></td>
                                        <td><?php echo htmlspecialchars($admin['created_at']); ?></td>
                                        <td><?php echo getStatusLabel($admin['status']); ?></td>
                                        <td>
                                            <div class="actions">
                                                <form action="" method="POST" style="display:inline;" onsubmit="return showDeactivateConfirmDialog(event);">
                                                    <input type="hidden" name="admin_id" value="<?= htmlspecialchars($admin['admin_id']); ?>">
                                                    <input type="hidden" name="action" value="deactivate_admin">
                                                    <button type="submit" class="delete-admin-btn"><i class="bi bi-x-square"></i></button>
                                                </form>
                                                <button type="button" class="edit-admin-btn" data-admin-id="<?= htmlspecialchars($admin['admin_id']); ?>"><i class="bi bi-pencil-square"></i></button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    <?php else : ?>
                        <?php include $_SERVER['DOCUMENT_ROOT'] . "/mips/components/no_data_found.php"; ?>
                    <?php endif; ?>
                </div>
                <?php if (!empty($all_admins)) : ?>
                    <?php include $_SERVER['DOCUMENT_ROOT'] . "/mips/components/pagination.php"; ?>
                <?php endif; ?>
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
        <div id="alert-container"></div>
        <form id="admin-form-ajax" method="post">
            <input type="hidden" name="admin_id" value="">
            <div class="input-container">
                <h2>Admin Name<sup>*</sup></h2>
                <div class="input-field">
                    <input type="text" name="name" value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>" required>
                </div>
                <p>Please enter the admin's full name</p>
            </div>
            <div class="input-container">
                <h2>Admin Email<sup>*</sup></h2>
                <div class="input-field">
                    <input type="email" name="email" value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" required>
                </div>
                <p>Please enter the admin's email address</p>
            </div>
            <div class="input-container">
                <h2>Password<sup>*</sup></h2>
                <div class="input-field">
                    <input type="password" name="password" required>
                </div>
                <p>Please enter a secure password</p>
            </div>
            <div class="input-container">
                <h2>Confirm Password<sup>*</sup></h2>
                <div class="input-field">
                    <input type="password" name="confirm_password" required>
                </div>
                <p>Please confirm the password</p>
            </div>
            <div class="controls">
                <button type="button" class="cancel">Cancel</button>
                <button type="reset">Clear</button>
                <button type="submit" name="submit">Publish</button>
            </div>
        </form>
    </dialog>
    <?php include $_SERVER['DOCUMENT_ROOT'] . "/mips/components/confirm_dialog.php"; ?>
    <script src="/mips/javascript/common.js"></script>
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

                            document.querySelector('#add-edit-data [name="password"]').removeAttribute('required');
                            document.querySelector('#add-edit-data [name="confirm_password"]').removeAttribute('required');

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

        const adminForm = document.getElementById('admin-form-ajax');

        adminForm.addEventListener('submit', function(e) {
            e.preventDefault();

            const name = document.querySelector('#add-edit-data [name="name"]').value;
            const email = document.querySelector('#add-edit-data [name="email"]').value;
            const password = document.querySelector('#add-edit-data [name="password"]').value;
            const confirmPassword = document.querySelector('#add-edit-data [name="confirm_password"]').value;

            if (password !== confirmPassword) {
                showAlert('Passwords do not match!');
                return;
            }

            fetch('/mips/admin/ajax.php?action=save_admin', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: new URLSearchParams({
                        name: name,
                        email: email,
                        password: password,
                        confirm_password: confirmPassword,
                        admin_id: document.querySelector('#add-edit-data [name="admin_id"]').value
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        window.location.reload();
                    } else {
                        showAlert(data.error || 'An error occurred while saving the admin.');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showAlert('An unexpected error occurred.');
                });
        });

        function showAlert(message) {
            const alertHtml = `<div class="mini-alert">${message}</div>`;
            const alertContainer = document.getElementById('alert-container');
            alertContainer.innerHTML = alertHtml;

            setTimeout(function() {
                const alertElement = document.querySelector('.mini-alert');
                if (alertElement) {
                    alertElement.style.opacity = '0';
                    setTimeout(() => alertElement.remove(), 600);
                }
            }, 3000);
        }

        document.querySelectorAll('#add-edit-data .cancel').forEach(button => {
            button.addEventListener('click', function() {
                document.querySelector('#add-edit-data h1').textContent = "Add New Admin";
            });
        });
    </script>
</body>

</html>