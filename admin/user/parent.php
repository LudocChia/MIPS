<?php

$database_table = "Parent";
$rows_per_page = 12;
include $_SERVER['DOCUMENT_ROOT'] . "/mips/php/admin.php";

function getAllParents($pdo, $start, $rows_per_page)
{
    $sql = "SELECT * FROM Parent WHERE status = 0 LIMIT :start, :rows_per_page";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':start', $start, PDO::PARAM_INT);
    $stmt->bindParam(':rows_per_page', $rows_per_page, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$all_parents = getAllParents($pdo, $start, $rows_per_page);

function getAllStudents($pdo)
{
    $sql = "SELECT student_id, student_name FROM Student WHERE status = 0";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$all_students = getAllStudents($pdo);

function generateParentId()
{
    return uniqid('PR');
}

function generateParentCartID()
{
    return uniqid('CR');
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
                    $sql = "INSERT INTO Parent (parent_id, parent_name, parent_email, parent_password, status) 
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

                header('Location:' . $_SERVER['PHP_SELF']);
                exit();
            } catch (PDOException $e) {
                $pdo->rollBack();
                echo "<script>alert('Database error: " . $e->getMessage() . "');</script>";
            }
        }
    }
}

$pageTitle = "Parent Management - MIPS";
include $_SERVER['DOCUMENT_ROOT'] . "/mips/components/admin_head.php"; ?>

<body>
    <?php include $_SERVER['DOCUMENT_ROOT'] . "/mips/components/admin_header.php"; ?>
    <div class="container">
        <?php include $_SERVER['DOCUMENT_ROOT'] . "/mips/components/admin_sidebar.php"; ?>
        <main class="parent">
            <div class="wrapper">
                <div class="title">
                    <div class="left">
                        <h1>Students Parent</h1>
                    </div>
                    <div class="right">
                        <button class="btn btn-outline-primary" id="open-popup"><i class="bi bi-person-fill-add"></i>Add New Parent</button>
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
                                        <form action="" method="POST" style="display:inline;" onsubmit="return showDeactivateConfirmDialog(event);">
                                            <input type="hidden" name="parent_id" value="<?= htmlspecialchars($parent['parent_id']); ?>">
                                            <input type="hidden" name="action" value="deactivate_parent">
                                            <button type="submit" class="delete-parent-btn"><i class="bi bi-x-square"></i></button>
                                        </form>
                                        <button type="button" class="edit-parent-btn" data-parent-id="<?= htmlspecialchars($parent['parent_id']); ?>"><i class="bi bi-pencil-square"></i></button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php include $_SERVER['DOCUMENT_ROOT'] . "/mips/components/pagination.php"; ?>
            </div>
        </main>
    </div>
    <dialog id="add-edit-data">
        <div class="title">
            <div class="left">
                <h1>Add Parent</h1>
            </div>
            <div class="right">
                <button id="cancel"><i class="bi bi-x-circle"></i></button>
            </div>
        </div>
        <form action="" method="post">
            <input type="hidden" name="parent_id" value="">
            <div class="input-container">
                <h2>Parent Name<sup>*</sup></h2>
                <div class="input-field">
                    <input type="text" name="name" value="" required>
                </div>
                <p>Please enter the parent's full name.</p>
            </div>
            <div class="input-container">
                <h2>Parent Email<sup>*</sup></h2>
                <div class="input-field">
                    <input type="email" name="email" value="" required>
                </div>
                <p>Please enter the parent's email address.</p>
            </div>
            <div class="input-container">
                <h2>Password<sup>*</sup></h2>
                <div class="input-field">
                    <input type="password" name="password" required>
                </div>
                <p>Please enter a secure password.</p>
            </div>
            <div class="input-container">
                <h2>Confirm Password<sup>*</sup></h2>
                <div class="input-field">
                    <input type="password" name="confirm_password" required>
                    <p>Please confirm the password.</p>
                </div>
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
            <div class="input-container controls">
                <button type="button" class="cancel">Cancel</button>
                <button type="reset">Clear</button>
                <button type="submit" name="submit">Publish</button>
            </div>
        </form>
    </dialog>
    <?php include $_SERVER['DOCUMENT_ROOT'] . "/mips/components/confirm_dialog.php"; ?>
    <script src="/mips/javascript/common.js"></script>
    <script src="/mips/javascript/admin.js"></script>
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
                fetch(`/mips/admin/ajax.php?action=get_parent`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded'
                        },
                        body: `parent_id=${encodeURIComponent(parentId)}`
                    })
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
    </script>
</body>

</html>