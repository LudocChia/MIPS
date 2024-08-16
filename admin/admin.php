<?php

session_start();

include "../components/db_connect.php";

$currentPage = basename($_SERVER['PHP_SELF']);

if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit();
}

function getAllAdmins($pdo)
{
    $sql = "SELECT * FROM Admin WHERE is_deleted = 0";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$all_admins = getAllAdmins($pdo);

function generateAdminId()
{
    $prefix = "AD";
    $randomString = bin2hex(random_bytes(4));
    return $prefix . $randomString;
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
                $sql = "INSERT INTO Admin (admin_id, admin_name, admin_email, admin_password, admin_type, is_deleted) 
                        VALUES (:adminId, :name, :email, :password, :adminType, 0)";
                $stmt = $pdo->prepare($sql);
                $stmt->bindParam(':adminId', $adminId);
                $stmt->bindParam(':name', $name);
                $stmt->bindParam(':email', $email);
                $stmt->bindParam(':password', $hashedPassword);
                $stmt->bindParam(':adminType', $adminType);
                $stmt->execute();
            } catch (PDOException $e) {
                echo "<script>alert('Database error: " . $e->getMessage() . "');</script>";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bookshop Admin - MIPS</title>
    <link rel="icon" type="image/x-icon" href="../images/Mahans_IPS_icon.png">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
    <script src="https://code.jquery.com/jquery-3.7.1.js" integrity="sha256-eKhayi8LEQwp4NKxN+CfCh+3qOVUtJn3QNZ0TciWLP4=" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="../css/base.css">
    <link rel="stylesheet" href="../css/common.css">
    <link rel="stylesheet" href="../css/admin.css">
</head>

<body>
    <?php include "../components/admin_header.php"; ?>
    <div class="container">
        <?php include "../components/admin_sidebar.php"; ?>
        <main class="admin">
            <div class="wrapper">
                <div class="title">
                    <div class="left">
                        <h1>Mahans Admin</h1>
                    </div>
                    <div class="right">
                        <button class="btn btn-outline" id="open-popup"><i class="bi bi-person-fill-add"></i>Add New Admin</button>
                        <?php
                        try {
                            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                            $countQuery = "SELECT COUNT(*) FROM Admin WHERE is_deleted = 0";
                            $stmt = $pdo->prepare($countQuery);
                            $stmt->execute();
                            $count = $stmt->fetchColumn();

                            echo "<p>Total $count Admin(s)</p>";
                        } catch (PDOException $e) {
                            echo "<script>alert('Database error: " . $e->getMessage() . "');</script>";
                        }
                        ?>
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
                                        <form action="" method="POST" style="display:inline;">
                                            <input type="hidden" name="admin_id" value="<?= htmlspecialchars($admin['admin_id']); ?>">
                                            <input type="hidden" name="delete" value="true">
                                            <button type="submit" class="delete-admin-btn"><i class="bi bi-x-square-fill"></i></button>
                                        </form>
                                        <button type="button" class="edit-admin-btn" data-admin-id="<?= htmlspecialchars($admin['admin_id']); ?>"><i class="bi bi-pencil-square"></i></button>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>
    <dialog id="add-edit-data">
        <h1>Add New Admin</h1>
        <form action="" method="post" enctype="multipart/form-data">
            <div class="input-field">
                <h2>Admin Name<sup>*</sup></h2>
                <input type="text" name="name" value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>" required>
                <p>Please enter the admin's full name.</p>
            </div>
            <div class="input-field">
                <h2>Admin Email<sup>*</sup></h2>
                <input type="email" name="email" value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" required>
                <p>Please enter the admin's email address.</p>
            </div>
            <div class="input-field">
                <h2>Password<sup>*</sup></h2>
                <input type="password" name="password" required>
                <p>Please enter a secure password.</p>
            </div>
            <div class="input-field">
                <h2>Confirm Password<sup>*</sup></h2>
                <input type="password" name="confirm_password" required>
                <p>Please confirm the password.</p>
            </div>
            <div class="controls">
                <button type="button" class="cancel">Cancel</button>
                <button type="reset">Clear</button>
                <button type="submit" name="submit">Publish</button>
            </div>
        </form>
    </dialog>
    <dialog id="delete-confirm-dialog">
        <form method="dialog">
            <h1>Admin will be Deactivated!</h1>
            <label>Are you sure to proceed?</label>
            <div class="controls">
                <button value="cancel" class="cancel">Cancel</button>
                <button value="confirm" class="deactivate">Deactivate</button>
            </div>
        </form>
    </dialog>
    <script src="../javascript/admin.js"></script>
    <script>
        document.querySelectorAll('.edit-admin-btn').forEach(button => {
            button.addEventListener('click', function() {
                const adminId = this.dataset.adminId;

                fetch(`ajax.php?action=get_admin&admin_id=${adminId}`)
                    .then(response => response.json())
                    .then(admin => {
                        if (admin.error) {
                            alert(admin.error);
                        } else {
                            document.querySelector('#add-edit-data [name="name"]').value = admin.admin_name;
                            document.querySelector('#add-edit-data [name="email"]').value = admin.admin_email;

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