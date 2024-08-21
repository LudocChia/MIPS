<?php

session_start();

include $_SERVER['DOCUMENT_ROOT'] . "/mahans/components/db_connect.php";

if (isset($_SESSION['admin_id'])) {
    header('Location: ./login.php');
    exit();
}

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
            $_SESSION['admin_image'] = !empty($admin['admin_image']) ? $admin['admin_image'] : '/mahans/images/default_profile.png';


            header("Location: /mahans/admin");
            exit;
        } else {
            $errorMsg = "Invalid email or password.";
        }
    } catch (PDOException $e) {
        $errorMsg = "Database error: " . $e->getMessage();
    }
}

?>

<?php $pageTitle = "Admin Login Page - MIPS";
include $_SERVER['DOCUMENT_ROOT'] . "/mahans/components/admin_head.php"; ?>

<body>
    <main id="login-form">
        <div class="container">
            <div class="wrapper">
                <div class="title">
                    <img src="/mahans/images/Mahans_IPS_logo.png" alt="Mahans_ISP_Logo">
                </div>
                <?php if (!empty($errorMsg)) : ?>
                    <div class="alert alert-danger">
                        <?php echo htmlspecialchars($errorMsg); ?>
                    </div>
                <?php endif; ?>
                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
                    <div class="input-container">
                        <div class="input-field">
                            <i class="fas fa-user"></i>
                            <input type="text" name="email" placeholder="Email" value="<?php echo htmlspecialchars($email); ?>" required>
                        </div>
                        <p>Please enter your email</p>
                    </div>
                    <div class="input-container">
                        <div class="input-field">
                            <i class="fas fa-lock"></i>
                            <input type="password" name="password" placeholder="Password" required>
                        </div>
                        <p>Please enter your password</p>
                    </div>
                    <div class="pass">
                        <a href="#">Forgot password?</a>
                    </div>
                    <div class="controls">
                        <button type="submit" class="btn">Login</button>
                    </div>
                </form>
            </div>
        </div>
    </main>
</body>

</html>