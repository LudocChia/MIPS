<?php
class Action
{
    private $db;

    public function __construct()
    {
        include '../components/db_connect.php';
        $this->db = $pdo;
    }

    public function get_parent($parentId)
    {
        $sql = "
            SELECT parent_id, parent_name, parent_email
            FROM Parent
            WHERE parent_id = :parent_id AND is_deleted = 0
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':parent_id', $parentId);
        $stmt->execute();
        $parent = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($parent) {
            return json_encode($parent);
        } else {
            return json_encode(['error' => 'Parent not found']);
        }
    }

    public function get_admin($admin_id)
    {
        $sql = "
            SELECT admin_id, admin_name, admin_email, register_date 
            FROM Admin
            WHERE admin_id = :admin_id AND is_deleted = 0
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':admin_id', $admin_id);
        $stmt->execute();
        $admin = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($admin) {
            return json_encode($admin);
        } else {
            return json_encode(['error' => 'Admin not found']);
        }
    }
    public function get_product($product_id)
    {
        $sql = "
            SELECT p.product_id, p.product_name, p.product_description, p.product_price,
            p.stock_quantity, p.color, p.gender, p.category_id
            FROM Product p
            WHERE p.product_id = :product_id AND p.is_deleted = 0
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':product_id', $product_id);
        $stmt->execute();
        $product = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($product) {
            $sizeSql = "
                SELECT size_id
                FROM Product_Size
                WHERE product_id = :product_id
            ";
            $sizeStmt = $this->db->prepare($sizeSql);
            $sizeStmt->bindParam(':product_id', $product_id);
            $sizeStmt->execute();
            $sizes = $sizeStmt->fetchAll(PDO::FETCH_COLUMN);

            $product['sizes'] = $sizes;

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
}
