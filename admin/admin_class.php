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

    private function check_email_exists($email, $table, $email_field, $id_field = null, $id_value = null)
    {
        $sql = "SELECT * FROM $table WHERE $email_field = :email";
        if ($id_field && $id_value) {
            $sql .= " AND $id_field != :id_value";
        }
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        if ($id_field && $id_value) {
            $stmt->bindParam(':id_value', $id_value, PDO::PARAM_STR);
        }
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            return json_encode(['error' => 'Email is already in use!']);
        }

        return json_encode(['success' => true]);
    }

    private function validate_password($password, $confirm_password)
    {
        if ($password !== $confirm_password) {
            return json_encode(['error' => 'Passwords do not match!']);
        }
        return json_encode(['success' => true]);
    }

    public function login($email, $password)
    {
        try {
            $sql = "SELECT * FROM Admin WHERE admin_email = :email AND status IN (0, -1)";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':email', $email);
            $stmt->execute();

            $admin = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($admin && password_verify($password, $admin['admin_password'])) {
                session_start();
                $_SESSION['user_type'] = 'admin';
                $_SESSION['user_id'] = $admin['admin_id'];
                $_SESSION['user_name'] = $admin['admin_name'];
                $_SESSION['user_email'] = $admin['admin_email'];
                $_SESSION['user_status'] = $admin['status'];
                $_SESSION['user_image'] = $admin['admin_image'] ?? '/mips/images/default_profile.png';

                if ($admin['status'] == -1) {
                    return json_encode(['new_user' => true, 'redirect' => '/mips/activate.php']);
                }

                return json_encode(['success' => true, 'redirect' => '/mips/admin']);
            } else {
                return json_encode(['success' => false, 'error' => 'Invalid email or password.']);
            }
        } catch (PDOException $e) {
            return json_encode(['error' => 'Database error: ' . $e->getMessage()]);
        }
    }

    // Admin Functions
    public function deactivate_admin($admin_id)
    {
        $sql = "UPDATE Admin SET status = 1 WHERE admin_id = :admin_id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':admin_id', $admin_id);
        return $this->execute_statement($stmt);
    }

    public function recover_admin($admin_id)
    {
        $sql = "UPDATE Admin SET status = 0 WHERE admin_id = :admin_id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':admin_id', $admin_id);
        return $this->execute_statement($stmt);
    }

    public function get_admin($admin_id)
    {
        $sql = "
            SELECT admin_id, admin_name, admin_email, created_at
            FROM Admin
            WHERE admin_id = :admin_id AND status in (-1, 0)
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':admin_id', $admin_id);
        $stmt->execute();
        $admin = $stmt->fetch(PDO::FETCH_ASSOC);

        return $admin ? json_encode($admin) : json_encode(['error' => 'Admin not found']);
    }


    public function save_admin($admin_id, $admin_name, $admin_email, $admin_password, $confirm_password, $admin_type = 'admin')
    {
        $emailCheck = json_decode($this->check_email_exists($admin_email, 'Admin', 'admin_email', 'admin_id', $admin_id), true);
        if (isset($emailCheck['error'])) {
            return json_encode($emailCheck);
        }

        $passwordCheck = json_decode($this->validate_password($admin_password, $confirm_password), true);
        if (isset($passwordCheck['error'])) {
            return json_encode($passwordCheck);
        }

        $hashed_password = password_hash($admin_password, PASSWORD_DEFAULT);

        if (!empty($admin_id)) {
            if (!empty($admin_password)) {
                $sql = "UPDATE Admin SET admin_name = :name, admin_email = :email, admin_password = :password, admin_type = :adminType WHERE admin_id = :adminId";
                $stmt = $this->db->prepare($sql);
                $stmt->bindParam(':password', $hashed_password);
            } else {
                $sql = "UPDATE Admin SET admin_name = :name, admin_email = :email, admin_type = :adminType WHERE admin_id = :adminId";
                $stmt = $this->db->prepare($sql);
            }
            $stmt->bindParam(':adminId', $admin_id);
        } else {
            $admin_id = uniqid("AD");
            $sql = "INSERT INTO Admin (admin_id, admin_name, admin_email, admin_password, admin_type) VALUES (:adminId, :name, :email, :password, :adminType)";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':adminId', $admin_id);
            $stmt->bindParam(':password', $hashed_password);
        }

        $stmt->bindParam(':name', $admin_name);
        $stmt->bindParam(':email', $admin_email);
        $stmt->bindParam(':adminType', $admin_type);

        return $this->execute_statement($stmt);
    }

    // Parent Functions
    public function deactivate_parent($parent_id)
    {
        $sql = "UPDATE Parent SET status = 1 WHERE parent_id = :parent_id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':parent_id', $parent_id);
        return $this->execute_statement($stmt);
    }

    public function recover_parent($parent_id)
    {
        $sql = "UPDATE Parent SET status = 0 WHERE parent_id = :parent_id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':parent_id', $parent_id);
        return $this->execute_statement($stmt);
    }

    public function get_parent($parent_id)
    {
        $sql = "
            SELECT parent_id, parent_name, parent_email, parent_phone
            FROM Parent
            WHERE parent_id = :parent_id AND status in (-1, 0)
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':parent_id', $parent_id);
        $stmt->execute();
        $parent = $stmt->fetch(PDO::FETCH_ASSOC);

        return $parent ? json_encode($parent) : json_encode(['error' => 'Parent not found']);
    }

    public function save_parent($parent_id, $parent_name, $parent_email, $parent_phone, $parent_password, $confirm_password, $admin_id)
    {
        // 调用 check_email_exists 函数时，传入 'parent_email' 作为字段名
        $emailCheck = json_decode($this->check_email_exists($parent_email, 'Parent', 'parent_email', 'parent_id', $parent_id), true);
        if (isset($emailCheck['error'])) {
            return json_encode($emailCheck);
        }

        $passwordCheck = json_decode($this->validate_password($parent_password, $confirm_password), true);
        if (isset($passwordCheck['error'])) {
            return json_encode($passwordCheck);
        }

        $hashed_password = password_hash($parent_password, PASSWORD_DEFAULT);

        if (!empty($parent_id)) {
            if (!empty($parent_password)) {
                $sql = "UPDATE Parent SET parent_name = :name, parent_email = :email, parent_phone = :phone, parent_password = :password, admin_id = :admin_id WHERE parent_id = :parent_id";
                $stmt = $this->db->prepare($sql);
                $stmt->bindParam(':password', $hashed_password);
            } else {
                $sql = "UPDATE Parent SET parent_name = :name, parent_email = :email, parent_phone = :phone, admin_id = :admin_id WHERE parent_id = :parent_id";
                $stmt = $this->db->prepare($sql);
            }
            $stmt->bindParam(':parent_id', $parent_id, PDO::PARAM_STR);
        } else {
            $parent_id = uniqid("PR");
            $sql = "INSERT INTO Parent (parent_id, parent_name, parent_email, parent_phone, parent_password, admin_id) VALUES (:parent_id, :name, :email, :phone, :password, :admin_id)";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':parent_id', $parent_id, PDO::PARAM_STR);
            $stmt->bindParam(':password', $hashed_password);
            $stmt->bindParam(':name', $parent_name, PDO::PARAM_STR);
            $stmt->bindParam(':email', $parent_email, PDO::PARAM_STR);
            $stmt->bindParam(':phone', $parent_phone, PDO::PARAM_STR);
            $stmt->bindParam(':admin_id', $admin_id, PDO::PARAM_STR);

            if ($this->execute_statement($stmt) === json_encode(['success' => true])) {
                $this->create_shopping_cart($parent_id);
                return json_encode(['success' => true]);
            } else {
                return json_encode(['error' => 'Failed to save parent.']);
            }
        }

        $stmt->bindParam(':name', $parent_name, PDO::PARAM_STR);
        $stmt->bindParam(':email', $parent_email, PDO::PARAM_STR);
        $stmt->bindParam(':phone', $parent_phone, PDO::PARAM_STR);
        $stmt->bindParam(':admin_id', $admin_id, PDO::PARAM_STR);

        return $this->execute_statement($stmt);
    }

    private function create_shopping_cart($parent_id)
    {
        $cart_id = uniqid("SC");
        $sql = "INSERT INTO Cart (cart_id, parent_id, created_at) VALUES (:cart_id, :parent_id, NOW())";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':cart_id', $cart_id, PDO::PARAM_STR);
        $stmt->bindParam(':parent_id', $parent_id, PDO::PARAM_STR);

        return $this->execute_statement($stmt);
    }

    public function get_default_parents()
    {
        try {
            $sql = "SELECT parent_id, parent_name FROM Parent WHERE status IN(0, -1) ORDER BY created_at DESC LIMIT 5";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            $parents = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return json_encode($parents);
        } catch (PDOException $e) {
            return json_encode(['error' => 'Error fetching parents: ' . $e->getMessage()]);
        }
    }

    public function search_parent($query)
    {
        $sql = "SELECT parent_id, parent_name FROM Parent WHERE parent_name LIKE :query OR parent_id LIKE :query AND status IN(0, -1) ORDER BY created_at DESC LIMIT 5";
        $stmt = $this->db->prepare($sql);
        $searchQuery = "%$query%";
        $stmt->bindParam(':query', $searchQuery);
        $stmt->execute();
        $parents = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return json_encode($parents);
    }

    // Product Category Functions
    public function deactivate_product_category($category_id)
    {
        $sql = "UPDATE Product_Category SET status = 1 WHERE category_id = :category_id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':category_id', $category_id);
        return $this->execute_statement($stmt);
    }

    public function delete_product_category($category_id)
    {
        try {
            $stmt = $this->db->prepare("SELECT category_icon FROM Product_Category WHERE category_id = :category_id");
            $stmt->bindParam(':category_id', $category_id);
            $stmt->execute();
            $category = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($category && !empty($category['category_icon'])) {
                $imagePath = $_SERVER['DOCUMENT_ROOT'] . '/mips/uploads/category/' . $category['category_icon'];
                if (file_exists($imagePath)) {
                    unlink($imagePath);
                }
            }

            $stmt = $this->db->prepare("DELETE FROM Product_Category WHERE category_id = :category_id");
            $stmt->bindParam(':category_id', $category_id);
            $stmt->execute();
        } catch (PDOException $e) {
            return json_encode(['error' => 'Failed to delete product category: ' . $e->getMessage()]);
        }

        return json_encode(['success' => true]);
    }

    public function recover_product_category($category_id)
    {
        $sql = "UPDATE Product_Category SET status = 0 WHERE category_id = :category_id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':category_id', $category_id);
        return $this->execute_statement($stmt);
    }

    public function get_category($category_id)
    {
        $sql = "
            SELECT category_id, category_name, category_icon, parent_id 
            FROM Product_Category 
            WHERE category_id = :category_id AND status = 0
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
            WHERE category_id = :subcategory_id AND status = 0
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':subcategory_id', $subcategory_id);
        $stmt->execute();
        $subcategory = $stmt->fetch(PDO::FETCH_ASSOC);

        return $subcategory ? json_encode($subcategory) : json_encode(['error' => 'Subcategory not found']);
    }


    // Product Size Functions
    public function deactivate_product_size($product_size_id)
    {
        $sql = "UPDATE Product_Size SET status = 1 WHERE product_size_id = :product_size_id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':product_size_id', $product_size_id);
        return $this->execute_statement($stmt);
    }

    public function recover_product_size($product_size_id)
    {
        $sql = "UPDATE Product_Size SET status = 0 WHERE product_size_id = :product_size_id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':product_size_id', $product_size_id);
        return $this->execute_statement($stmt);
    }

    // Announcement Functions
    public function deactivate_announcement($announcement_id)
    {
        $sql = "UPDATE Announcement SET status = 1 WHERE announcement_id = :announcement_id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':announcement_id', $announcement_id);
        return $this->execute_statement($stmt);
    }

    public function recover_announcement($announcement_id)
    {
        $sql = "UPDATE Announcement SET status = 0 WHERE announcement_id = :announcement_id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':announcement_id', $announcement_id);
        return $this->execute_statement($stmt);
    }

    public function get_announcement($announcement_id)
    {
        $sql = "SELECT announcement_id, admin_id, announcement_image_url, announcement_url, announcement_title, announcement_message, created_at, updated_at
                FROM Announcement
                WHERE announcement_id = :announcement_id AND status = 0";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':announcement_id', $announcement_id);
        $stmt->execute();
        $announcement = $stmt->fetch(PDO::FETCH_ASSOC);
        return $announcement ? json_encode($announcement) : json_encode(['error' => 'Announcement not found']);
    }

    public function delete_announcement($announcement_id)
    {
        try {
            $stmt = $this->db->prepare("SELECT announcement_image_url FROM Announcement WHERE announcement_id = :announcement_id");
            $stmt->bindParam(':announcement_id', $announcement_id);
            $stmt->execute();
            $announcement = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($announcement && !empty($announcement['announcement_image_url'])) {
                $imagePath = $_SERVER['DOCUMENT_ROOT'] . '/mips/uploads/announcement/' . $announcement['announcement_image_url'];
                if (file_exists($imagePath)) {
                    unlink($imagePath);
                }
            }

            $stmt = $this->db->prepare("DELETE FROM Announcement WHERE announcement_id = :announcement_id");
            $stmt->bindParam(':announcement_id', $announcement_id);
            $stmt->execute();

            return json_encode(['success' => 'Announcement deleted successfully']);
        } catch (PDOException $e) {
            return json_encode(['error' => 'Database error: ' . $e->getMessage()]);
        }
    }


    // Order Functions
    public function update_order_status($order_id, $new_status)
    {
        try {
            $this->db->beginTransaction();

            $query = "SELECT p.payment_status, oi.product_id, oi.product_quantity
                        FROM Orders o
                        JOIN Payment p ON o.order_id = p.order_id
                        JOIN Order_Item oi ON oi.order_id = o.order_id
                        WHERE o.order_id = :order_id AND o.status = 0
            ";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':order_id', $order_id);
            $stmt->execute();
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (!$results) {
                throw new Exception("Order not found or no items linked with it.");
            }

            $current_status = $results[0]['payment_status'];

            $updateSql = "UPDATE Orders o
                        JOIN Payment p ON o.order_id = p.order_id
                        SET p.payment_status = :new_status
                        WHERE o.order_id = :order_id AND o.status = 0
            ";
            $updateStmt = $this->db->prepare($updateSql);
            $updateStmt->bindParam(':order_id', $order_id);
            $updateStmt->bindParam(':new_status', $new_status);
            $updateStmt->execute();

            foreach ($results as $item) {
                if ($current_status != 'completed' && $new_status == 'completed') {
                    $stockSql = "UPDATE Product SET stock_quantity = stock_quantity - :quantity WHERE product_id = :product_id";
                } elseif ($current_status == 'completed' && $new_status != 'completed') {
                    $stockSql = "UPDATE Product SET stock_quantity = stock_quantity + :quantity WHERE product_id = :product_id";
                } else {
                    continue;
                }
                $stockStmt = $this->db->prepare($stockSql);
                $stockStmt->bindParam(':quantity', $item['product_quantity']);
                $stockStmt->bindParam(':product_id', $item['product_id']);
                $stockStmt->execute();
            }

            $this->db->commit();
            return json_encode(['success' => 'Order status udated successfully']);
        } catch (Exception $e) {
            return json_encode(['error' => 'Database error: ' . $e->getMessage()]);
        }
    }


    public function delete_order($order_id)
    {
        try {
            $this->db->beginTransaction();

            $sqlGetImage = "SELECT payment_image FROM Payment WHERE order_id = :order_id";
            $stmtGetImage = $this->db->prepare($sqlGetImage);
            $stmtGetImage->bindParam(':order_id', $order_id);
            $stmtGetImage->execute();
            $paymentImage = $stmtGetImage->fetch(PDO::FETCH_ASSOC);

            if ($paymentImage && !empty($paymentImage['payment_image'])) {
                $imagePath = $_SERVER['DOCUMENT_ROOT'] . '/mips/uploads/receipts/' . $paymentImage['payment_image'];
                if (file_exists($imagePath)) {
                    unlink($imagePath);
                }
            }

            $sqlPayment = "DELETE FROM Payment WHERE order_id = :order_id";
            $stmtPayment = $this->db->prepare($sqlPayment);
            $stmtPayment->bindParam(':order_id', $order_id);
            $stmtPayment->execute();

            $sqlOrderItemStudent = "DELETE FROM Order_Item_Student WHERE order_item_id IN (SELECT order_item_id FROM Order_Item WHERE order_id = :order_id)";
            $stmtOrderItemStudent = $this->db->prepare($sqlOrderItemStudent);
            $stmtOrderItemStudent->bindParam(':order_id', $order_id);
            $stmtOrderItemStudent->execute();

            $sqlOrderItem = "DELETE FROM Order_Item WHERE order_id = :order_id";
            $stmtOrderItem = $this->db->prepare($sqlOrderItem);
            $stmtOrderItem->bindParam(':order_id', $order_id);
            $stmtOrderItem->execute();

            $sqlOrder = "DELETE FROM Orders WHERE order_id = :order_id";
            $stmtOrder = $this->db->prepare($sqlOrder);
            $stmtOrder->bindParam(':order_id', $order_id);
            $stmtOrder->execute();

            $this->db->commit();
            return json_encode(['success' => 'Order and all related data including payment image deleted successfully']);
        } catch (PDOException $e) {
            return json_encode(['error' => 'Database error: ' . $e->getMessage()]);
        }
    }

    // Retrieve pending order count
    public function get_pending_count()
    {
        $sql = "
            SELECT COUNT(*)
            FROM Orders o
            JOIN Payment p ON o.order_id = p.order_id
            WHERE o.status = 0 AND p.payment_status = 'pending'
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

    // student Functions
    public function deactivate_student($student_id)
    {
        $sql = "UPDATE Student SET status = 1 WHERE student_id = :student_id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':student_id', $student_id);
        return $this->execute_statement($stmt);
    }

    public function delete_student($student_id)
    {
        $sql = "DELETE FROM Student WHERE student_id = :student_id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':student_id', $student_id);
        return $this->execute_statement($stmt);
    }

    public function recover_student($student_id)
    {
        $sql = "UPDATE Student SET status = 0 WHERE student_id = :student_id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':student_id', $student_id);
        return $this->execute_statement($stmt);
    }

    public function activate_student($student_id)
    {
        $sql = "UPDATE Student SET status = 0 WHERE student_id = :student_id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':student_id', $student_id);
        return $this->execute_statement($stmt);
    }

    public function get_student($student_id)
    {
        $sql = "SELECT * FROM Student WHERE student_id = :student_id AND status = 0";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':student_id', $student_id);
        $stmt->execute();
        $student = $stmt->fetch(PDO::FETCH_ASSOC);

        return $student ? json_encode($student) : json_encode(['error' => 'Student not found']);
    }

    public function get_student_prefix($class_id)
    {
        $sql = "SELECT g.student_id_prefix 
                FROM Class c
                LEFT JOIN Grade g ON c.grade_id = g.grade_id 
                WHERE c.class_id = :class_id AND c.status = 0";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':class_id', $class_id);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result) {
            return json_encode(['prefix' => $result['student_id_prefix']]);
        } else {
            return json_encode(['error' => 'Prefix not found for the selected class']);
        }
    }


    // Retrieve class data
    public function get_class($class_id)
    {
        $sql = "
            SELECT c.class_id, c.class_name, c.grade_id, c.class_teacher_id, g.grade_name, a.admin_name AS teacher_name
            FROM Class c
            LEFT JOIN Grade g ON c.grade_id = g.grade_id
            LEFT JOIN Admin a ON c.class_teacher_id = a.admin_id
            WHERE c.class_id = :class_id AND c.status = 0
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
        $sql = "SELECT grade_id, grade_name, grade_level, student_id_prefix
                FROM Grade
                WHERE grade_id = :grade_id AND status = 0
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
        $sql = "SELECT o.order_id, o.order_price, o.order_datetime, p.payment_status, p.payment_image, parent.parent_name
                FROM Orders o
                LEFT JOIN Payment p ON o.order_id = p.order_id
                LEFT JOIN Parent_Student ps ON o.parent_student_id = ps.parent_student_id
                LEFT JOIN Parent parent ON ps.parent_id = parent.parent_id
                WHERE o.order_id = :order_id AND o.status = 0";

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':order_id', $order_id);
        $stmt->execute();
        $order = $stmt->fetch(PDO::FETCH_ASSOC);

        $sqlItems = "SELECT oi.product_id, oi.product_quantity, oi.order_subtotal, prod.product_name, prod_img.image_url AS product_image,
                        student.student_name, class.class_name
                    FROM Order_Item oi
                    LEFT JOIN Product prod ON oi.product_id = prod.product_id
                    LEFT JOIN Product_Image prod_img ON prod.product_id = prod_img.product_id AND prod_img.sort_order = 1
                    LEFT JOIN Order_Item_Student ois ON oi.order_item_id = ois.order_item_id
                    LEFT JOIN Student student ON ois.student_id = student.student_id
                    LEFT JOIN Class class ON student.class_id = class.class_id
                    WHERE oi.order_id = :order_id AND oi.status = 0";

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


    // Product Functions
    public function deactivate_product($product_id)
    {
        $sql = "UPDATE Product SET status = 1 WHERE product_id = :product_id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':product_id', $product_id);
        return $this->execute_statement($stmt);
    }

    public function delete_product($product_id) {}

    public function recover_product($product_id)
    {
        $sql = "UPDATE Product SET status = 0 WHERE product_id = :product_id";
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
            WHERE p.product_id = :product_id AND p.status = 0
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':product_id', $product_id, PDO::PARAM_STR);
        $stmt->execute();
        $product = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($product) {
            $sizeSql = "
                SELECT size_id
                FROM Product_Size
                WHERE product_id = :product_id
            ";
            $sizeStmt = $this->db->prepare($sizeSql);
            $sizeStmt->bindParam(':product_id', $product_id, PDO::PARAM_STR);
            $sizeStmt->execute();
            $sizes = $sizeStmt->fetchAll(PDO::FETCH_COLUMN);

            $imageSql = "
                SELECT image_url
                FROM Product_Image
                WHERE product_id = :product_id
                ORDER BY sort_order ASC
            ";
            $imageStmt = $this->db->prepare($imageSql);
            $imageStmt->bindParam(':product_id', $product_id, PDO::PARAM_STR);
            $imageStmt->execute();
            $images = $imageStmt->fetchAll(PDO::FETCH_COLUMN);

            $product['sizes'] = $sizes;
            $product['images'] = $images;

            return json_encode($product);
        } else {
            return json_encode(['error' => 'Product not found']);
        }
    }

    // Grade and Class Functions
    public function deactivate_class($class_id)
    {
        $sql = "UPDATE Class SET status = 1 WHERE class_id = :class_id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':class_id', $class_id);
        return $this->execute_statement($stmt);
    }

    public function recover_class($class_id)
    {
        $sql = "UPDATE Class SET status = 0 WHERE class_id = :class_id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':class_id', $class_id);
        return $this->execute_statement($stmt);
    }

    public function deactivate_grade($grade_id)
    {
        $sql = "UPDATE Grade SET status = 1 WHERE grade_id = :grade_id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':grade_id', $grade_id);
        return $this->execute_statement($stmt);
    }

    public function recover_grade($grade_id)
    {
        $sql = "UPDATE Grade SET status = 0 WHERE grade_id = :grade_id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':grade_id', $grade_id);
        return $this->execute_statement($stmt);
    }

    // Event functions
    public function get_event($event_id)
    {
        $sql = "SELECT * FROM event WHERE event_id = :event_id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':event_id', $event_id, PDO::PARAM_STR);
        $stmt->execute();
        $event = $stmt->fetch(PDO::FETCH_ASSOC);

        return $event ? json_encode($event) : json_encode(['error' => 'Event not found']);
    }

    public function save_event($event_id, $name, $time, $place, $date, $description)
    {
        if (!empty($event_id)) {
            $sql = "UPDATE event SET name = :name, time = :time, place = :place, date = :date, description = :description WHERE event_id = :event_id";
            $stmt = $this->db->prepare($sql);
        } else {
            $event_id = uniqid("EV");
            $sql = "INSERT INTO event (event_id, name, time, place, date, description) VALUES (:event_id, :name, :time, :place, :date, :description)";
            $stmt = $this->db->prepare($sql);
        }

        $stmt->bindParam(':event_id', $event_id);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':time', $time);
        $stmt->bindParam(':place', $place);
        $stmt->bindParam(':date', $date);
        $stmt->bindParam(':description', $description);

        return $this->execute_statement($stmt);
    }

    public function delete_event($event_id)
    {
        try {
            $this->db->beginTransaction();

            $stmt = $this->db->prepare("DELETE FROM donator WHERE event_meal_id IN (SELECT event_meal_id FROM event_meal WHERE event_id = :event_id)");
            $stmt->bindParam(':event_id', $event_id);
            $stmt->execute();

            $stmt = $this->db->prepare("DELETE FROM meals WHERE event_meal_id IN (SELECT event_meal_id FROM event_meal WHERE event_id = :event_id)");
            $stmt->bindParam(':event_id', $event_id);
            $stmt->execute();

            $stmt = $this->db->prepare("DELETE FROM event_meal WHERE event_id = :event_id");
            $stmt->bindParam(':event_id', $event_id);
            $stmt->execute();

            $stmt = $this->db->prepare("DELETE FROM event WHERE event_id = :event_id");
            $stmt->bindParam(':event_id', $event_id);
            $stmt->execute();

            $this->db->commit();

            return json_encode(['success' => 'Event and related data deleted successfully.']);
        } catch (PDOException $e) {
            $this->db->rollBack();
            return json_encode(['error' => 'Error deleting event: ' . $e->getMessage()]);
        }
    }

    // Meal Functions
    public function save_meal($meal_id, $meal_name, $sets, $person_per_set)
    {
        if (!empty($meal_id)) {
            $sql = "UPDATE Meals SET meal_name = :meal_name, sets = :sets, person_per_set = :person_per_set WHERE meal_id = :meal_id";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':meal_id', $meal_id);
            $stmt->bindParam(':meal_name', $meal_name);
            $stmt->bindParam(':sets', $sets);
            $stmt->bindParam(':person_per_set', $person_per_set);
        } else {
            return json_encode(['error' => 'Meal ID is required for update']);
        }

        return $this->execute_statement($stmt);
    }

    public function delete_meal($meal_id)
    {
        $sql = "DELETE FROM Meals WHERE meal_id = :meal_id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':meal_id', $meal_id);
        return $this->execute_statement($stmt);
    }

    public function get_meal($meal_id)
    {
        try {
            $sql = "SELECT meal_id, meal_name, sets, person_per_set FROM meals WHERE meal_id = :meal_id";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':meal_id', $meal_id);
            $stmt->execute();
            $meal = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($meal) {
                return $meal;
            } else {
                return ['error' => 'Meal not found'];
            }
        } catch (PDOException $e) {
            return ['error' => 'Database error: ' . $e->getMessage()];
        }
    }
}
