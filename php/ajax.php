<?php
session_start();
ob_start();
date_default_timezone_set("Asia/Kuala_Lumpur");

$action = $_GET['action'] ?? '';
include $_SERVER['DOCUMENT_ROOT'] . "/mips/php/admin&customer_class.php";
$crud = new Action();

switch ($action) {
    case 'login':
        if (isset($_POST['email'], $_POST['password'], $_POST['user_type'])) {
            $email = $_POST['email'];
            $password = $_POST['password'];
            $userType = $_POST['user_type'];
            $currentPage = $_POST['current_page'] ?? null;
            $productId = $_POST['pid'] ?? null;
            echo $crud->login($email, $password, $userType, $currentPage, $productId);
        } else {
            echo json_encode(['error' => 'Email, password, and user type are required']);
        }
        break;

    case 'update_password':
        $newPassword = $_POST['new_password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        $userId = $_SESSION['user_id'] ?? null;
        $userType = $_SESSION['user_type'] ?? null;

        if ($userId && $userType) {
            $result = $crud->update_password($userId, $userType, $newPassword, $confirmPassword);
            echo json_encode($result);
        } else {
            echo json_encode(['error' => 'User not logged in']);
        }
        break;

    case 'activate_account':
        if (isset($_POST['user_name'], $_POST['new_password'], $_POST['confirm_password'])) {
            $userName = $_POST['user_name'];
            $newPassword = $_POST['new_password'];
            $confirmPassword = $_POST['confirm_password'];

            $userType = $_SESSION['user_type'] ?? null;

            if ($userType) {
                if ($userType === 'admin') {
                    $userId = $_SESSION['admin_id'] ?? null;
                } elseif ($userType === 'parent') {
                    $userId = $_SESSION['user_id'] ?? null;
                } else {
                    echo json_encode(['error' => 'Invalid user type.']);
                    exit();
                }

                if ($userId) {
                    echo $crud->activate_account($userId, $userType, $userName, $newPassword, $confirmPassword);
                } else {
                    echo json_encode(['error' => 'User not logged in.']);
                }
            } else {
                echo json_encode(['error' => 'User type not found in session.']);
            }
        } else {
            echo json_encode(['error' => 'All fields are required.']);
        }
        break;


    default:
        echo json_encode(['error' => 'Invalid action']);
}
