<?php
session_start();
ob_start();
date_default_timezone_set("Asia/Kuala_Lumpur");

$action = $_GET['action'];
include $_SERVER['DOCUMENT_ROOT'] . "/mips/php/admin&customer_class.php";
$crud = new Action();

switch ($action) {
    case 'update_password':
        $newPassword = $_POST['new_password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        if ($newPassword === $confirmPassword) {
            $userId = $_SESSION['user_id'];
            $userType = $_SESSION['user_type'];
            $result = $crud->update_password($userId, $userType, $newPassword);
            echo json_encode($result);
        } else {
            echo json_encode(['error' => 'Passwords do not match']);
        }
        break;

    case 'activate_account':
        if (isset($_POST['user_name'], $_POST['new_password'], $_POST['confirm_password'])) {
            $userName = $_POST['user_name'];
            $newPassword = $_POST['new_password'];
            $confirmPassword = $_POST['confirm_password'];
            $userId = $_SESSION['user_id'];
            $userType = $_SESSION['user_type'];

            echo $crud->activate_account($userId, $userType, $userName, $newPassword, $confirmPassword);
        } else {
            echo json_encode(['error' => 'All fields are required']);
        }
        break;


    default:
        echo json_encode(['error' => 'Invalid action']);
}
