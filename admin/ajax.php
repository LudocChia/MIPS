<?php
ob_start();
date_default_timezone_set("Asia/Manila");

$action = $_GET['action'];
include 'admin_class.php';
$crud = new Action();

switch ($action) {
    case 'get_parent':
        if (isset($_GET['parent_id'])) {
            echo $crud->get_parent($_GET['parent_id']);
        } else {
            echo json_encode(['error' => 'Parent ID not provided']);
        }
    case 'get_admin':
        if (isset($_GET['admin_id'])) {
            echo $crud->get_admin($_GET['admin_id']);
        } else {
            echo json_encode(['error' => 'Admin ID not provided']);
        }
        break;
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
