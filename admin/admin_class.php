<?php

session_start();
include "../components/db_connect.php";

if (isset($_GET['product_id'])) {
    $productId = $_GET['product_id'];

    $sql = "
        SELECT p.product_id, p.product_name, p.product_description, p.product_price, p.product_unit_price,
        p.stock_quantity, p.color, p.gender, p.category_id
        FROM Product p
        WHERE p.product_id = :product_id AND p.is_deleted = 0
        ";

    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':product_id', $productId);
    $stmt->execute();
    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($product) {
        echo json_encode($product);
    } else {
        echo json_encode(['error' => 'Product not found']);
    }
} else {
    echo json_encode(['error' => 'Invalid request']);
}

// admin_class.php
class Action
{
    private $db;

    public function __construct()
    {
        ob_start();
        include 'components/db_connect.php';
        $this->db = $conn;
    }

    // Delete Product Method
    function delete_product()
    {
        extract($_POST);
        $delete = $this->db->query("DELETE FROM Product WHERE product_id = $product_id");
        if ($delete) {
            return 1;
        } else {
            return 0;
        }
    }
}
