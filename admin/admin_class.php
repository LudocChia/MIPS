<?php
class Action
{
    private $db;

    public function __construct()
    {
        include '../components/db_connect.php';
        $this->db = $pdo;
    }

    public function get_product($product_id)
    {
        $sql = "
            SELECT p.product_id, p.product_name, p.product_description, p.product_price, p.product_unit_price,
            p.stock_quantity, p.color, p.gender, p.category_id
            FROM Product p
            WHERE p.product_id = :product_id AND p.is_deleted = 0
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':product_id', $product_id);
        $stmt->execute();
        $product = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($product) {
            return json_encode($product);
        } else {
            return json_encode(['error' => 'Product not found']);
        }
    }

    public function delete_product()
    {
        extract($_POST);
        $delete = $this->db->query("DELETE FROM Product WHERE product_id = $product_id");
        return $delete ? 1 : 0;
    }

    // 其他处理函数
}
