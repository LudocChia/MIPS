<?php

session_start();

include "../components/db_connect.php";

if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit();
}

$currentPage = basename($_SERVER['PHP_SELF']);

function getAllGrades($pdo)
{
    $sql = "SELECT * FROM Grade WHERE is_deleted = 0";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$all_grades = getAllGrades($pdo);

$msg = [];
if (isset($_POST["submit"])) {
    $className = $_POST["class_name"];
    $gradeId = $_POST["grade_id"];

    $sql = "INSERT INTO Class (class_name, grade_id, is_deleted) VALUES (:className, :gradeId, 0)";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':className', $className);
    $stmt->bindParam(':gradeId', $gradeId);
    try {
        $stmt->execute();
        header('Location: class.php');
        exit();
    } catch (PDOException $e) {
        echo "<script>alert('Database error: " . $e->getMessage() . "');</script>";
    }
}

function getClasses($pdo)
{
    $sql = "
        SELECT c.class_id, c.class_name, g.grade_name, 
               (SELECT COUNT(*) FROM Student s WHERE s.class_id = c.class_id AND s.is_deleted = 0) AS student_count
        FROM Class c
        JOIN Grade g ON c.grade_id = g.grade_id
        WHERE c.is_deleted = 0
    ";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$all_classes = getClasses($pdo);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Class Management - MIPS</title>
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
        <?php include "../components/admin_sidebar.php"; ?>
        <!-- END OF ASIDE -->
        <main class="class">
            <div class="wrapper">
                <div class="title">
                    <div class="left">
                        <h1>Class Management</h1>
                    </div>
                    <div class="right">
                        <?php
                        try {
                            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                            $countQuery = "SELECT COUNT(*) FROM Class WHERE is_deleted = 0";
                            $stmt = $pdo->prepare($countQuery);
                            $stmt->execute();
                            $count = $stmt->fetchColumn();

                            echo "<p>Total $count Classes</p>";
                        } catch (PDOException $e) {
                            echo "<script>alert('Database error: " . $e->getMessage() . "');</script>";
                        }
                        ?>
                        <button id="open-popup" class="btn btn-outline"><i class="bi bi-plus-circle"></i>Add New Class</button>
                    </div>
                </div>
                <div class="table-body">
                    <table>
                        <thead>
                            <tr>
                                <th>Class Name</th>
                                <th>Grade Name</th>
                                <th>Total Students</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($all_classes as $class) : ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($class['class_name']); ?></td>
                                    <td><?php echo htmlspecialchars($class['grade_name']); ?></td>
                                    <td><?php echo htmlspecialchars($class['student_count']); ?></td>
                                    <td>
                                        <form action="" method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to deactivate this class?');">
                                            <input type="hidden" name="class_id" value="<?= htmlspecialchars($class['class_id']); ?>">
                                            <input type="hidden" name="deactivate" value="true">
                                            <button type="submit" class="delete-class-btn"><i class="bi bi-x-square-fill"></i></button>
                                        </form>
                                        <button type="button" class="edit-class-btn" data-class-id="<?= htmlspecialchars($class['class_id']); ?>"><i class="bi bi-pencil-square"></i></button>
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
        <div class="title">
            <div class="left">
                <h1>Add New Class</h1>
            </div>
            <div class="right">
                <button class="cancel"><i class="bi bi-x-circle"></i></button>
            </div>
        </div>
        <form action="" method="post">
            <div class="input-field">
                <h2>Class Name<sup>*</sup></h2>
                <input type="text" name="class_name" value="<?php echo isset($_POST['class_name']) ? htmlspecialchars($_POST['class_name']) : ''; ?>" required>
                <p>Please enter the name of the class.</p>
            </div>
            <div class="input-field">
                <h2>Grade<sup>*</sup></h2>
                <select name="grade_id" required>
                    <option value="">Select Grade</option>
                    <?php foreach ($all_grades as $grade) : ?>
                        <option value="<?= htmlspecialchars($grade['grade_id']) ?>"><?= htmlspecialchars($grade['grade_name']) ?></option>
                    <?php endforeach; ?>
                </select>
                <p>Please select the grade.</p>
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
            <h1>Your Class will be Deactivated!</h1>
            <label>Are you sure to proceed?</label>
            <div class="btns">
                <button value="cancel" class="btn1">Cancel Process</button>
                <button value="confirm" class="btn2">Deactivate Class</button>
            </div>
        </form>
    </dialog>
    <script src="../javascript/admin.js"></script>
    <script>
        document.querySelectorAll('.edit-class-btn').forEach(button => {
            button.addEventListener('click', function() {
                const classId = this.dataset.classId;

                fetch(`ajax.php?action=get_class&class_id=${classId}`)
                    .then(response => response.json())
                    .then(classData => {
                        if (classData.error) {
                            alert(classData.error);
                        } else {
                            document.querySelector('#add-edit-data [name="class_name"]').value = classData.class_name;
                            document.querySelector('#add-edit-data [name="grade_id"]').value = classData.grade_id;

                            document.querySelector('#add-edit-data h1').textContent = "Edit Class";
                            document.getElementById('add-edit-data').showModal();
                        }
                    })
                    .catch(error => {
                        console.error('Error fetching class data:', error);
                        alert('Failed to load class data.');
                    });
            });
        });
    </script>
</body>

</html>