<?php

$database_table = "Student";
$rows_per_page = 12;
include $_SERVER['DOCUMENT_ROOT'] . "/mips/php/admin.php";
include $_SERVER['DOCUMENT_ROOT'] . "/mips/php/deactivated_pagination.php";

function getDeactivatedStudents($pdo, $start, $rows_per_page)
{
    $sql = "SELECT s.*, c.class_name, p.parent_name
            FROM Student s
            LEFT JOIN Class c ON s.class_id = c.class_id
            LEFT JOIN Parent_Student ps ON s.student_id = ps.student_id
            LEFT JOIN Parent p ON ps.parent_id = p.parent_id
            WHERE s.status = 1
            LIMIT :start, :rows_per_page";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':start', $start, PDO::PARAM_INT);
    $stmt->bindParam(':rows_per_page', $rows_per_page, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$deactivated_students = getDeactivatedStudents($pdo, $start, $rows_per_page);

$pageTitle = "Deactivated Students - MIPS";
include $_SERVER['DOCUMENT_ROOT'] . "/mips/components/admin_head.php";
?>

<body id="<?php echo $id ?>">
    <?php include $_SERVER['DOCUMENT_ROOT'] . "/mips/components/admin_header.php"; ?>
    <div class="container">
        <?php include $_SERVER['DOCUMENT_ROOT'] . "/mips/components/admin_sidebar.php"; ?>
        <main class="student">
            <div class="wrapper">
                <div class="title">
                    <div class="left">
                        <h1>Deactivated Student Accounts</h1>
                    </div>
                    <div class="right">
                        <a href="/mips/admin/deactivated/user/"><i class="bi bi-arrow-90deg-up"></i>Deactivated User Menu</a>
                    </div>
                </div>
                <?php if (!empty($deactivated_students)) : ?>
                    <div class="table-container">
                        <table>
                            <thead>
                                <tr>
                                    <th>
                                        <h3>Student ID</h3>
                                    </th>
                                    <th>
                                        <h3>Student Name</h3>
                                    </th>
                                    <th>
                                        <h3>Student Parent Name</h3>
                                    </th>
                                    <th>
                                        <h3>Class</h3>
                                    </th>
                                    <th>
                                        <h3>Register Date</h3>
                                    </th>
                                    <th>
                                        <h3>Actions</h3>
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($deactivated_students as $student) : ?>
                                    <tr>
                                        <td><?= htmlspecialchars($student['student_id']); ?></td>
                                        <td><?= htmlspecialchars($student['student_name']); ?></td>
                                        <td><?= htmlspecialchars($student['parent_name'] ?? '-') ?></td>
                                        <td><?= htmlspecialchars($student['class_name']); ?></td>
                                        <td><?= htmlspecialchars($student['created_at']); ?></td>
                                        <td>
                                            <div class="actions">
                                                <form action="" method="POST" style="display:inline;" onsubmit="return showDeleteConfirmDialog(event);">
                                                    <input type="hidden" name="student_id" value="<?= htmlspecialchars($student['student_id']); ?>">
                                                    <input type="hidden" name="action" value="delete_student">
                                                    <button type="submit" class="delete-student-btn"><i class="bi bi-x-square"></i></button>
                                                </form>
                                                <form action="" method="POST" style="display:inline;" onsubmit="return showRecoverConfirmDialog(event);">
                                                    <input type="hidden" name="student_id" value="<?= htmlspecialchars($student['student_id']); ?>">
                                                    <input type="hidden" name="action" value="recover_student">
                                                    <button type="submit" class="recover-student-btn"><i class="bi bi-arrow-clockwise"></i></button>
                                                </form>
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
                    <?php if (!empty($deactivated_students)) : ?>
                        <?php include $_SERVER['DOCUMENT_ROOT'] . "/mips/components/pagination.php"; ?>
                    <?php endif; ?>
            </div>
        </main>
    </div>
    <?php include $_SERVER['DOCUMENT_ROOT'] . "/mips/components/confirm_dialog.php"; ?>
    <script src="/mips/javascript/common.js"></script>
    <script src="/mips/javascript/admin.js"></script>
</body>

</html>