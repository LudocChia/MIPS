<?php

include "./components/db_connect.php";

$errorMsg = '';
$email = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    $sqlParent = "SELECT * FROM Parent WHERE parent_email = :email AND is_deleted = 0";
    $stmtParent = $pdo->prepare($sqlParent);
    $stmtParent->bindParam(':email', $email);

    $sqlStudent = "SELECT * FROM Student WHERE student_email = :email AND is_deleted = 0";
    $stmtStudent = $pdo->prepare($sqlStudent);
    $stmtStudent->bindParam(':email', $email);

    try {
        $stmtParent->execute();
        $parent = $stmtParent->fetch(PDO::FETCH_ASSOC);

        if ($parent && password_verify($password, $parent['parent_password'])) {
            // Parent login
            $_SESSION['user_type'] = 'parent';
            $_SESSION['user_id'] = $parent['parent_id'];
            $_SESSION['user_name'] = $parent['parent_name'];
            $_SESSION['user_email'] = $parent['parent_email'];
            $_SESSION['user_image'] = !empty($parent['parent_image']) ? $parent['parent_image'] : './images/default_profile.png';

            header("Location: index.php");
            exit;
        }

        $stmtStudent->execute();
        $student = $stmtStudent->fetch(PDO::FETCH_ASSOC);

        if ($student && password_verify($password, $student['student_password'])) {
            $_SESSION['user_type'] = 'student';
            $_SESSION['user_id'] = $student['student_id'];
            $_SESSION['user_name'] = $student['student_name'];
            $_SESSION['user_email'] = $student['student_email'];
            $_SESSION['user_image'] = !empty($student['student_image']) ? $student['student_image'] : './images/default_profile.png';

            header("Location: index.php");
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
        <img src="./images/Mahans_internation_primary_school_logo.png" alt="Mahans_ISP_Logo">
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
            <button type="button" class="cancel">Cancel</button>
            <button type="submit" class="btn">Login</button>
        </div>
    </form>
</dialog>

<script>
    document.querySelector("#login-btn").addEventListener("click", function() {
        scrollPosition = window.pageYOffset;
        scrollPosition = window.pageYOffset;

        document.body.style.overflowY = 'hidden';
        document.body.style.paddingRight = '15px';
        document.body.style.backgroundColor = 'white';

        document.getElementById('login-form').showModal();
    });

    document.querySelector('#login-form .cancel').addEventListener('click', function() {
        const dialog = document.getElementById('login-form');
        dialog.close();
        dialog.querySelector('form').reset();

        document.body.style.overflowY = '';
        document.body.style.paddingRight = '';
        document.body.style.backgroundColor = '';

        window.scrollTo(0, scrollPosition);
    });
</script>