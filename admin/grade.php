<?php

session_start();

include "../components/db_connect.php";

if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit();
}

$currentPage = basename($_SERVER['PHP_SELF']);

$msg = [];
if (isset($_POST["submit"])) {
    $gradeName = $_POST["grade_name"];
    $gradeLevel = $_POST["grade_level"];

    $sql = "INSERT INTO Grade (grade_name, grade_level, is_deleted) VALUES (:gradeName, :gradeLevel, 0)";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':gradeName', $gradeName);
    $stmt->bindParam(':gradeLevel', $gradeLevel);
    try {
        $stmt->execute();
        header('Location: grade.php');
        exit();
    } catch (PDOException $e) {
        echo "<script>alert('Database error: " . $e->getMessage() . "');</script>";
    }
}

function getGrades($pdo)
{
    $sql = "SELECT * FROM Grade WHERE is_deleted = 0";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$all_grades = getGrades($pdo);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Grade Management - MIPS</title>
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
        <!-- END OF ASIDE -->
        <main class="grade">
            <div class="wrapper">
                <div class="title">
                    <div class="left">
                        <h1>Grade Management</h1>
                    </div>
                    <div class="right">
                        <button id="open-popup" class="btn btn-outline"><i class="bi bi-plus-circle"></i>Add New Grade</button>
                    </div>
                </div>
                <div class="table-body">
                    <table>
                        <thead>
                            <tr>
                                <th>Grade Name</th>
                                <th>Grade Level</th>
                                <th>Total Classes</th>
                                <th>Total Students</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($all_grades as $grade) : ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($grade['grade_name']); ?></td>
                                    <td><?php echo htmlspecialchars($grade['grade_level']); ?></td>
                                    <td></td>
                                    <td></td>
                                    <td>
                                        <form action="" method="POST" style="display:inline;" onsubmit="return showDeleteConfirmDialog(event);">
                                            <input type="hidden" name="grade_id" value="<?= htmlspecialchars($grade['grade_id']); ?>">
                                            <input type="hidden" name="deactivate" value="true">
                                            <button type="submit" class="delete-grade-btn"><i class="bi bi-x-square-fill"></i></button>
                                        </form>
                                        <button type="button" class="edit-grade-btn" data-grade-id="<?= htmlspecialchars($grade['grade_id']); ?>"><i class="bi bi-pencil-square"></i></button>
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
                <h1>Add New Grade</h1>
            </div>
            <div class="right">
                <button class="cancel"><i class="bi bi-x-circle"></i></button>
            </div>
        </div>
        <form action="" method="post" enctype="multipart/form-data">
            <div class="input-field">
                <h2>Grade Name<sup>*</sup></h2>
                <input type="text" name="grade_name" value="<?php echo isset($_POST['grade_name']) ? htmlspecialchars($_POST['grade_name']) : ''; ?>" required>
                <p>Please enter the name of the grade.</p>
            </div>
            <div class="input-field">
                <h2>Grade Level<sup>*</sup></h2>
                <input type="number" name="grade_level" value="<?php echo isset($_POST['grade_level']) ? htmlspecialchars($_POST['grade_level']) : ''; ?>" required>
                <p>Please enter the level of the grade.</p>
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
            <h1>Your Grade will be Deactivated!</h1>
            <label>Are you sure to proceed?</label>
            <div class="btns">
                <button value="cancel" class="btn1">Cancel Process</button>
                <button value="confirm" class="btn2">Deactivate Grade</button>
            </div>
        </form>
    </dialog>
    <script src="../javascript/admin.js"></script>
    <script>
        document.querySelectorAll('.edit-grade-btn').forEach(button => {
            button.addEventListener('click', function() {
                const gradeId = this.dataset.gradeId;

                fetch(`ajax.php?action=get_grade&grade_id=${gradeId}`)
                    .then(response => response.json())
                    .then(grade => {
                        if (grade.error) {
                            alert(grade.error);
                        } else {
                            document.querySelector('#add-edit-data [name="grade_name"]').value = grade.grade_name;
                            document.querySelector('#add-edit-data [name="grade_level"]').value = grade.grade_level;

                            document.querySelector('#add-edit-data h1').textContent = "Edit Grade";
                            document.getElementById('add-edit-data').showModal();
                        }
                    })
                    .catch(error => {
                        console.error('Error fetching grade data:', error);
                        alert('Failed to load grade data.');
                    });
            });
        });
    </script>
</body>

</html>