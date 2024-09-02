<?php

$database_table = "Parent";
$rows_per_page = 12;
include $_SERVER['DOCUMENT_ROOT'] . "/mips/php/admin.php";
include $_SERVER['DOCUMENT_ROOT'] . "/mips/php/activate_pagination.php";

function getDeactivatedParents($pdo, $start, $rows_per_page)
{
    $sql = "SELECT * FROM Parent WHERE status = 1 LIMIT :start, :rows_per_page";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':start', $start, PDO::PARAM_INT);
    $stmt->bindParam(':rows_per_page', $rows_per_page, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$deactivated_parents = getDeactivatedParents($pdo, $start, $rows_per_page);

function getAllStudents($pdo)
{
    $sql = "SELECT student_id, student_name FROM Student WHERE status = 0";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$all_students = getAllStudents($pdo);

?>

<?php $pageTitle = "Deactivated Users - MIPS";
include $_SERVER['DOCUMENT_ROOT'] . "/mips/components/admin_head.php"; ?>

<body id="<?php echo $id ?>">
    <?php include $_SERVER['DOCUMENT_ROOT'] . "/mips/components/admin_header.php"; ?>
    <div class="container">
        <?php include $_SERVER['DOCUMENT_ROOT'] . "/mips/components/admin_sidebar.php"; ?>
        <main class="parent">
            <div class="wrapper">
                <div class="title">
                    <div class="left">
                        <h1>Student Parent Accounts Recycle Bin</h1>
                    </div>
                    <div class="right">
                        <a href="/mips/admin/recycleBin.php"><i class="bi bi-arrow-return-left"></i>Recycle Bin Menu</a>
                    </div>
                </div>
                <div class="table-body">
                    <table>
                        <thead>
                            <tr>
                                <!-- <th><input type="checkbox"></th> -->
                                <th>Parent ID</th>
                                <th>Parent Name</th>
                                <th>Parent Email</th>
                                <th>Register Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($deactivated_parents as $parent) : ?>
                                <tr>
                                    <!-- <td><input type="checkbox" class="parent-checkbox" value="<?= htmlspecialchars($parent['parent_id']); ?>"></td> -->
                                    <td><?= htmlspecialchars($parent['parent_id']); ?></td>
                                    <td><?= htmlspecialchars($parent['parent_name']); ?></td>
                                    <td><?= htmlspecialchars($parent['parent_email']); ?></td>
                                    <td><?= htmlspecialchars($parent['register_datetime']); ?></td>
                                    <td>
                                        <button type="button" class="view-order-detail-btn" data-order-id="<?= htmlspecialchars($order['order_id']); ?>"><i class="bi bi-info-circle-fill"></i></button>
                                        <form action="" method="POST" style="display:inline;" onsubmit="return showRecoverConfirmDialog(event);">
                                            <input type="hidden" name="parent_id" value="<?= htmlspecialchars($parent['parent_id']); ?>">
                                            <input type="hidden" name="action" value="recover_parent">
                                            <button type="submit" class="recover-parent-btn"><i class="bi bi-arrow-clockwise"></i></button>
                                        </form>
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
    <?php include $_SERVER['DOCUMENT_ROOT'] . "/mips/components/confirm_dialog.php"; ?>
    <script src="/mips/javascript/admin.js"></script>
</body>

</html>