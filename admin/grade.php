<?php

session_start();

include $_SERVER['DOCUMENT_ROOT'] . "/mips/components/db_connect.php";

if (!isset($_SESSION['admin_id'])) {
    header('Location: /mips/admin/login.php');
    exit();
}

$currentPage = basename($_SERVER['PHP_SELF']);

function getGrades($pdo)
{
    $sql = "
        SELECT 
            g.grade_id, 
            g.grade_name, 
            g.grade_level, 
            (SELECT COUNT(*) FROM Class c WHERE c.grade_id = g.grade_id AND c.is_deleted = 0) AS total_classes,
            (SELECT COUNT(*) FROM Student s JOIN Class c ON s.class_id = c.class_id WHERE c.grade_id = g.grade_id AND s.is_deleted = 0) AS total_students
        FROM 
            Grade g 
        WHERE 
            g.is_deleted = 0
    ";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$all_grades = getGrades($pdo);

$msg = [];
if (isset($_POST["submit"])) {
    $gradeName = $_POST["grade_name"];
    $gradeLevel = $_POST["grade_level"];
    $gradeId = isset($_POST["grade_id"]) ? $_POST["grade_id"] : null;

    if ($gradeId) {
        $sql = "UPDATE Grade SET grade_name = :gradeName, grade_level = :gradeLevel WHERE grade_id = :gradeId";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':gradeName', $gradeName);
        $stmt->bindParam(':gradeLevel', $gradeLevel);
        $stmt->bindParam(':gradeId', $gradeId);
    } else {
        $sql = "INSERT INTO Grade (grade_name, grade_level, is_deleted) VALUES (:gradeName, :gradeLevel, 0)";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':gradeName', $gradeName);
        $stmt->bindParam(':gradeLevel', $gradeLevel);
    }

    try {
        $stmt->execute();
        header('Location: grade.php');
        exit();
    } catch (PDOException $e) {
        echo "<script>alert('Database error: " . $e->getMessage() . "');</script>";
    }
}


$pageTitle = "Grade Management - MIPS";
include $_SERVER['DOCUMENT_ROOT'] . "/mips/components/admin_head.php"; ?>

<body>
    <?php include  $_SERVER['DOCUMENT_ROOT'] . "/mips/components/admin_header.php"; ?>
    <div class="container">
        <?php include  $_SERVER['DOCUMENT_ROOT'] . "/mips/components/admin_sidebar.php"; ?>
        <!-- END OF ASIDE -->
        <main class="grade">
            <div class="wrapper">
                <div class="title">
                    <div class="left">
                        <h1>Grade Management</h1>
                    </div>
                    <div class="right">
                        <button id="open-popup" class="btn btn-outline-primary"><i class="bi bi-plus-circle"></i>Add New Grade</button>
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
                                    <td><?php echo htmlspecialchars($grade['total_classes']); ?></td>
                                    <td><?php echo htmlspecialchars($grade['total_students']); ?></td>
                                    <td>
                                        <form action="" method="POST" style="display:inline;" onsubmit="return showDeactivateConfirmDialog(event);">
                                            <input type="hidden" name="grade_id" value="<?= htmlspecialchars($grade['grade_id']); ?>">
                                            <input type="hidden" name="action" value="deactivate_grade">
                                            <button type="submit" class="delete-grade-btn"><i class="bi bi-x-square"></i></button>
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
            <input type="hidden" name="grade_id" value="">
            <div class="input-container">
                <h2>Grade Name<sup>*</sup></h2>
                <div class="input-field">
                    <input type="text" name="grade_name" value="<?php echo isset($_POST['grade_name']) ? htmlspecialchars($_POST['grade_name']) : ''; ?>" required>
                </div>
                <p>Please enter the name of the grade.</p>
            </div>
            <div class="input-container">
                <h2>Grade Level<sup>*</sup></h2>
                <div class="input-field">
                    <input type="number" name="grade_level" value="<?php echo isset($_POST['grade_level']) ? htmlspecialchars($_POST['grade_level']) : ''; ?>" required>
                </div>
                <p>Please enter the level of the grade.</p>
            </div>
            <div class="input-container controls">
                <button type="button" class="cancel">Cancel</button>
                <button type="reset">Clear</button>
                <button type="submit" name="submit">Publish</button>
            </div>
        </form>
    </dialog>
    <?php include  $_SERVER['DOCUMENT_ROOT'] . "/mips/components/confirm_dialog.php"; ?>
    <script src="/mips/javascript/admin.js"></script>
    <script>
        document.querySelectorAll('.edit-grade-btn').forEach(button => {
            button.addEventListener('click', function() {
                const gradeId = this.dataset.gradeId;
                fetch(`/mips/admin/ajax.php?action=get_grade&grade_id=${gradeId}`)
                    .then(response => response.json())
                    .then(grade => {
                        if (grade.error) {
                            alert(grade.error);
                        } else {
                            document.querySelector('#add-edit-data [name="grade_id"]').value = grade.grade_id;
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