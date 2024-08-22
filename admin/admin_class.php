<?php
class Action
{
    private $db;

    public function __construct()
    {
        include $_SERVER['DOCUMENT_ROOT'] . "/mips/components/db_connect.php";
        $this->db = $pdo;
    }

    private function execute_statement($stmt)
    {
        try {
            $stmt->execute();
            return json_encode(['success' => true]);
        } catch (PDOException $e) {
            return json_encode(['error' => 'Database error: ' . $e->getMessage()]);
        }
    }

    // Admin Functions
    public function deactivate_admin($admin_id)
    {
        $sql = "UPDATE Admin SET is_deleted = 1 WHERE admin_id = :admin_id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':admin_id', $admin_id);
        return $this->execute_statement($stmt);
    }

    public function recover_admin($admin_id)
    {
        $sql = "UPDATE Admin SET is_deleted = 0 WHERE admin_id = :admin_id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':admin_id', $admin_id);
        return $this->execute_statement($stmt);
    }

    // Parent Functions
    public function deactivate_parent($parent_id)
    {
        $sql = "UPDATE Parent SET is_deleted = 1 WHERE parent_id = :parent_id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':parent_id', $parent_id);
        return $this->execute_statement($stmt);
    }

    public function recover_parent($parent_id)
    {
        $sql = "UPDATE Parent SET is_deleted = 0 WHERE parent_id = :parent_id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':parent_id', $parent_id);
        return $this->execute_statement($stmt);
    }

    // Product Category Functions
    public function deactivate_product_category($category_id)
    {
        $sql = "UPDATE Product_Category SET is_deleted = 1 WHERE category_id = :category_id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':category_id', $category_id);
        return $this->execute_statement($stmt);
    }

    public function recover_product_category($category_id)
    {
        $sql = "UPDATE Product_Category SET is_deleted = 0 WHERE category_id = :category_id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':category_id', $category_id);
        return $this->execute_statement($stmt);
    }

    // Product Size Functions
    public function deactivate_product_size($product_size_id)
    {
        $sql = "UPDATE Product_Size SET is_deleted = 1 WHERE product_size_id = :product_size_id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':product_size_id', $product_size_id);
        return $this->execute_statement($stmt);
    }

    public function recover_product_size($product_size_id)
    {
        $sql = "UPDATE Product_Size SET is_deleted = 0 WHERE product_size_id = :product_size_id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':product_size_id', $product_size_id);
        return $this->execute_statement($stmt);
    }

    // Order Functions
    public function deactivate_order($order_id)
    {
        $sql = "UPDATE Orders SET is_deleted = 1 WHERE order_id = :order_id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':order_id', $order_id);
        return $this->execute_statement($stmt);
    }

    public function recover_order($order_id)
    {
        $sql = "UPDATE Orders SET is_deleted = 0 WHERE order_id = :order_id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':order_id', $order_id);
        return $this->execute_statement($stmt);
    }

    // Retrieve category data
    public function get_category($category_id)
    {
        $sql = "
            SELECT category_id, category_name, category_icon, parent_id 
            FROM Product_Category 
            WHERE category_id = :category_id AND is_deleted = 0
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':category_id', $category_id);
        $stmt->execute();
        $category = $stmt->fetch(PDO::FETCH_ASSOC);

        return $category ? json_encode($category) : json_encode(['error' => 'Category not found']);
    }

    public function get_subcategory($subcategory_id)
    {
        $sql = "
            SELECT category_id, category_name, category_icon, parent_id
            FROM Product_Category
            WHERE category_id = :subcategory_id AND is_deleted = 0
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':subcategory_id', $subcategory_id);
        $stmt->execute();
        $subcategory = $stmt->fetch(PDO::FETCH_ASSOC);

        return $subcategory ? json_encode($subcategory) : json_encode(['error' => 'Subcategory not found']);
    }

    // Retrieve pending order count
    public function get_pending_count()
    {
        $sql = "
            SELECT COUNT(*)
            FROM Orders o
            JOIN Payment p ON o.order_id = p.order_id
            WHERE o.is_deleted = 0 AND p.payment_status = 'pending'
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchColumn();
    }

    // Retrieve size data
    public function get_size($size_id)
    {
        $sql = "SELECT * FROM Sizes WHERE size_id = :size_id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':size_id', $size_id);
        $stmt->execute();
        $size = $stmt->fetch(PDO::FETCH_ASSOC);

        return $size ? json_encode($size) : json_encode(['error' => 'Size not found']);
    }

    public function get_order_details($order_id)
    {
        $sql = "SELECT o.order_id, o.order_datetime, o.order_price, p.parent_name, pm.payment_status, pm.payment_image
                FROM Orders o
                JOIN Parent_Student ps ON o.parent_student_id = ps.parent_student_id
                JOIN Parent p ON ps.parent_id = p.parent_id
                JOIN Payment pm ON o.order_id = pm.order_id
                WHERE o.order_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$order_id]);
        $order = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($order) {
            $stmtItems = $this->db->prepare("SELECT oi.product_id, p.product_name, oi.product_quantity AS quantity, oi.order_subtotal AS subtotal
                                             FROM Order_Item oi JOIN Product p ON oi.product_id = p.product_id WHERE oi.order_id = ?");
            $stmtItems->execute([$order_id]);
            $order_items = $stmtItems->fetchAll(PDO::FETCH_ASSOC);

            return json_encode(['order' => $order, 'order_items' => $order_items]);
        } else {
            return json_encode(['error' => 'Order not found']);
        }
    }

    // Update order status
    public function update_order_status($order_id, $order_status)
    {
        $sql = "UPDATE Orders o
            JOIN Payment p ON o.order_id = p.order_id
            SET p.payment_status = :order_status
            WHERE o.order_id = :order_id AND o.is_deleted = 0";

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':order_id', $order_id);
        $stmt->bindParam(':order_status', $order_status);
        return $this->execute_statement($stmt);
    }

    // student Functions
    public function deactivate_student($student_id)
    {
        $sql = "UPDATE Student SET is_deleted = 1 WHERE student_id = :student_id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':student_id', $student_id);
        return $this->execute_statement($stmt);
    }

    public function activate_student($student_id)
    {
        $sql = "UPDATE Student SET is_deleted = 0 WHERE student_id = :student_id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':student_id', $student_id);
        return $this->execute_statement($stmt);
    }

    public function get_student($student_id)
    {
        $sql = "SELECT * FROM Student WHERE student_id = :student_id AND is_deleted = 0";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':student_id', $student_id);
        $stmt->execute();
        $student = $stmt->fetch(PDO::FETCH_ASSOC);

        return $student ? json_encode($student) : json_encode(['error' => 'Student not found']);
    }

    // Retrieve class data
    public function get_class($class_id)
    {
        $sql = "
            SELECT c.class_id, c.class_name, c.grade_id, c.class_teacher_id, g.grade_name, a.admin_name AS teacher_name
            FROM Class c
            LEFT JOIN Grade g ON c.grade_id = g.grade_id
            LEFT JOIN Admin a ON c.class_teacher_id = a.admin_id
            WHERE c.class_id = :class_id AND c.is_deleted = 0
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':class_id', $class_id);
        $stmt->execute();
        $class = $stmt->fetch(PDO::FETCH_ASSOC);

        return $class ? json_encode($class) : json_encode(['error' => 'Class not found']);
    }

    // Retrieve grade data
    public function get_grade($grade_id)
    {
        $sql = "
            SELECT grade_id, grade_name, grade_level
            FROM Grade
            WHERE grade_id = :grade_id AND is_deleted = 0
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':grade_id', $grade_id);
        $stmt->execute();
        $grade = $stmt->fetch(PDO::FETCH_ASSOC);

        return $grade ? json_encode($grade) : json_encode(['error' => 'Grade not found']);
    }

    // Retrieve order data
    public function get_order($order_id)
    {
        $sql = "
            SELECT o.order_id, o.order_price, o.order_datetime, p.payment_status, p.payment_image,
                   parent.parent_name
            FROM Orders o
            LEFT JOIN Payment p ON o.order_id = p.order_id
            LEFT JOIN Parent_Student ps ON o.parent_student_id = ps.parent_student_id
            LEFT JOIN Parent parent ON ps.parent_id = parent.parent_id
            WHERE o.order_id = :order_id AND o.is_deleted = 0
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':order_id', $order_id);
        $stmt->execute();
        $order = $stmt->fetch(PDO::FETCH_ASSOC);

        $sqlItems = "
            SELECT oi.product_id, oi.product_quantity, oi.order_subtotal, 
                   prod.product_name, prod_img.image_url AS product_image
            FROM Order_Item oi
            LEFT JOIN Product prod ON oi.product_id = prod.product_id
            LEFT JOIN Product_Image prod_img ON prod.product_id = prod_img.product_id AND prod_img.sort_order = 1
            WHERE oi.order_id = :order_id AND oi.is_deleted = 0
        ";

        $stmtItems = $this->db->prepare($sqlItems);
        $stmtItems->bindParam(':order_id', $order_id);
        $stmtItems->execute();
        $orderItems = $stmtItems->fetchAll(PDO::FETCH_ASSOC);

        if ($order) {
            $order['items'] = $orderItems;
            return json_encode($order);
        } else {
            return json_encode(['error' => 'Order not found']);
        }
    }

    // Retrieve parent data
    public function get_parent($parent_id)
    {
        $sql = "
            SELECT parent_id, parent_name, parent_email
            FROM Parent
            WHERE parent_id = :parent_id AND is_deleted = 0
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':parent_id', $parent_id);
        $stmt->execute();
        $parent = $stmt->fetch(PDO::FETCH_ASSOC);

        return $parent ? json_encode($parent) : json_encode(['error' => 'Parent not found']);
    }

    // Retrieve admin data
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

        return $admin ? json_encode($admin) : json_encode(['error' => 'Admin not found']);
    }

    // Product Functions
    public function deactivate_product($product_id)
    {
        $sql = "UPDATE Product SET is_deleted = 1 WHERE product_id = :product_id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':product_id', $product_id);
        return $this->execute_statement($stmt);
    }

    public function recover_product($product_id)
    {
        $sql = "UPDATE Product SET is_deleted = 0 WHERE product_id = :product_id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':product_id', $product_id);
        return $this->execute_statement($stmt);
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

    // Grade and Class Functions
    public function deactivate_class($class_id)
    {
        $sql = "UPDATE Class SET is_deleted = 1 WHERE class_id = :class_id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':class_id', $class_id);
        return $this->execute_statement($stmt);
    }

    public function recover_class($class_id)
    {
        $sql = "UPDATE Class SET is_deleted = 0 WHERE class_id = :class_id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':class_id', $class_id);
        return $this->execute_statement($stmt);
    }

    public function deactivate_grade($grade_id)
    {
        $sql = "UPDATE Grade SET is_deleted = 1 WHERE grade_id = :grade_id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':grade_id', $grade_id);
        return $this->execute_statement($stmt);
    }

    public function recover_grade($grade_id)
    {
        $sql = "UPDATE Grade SET is_deleted = 0 WHERE grade_id = :grade_id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':grade_id', $grade_id);
        return $this->execute_statement($stmt);
    }
}
