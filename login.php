<?php

session_start();
include $_SERVER['DOCUMENT_ROOT'] . "/mips/components/db_connect.php";
$product_id = $_GET['pid'] ?? null;
$email = '';

?>

<head>
    <script src="https://code.jquery.com/jquery-3.7.1.js" integrity="sha256-eKhayi8LEQwp4NKxN+CfCh+3qOVUtJn3QNZ0TciWLP4=" crossorigin="anonymous"></script>
    <style>
        .login-alert {
            padding: 10px;
            background-color: rgba(128, 128, 128, 0.9);
            color: white;
            opacity: 1;
            transition: opacity 0.6s;
            position: absolute;
            top: 20px;
            right: 20px;
            z-index: 9999;
        }
    </style>
</head>

<div id="login-form">
    <div class="logo-container">
        <img src="/mips/images/MIPS_icon.png" alt="MIPS_Logo">
    </div>
    <div id="alert-container"></div>
    <form id="login-form-ajax" method="POST">
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
            <button type="button" class="btn btn-outline-gray cancel">Cancel</button>
            <button type="submit" name="login" class="btn btn-outline-primary login">Login</button>
        </div>
    </form>
</div>
<script>
    $(document).ready(function() {
        $('#login-form-ajax').on('submit', function(e) {
            e.preventDefault();

            const email = $('input[name="email"]').val();
            const password = $('input[name="password"]').val();

            $.ajax({
                type: 'POST',
                url: '/mips/ajax.php?action=login',
                data: $.param({
                    email: email,
                    password: password
                }),
                contentType: 'application/x-www-form-urlencoded',
                success: function(response) {
                    const result = JSON.parse(response);
                    if (result.success) {
                        window.location.href = result.redirect;
                    } else {
                        showAlert(result.error);
                    }
                },
                error: function() {
                    showAlert('An error occurred while processing the request.');
                }
            });
        });

        function showAlert(message) {
            const alertHtml = `<div class="login-alert">${message}</div>`;
            $('#alert-container').html(alertHtml);
            setTimeout(function() {
                $('.login-alert').fadeOut('slow', function() {
                    $(this).remove();
                });
            }, 3000);
        }
    });
</script>