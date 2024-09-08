<?php
class Action
{
    private $db;

    public function __construct()
    {
        include $_SERVER['DOCUMENT_ROOT'] . "/mips/components/db_connect.php";
        $this->db = $pdo;
    }

    public function update_password($user_id, $user_type, $new_password)
    {
        $hashedPassword = password_hash($new_password, PASSWORD_DEFAULT);
        $table = ($user_type === 'admin') ? 'Admin' : 'Parent';
        $userIdColumn = ($user_type === 'admin') ? 'admin_id' : 'parent_id';

        $sql = "UPDATE $table SET {$user_type}_password = :new_password, status = 0 WHERE $userIdColumn = :user_id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':new_password', $hashedPassword);
        $stmt->bindParam(':user_id', $user_id);

        try {
            $stmt->execute();
            $_SESSION["user_status"] = 0;
            $redirectUrl = ($user_type === 'admin') ? '/mips/admin/' : '/mips';
            return ['success' => true, 'redirect' => $redirectUrl];
        } catch (PDOException $e) {
            return ['error' => 'Failed to update password: ' . $e->getMessage()];
        }
    }
}
