<?php
class Action
{
    private $db;

    public function __construct()
    {
        session_start();
        include $_SERVER['DOCUMENT_ROOT'] . '/mips/components/db_connect.php';
        $this->db = $pdo;
    }

    // Order Functions
    public function get_orders($parent_id, $status)
    {
        try {
            $sql = "
            SELECT o.*, p.payment_status 
            FROM Orders o 
            JOIN Payment p ON o.order_id = p.order_id
            WHERE o.parent_student_id = (SELECT parent_student_id FROM Parent_Student WHERE parent_id = :parent_id LIMIT 1)";

            if ($status !== 'all') {
                $sql .= " AND p.payment_status = :status";
            }
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':parent_id', $parent_id);
            if ($status !== 'all') {
                $stmt->bindParam(':status', $status);
            }
            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return json_encode(['success' => true, 'data' => $result]);
        } catch (PDOException $e) {
            return json_encode(['error' => 'Database error: ' . $e->getMessage()]);
        }
    }

    public function add_to_cart($parent_id, $product_id, $quantity, $product_size_id)
    {
        try {
            $sql_cart = "SELECT cart_id FROM Cart WHERE parent_id = :parent_id";
            $stmt_cart = $this->db->prepare($sql_cart);
            $stmt_cart->bindParam(':parent_id', $parent_id);
            $stmt_cart->execute();
            $cart = $stmt_cart->fetch(PDO::FETCH_ASSOC);

            $cart_id = $cart['cart_id'];

            $sql_cart_item = "SELECT cart_item_id, product_quantity FROM Cart_Item WHERE cart_id = :cart_id AND product_id = :product_id";

            if ($product_size_id === null || $product_size_id === '') {
                $sql_cart_item .= " AND product_size_id IS NULL";
            } else {
                $sql_cart_item .= " AND product_size_id = :product_size_id";
            }

            $stmt_cart_item = $this->db->prepare($sql_cart_item);
            $stmt_cart_item->bindParam(':cart_id', $cart_id);
            $stmt_cart_item->bindParam(':product_id', $product_id);
            if ($product_size_id !== null && $product_size_id !== '') {
                $stmt_cart_item->bindParam(':product_size_id', $product_size_id);
            }
            $stmt_cart_item->execute();
            $cart_item = $stmt_cart_item->fetch(PDO::FETCH_ASSOC);

            if ($cart_item) {
                $sql_update = "UPDATE Cart_Item SET product_quantity = product_quantity + :quantity WHERE cart_item_id = :cart_item_id";
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
                $sql_insert_item = "INSERT INTO Cart_Item (cart_item_id, cart_id, product_id, product_size_id, product_quantity) 
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

    public function purchase($selectedProducts, $selectedSizes, $totalItemQuantities, $totalPriceItems, $totalPrice, $selectedChildren, $parentId, $paymentImage)
    {
        $fileName = $this->handleFileUpload($paymentImage);
        if (!$fileName) {
            return json_encode(['error' => 'Failed to upload receipt']);
        }

        try {
            $this->db->beginTransaction();

            $orderQuery = "INSERT INTO Orders (order_id, parent_id, order_price) 
                            VALUES (:order_id, (SELECT parent_id FROM Parent_Student WHERE parent_id = :parent_id LIMIT 1), :order_price)";
            $orderStmt = $this->db->prepare($orderQuery);
            $orderId = uniqid('ORD');
            $orderStmt->bindParam(':order_id', $orderId);
            $orderStmt->bindParam(':parent_id', $parentId);
            $orderStmt->bindParam(':order_price', $totalPrice);
            $orderStmt->execute();

            foreach ($selectedProducts as $index => $productId) {
                $orderItemQuery = "INSERT INTO Order_Item (order_item_id, order_id, product_id, product_size_id, product_quantity, order_subtotal) 
                                    VALUES (:order_item_id, :order_id, :product_id, :product_size_id, :product_quantity, :order_subtotal)";
                $orderItemStmt = $this->db->prepare($orderItemQuery);
                $orderItemId = uniqid('OI');
                $currentSizeId = $selectedSizes[$index];
                $currentQuantity = $totalItemQuantities[$index];
                $currentSubtotal = $totalPriceItems[$index];
                $orderItemStmt->bindParam(':order_item_id', $orderItemId);
                $orderItemStmt->bindParam(':order_id', $orderId);
                $orderItemStmt->bindParam(':product_id', $productId);
                $orderItemStmt->bindParam(':product_size_id', $currentSizeId);
                $orderItemStmt->bindParam(':product_quantity', $currentQuantity);
                $orderItemStmt->bindParam(':order_subtotal', $currentSubtotal);
                $orderItemStmt->execute();

                $childrenIds = explode(',', $selectedChildren[$index]);
                foreach ($childrenIds as $childId) {
                    $orderItemStudentQuery = "INSERT INTO Order_Item_Student (order_item_student_id, order_item_id, student_id)
                                                VALUES (:order_item_student_id, :order_item_id, :student_id)";
                    $orderItemStudentStmt = $this->db->prepare($orderItemStudentQuery);
                    $orderItemStudentId = uniqid('OIS');
                    $orderItemStudentStmt->bindParam(':order_item_student_id', $orderItemStudentId);
                    $orderItemStudentStmt->bindParam(':order_item_id', $orderItemId);
                    $orderItemStudentStmt->bindParam(':student_id', $childId);
                    $orderItemStudentStmt->execute();
                }
            }

            $paymentQuery = "INSERT INTO Payment (payment_id, parent_id, order_id, payment_amount, payment_status, payment_image) 
                                VALUES (:payment_id, :parent_id, :order_id, :payment_amount, 'pending', :payment_image)";
            $paymentStmt = $this->db->prepare($paymentQuery);
            $paymentId = uniqid('PAY');
            $paymentStmt->bindParam(':payment_id', $paymentId);
            $paymentStmt->bindParam(':parent_id', $parentId);
            $paymentStmt->bindParam(':order_id', $orderId);
            $paymentStmt->bindParam(':payment_amount', $totalPrice);
            $paymentStmt->bindParam(':payment_image', $fileName);
            $paymentStmt->execute();

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

    public function get_cart_items($parent_id)
    {
        try {
            $sql = "SELECT ci.cart_item_id,
                        ci.product_quantity,
                        ci.product_size_id,
                        ps.size_name AS product_size,
                        p.product_id,
                        p.product_name,
                        p.product_price,
                        p.status,
                        pi.image_url,
                        GROUP_CONCAT(DISTINCT s.student_id, ':', s.student_name) AS children
                    FROM Cart_Item ci
                    JOIN Product p ON ci.product_id = p.product_id
                    JOIN Product_Image pi ON p.product_id = pi.product_id AND pi.sort_order = 1
                    LEFT JOIN Parent_Student ps2 ON ps2.parent_id = :parent_id
                    LEFT JOIN Student s ON ps2.student_id = s.student_id
                    LEFT JOIN Product_Size psz ON ci.product_size_id = psz.product_size_id
                    LEFT JOIN Sizes ps ON psz.size_id = ps.size_id
                    WHERE ci.cart_id = (SELECT cart_id FROM Cart WHERE parent_id = :parent_id)
                    GROUP BY ci.cart_item_id";

            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':parent_id', $parent_id);
            $stmt->execute();
            $cart_items = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return json_encode($cart_items);
        } catch (PDOException $e) {
            return json_encode(['error' => 'Database error: ' . $e->getMessage()]);
        }
    }

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
}
