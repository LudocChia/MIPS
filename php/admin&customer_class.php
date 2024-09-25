<?php
class Action
{
    private $db;

    public function __construct()
    {
        include $_SERVER['DOCUMENT_ROOT'] . "/mips/components/db_connect.php";
        $this->db = $pdo;
    }

    public function login($email, $password, $userType, $currentPage = null, $productId = null)
    {

        try {
            if ($userType === 'admin') {
                $sql = "SELECT * FROM Admin WHERE admin_email = :email AND status IN (0, -1)";
                $stmt = $this->db->prepare($sql);
            } else if ($userType === 'parent') {
                $sql = "SELECT * FROM Parent WHERE parent_email = :email AND status IN (0, -1)";
                $stmt = $this->db->prepare($sql);
            } else {
                return json_encode(['error' => 'Invalid user type']);
            }

            $stmt->bindParam(':email', $email);
            $stmt->execute();

            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user) {
                $passwordColumn = ($userType === 'admin') ? 'admin_password' : 'parent_password';

                if (password_verify($password, $user[$passwordColumn])) {
                    if ($userType === 'admin') {
                        $_SESSION['admin_id'] = $user['admin_id'];
                        $_SESSION['admin_name'] = $user['admin_name'];
                        $_SESSION['admin_email'] = $user['admin_email'];
                        $_SESSION['admin_image'] = !empty($user['admin_image']) ? $user['admin_image'] : '/mips/images/default_profile.png';
                        $_SESSION['admin_status'] = $user['status'];
                        $_SESSION['admin_type'] = $userType;
                    } else {
                        $_SESSION['user_id'] = $user['parent_id'];
                        $_SESSION['user_name'] = $user['parent_name'];
                        $_SESSION['user_email'] = $user['parent_email'];
                        $_SESSION['user_image'] = !empty($user['parent_image']) ? $user['parent_image'] : '/mips/images/default_profile.png';
                        $_SESSION['user_status'] = $user['status'];
                        $_SESSION['user_type'] = $userType;
                    }

                    if ($user['status'] == -1) {
                        $redirectUrl = ($userType === 'admin') ? '/mips/admin/activate.php' : '/mips/activate.php';
                        return json_encode(['success' => true, 'redirect' => $redirectUrl]);
                    }

                    $redirectUrl = ($userType === 'admin') ? '/mips/admin/' : ($productId ? "/mips/item.php?pid=" . $productId : ($currentPage ?? '/mips'));
                    return json_encode(['success' => true, 'redirect' => $redirectUrl]);
                } else {
                    return json_encode(['success' => false, 'error' => 'Invalid email or password.']);
                }
            } else {
                return json_encode(['success' => false, 'error' => 'Invalid email or password.']);
            }
        } catch (PDOException $e) {
            return json_encode(['error' => 'Database error: ' . $e->getMessage()]);
        }
    }

    public function logout($userType)
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if ($userType === 'admin') {
            unset($_SESSION['admin_id']);
            unset($_SESSION['admin_name']);
            unset($_SESSION['admin_email']);
            unset($_SESSION['admin_image']);
            unset($_SESSION['admin_status']);
            unset($_SESSION['admin_type']);
        } else if ($userType === 'parent') {
            unset($_SESSION['user_id']);
            unset($_SESSION['user_name']);
            unset($_SESSION['user_email']);
            unset($_SESSION['user_image']);
            unset($_SESSION['user_status']);
            unset($_SESSION['user_type']);
        }

        if (empty($_SESSION)) {
            session_destroy();
        }

        return json_encode(['success' => true, 'message' => 'Successfully logged out']);
    }


    public function update_password($user_id, $user_type, $new_password, $confirm_password)
    {
        $passwordValidation = $this->validate_password($new_password, $confirm_password);
        if ($passwordValidation !== true) {
            return ['error' => $passwordValidation];
        }

        $table = ($user_type === 'admin') ? 'Admin' : 'Parent';
        $userIdColumn = ($user_type === 'admin') ? 'admin_id' : 'parent_id';
        $passwordColumn = ($user_type === 'admin') ? 'admin_password' : 'parent_password';

        $sql = "SELECT {$passwordColumn} FROM $table WHERE $userIdColumn = :user_id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if (password_verify($new_password, $result[$passwordColumn])) {
            return ['error' => 'Cannot reuse previous passwords.'];
        }

        $hashedPassword = password_hash($new_password, PASSWORD_DEFAULT);

        $sql = "UPDATE $table SET $passwordColumn = :new_password, status = 0 WHERE $userIdColumn = :user_id";
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
        if (!preg_match("/^[a-zA-Z\s]+$/", $user_name)) {
            return json_encode(['error' => 'Name must only contain alphabetic characters']);
        }

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
            $sql = "UPDATE $table 
                    SET $userNameColumn = :user_name, $passwordColumn = :password, status = 0, created_at = NOW() 
                    WHERE $userIdColumn = :user_id";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':user_name', $user_name);
            $stmt->bindParam(':password', $hashed_password);
            $stmt->bindParam(':user_id', $user_id);
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                $_SESSION[($user_type === 'admin') ? 'admin_name' : 'user_name'] = $user_name;
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
