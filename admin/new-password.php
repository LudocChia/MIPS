<?php

session_start();

include $_SERVER['DOCUMENT_ROOT'] . "/mips/components/db_connect.php";

if (!isset($_SESSION['admin_id'])) {
    header('Location: /mips/admin/login.php');
    exit();
}

if (isset($_POST['submit'])) {
    $newPassword = $_POST['new_password'];
    $confirmPassword = $_POST['confirm_password'];

    if ($newPassword === $confirmPassword) {
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        $sql = "UPDATE Admin SET admin_password = :new_password, status = 0 WHERE admin_id = :admin_id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':new_password', $hashedPassword);
        $stmt->bindParam(':admin_id', $_SESSION['admin_id']);
        $stmt->execute();

        $_SESSION['admin_status'] = 0;

        header('Location: /mips/admin/');
        exit();
    } else {
        $error = "Passwords do not match.";
    }
}

$pageTitle = "Set New Password - MIPS";
include $_SERVER['DOCUMENT_ROOT'] . "/mips/components/admin_head.php";
?>

<body>
    <main class="set-new-password-form">
        <div class="container">
            <div class="wrapper">
                <div class="logo-container">
                    <img src="/mips/images/MIPS_logo.png" alt="MIPS_Logo">
                </div>
                <div class="title">
                    <h1>Set New Password</h1>
                </div>
                <form method="POST">
                    <div class="input-container">
                        <div class="input-field">
                            <i class="fas fa-lock"></i>
                            <input type="password" id="new_password" name="new_password" placeholder="New Password" required>
                        </div>
                        <p>Please enter your new password</p>
                    </div>
                    <div class="input-container">
                        <div class="input-field">
                            <i class="fas fa-lock"></i>
                            <input type="password" id="confirm_password" name="confirm_password" placeholder="Confirm Password" required>
                        </div>
                        <p>Please confirm your new password</p>
                    </div>
                    <div class="controls">
                        <button type="submit" name="submit" class="btn">Update Password</button>
                    </div>
                </form>
            </div>
        </div>
    </main>
</body>

</html>