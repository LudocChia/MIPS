<?php
ob_start();
date_default_timezone_set("Asia/Manila");

$action = $_GET['action'];
include 'admin_class.php';
$crud = new Action();

switch ($action) {
    case 'get_product':
        if (isset($_GET['product_id'])) {
            echo $crud->get_product($_GET['product_id']);
        } else {
            echo json_encode(['error' => 'Product ID not provided']);
        }
        break;
    case 'delete_product':
        echo $crud->delete_product();
        break;
    default:
        echo json_encode(['error' => 'Invalid action']);
        break;
}
