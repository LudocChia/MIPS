<?php

session_start();

include "../components/db_connect.php";

function getAllParents($pdo)
{
    $sql = "SELECT * FROM Parent WHERE is_deleted = 0";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$all_parents = getAllParents($pdo);

function generateParentId()
{
    $prefix = "PR";
    $randomString = bin2hex(random_bytes(4));
    return $prefix . $randomString;
}

if (isset($_POST["submit"])) {
    $parentId = generateParentId();
    $name = $_POST["name"];
    $email = $_POST["email"];
    $password = $_POST["password"];
    $confirmPassword = $_POST["confirm_password"];
    $studentIds = $_POST["student_ids"];
    $relationships = $_POST["relationships"];

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

                // Insert parent information
                $sql = "INSERT INTO Parent (parent_id, parent_name, parent_email, parent_password, is_deleted) 
                        VALUES (:parentId, :name, :email, :password, 0)";
                $stmt = $pdo->prepare($sql);
                $stmt->bindParam(':parentId', $parentId);
                $stmt->bindParam(':name', $name);
                $stmt->bindParam(':email', $email);
                $stmt->bindParam(':password', $hashedPassword);
                $stmt->execute();

                // Insert each child information
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
    <title>All Parents - Mahans School</title>
    <link rel="icon" type="image/x-icon" href="../images/Mahans_internation_primary_school_logo.png">
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
        <aside>
            <button id="close-btn">
                <i class="bi bi-x"></i>
            </button>
            <div class="sidebar">
                <ul>
                    <li>
                        <a href="index.php"><i class="bi bi-grid-1x2-fill"></i>
                            <h4>Dashboard</h4>
                        </a>
                    </li>
                    <li>
                        <a href="#" class="bookshop-btn">
                            <i class="bi bi-shop-window"></i>
                            <h4>Bookshop</h4>
                            <i class="bi bi-chevron-down first"></i>
                        </a>
                        <ul class="bookshop-show">
                            <li><a href="mainCategory.php"><i class="bi bi-tags-fill"></i>
                                    <h4>Main Category</h4>
                                </a>
                            </li>
                            <li><a href="subcategory.php"><i class="bi bi-tag-fill"></i>
                                    <h4>Subcategory</h4>
                                </a>
                            </li>
                            <li><a href="size.php"><i class="bi bi-aspect-ratio-fill"></i>
                                    <h4>Product Size</h4>
                                </a>
                            </li>
                            <li><a href="product.php"><i class="bi bi-box-seam-fill"></i>
                                    <h4>All Product</h4>
                                </a>
                            </li>
                        </ul>
                    </li>
                    <li>
                        <a href="#" class="user-btn">
                            <i class="bi bi-person-fill"></i>
                            <h4>User Type</h4>
                            <i class="bi bi-chevron-down second"></i>
                        </a>
                        <ul class="user-show">
                            <li><a href="admin.php"><i class="bi bi-person-fill-gear"></i>
                                    <h4>All Admin</h4>
                                </a>
                            </li>
                            <li><a href="teacher.php"><i class="bi bi-mortarboard-fill"></i>
                                    <h4>All Teacher</h4>
                                </a>
                            </li>
                            <li>
                                <a href="parent.php" class="active"><i class="bi bi-people-fill"></i>
                                    <h4>All Parent</h4>
                                </a>
                            </li>
                            <li>
                                <a href="student.php"><i class="bi bi-people-fill"></i>
                                    <h4>All Student</h4>
                                </a>
                            </li>
                        </ul>
                    </li>
                    <li>
                        <a href="order.php">
                            <i class="bi bi-receipt"></i>
                            <h4>Order</h4>
                        </a>
                    </li>
                </ul>
            </div>
        </aside>
        <!-- END OF ASIDE -->
        <main class="parent">
            <div class="wrapper">
                <div class="title">
                    <div class="left">
                        <h1>Mahans Parents</h1>
                    </div>
                    <div class="right">
                        <button id="open-popup" class="btn btn-outline"><i class="bi bi-person-fill-add"></i>Add New Parent</button>
                        <?php
                        try {
                            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
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
                                <th>
                                    <h3>Parent ID</h3>
                                </th>
                                <th>
                                    <h3>Parent Name</h3>
                                </th>
                                <th>
                                    <h3>Parent Email</h3>
                                </th>
                                <th>
                                    <h3>Parent Register Date</h3>
                                </th>
                                <th>
                                    <h3>Actions</h3>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($all_parents as $parent) : ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($parent['parent_id']); ?></td>
                                    <td><?php echo htmlspecialchars($parent['parent_name']); ?></td>
                                    <td><?php echo htmlspecialchars($parent['parent_email']); ?></td>
                                    <td><?php echo htmlspecialchars($parent['register_datetime']); ?></td>
                                    <td>
                                        <form action="" method="POST" style="display:inline;" onsubmit="return showDeleteConfirmDialog(event);">
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
        <form action="" method="post" enctype="multipart/form-data">
            <input type="hidden" name="parent_id" value="">
            <div class="input-field">
                <h2>Parent Name<sup>*</sup></h2>
                <input type="text" name="name" value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>" required>
                <p>Please enter the parent's full name.</p>
            </div>
            <div class="input-field">
                <h2>Parent Email<sup>*</sup></h2>
                <input type="email" name="email" value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" required>
                <p>Please enter the parent's email address.</p>
            </div>
            <div class="input-field">
                <h2>Password<sup>*</sup></h2>
                <input type="password" name="password" required>
                <p>Please enter a secure password.</p>
            </div>
            <div class="input-field">
                <h2>Confirm Password<sup>*</sup></h2>
                <input type="password" name="confirm_password" required>
                <p>Please confirm the password.</p>
            </div>
            <div class="input-field">
                <h2>Children Information</h2>
                <div id="children-info">
                    <!-- Placeholder for dynamic child information input fields -->
                    <div class="child-info">
                        <label>Student ID:</label>
                        <input type="text" name="student_ids[]" required>
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
                </div>
                <button type="button" id="add-child-btn">Add Another Child</button>
            </div>
            <div class="input-field controls">
                <button type="button" class="cancel">Cancel</button>
                <button type="reset">Clear</button>
                <button type="submit" name="submit">Publish</button>
            </div>
        </form>
    </dialog>
    <dialog id="delete-confirm-dialog">
        <form method="dialog">
            <h1>This Parent will be Deactivated!</h1>
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
            <label>Student ID:</label>
            <input type="text" name="student_ids[]" required>
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