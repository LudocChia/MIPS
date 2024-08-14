<?php
ob_start();
date_default_timezone_set("Asia/Manila");

$action = $_GET['action'];
include 'admin_class.php';
$crud = new Action();

switch ($action) {
    case 'get_pending_count':
        echo $crud->get_pending_count();
        break;
    case 'get_order':
        if (isset($_GET['order_id'])) {
            echo $crud->get_order($_GET['order_id']);
        } else {
            echo json_encode(['error' => 'Order ID not provided']);
        }
        break;
    case 'get_parent':
        if (isset($_GET['parent_id'])) {
            echo $crud->get_parent($_GET['parent_id']);
        } else {
            echo json_encode(['error' => 'Parent ID not provided']);
        }
        break;
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
    case 'get_class':
        if (isset($_GET['class_id'])) {
            echo $crud->get_class($_GET['class_id']);
        } else {
            echo json_encode(['error' => 'Class ID not provided']);
        }
        break;
    case 'get_grade':
        if (isset($_GET['grade_id'])) {
            echo $crud->get_grade($_GET['grade_id']);
        } else {
            echo json_encode(['error' => 'Grade ID not provided']);
        }
        break;
    case 'delete_product':
        echo $crud->delete_product();
        break;
    case 'update_order_status':
        if (isset($_POST['order_id']) && isset($_POST['order_status'])) {
            echo $crud->update_order_status($_POST['order_id'], $_POST['order_status']);
        } else {
            echo json_encode(['error' => 'Order ID or status not provided']);
        }
        break;
    default:
        echo json_encode(['error' => 'Invalid action']);
        break;
}
