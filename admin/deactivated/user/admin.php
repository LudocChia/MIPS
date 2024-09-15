<?php

$database_table = "Admin";
$rows_per_page = 12;
include $_SERVER['DOCUMENT_ROOT'] . "/mips/php/admin.php";
include $_SERVER['DOCUMENT_ROOT'] . "/mips/php/deactivated_pagination.php";

function getDeactivatedAdmins($pdo, $start, $rows_per_page)
{
    $sql = "SELECT *
            FROM Admin
            WHERE status = 1
            LIMIT :start, :rows_per_page";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':start', $start, PDO::PARAM_INT);
    $stmt->bindParam(':rows_per_page', $rows_per_page, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$deactivated_admins = getDeactivatedAdmins($pdo, $start, $rows_per_page);

$pageTitle = "Deactivated Admins - MIPS";
include $_SERVER['DOCUMENT_ROOT'] . "/mips/components/admin_head.php";
?>

<body id="<?php echo $id ?>">
    <?php include $_SERVER['DOCUMENT_ROOT'] . "/mips/components/admin_header.php"; ?>
    <div class="container">
        <?php include $_SERVER['DOCUMENT_ROOT'] . "/mips/components/admin_sidebar.php"; ?>
        <main class="admin">
            <div class="wrapper">
                <div class="title">
                    <div class="left">
                        <h1>Deactivated Admin Accounts</h1>
                    </div>
                    <div class="right">
                        <a href="/mips/admin/deactivated/user/"><i class="bi bi-arrow-90deg-up"></i>Deactivated User Menu</a>
                    </div>
                </div>
                <?php if (!empty($deactivated_admins)) : ?>
                    <div class="table-container">
                        <table>
                            <thead>
                                <tr>
                                    <th>Admin ID</th>
                                    <th>Admin Name</th>
                                    <th>Admin Email</th>
                                    <th>Admin Type</th>
                                    <th>Created At</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($deactivated_admins as $admin) : ?>
                                    <tr>
                                        <td><?= htmlspecialchars($admin['admin_id']); ?></td>
                                        <td><?= htmlspecialchars($admin['admin_name']); ?></td>
                                        <td><?= htmlspecialchars($admin['admin_email']); ?></td>
                                        <td><?= htmlspecialchars($admin['admin_type']); ?></td>
                                        <td><?= htmlspecialchars($admin['created_at']); ?></td>
                                        <td>
                                            <div class="actions">
                                                <form action="" method="POST" style="display:inline;" onsubmit="return showDeleteConfirmDialog(event);">
                                                    <input type="hidden" name="admin_id" value="<?= htmlspecialchars($admin['admin_id']); ?>">
                                                    <input type="hidden" name="action" value="delete_admin">
                                                    <button type="submit" class="delete-admin-btn"><i class="bi bi-x-square"></i></button>
                                                </form>
                                                <form action="" method="POST" style="display:inline;" onsubmit="return showRecoverConfirmDialog(event);">
                                                    <input type="hidden" name="admin_id" value="<?= htmlspecialchars($admin['admin_id']); ?>">
                                                    <input type="hidden" name="action" value="recover_admin">
                                                    <button type="submit" class="recover-admin-btn"><i class="bi bi-arrow-clockwise"></i></button>
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
                    <?php if (!empty($deactivated_admins)) : ?>
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