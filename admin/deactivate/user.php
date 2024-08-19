<?php

session_start();

include $_SERVER['DOCUMENT_ROOT'] . "/mahans/components/db_connect.php";

if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit();
}

$currentPage = basename($_SERVER['PHP_SELF']);

function getDeactivatedParents($pdo)
{
    $sql = "SELECT * FROM Parent WHERE is_deleted = 1";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$deactivated_parents = getDeactivatedParents($pdo);

function getAllStudents($pdo)
{
    $sql = "SELECT student_id, student_name FROM Student WHERE is_deleted = 0";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$all_students = getAllStudents($pdo);

function generateParentId()
{
    return uniqid('PAR');
}

function generateParentCartID()
{
    return uniqid('CART');
}

if (isset($_POST["submit"])) {
    $parentId = generateParentID();
    $name = $_POST["name"];
    $email = $_POST["email"];
    $password = $_POST["password"];
    $confirmPassword = $_POST["confirm_password"];
    $studentIds = $_POST["student_ids"] ?? [];
    $relationships = $_POST["relationships"] ?? [];
    $cartId = generateParentCartID();

    if (empty($name) || empty($email) || empty($password) || empty($confirmPassword)) {
        echo "<script>alert('Please fill in all required fields.');</script>";
    } elseif ($password !== $confirmPassword) {
        echo "<script>alert('Passwords do not match.');</script>";
    } else {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            echo "<script>alert('Invalid email format.');</script>";
        } else {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            try {
                $pdo->beginTransaction();

                $sql = "SELECT * FROM Parent WHERE parent_id = :parentId";
                $stmt = $pdo->prepare($sql);
                $stmt->bindParam(':parentId', $parentId);
                $stmt->execute();
                $existingParent = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($existingParent) {
                    $sql = "UPDATE Parent SET parent_name = :name, parent_email = :email, parent_password = :password WHERE parent_id = :parentId";
                } else {
                    $sql = "INSERT INTO Parent (parent_id, parent_name, parent_email, parent_password, is_deleted) 
                            VALUES (:parentId, :name, :email, :password, 0)";
                }

                $stmt = $pdo->prepare($sql);
                $stmt->bindParam(':parentId', $parentId);
                $stmt->bindParam(':name', $name);
                $stmt->bindParam(':email', $email);
                $stmt->bindParam(':password', $hashedPassword);
                $stmt->execute();

                $sql = "INSERT INTO Cart (cart_id, parent_id) VALUES (:cartId, :parentId)";
                $stmt = $pdo->prepare($sql);
                $stmt->bindParam(':cartId', $cartId);
                $stmt->bindParam(':parentId', $parentId);
                $stmt->execute();

                $sql = "DELETE FROM Parent_Student WHERE parent_id = :parentId";
                $stmt = $pdo->prepare($sql);
                $stmt->bindParam(':parentId', $parentId);
                $stmt->execute();

                foreach ($studentIds as $index => $studentId) {
                    $relationship = $relationships[$index];

                    $sql = "INSERT INTO Parent_Student (parent_id, student_id, relationship) 
                            VALUES (:parentId, :studentId, :relationship)";
                    $stmt = $pdo->prepare($sql);

                    $stmt->bindParam(':parentId', $parentId);
                    $stmt->bindParam(':studentId', $studentId);
                    $stmt->bindParam(':relationship', $relationship);
                    $stmt->execute();
                }

                $pdo->commit();

                header('Location: parent.php');
                exit();
            } catch (PDOException $e) {
                $pdo->rollBack();
                echo "<script>alert('Database error: " . $e->getMessage() . "');</script>";
            }
        }
    }
}

if (isset($_POST['deactivate'])) {
    $parentId = $_POST['parent_id'];

    $sql = "UPDATE Parent SET is_deleted = 1 WHERE parent_id = :parent_id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':parent_id', $parentId);

    try {
        $stmt->execute();
        header('Location: parent.php');
        exit();
    } catch (PDOException $e) {
        echo "<script>alert('Database error: " . $e->getMessage() . "');</script>";
    }
}
?>

<?php $pageTitle = "Deactivated Users - MIPS";
include $_SERVER['DOCUMENT_ROOT'] . "/mahans/components/admin_head.php"; ?>

<body>
    <?php include $_SERVER['DOCUMENT_ROOT'] . "/mahans/components/admin_header.php"; ?>
    <div class="container">
        <?php include $_SERVER['DOCUMENT_ROOT'] . "/mahans/components/admin_sidebar.php"; ?>
        <main class="parent">
            <div class="wrapper">
                <div class="title">
                    <div class="left">
                        <h1>Student Parents</h1>
                    </div>
                    <div class="right">

                    </div>
                </div>
                <div class="table-body">
                    <table>
                        <thead>
                            <tr>
                                <th><input type="checkbox"></th>
                                <th>Parent ID</th>
                                <th>Parent Name</th>
                                <th>Parent Email</th>
                                <th>Register Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($deactivated_parents as $parent) : ?>
                                <tr>
                                    <td><input type="checkbox" class="parent-checkbox" value="<?= htmlspecialchars($parent['parent_id']); ?>"></td>
                                    <td><?= htmlspecialchars($parent['parent_id']); ?></td>
                                    <td><?= htmlspecialchars($parent['parent_name']); ?></td>
                                    <td><?= htmlspecialchars($parent['parent_email']); ?></td>
                                    <td><?= htmlspecialchars($parent['register_datetime']); ?></td>
                                    <td>
                                        <form action="" method="POST" style="display:inline;" onsubmit="return showRecoverConfirmDialog(event);">
                                            <input type="hidden" name="parent_id" value="<?= htmlspecialchars($parent['parent_id']); ?>">
                                            <input type="hidden" name="recover" value="true">
                                            <button type="submit" class="recover-parent-btn"><i class="bi bi-arrow-clockwise"></i> Recover</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>

                    </table>
                </div>
            </div>
        </main>
    </div>
    <?php include $_SERVER['DOCUMENT_ROOT'] . "/mahans/components/deactivate_confirm_dialog.php"; ?>
    <script src="/mahans/javascript/admin.js"></script>
    <script>
        document.getElementById('add-child-btn').addEventListener('click', function() {
            const childInfoDiv = document.createElement('div');
            childInfoDiv.classList.add('child-info');
            childInfoDiv.innerHTML = `
            <label>Student:</label>
            <select name="student_ids[]" required>
                <option value="">Select a Student</option>
                <?php foreach ($all_students as $student) : ?>
                    <option value="<?= htmlspecialchars($student['student_id']); ?>">
                        <?= htmlspecialchars($student['student_name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <label>Relationship:</label>
            <select name="relationships[]" required>
                <option value="father">Father</option>
                <option value="mother">Mother</option>
                <option value="brother">Brother</option>
                <option value="sister">Sister</option>
                <option value="grandparent">Grandparent</option>
                <option value="relative">Relative</option>
                <option value="guardian">Guardian</option>
            </select>
        `;
            document.getElementById('children-info').appendChild(childInfoDiv);
        });

        document.querySelectorAll('.edit-parent-btn').forEach(button => {
            button.addEventListener('click', function() {
                const parentId = this.dataset.parentId;

                fetch(`ajax.php?action=get_parent&parent_id=${parentId}`)
                    .then(response => response.json())
                    .then(parent => {
                        if (parent.error) {
                            alert(parent.error);
                        } else {
                            document.querySelector('#add-edit-data [name="parent_id"]').value = parent.parent_id;
                            document.querySelector('#add-edit-data [name="name"]').value = parent.parent_name;
                            document.querySelector('#add-edit-data [name="email"]').value = parent.parent_email;
                            document.querySelector('#add-edit-data h1').textContent = "Edit Parent";
                            document.getElementById('add-edit-data').showModal();
                        }
                    })
                    .catch(error => {
                        console.error('Error fetching parent data:', error);
                        alert('Failed to load parent data.');
                    });
            });
        });

        document.querySelector('.cancel').addEventListener('click', function() {
            document.getElementById('add-edit-data').close();
        });
    </script>
</body>

</html>