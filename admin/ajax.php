// ajax.php
<?php
ob_start();
date_default_timezone_set("Asia/Manila");

$action = $_GET['action'];
include 'admin_class.php';
$crud = new Action();

if ($action == 'delete_product') {
    $delete = $crud->delete_product();
    if ($delete)
        echo $delete;
}
?>