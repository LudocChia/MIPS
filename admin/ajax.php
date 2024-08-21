<?php
ob_start();
date_default_timezone_set("Asia/Kuala_Lumpur");

$action = $_GET['action'];
include 'admin_class.php';
$crud = new Action();

switch ($action) {
    case 'deactivate_parent':
        if (isset($_POST['parent_id'])) {
            echo $crud->deactivate_parent($_POST['parent_id']);
        } else {
            echo json_encode(['error' => 'Parent ID not provided']);
        }
        break;

    case 'recover_parent':
        if (isset($_POST['parent_id'])) {
            echo $crud->recover_parent($_POST['parent_id']);
        } else {
            echo json_encode(['error' => 'Parent ID not provided']);
        }
        break;

    case 'deactivate_order':
        if (isset($_POST['order_id'])) {
            echo $crud->deactivate_order($_POST['order_id']);
        } else {
            echo json_encode(['error' => 'Order ID not provided']);
        }
        break;

    case 'recover_order':
        if (isset($_POST['order_id'])) {
            echo $crud->recover_order($_POST['order_id']);
        } else {
            echo json_encode(['error' => 'Order ID not provided']);
        }
        break;

    case 'deactivate_product_category':
        if (isset($_POST['category_id'])) {
            echo $crud->deactivate_product_category($_POST['category_id']);
        } else {
            echo json_encode(['error' => 'Category ID not provided']);
        }
        break;

    case 'recover_product_category':
        if (isset($_POST['category_id'])) {
            echo $crud->recover_product_category($_POST['category_id']);
        } else {
            echo json_encode(['error' => 'Category ID not provided']);
        }
        break;

    case 'get_category':
        if (isset($_GET['category_id'])) {
            echo $crud->get_category($_GET['category_id']);
        } else {
            echo json_encode(['error' => 'Category ID not provided']);
        }
        break;

    case 'get_size':
        if (isset($_GET['size_id'])) {
            echo $crud->get_size($_GET['size_id']);
        } else {
            echo json_encode(['error' => 'Size ID not provided']);
        }
        break;

    case 'get_pending_count':
        echo $crud->get_pending_count();
        break;

    case 'get_order':
        if (isset($_POST['order_id'])) {
            echo $crud->get_order($_POST['order_id']);
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

    case 'get_student':
        if (isset($_GET['student_id'])) {
            echo $crud->get_student($_GET['student_id']);
        } else {
            echo json_encode(['error' => 'Student ID not provided']);
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

    case 'get_subcategory':
        if (isset($_GET['subcategory_id'])) {
            echo $crud->get_subcategory($_GET['subcategory_id']);
        } else {
            echo json_encode(['error' => 'Subcategory ID not provided']);
        }
        break;

    case 'update_order_status':
        if (isset($_POST['order_id']) && isset($_POST['order_status'])) {
            echo $crud->update_order_status($_POST['order_id'], $_POST['order_status']);
        } else {
            echo json_encode(['error' => 'Order ID or order status not provided']);
        }
        break;

    default:
        echo json_encode(['error' => 'Invalid action']);
        break;
}
