<?php

session_start();

include $_SERVER['DOCUMENT_ROOT'] . "/mips/components/db_connect.php";

if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_type'])) {
    if (isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'parent') {
        header('Location: /mips/login.php');
    } else {
        header('Location: /mips/admin/login.php');
    }
    exit();
}

$userType = $_SESSION['user_type'];
$userId = $_SESSION['user_id'];
$table = ($userType === 'admin') ? 'Admin' : 'Parent';

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
                    <h1>Activate Account</h1>
                </div>
                <div id="alert-container"></div>
                <form method="POST" id="activate-form">
                    <div class="input-container">
                        <div class="input-field">
                            <i class="fas fa-user"></i>
                            <input type="text" id="name" name="name" placeholder="Your Name" required>
                        </div>
                        <p>Please enter your name</p>
                    </div>
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
                    <div class="input-container">
                        <h2>Password Criteria</h2>
                        <table class="two-columns">
                            <tr>
                                <td style="width: 5%">1.</td>
                                <td style="width: 95%">Password should include at least one number and one special symbol.</td>
                            </tr>
                            <tr>
                                <td style="width: 5%">2.</td>
                                <td style="width: 95%">Passwords cannot begin or end with a space.</td>
                            </tr>
                            <tr>
                                <td style="width: 5%">3.</td>
                                <td style="width: 95%">Passwords must be at least 6 characters long.</td>
                            </tr>
                        </table>
                    </div>
                    <div class="controls">
                        <button type="submit" name="submit" class="btn">Update Password</button>
                    </div>
                </form>
            </div>
        </div>
    </main>
    <script>
        document.querySelector('#activate-form').addEventListener('submit', function(e) {
            e.preventDefault();

            const name = document.querySelector('#name').value;
            const newPassword = document.querySelector('#new_password').value;
            const confirmPassword = document.querySelector('#confirm_password').value;

            fetch('/mips/php/ajax.php?action=activate_account', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: new URLSearchParams({
                        name: name,
                        new_password: newPassword,
                        confirm_password: confirmPassword
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        window.location.href = data.redirect;
                    } else {
                        showAlert(data.error);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showAlert('An error occurred while processing the request.');
                });
        });

        function showAlert(message) {
            const alertHtml = `<div class="mini-alert">${message}</div>`;
            document.getElementById('alert-container').innerHTML = alertHtml;

            setTimeout(function() {
                const alertElement = document.querySelector('.mini-alert');
                if (alertElement) {
                    alertElement.style.opacity = '0';
                    setTimeout(() => alertElement.remove(), 600);
                }
            }, 3000);
        }
    </script>

</body>

</html>