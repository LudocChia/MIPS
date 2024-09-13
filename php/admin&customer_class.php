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
        if (strlen($new_password) < 6) {
            return ['error' => 'Password must be at least 6 characters long.'];
        }
        if (!preg_match('/[0-9]/', $new_password)) {
            return ['error' => 'Password must contain at least one number.'];
        }
        if (!preg_match('/[!@#$%^&*(),.?":{}|<>]/', $new_password)) {
            return ['error' => 'Password must contain at least one special character.'];
        }
        if (preg_match('/^\s|\s$/', $new_password)) {
            return ['error' => 'Password cannot start or end with a space.'];
        }

        $table = ($user_type === 'admin') ? 'Admin' : 'Parent';
        $userIdColumn = ($user_type === 'admin') ? 'admin_id' : 'parent_id';

        $sql = "SELECT {$user_type}_password FROM $table WHERE $userIdColumn = :user_id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if (password_verify($new_password, $result["{$user_type}_password"])) {
            return ['error' => 'Cannot reuse previous passwords.'];
        }

        $hashedPassword = password_hash($new_password, PASSWORD_DEFAULT);

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

    public function activate_account($user_id, $user_type, $user_name, $new_password, $confirm_password)
    {
        $passwordValidation = $this->validate_password($new_password, $confirm_password);
        if ($passwordValidation !== true) {
            return json_encode(['error' => $passwordValidation]);
        }

        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

        $table = ($user_type === 'admin') ? 'Admin' : 'Parent';
        $userIdColumn = ($user_type === 'admin') ? 'admin_id' : 'parent_id';
        $userNameColumn = ($user_type === 'admin') ? 'admin_name' : 'parent_name';
        $passwordColumn = ($user_type === 'admin') ? 'admin_password' : 'parent_password';

        try {
            $sql = "UPDATE $table SET $userNameColumn = :user_name, $passwordColumn = :password, status = 0 WHERE $userIdColumn = :user_id";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':user_name', $user_name);
            $stmt->bindParam(':password', $hashed_password);
            $stmt->bindParam(':user_id', $user_id);
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                $_SESSION['user_name'] = $user_name;
                $_SESSION['user_status'] = 0;
                $redirectUrl = ($user_type === 'admin') ? '/mips/admin/' : '/mips';
                return json_encode(['success' => true, 'redirect' => $redirectUrl]);
            } else {
                return json_encode(['error' => 'Failed to activate account']);
            }
        } catch (PDOException $e) {
            return json_encode(['error' => 'Database error: ' . $e->getMessage()]);
        }
    }

    private function validate_password($password, $confirm_password)
    {
        if ($password !== $confirm_password) {
            return 'Passwords do not match';
        }
        if (strlen($password) < 6) {
            return 'Password must be at least 6 characters long';
        }
        if (preg_match('/^\s|\s$/', $password)) {
            return 'Password cannot begin or end with a space';
        }
        if (!preg_match('/[0-9]/', $password) || !preg_match('/[\W]/', $password)) {
            return 'Password must include at least one number and one special symbol';
        }
        return true;
    }
}
