<?php

include "./components/db_connect.php";

$product_id = $_GET['pid'] ?? null;

$errorMsg = '';
$email = '';

if (isset($_POST["login"])) {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    $sqlParent = "SELECT * FROM Parent WHERE parent_email = :email AND is_deleted = 0";
    $stmtParent = $pdo->prepare($sqlParent);
    $stmtParent->bindParam(':email', $email);

    try {
        $stmtParent->execute();
        $parent = $stmtParent->fetch(PDO::FETCH_ASSOC);

        if ($parent && password_verify($password, $parent['parent_password'])) {
            $_SESSION['user_type'] = 'parent';
            $_SESSION['user_id'] = $parent['parent_id'];
            $_SESSION['user_name'] = $parent['parent_name'];
            $_SESSION['user_email'] = $parent['parent_email'];
            $_SESSION['user_image'] = !empty($parent['parent_image']) ? $parent['parent_image'] : './images/default_profile.png';

            if ($product_id) {
                header("Location: item.php?pid=" . $product_id);
                exit;
            }

            header("Location: " . $_SERVER['PHP_SELF']);
            exit;
        }

        $errorMsg = "Invalid email or password.";
    } catch (PDOException $e) {
        $errorMsg = "Database error: " . $e->getMessage();
    }
}

?>

<dialog id="login-form">
    <div class="title">
        <img src="./images/Mahans_IPS_icon.png" alt="Mahans_ISP_Logo">
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
        <div class="input-container controls">
            <button type="button" class="btn btn-outline-gray cancel">Cancel</button>
            <button type="submit" name="login" class="btn">Login</button>
        </div>
    </form>
</dialog>