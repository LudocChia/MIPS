<?php
class Action
{
    private $db;

    public function __construct()
    {
        include './components/db_connect.php';
        $this->db = $pdo;
    }

    public function add_to_cart($parent_id, $product_id, $quantity, $product_size_id)
    {
        try {
            $sql_cart = "
            SELECT cart_id 
            FROM Cart 
            WHERE parent_id = :parent_id
        ";
            $stmt_cart = $this->db->prepare($sql_cart);
            $stmt_cart->bindParam(':parent_id', $parent_id);
            $stmt_cart->execute();
            $cart = $stmt_cart->fetch(PDO::FETCH_ASSOC);

            $cart_id = $cart['cart_id'];

            $sql_cart_item = "
            SELECT cart_item_id, product_quantity 
            FROM Cart_Item 
            WHERE cart_id = :cart_id AND product_id = :product_id AND product_size_id = :product_size_id
        ";
            $stmt_cart_item = $this->db->prepare($sql_cart_item);
            $stmt_cart_item->bindParam(':cart_id', $cart_id);
            $stmt_cart_item->bindParam(':product_id', $product_id);
            $stmt_cart_item->bindParam(':product_size_id', $product_size_id);
            $stmt_cart_item->execute();
            $cart_item = $stmt_cart_item->fetch(PDO::FETCH_ASSOC);

            if ($cart_item) {
                $sql_update = "
                UPDATE Cart_Item 
                SET product_quantity = product_quantity + :quantity 
                WHERE cart_item_id = :cart_item_id
            ";
                $stmt_update = $this->db->prepare($sql_update);
                $stmt_update->bindParam(':quantity', $quantity);
                $stmt_update->bindParam(':cart_item_id', $cart_item['cart_item_id']);
                $stmt_update->execute();

                if ($stmt_update->rowCount() > 0) {
                    return json_encode(['success' => 'Cart item updated successfully']);
                } else {
                    return json_encode(['error' => 'Failed to update cart item']);
                }
            } else {
                $cart_item_id = uniqid('CRIT');
                $sql_insert_item = "
                INSERT INTO Cart_Item (cart_item_id, cart_id, product_id, product_size_id, product_quantity) 
                VALUES (:cart_item_id, :cart_id, :product_id, :product_size_id, :quantity)
            ";
                $stmt_insert_item = $this->db->prepare($sql_insert_item);
                $stmt_insert_item->bindParam(':cart_item_id', $cart_item_id);
                $stmt_insert_item->bindParam(':cart_id', $cart_id);
                $stmt_insert_item->bindParam(':product_id', $product_id);
                $stmt_insert_item->bindParam(':product_size_id', $product_size_id);
                $stmt_insert_item->bindParam(':quantity', $quantity);
                $stmt_insert_item->execute();

                if ($stmt_insert_item->rowCount() > 0) {
                    return json_encode(['success' => 'Product added to cart successfully']);
                } else {
                    return json_encode(['error' => 'Failed to add product to cart']);
                }
            }
        } catch (PDOException $e) {
            return json_encode(['error' => 'Database error: ' . $e->getMessage()]);
        }
    }

    public function purchase($productId, $sizeId, $productPrice, $selectedChildren, $parentId, $paymentImage)
    {
        $fileName = $this->handleFileUpload($paymentImage);
        if (!$fileName) {
            return json_encode(['error' => 'Failed to upload receipt']);
        }

        try {
            $this->db->beginTransaction();

            foreach ($selectedChildren as $childId) {
                $orderQuery = "
                INSERT INTO Orders (order_id, parent_student_id, order_price) 
                VALUES (:order_id, (SELECT parent_student_id FROM Parent_Student WHERE parent_id = :parent_id AND student_id = :student_id), :order_price)
            ";
                $orderStmt = $this->db->prepare($orderQuery);
                $orderId = uniqid('ORD');
                $orderStmt->bindParam(':order_id', $orderId);
                $orderStmt->bindParam(':parent_id', $parentId);
                $orderStmt->bindParam(':student_id', $childId);
                $orderStmt->bindParam(':order_price', $productPrice);
                $orderStmt->execute();

                $orderItemQuery = "
                INSERT INTO Order_Item (order_item_id, order_id, product_id, product_size_id, product_quantity, order_subtotal) 
                VALUES (:order_item_id, :order_id, :product_id, :product_size_id, 1, :order_subtotal)
            ";
                $orderItemStmt = $this->db->prepare($orderItemQuery);
                $orderItemId = uniqid('OI');
                $orderItemStmt->bindParam(':order_item_id', $orderItemId);
                $orderItemStmt->bindParam(':order_id', $orderId);
                $orderItemStmt->bindParam(':product_id', $productId);
                $orderItemStmt->bindParam(':product_size_id', $sizeId);
                $orderItemStmt->bindParam(':order_subtotal', $productPrice);
                $orderItemStmt->execute();

                $paymentQuery = "
                INSERT INTO Payment (payment_id, parent_student_id, order_id, payment_amount, payment_status, payment_image) 
                VALUES (:payment_id, (SELECT parent_student_id FROM Parent_Student WHERE parent_id = :parent_id AND student_id = :student_id), :order_id, :payment_amount, 'pending', :payment_image)
            ";
                $paymentStmt = $this->db->prepare($paymentQuery);
                $paymentId = uniqid('PAY');
                $paymentStmt->bindParam(':payment_id', $paymentId);
                $paymentStmt->bindParam(':parent_id', $parentId);
                $paymentStmt->bindParam(':student_id', $childId);
                $paymentStmt->bindParam(':order_id', $orderId);
                $paymentStmt->bindParam(':payment_amount', $productPrice);
                $paymentStmt->bindParam(':payment_image', $fileName);
                $paymentStmt->execute();
            }

            $this->db->commit();
            return json_encode(['success' => true, 'message' => 'Purchase successful']);
        } catch (PDOException $e) {
            $this->db->rollBack();
            return json_encode(['error' => 'Purchase failed: ' . $e->getMessage()]);
        }
    }

    private function handleFileUpload($file)
    {
        if ($file['error'] === UPLOAD_ERR_NO_FILE) {
            return null;
        }

        $allowedfileExtensions = ['jpg', 'jpeg', 'png'];
        $fileName = $file['name'];
        $fileTmpPath = $file['tmp_name'];
        $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        if (in_array($fileExtension, $allowedfileExtensions)) {
            $newFileName = uniqid() . '.' . $fileExtension;
            $dest_path = 'uploads/receipts/' . $newFileName;

            if (move_uploaded_file($fileTmpPath, $dest_path)) {
                return $newFileName;
            } else {
                return null;
            }
        } else {
            return null;
        }
    }


    public function update_cart_item($cart_item_id, $quantity)
    {
        try {
            $sql = "UPDATE Cart_Item SET product_quantity = :quantity WHERE cart_item_id = :cart_item_id";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':quantity', $quantity);
            $stmt->bindParam(':cart_item_id', $cart_item_id);
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                return json_encode(['success' => 'Cart item updated successfully']);
            } else {
                return json_encode(['error' => 'Failed to update cart item']);
            }
        } catch (PDOException $e) {
            return json_encode(['error' => 'Database error: ' . $e->getMessage()]);
        }
    }

    // 删除购物车项目
    public function delete_cart_item($cart_item_id)
    {
        try {
            $sql = "DELETE FROM Cart_Item WHERE cart_item_id = :cart_item_id";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':cart_item_id', $cart_item_id);
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                return json_encode(['success' => 'Cart item deleted successfully']);
            } else {
                return json_encode(['error' => 'Failed to delete cart item']);
            }
        } catch (PDOException $e) {
            return json_encode(['error' => 'Database error: ' . $e->getMessage()]);
        }
    }

    // 获取购物车项目
    public function get_cart_items($parent_id)
    {
        try {
            $sql = "
                    SELECT 
                    ci.cart_item_id,
                    ci.product_quantity,
                    p.product_name,
                    p.product_price,
                    p.is_deleted,
                    pi.image_url,
                    p.product_price
                FROM Cart_Item ci
                JOIN Product p ON ci.product_id = p.product_id
                JOIN Product_Image pi ON p.product_id = pi.product_id AND pi.sort_order = 1
                WHERE ci.cart_id = (SELECT cart_id FROM Cart WHERE parent_id = :parent_id)
            ";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':parent_id', $parent_id);
            $stmt->execute();
            $cart_items = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return json_encode($cart_items);
        } catch (PDOException $e) {
            return json_encode(['error' => 'Database error: ' . $e->getMessage()]);
        }
    }

    // 删除选中的购物车项目
    public function delete_selected($cart_item_ids)
    {
        try {
            $ids = implode(',', array_map('intval', $cart_item_ids));
            $sql = "DELETE FROM Cart_Item WHERE cart_item_id IN ($ids)";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                return json_encode(['success' => 'Selected cart items deleted successfully']);
            } else {
                return json_encode(['error' => 'Failed to delete selected cart items']);
            }
        } catch (PDOException $e) {
            return json_encode(['error' => 'Database error: ' . $e->getMessage()]);
        }
    }

    // 清空购物车
    public function clear_cart($parent_id)
    {
        try {
            $sql = "
                DELETE FROM Cart_Item 
                WHERE cart_id = (SELECT cart_id FROM Cart WHERE parent_id = :parent_id)
            ";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':parent_id', $parent_id);
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                return json_encode(['success' => 'Cart cleared successfully']);
            } else {
                return json_encode(['error' => 'Failed to clear cart']);
            }
        } catch (PDOException $e) {
            return json_encode(['error' => 'Database error: ' . $e->getMessage()]);
        }
    }

    // 处理结账操作
    public function checkout($selected_item_ids)
    {
        try {
            $this->db->beginTransaction();

            // 假设每个购物车项目都与一个 Parent_Student 关联
            $parent_student_id = null;
            $total_price = 0;

            // 获取第一个选定的购物车项以确定 parent_student_id
            $sql = "
            SELECT ci.product_quantity, p.product_price, ps.parent_student_id
            FROM Cart_Item ci
            JOIN Product p ON ci.product_id = p.product_id
            JOIN Parent_Student ps ON ci.cart_id = ps.parent_id
            WHERE ci.cart_item_id = :cart_item_id
        ";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':cart_item_id', $selected_item_ids[0]);
            $stmt->execute();
            $first_item = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($first_item) {
                $parent_student_id = $first_item['parent_student_id'];
            } else {
                throw new Exception('Invalid cart item or parent_student_id not found.');
            }

            // 创建订单
            $order_id = uniqid('ORD');
            $sql_order = "
            INSERT INTO Orders (order_id, parent_student_id, order_datetime, order_price, is_deleted)
            VALUES (:order_id, :parent_student_id, NOW(), 0, 0)
        ";
            $stmt_order = $this->db->prepare($sql_order);
            $stmt_order->bindParam(':order_id', $order_id);
            $stmt_order->bindParam(':parent_student_id', $parent_student_id);
            $stmt_order->execute();

            // 插入订单项并计算总价
            foreach ($selected_item_ids as $cart_item_id) {
                $sql_item = "
                SELECT ci.product_id, ci.product_size_id, ci.product_quantity, p.product_price
                FROM Cart_Item ci
                JOIN Product p ON ci.product_id = p.product_id
                WHERE ci.cart_item_id = :cart_item_id
            ";
                $stmt_item = $this->db->prepare($sql_item);
                $stmt_item->bindParam(':cart_item_id', $cart_item_id);
                $stmt_item->execute();
                $item = $stmt_item->fetch(PDO::FETCH_ASSOC);

                if ($item) {
                    $order_item_id = uniqid('ORIT');
                    $order_subtotal = $item['product_price'] * $item['product_quantity'];
                    $total_price += $order_subtotal;

                    $sql_order_item = "
                    INSERT INTO Order_Item (order_item_id, order_id, product_id, product_size_id, product_quantity, order_subtotal, is_deleted)
                    VALUES (:order_item_id, :order_id, :product_id, :product_size_id, :product_quantity, :order_subtotal, 0)
                ";
                    $stmt_order_item = $this->db->prepare($sql_order_item);
                    $stmt_order_item->bindParam(':order_item_id', $order_item_id);
                    $stmt_order_item->bindParam(':order_id', $order_id);
                    $stmt_order_item->bindParam(':product_id', $item['product_id']);
                    $stmt_order_item->bindParam(':product_size_id', $item['product_size_id']);
                    $stmt_order_item->bindParam(':product_quantity', $item['product_quantity']);
                    $stmt_order_item->bindParam(':order_subtotal', $order_subtotal);
                    $stmt_order_item->execute();
                } else {
                    throw new Exception('Invalid cart item or product not found.');
                }
            }

            // 更新订单总价
            $sql_update_order = "
            UPDATE Orders
            SET order_price = :total_price
            WHERE order_id = :order_id
        ";
            $stmt_update_order = $this->db->prepare($sql_update_order);
            $stmt_update_order->bindParam(':total_price', $total_price);
            $stmt_update_order->bindParam(':order_id', $order_id);
            $stmt_update_order->execute();

            // 删除购物车项目或将其标记为已处理
            $ids = implode(',', array_map('intval', $selected_item_ids));
            $sql_delete_cart_items = "DELETE FROM Cart_Item WHERE cart_item_id IN ($ids)";
            $this->db->exec($sql_delete_cart_items);

            $this->db->commit();

            return json_encode(['success' => true, 'orderId' => $order_id]);
        } catch (Exception $e) {
            $this->db->rollBack();
            return json_encode(['error' => $e->getMessage()]);
        }
    }
}
