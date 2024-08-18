<?php
ob_start();
date_default_timezone_set("Asia/Kuala_Lumpur");

$action = $_GET['action'];
include 'customer_class.php';
$crud = new Action();

switch ($action) {
    case 'add_to_cart':
        if (isset($_POST['customer_id'], $_POST['product_id'], $_POST['qty'], $_POST['product_size_id'])) {
            echo $crud->add_to_cart($_POST['customer_id'], $_POST['product_id'], $_POST['qty'], $_POST['product_size_id']);
        } else {
            echo json_encode(['error' => 'Invalid or missing input data']);
        }
        break;
    default:
        echo json_encode(['error' => 'Invalid action']);
        break;
}
