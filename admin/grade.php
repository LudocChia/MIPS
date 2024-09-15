<?php

$database_table = "grade";
$rows_per_page = 12;
include $_SERVER['DOCUMENT_ROOT'] . "/mips/php/admin.php";
include $_SERVER['DOCUMENT_ROOT'] . "/mips/php/activate_pagination.php";

function getGrades($pdo, $start, $rows_per_page)
{
    $sql = "SELECT 
            g.grade_id, 
            g.grade_name, 
            g.grade_level, 
            g.student_id_prefix, 
            (SELECT COUNT(*) FROM Class c WHERE c.grade_id = g.grade_id AND c.status = 0) AS total_classes,
            (SELECT COUNT(*) FROM Student s JOIN Class c ON s.class_id = c.class_id WHERE c.grade_id = g.grade_id AND s.status = 0) AS total_students
        FROM 
            Grade g 
        WHERE 
            g.status = 0
        LIMIT :start, :rows_per_page";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':start', $start, PDO::PARAM_INT);
    $stmt->bindParam(':rows_per_page', $rows_per_page, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$all_grades = getGrades($pdo, $start, $rows_per_page);

function generateGradeId()
{
    return uniqid("GR");
}

$msg = [];
if (isset($_POST["submit"])) {
    $gradeId = isset($_POST["grade_id"]) && !empty($_POST["grade_id"]) ? $_POST["grade_id"] : generateGradeId();
    $gradeName = $_POST["grade_name"];
    $gradeLevel = $_POST["grade_level"];
    $studentIdPrefix = $_POST["student_id_prefix"];

    if (isset($_POST['grade_id']) && !empty($_POST['grade_id'])) {
        $sql = "UPDATE Grade SET grade_name = :gradeName, grade_level = :gradeLevel, student_id_prefix = :studentIdPrefix, admin_id = :adminId WHERE grade_id = :gradeId";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':gradeId', $gradeId);
        $stmt->bindParam(':gradeName', $gradeName);
        $stmt->bindParam(':gradeLevel', $gradeLevel);
        $stmt->bindParam(':studentIdPrefix', $studentIdPrefix);
        $stmt->bindParam(':adminId', $_SESSION['admin_id']);
    } else {
        $sql = "INSERT INTO Grade (grade_id, grade_name, grade_level, student_id_prefix, status, admin_id) VALUES (:gradeId, :gradeName, :gradeLevel, :studentIdPrefix, 0, :adminId)";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':gradeId', $gradeId);
        $stmt->bindParam(':gradeName', $gradeName);
        $stmt->bindParam(':gradeLevel', $gradeLevel);
        $stmt->bindParam(':studentIdPrefix', $studentIdPrefix);
        $stmt->bindParam(':adminId', $_SESSION['admin_id']);
    }

    include $_SERVER['DOCUMENT_ROOT'] . "/mips/php/refresh_page.php";
}

$pageTitle = "Grade Management - MIPS";
include $_SERVER['DOCUMENT_ROOT'] . "/mips/components/admin_head.php"; ?>

<body id="<?php echo $id ?>">
    <?php include $_SERVER['DOCUMENT_ROOT'] . "/mips/components/admin_header.php"; ?>
    <div class="container">
        <?php include $_SERVER['DOCUMENT_ROOT'] . "/mips/components/admin_sidebar.php"; ?>
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
                <div class="table-container">
                    <?php if (!empty($all_grades)) : ?>
                        <table>
                            <thead>
                                <tr>
                                    <th>Grade Name</th>
                                    <th>Grade Level</th>
                                    <th>Student ID Prefix</th>
                                    <th>Total Classes</th>
                                    <th>Total Students</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($all_grades as $grade) : ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($grade['grade_name']); ?></td>
                                        <td style="text-align: center;"><?php echo htmlspecialchars($grade['grade_level']); ?></td>
                                        <td><?php echo htmlspecialchars($grade['student_id_prefix']); ?></td>
                                        <td style="text-align: center;"><?php echo htmlspecialchars($grade['total_classes']); ?></td>
                                        <td style="text-align: center;"><?php echo htmlspecialchars($grade['total_students']); ?></td>
                                        <td>
                                            <div class="actions">
                                                <form action="" method="POST" style="display:inline;" onsubmit="return showDeactivateConfirmDialog(event);">
                                                    <input type="hidden" name="grade_id" value="<?= htmlspecialchars($grade['grade_id']); ?>">
                                                    <input type="hidden" name="action" value="deactivate_grade">
                                                    <button type="submit" class="delete-grade-btn"><i class="bi bi-x-square"></i></button>
                                                </form>
                                                <button type="button" class="edit-grade-btn" data-grade-id="<?= htmlspecialchars($grade['grade_id']); ?>"><i class="bi bi-pencil-square"></i></button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php else : ?>
                        <?php include $_SERVER['DOCUMENT_ROOT'] . "/mips/components/no_data_found.php"; ?>
                    <?php endif; ?>
                </div>
                <?php if (!empty($all_grades)) : ?>
                    <?php include $_SERVER['DOCUMENT_ROOT'] . "/mips/components/pagination.php"; ?>
                <?php endif; ?>
            </div>
        </main>
    </div>
    <dialog id="add-edit-data">
        <form action="" method="post" enctype="multipart/form-data">
            <div class="title">
                <div class="left">
                    <h1>Add New Grade</h1>
                </div>
                <div class="right">
                    <button class="cancel"><i class="bi bi-x-circle"></i></button>
                </div>
            </div>
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
            <div class="input-container">
                <h2>Student ID Prefix</h2>
                <div class="input-field">
                    <input type="text" name="student_id_prefix" value="<?php echo isset($_POST['student_id_prefix']) ? htmlspecialchars($_POST['student_id_prefix']) : ''; ?>">
                </div>
                <p>Please enter the student ID prefix.</p>
            </div>
            <div class="input-container controls">
                <button type="button" class="cancel">Cancel</button>
                <button type="reset" class="delete">Clear</button>
                <button type="submit" class="confirm">Publish</button>
            </div>
        </form>
    </dialog>
    <?php include  $_SERVER['DOCUMENT_ROOT'] . "/mips/components/confirm_dialog.php"; ?>
    <script src="/mips/javascript/common.js"></script>
    <script src="/mips/javascript/admin.js"></script>
    <script>
        document.querySelectorAll('.edit-grade-btn').forEach(button => {
            button.addEventListener('click', function() {
                document.querySelector('.confirm').textContent = "Publish";
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
                            document.querySelector('#add-edit-data [name="student_id_prefix"]').value = grade.student_id_prefix;
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

        document.querySelectorAll('#add-edit-data .cancel').forEach(button => {
            button.addEventListener('click', function() {
                document.querySelector('#add-edit-data h1').textContent = "Add New Grade";
            });
        });
    </script>
</body>

</html>