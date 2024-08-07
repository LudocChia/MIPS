<?php

session_start();

include "../components/db_connect.php";

$errorMsg = '';
$email = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    $sql = "SELECT * FROM Admin WHERE admin_email = :email AND is_deleted = 0";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':email', $email);

    try {
        $stmt->execute();

        $admin = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($admin && password_verify($password, $admin['admin_password'])) {
            $_SESSION['admin_id'] = $admin['admin_id'];
            $_SESSION['admin_name'] = $admin['admin_name'];
            $_SESSION['admin_type'] = $admin['admin_type'];
            $_SESSION['admin_email'] = $admin['admin_email'];
            $_SESSION['admin_image'] = $admin['admin_image'];


            header("Location: index.php");
            exit;
        } else {
            $errorMsg = "Invalid email or password.";
        }
    } catch (PDOException $e) {
        $errorMsg = "Database error: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login Page - Mahans School</title>
    <link rel="icon" type="image/x-icon" href="../images/Mahans_internation_primary_school_logo.png">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
    <script src="https://code.jquery.com/jquery-3.7.1.js" integrity="sha256-eKhayi8LEQwp4NKxN+CfCh+3qOVUtJn3QNZ0TciWLP4=" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="../css/base.css">
    <link rel="stylesheet" href="../css/admin.css">
</head>

<body>
    <main class="login">
        <div class="container">
            <div class="wrapper">
                <div class="title">
                    <img src="../images/Mahans_IPS_logo.png" alt="Mahans_ISP_Logo">
                </div>
                <?php if (!empty($errorMsg)) : ?>
                    <div class="alert alert-danger">
                        <?php echo htmlspecialchars($errorMsg); ?>
                    </div>
                <?php endif; ?>
                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
                    <div class="input-field">
                        <i class="fas fa-user"></i>
                        <input type="text" name="email" placeholder="Email" value="<?php echo htmlspecialchars($email); ?>" required>
                    </div>
                    <div class="input-field">
                        <i class="fas fa-lock"></i>
                        <input type="password" name="password" placeholder="Password" required>
                    </div>
                    <div class="pass">
                        <a href="#">Forgot password?</a>
                    </div>
                    <div class="input-field controls">
                        <input type="submit" value="Login">
                    </div>
                </form>
            </div>
        </div>
    </main>
</body>

</html>