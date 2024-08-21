<?php

session_start();

include $_SERVER['DOCUMENT_ROOT'] . "/mahans/components/db_connect.php";

if (!isset($_SESSION['admin_id'])) {
    header('Location: /mahans/admin/login.php');
    exit();
}

$currentPage = basename($_SERVER['PHP_SELF']);

function getDeactivatedParents($pdo)
{
    $sql = "SELECT * FROM Parent WHERE is_deleted = 1";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$deactivated_parents = getDeactivatedParents($pdo);

function getAllStudents($pdo)
{
    $sql = "SELECT student_id, student_name FROM Student WHERE is_deleted = 0";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$all_students = getAllStudents($pdo);

?>

<?php $pageTitle = "Deactivated Users - MIPS";
include $_SERVER['DOCUMENT_ROOT'] . "/mahans/components/admin_head.php"; ?>

<body>
    <?php include $_SERVER['DOCUMENT_ROOT'] . "/mahans/components/admin_header.php"; ?>
    <div class="container">
        <?php include $_SERVER['DOCUMENT_ROOT'] . "/mahans/components/admin_sidebar.php"; ?>
        <main class="parent">
            <div class="wrapper">
                <div class="title">
                    <div class="left">
                        <h1>Student Parents</h1>
                    </div>
                    <div class="right">

                    </div>
                </div>
                <div class="table-body">
                    <table>
                        <thead>
                            <tr>
                                <th><input type="checkbox"></th>
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
                                    <td><input type="checkbox" class="parent-checkbox" value="<?= htmlspecialchars($parent['parent_id']); ?>"></td>
                                    <td><?= htmlspecialchars($parent['parent_id']); ?></td>
                                    <td><?= htmlspecialchars($parent['parent_name']); ?></td>
                                    <td><?= htmlspecialchars($parent['parent_email']); ?></td>
                                    <td><?= htmlspecialchars($parent['register_datetime']); ?></td>
                                    <td>
                                        <button type="button" class="view-order-detail-btn" data-order-id="<?= htmlspecialchars($order['order_id']); ?>"><i class="bi bi-info-circle-fill"></i></button>
                                        <form action="" method="POST" style="display:inline;" onsubmit="showRecoverConfirmDialog(event);">
                                            <input type="hidden" name="parent_id" value="<?= htmlspecialchars($parent['parent_id']); ?>">
                                            <input type="hidden" name="recover" value="true">
                                            <button type="submit" class="recover-parent-btn"><i class="bi bi-arrow-clockwise"></i></button>
                                        </form>

                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>

                    </table>
                </div>
            </div>
        </main>
    </div>
    <?php include $_SERVER['DOCUMENT_ROOT'] . "/mahans/components/confirm_dialog.php"; ?>
    <script src="/mahans/javascript/admin.js"></script>
</body>

</html>