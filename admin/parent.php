<?php

session_start();

include "../components/db_connect.php";

if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit();
}

$currentPage = basename($_SERVER['PHP_SELF']);

function getAllParents($pdo)
{
    $sql = "SELECT * FROM Parent WHERE is_deleted = 0";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$all_parents = getAllParents($pdo);

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
    $prefix = "PR";
    $randomString = bin2hex(random_bytes(4));
    return $prefix . $randomString;
}

if (isset($_POST["submit"])) {
    $parentId = generateParentID();
    $name = $_POST["name"];
    $email = $_POST["email"];
    $password = $_POST["password"];
    $confirmPassword = $_POST["confirm_password"];
    $studentIds = $_POST["student_ids"] ?? [];
    $relationships = $_POST["relationships"] ?? [];

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

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mahans Parents - MIPS</title>
    <link rel="icon" type="image/x-icon" href="../images/Mahans_IPS_icon.png">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
    <script src="https://code.jquery.com/jquery-3.7.1.js" integrity="sha256-eKhayi8LEQwp4NKxN+CfCh+3qOVUtJn3QNZ0TciWLP4=" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="../css/base.css">
    <link rel="stylesheet" href="../css/common.css">
    <link rel="stylesheet" href="../css/admin.css">
</head>

<body>
    <?php include "../components/admin_header.php"; ?>
    <div class="container">
        <?php include "../components/admin_sidebar.php"; ?>
        <main class="parent">
            <div class="wrapper">
                <div class="title">
                    <div class="left">
                        <h1>Mahans Parents</h1>
                    </div>
                    <div class="right">
                        <button class="btn btn-outline-primary" id="open-popup"><i class="bi bi-person-fill-add"></i>Add New Parent</button>
                        <?php
                        try {
                            $countQuery = "SELECT COUNT(*) FROM Parent WHERE is_deleted = 0";
                            $stmt = $pdo->prepare($countQuery);
                            $stmt->execute();
                            $count = $stmt->fetchColumn();

                            echo "<p>Total $count Parent(s)</p>";
                        } catch (PDOException $e) {
                            echo "<script>alert('Database error: " . $e->getMessage() . "');</script>";
                        }
                        ?>
                    </div>
                </div>
                <div class="table-body">
                    <table>
                        <thead>
                            <tr>
                                <th>Parent ID</th>
                                <th>Parent Name</th>
                                <th>Parent Email</th>
                                <th>Register Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($all_parents as $parent) : ?>
                                <tr>
                                    <td><?= htmlspecialchars($parent['parent_id']); ?></td>
                                    <td><?= htmlspecialchars($parent['parent_name']); ?></td>
                                    <td><?= htmlspecialchars($parent['parent_email']); ?></td>
                                    <td><?= htmlspecialchars($parent['register_datetime']); ?></td>
                                    <td>
                                        <form action="" method="POST" style="display:inline;">
                                            <input type="hidden" name="parent_id" value="<?= htmlspecialchars($parent['parent_id']); ?>">
                                            <input type="hidden" name="deactivate" value="true">
                                            <button type="submit" class="delete-parent-btn"><i class="bi bi-x-square-fill"></i></button>
                                        </form>
                                        <button type="button" class="edit-parent-btn" data-parent-id="<?= htmlspecialchars($parent['parent_id']); ?>"><i class="bi bi-pencil-square"></i></button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>
    <dialog id="add-edit-data">
        <h1>Add/Edit Parent</h1>
        <form action="" method="post">
            <input type="hidden" name="parent_id" value="">
            <div class="input-container">
                <div class="input-field">
                    <h2>Parent Name<sup>*</sup></h2>
                    <input type="text" name="name" value="" required>
                </div>
                <p>Please enter the parent's full name.</p>
            </div>
            <div class="input-container">
                <div class="input-field">
                    <h2>Parent Email<sup>*</sup></h2>
                    <input type="email" name="email" value="" required>
                </div>
                <p>Please enter the parent's email address.</p>
            </div>
            <div class="input-container">
                <div class="input-field">
                    <h2>Password<sup>*</sup></h2>
                    <input type="password" name="password" required>
                </div>
                <p>Please enter a secure password.</p>
            </div>
            <div class="input-field">
                <h2>Confirm Password<sup>*</sup></h2>
                <input type="password" name="confirm_password" required>
                <p>Please confirm the password.</p>
            </div>
            <div class="input-container">
                <h2>Children Information</h2>
                <div id="children-info">
                    <div class="child-info">
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
                    </div>
                    <button type="button" id="add-child-btn">Add Another Child</button>
                </div>
            </div>
            <div class="controls">
                <button type="button" class="cancel">Cancel</button>
                <button type="reset">Clear</button>
                <button type="submit" name="submit">Publish</button>
            </div>
        </form>
    </dialog>
    <dialog id="delete-confirm-dialog">
        <form method="dialog">
            <h1>Parent will be Deactivated!</h1>
            <label>Are you sure to proceed?</label>
            <div class="controls">
                <button value="cancel" class="cancel">Cancel</button>
                <button value="confirm" class="deactivate">Deactivate</button>
            </div>
        </form>
    </dialog>
    <script src="../javascript/admin.js"></script>
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