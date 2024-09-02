<?php

$database_table = "class";
$rows_per_page = 12;
include $_SERVER['DOCUMENT_ROOT'] . "/mips/php/admin.php";
include $_SERVER['DOCUMENT_ROOT'] . "/mips/php/activate_pagination.php";

function getClasses($pdo, $start, $rows_per_page)
{
    $sql = "SELECT c.class_id, c.class_name, g.grade_name, 
               (SELECT COUNT(*) FROM Student s WHERE s.class_id = c.class_id AND s.status = 0) AS student_count
            FROM Class c
            JOIN Grade g ON c.grade_id = g.grade_id
            WHERE c.status = 0
            LIMIT :start, :rows_per_page";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':start', $start, PDO::PARAM_INT);
    $stmt->bindParam(':rows_per_page', $rows_per_page, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$all_classes = getClasses($pdo, $start, $rows_per_page);

function getAllGrades($pdo)
{
    $sql = "SELECT * FROM Grade WHERE status = 0";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$all_grades = getAllGrades($pdo);

function generateClassId()
{
    return uniqid("CL");
}

$msg = [];
if (isset($_POST["submit"])) {
    $classId = isset($_POST["class_id"]) && !empty($_POST["class_id"]) ? $_POST["class_id"] : generateClassId();
    $className = $_POST["class_name"];
    $gradeId = $_POST["grade_id"];
    $adminId = $_SESSION['admin_id'];

    if (isset($_POST['class_id']) && !empty($_POST['class_id'])) {
        $sql = "UPDATE Class SET class_name = :className, grade_id = :gradeId WHERE class_id = :classId";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':className', $className);
        $stmt->bindParam(':gradeId', $gradeId);
        $stmt->bindParam(':classId', $classId);
    } else {
        $sql = "INSERT INTO Class (class_id, class_name, grade_id, status, admin_id) VALUES (:classId, :className, :gradeId, 0, :adminId)";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':classId', $classId);
        $stmt->bindParam(':className', $className);
        $stmt->bindParam(':gradeId', $gradeId);
        $stmt->bindParam(':adminId', $adminId);
    }

    try {
        $stmt->execute();
        header('Location: class.php');
        exit();
    } catch (PDOException $e) {
        echo "<script>alert('Database error: " . $e->getMessage() . "');</script>";
    }
}

$pageTitle = "Class Management - MIPS";
include $_SERVER['DOCUMENT_ROOT'] . "/mips/components/admin_head.php"; ?>

<body>
    <?php include "../components/admin_header.php"; ?>
    <div class="container">
        <?php include "../components/admin_sidebar.php"; ?>
        <main class="class">
            <div class="wrapper">
                <div class="title">
                    <div class="left">
                        <h1>Class Management</h1>
                    </div>
                    <div class="right">
                        <button id="open-popup" class="btn btn-outline-primary"><i class="bi bi-plus-circle"></i>Add New Class</button>
                    </div>
                </div>
                <div class="table-body">
                    <?php if (!empty($all_classes)) : ?>
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
                                            <form action="" method="POST" style="display:inline;" onsubmit="return showDeactivateConfirmDialog(event);">
                                                <input type="hidden" name="class_id" value="<?= htmlspecialchars($class['class_id']); ?>">
                                                <input type="hidden" name="action" value="deactivate_class">
                                                <button type="submit" class="delete-class-btn"><i class="bi bi-x-square"></i></button>
                                            </form>
                                            <button type="button" class="edit-class-btn" data-class-id="<?= htmlspecialchars($class['class_id']); ?>"><i class="bi bi-pencil-square"></i></button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php else : ?>
                        <?php include $_SERVER['DOCUMENT_ROOT'] . "/mips/components/no_data_found.php"; ?>
                    <?php endif; ?>
                </div>
                <?php if (!empty($all_classes)) : ?>
                    <?php include $_SERVER['DOCUMENT_ROOT'] . "/mips/components/pagination.php"; ?>
                <?php endif; ?>
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
            <input type="hidden" name="class_id" value="">
            <div class="input-container">
                <h2>Class Name<sup>*</sup></h2>
                <div class="input-field">
                    <input type="text" name="class_name" value="<?php echo isset($_POST['class_name']) ? htmlspecialchars($_POST['class_name']) : ''; ?>" required>
                </div>
                <p>Please enter the name of the class.</p>
            </div>
            <div class="input-container">
                <h2>Grade<sup>*</sup></h2>
                <div class="input-field">
                    <select name="grade_id" required>
                        <option value="">Select Grade</option>
                        <?php foreach ($all_grades as $grade) : ?>
                            <option value="<?= htmlspecialchars($grade['grade_id']) ?>"><?= htmlspecialchars($grade['grade_name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <p>Please select the grade.</p>
            </div>
            <div class="input-container controls">
                <button type="button" class="cancel">Cancel</button>
                <button type="reset">Clear</button>
                <button type="submit" name="submit">Publish</button>
            </div>
        </form>
    </dialog>
    <?php include  $_SERVER['DOCUMENT_ROOT'] . "/mips/components/confirm_dialog.php"; ?>
    <script src="/mips/javascript/common.js"></script>
    <script src="/mips/javascript/admin.js"></script>
    <script>
        document.querySelectorAll('.edit-class-btn').forEach(button => {
            button.addEventListener('click', function() {
                const classId = this.dataset.classId;

                fetch(`/mips/admin/ajax.php?action=get_class&class_id=${classId}`)
                    .then(response => response.json())
                    .then(classData => {
                        if (classData.error) {
                            alert(classData.error);
                        } else {
                            document.querySelector('#add-edit-data [name="class_id"]').value = classData.class_id;
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