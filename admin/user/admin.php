<?php

$database_table = "admin";
$rows_per_page = 12;
include $_SERVER['DOCUMENT_ROOT'] . "/mips/php/admin.php";

function getAllAdmins($pdo, $start, $rows_per_page)
{
    $sql = "SELECT * FROM Admin WHERE status = 0 LIMIT :start, :rows_per_page";
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

if (isset($_POST["submit"])) {
    $name = $_POST["name"];
    $email = $_POST["email"];
    $password = $_POST["password"];
    $confirmPassword = $_POST["confirm_password"];
    $adminType = "admin";
    $adminId = generateAdminId();

    if (empty($name) || empty($email) || empty($password) || empty($confirmPassword)) {
        echo "<script>alert('Please fill in all required fields.');</script>";
    } elseif ($password !== $confirmPassword) {
        echo "<script>alert('Passwords do not match.');</script>";
    } else {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            echo "<script>alert('Invalid email format.');</script>";
        } else {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            try {
                $sql = "INSERT INTO Admin (admin_id, admin_name, admin_email, admin_password, admin_type, status) 
                        VALUES (:adminId, :name, :email, :password, :adminType, 0)";
                $stmt = $pdo->prepare($sql);
                $stmt->bindParam(':adminId', $adminId);
                $stmt->bindParam(':name', $name);
                $stmt->bindParam(':email', $email);
                $stmt->bindParam(':password', $hashedPassword);
                $stmt->bindParam(':adminType', $adminType);
                $stmt->execute();
                header('Location:' . $_SERVER['PHP_SELF']);
                exit();
            } catch (PDOException $e) {
                echo "<script>alert('Database error: " . $e->getMessage() . "');</script>";
            }
        }
    }
}

$pageTitle = "Admin Management - MIPS";
include $_SERVER['DOCUMENT_ROOT'] . "/mips/components/admin_head.php"; ?>

<body>
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
                                    <td><?php echo htmlspecialchars($admin['register_date']); ?></td>
                                    <td>
                                        <form action="" method="POST" style="display:inline;" onsubmit="return showDeactivateConfirmDialog(event);">
                                            <input type="hidden" name="admin_id" value="<?= htmlspecialchars($admin['admin_id']); ?>">
                                            <input type="hidden" name="action" value="deactivate_admin">
                                            <button type="submit" class="delete-admin-btn"><i class="bi bi-x-square"></i></button>
                                        </form>
                                        <button type="button" class="edit-admin-btn" data-admin-id="<?= htmlspecialchars($admin['admin_id']); ?>"><i class="bi bi-pencil-square"></i></button>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
                <?php include $_SERVER['DOCUMENT_ROOT'] . "/mips/components/pagination.php"; ?>
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