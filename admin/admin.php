<?php
include "../components/db_connect.php";

// Function to fetch all existing admins
function getAllAdmins($pdo)
{
    $sql = "SELECT * FROM Admin WHERE is_deleted = 0";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function generateAdminId()
{
    $prefix = "admin_";
    $randomString = bin2hex(random_bytes(4));
    return $prefix . $randomString;
}

$all_admins = getAllAdmins($pdo);

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
        // Validate email
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            echo "<script>alert('Invalid email format.');</script>";
        } else {
            // Hash the password
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            try {
                // Insert new admin into the database
                $sql = "INSERT INTO Admin (admin_id, admin_name, admin_email, admin_password, admin_type, is_deleted) 
                        VALUES (:adminId, :name, :email, :password, :adminType, 0)";
                $stmt = $pdo->prepare($sql);
                $stmt->bindParam(':adminId', $adminId);
                $stmt->bindParam(':name', $name);
                $stmt->bindParam(':email', $email);
                $stmt->bindParam(':password', $hashedPassword);
                $stmt->bindParam(':adminType', $adminType);
                $stmt->execute();

                echo "<script>alert('Admin Successfully Added');document.location.href ='admin.php';</script>";
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
    <title>Bookshop Admin - Mahans School</title>
    <link rel="icon" type="image/x-icon" href="../images/Mahans_internation_primary_school_logo.png">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
    <script src="https://code.jquery.com/jquery-3.7.1.js" integrity="sha256-eKhayi8LEQwp4NKxN+CfCh+3qOVUtJn3QNZ0TciWLP4=" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="../css/admin.css">
</head>

<body>
    <?php include "../components/admin_header.php"; ?>
    <div class="container">
        <aside>
            <button id="close-btn">
                <i class="bi bi-x"></i>
            </button>
            <div class="sidebar">
                <ul>
                    <li>
                        <a href="index.php"><i class="bi bi-grid-1x2-fill"></i>
                            <h4>Dashboard</h4>
                        </a>
                    </li>
                    <li>
                        <a href="#" class="bookshop-btn">
                            <i class="bi bi-shop-window"></i>
                            <h4>Bookshop</h4>
                            <i class="bi bi-chevron-down first"></i>
                        </a>
                        <ul class="bookshop-show">
                            <li><a href="mainCategory.php"><i class="bi bi-tags-fill"></i>
                                    <h4>Main Category</h4>
                                </a>
                            </li>
                            <li><a href="subcategory.php"><i class="bi bi-tag-fill"></i>
                                    <h4>Subcategory</h4>
                                </a>
                            </li>
                            <li><a href="product_size.php"><i class="bi bi-aspect-ratio-fill"></i>
                                    <h4>Product Size</h4>
                                </a>
                            </li>
                            <li><a href="product.php"><i class="bi bi-box-seam-fill"></i>
                                    <h4>All Product</h4>
                                </a>
                            </li>
                        </ul>
                    </li>
                    <li>
                        <a href="#" class="user-btn">
                            <i class="bi bi-person-fill"></i>
                            <h4>User Type</h4>
                            <i class="bi bi-chevron-down second"></i>
                        </a>
                        <ul class="user-show">
                            <li><a href="admin.php" class="active"><i class="bi bi-person-fill-gear"></i>
                                    <h4>All Admin</h4>
                                </a>
                            </li>
                            <li><a href="teacher.php"><i class="bi bi-mortarboard-fill"></i>
                                    <h4>All Teacher</h4>
                                </a>
                            </li>
                            <li>
                                <a href="parent.php"><i class="bi bi-people-fill"></i>
                                    <h4>All Parent</h4>
                                </a>
                            </li>
                        </ul>
                    </li>
                    <li>
                        <a href="order.php">
                            <i class="bi bi-receipt"></i>
                            <h4>Order</h4>
                        </a>
                    </li>
                </ul>
            </div>
        </aside>
        <!-- END OF ASIDE -->
        <main class="category">
            <div class="wrapper">
                <div class="title">
                    <div class="left">
                        <h1>Mahans Admin</h1>
                    </div>
                    <div class="right">
                        <button id="open-popup"><i class="bi bi-person-fill-add"></i> Add New Admin</button>
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
                <div class="box-container">
                    <?php foreach ($all_admins as $admin) : ?>
                        <div class="box">
                            <h3><?php echo htmlspecialchars($admin['admin_name']); ?></h3>
                            <a href="#">
                                <div class="image-container">
                                    <img src="../uploads/<?php echo htmlspecialchars($admin['admin_image']); ?>" alt="Image for <?php echo htmlspecialchars($admin['admin_name']); ?>">
                                </div>
                            </a>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </main>
    </div>
    <dialog>
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
                <button type="button" class="close-btn">Cancel</button>
                <button type="reset">Clear</button>
                <button type="submit" name="submit">Publish</button>
            </div>
        </form>
    </dialog>
    <script src="../javascript/admin.js"></script>
</body>

</html>