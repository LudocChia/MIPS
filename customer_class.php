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
}
