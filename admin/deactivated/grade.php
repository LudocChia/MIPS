<?php

$database_table = "Grade";
$rows_per_page = 12;
include $_SERVER['DOCUMENT_ROOT'] . "/mips/php/admin.php";
include $_SERVER['DOCUMENT_ROOT'] . "/mips/php/activate_pagination.php";

function getDeactivatedGrades($pdo, $start, $rows_per_page)
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
                g.status = 1
            LIMIT :start, :rows_per_page";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':start', $start, PDO::PARAM_INT);
    $stmt->bindParam(':rows_per_page', $rows_per_page, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$all_grades = getDeactivatedGrades($pdo, $start, $rows_per_page);

$pageTitle = "Deactivated Grades - MIPS";
include $_SERVER['DOCUMENT_ROOT'] . "/mips/components/admin_head.php"; ?>

<body id="<?php echo $id ?>">
    <?php include $_SERVER['DOCUMENT_ROOT'] . "/mips/components/admin_header.php"; ?>
    <div class="container">
        <?php include $_SERVER['DOCUMENT_ROOT'] . "/mips/components/admin_sidebar.php"; ?>
        <main class="grade">
            <div class="wrapper">
                <div class="title">
                    <div class="left">
                        <h1>Deactivated Grade</h1>
                    </div>
                    <div class="right">
                        <a href="/mips/admin/deactivated/"><i class="bi bi-arrow-90deg-up"></i>Deactivated Menu</a>
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
                                                <form action="" method="POST" style="display:inline;" onsubmit="return showDeleteConfirmDialog(event);">
                                                    <input type="hidden" name="grade_id" value="<?= htmlspecialchars($grade['grade_id']); ?>">
                                                    <input type="hidden" name="action" value="delete_grade">
                                                    <button type="submit" class="delete-grade-btn"><i class="bi bi-x-square"></i></button>
                                                </form>
                                                <form action="" method="POST" style="display:inline;" onsubmit="return showRecoverConfirmDialog(event);">
                                                    <input type="hidden" name="grade_id" value="<?= htmlspecialchars($grade['grade_id']); ?>">
                                                    <input type="hidden" name="action" value="recover_grade">
                                                    <button type="submit" class="recover-grade-btn"><i class="bi bi-arrow-clockwise"></i></button>
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
                <?php if (!empty($all_grades)) : ?>
                    <?php include $_SERVER['DOCUMENT_ROOT'] . "/mips/components/pagination.php"; ?>
                <?php endif; ?>
            </div>
        </main>
    </div>
    <?php include  $_SERVER['DOCUMENT_ROOT'] . "/mips/components/confirm_dialog.php"; ?>
    <script src="/mips/javascript/admin.js"></script>
</body>

</html>