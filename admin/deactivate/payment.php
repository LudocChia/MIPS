<?php

session_start();

include $_SERVER['DOCUMENT_ROOT'] . "/mips/components/db_connect.php";

if (!isset($_SESSION['admin_id'])) {
    header('Location: /mips/admin/login.php');
    exit();
}

$currentPage = basename($_SERVER['PHP_SELF']);

function getAllDeactivatedOrders($pdo)
{
    $sql = "SELECT o.order_id, o.order_datetime, o.order_price, p.parent_name, pm.payment_status
            FROM Orders o
            JOIN Parent_Student ps ON o.parent_student_id = ps.parent_student_id
            JOIN Parent p ON ps.parent_id = p.parent_id
            JOIN Payment pm ON o.order_id = pm.order_id
            WHERE o.is_deleted = 1";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$all_orders = getAllDeactivatedOrders($pdo);

$pageTitle = "Deactivated Orders - MIPS";
include $_SERVER['DOCUMENT_ROOT'] . "/mips/components/admin_head.php"; ?>

<body>
    <?php include  $_SERVER['DOCUMENT_ROOT'] . "/mips/components/admin_header.php"; ?>
    <div class="container">
        <?php include  $_SERVER['DOCUMENT_ROOT'] . "/mips/components/admin_sidebar.php"; ?>
        <main class="admin">
            <div class="wrapper">
                <div class="title">
                    <div class="left">
                        <h1>Deactivated Orders</h1>
                    </div>
                </div>
                <div class="table-body">
                    <table>
                        <thead>
                            <tr>
                                <th>
                                    <h3>Order ID</h3>
                                </th>
                                <th>
                                    <h3>Parent Name</h3>
                                </th>
                                <th>
                                    <h3>Order Date</h3>
                                </th>
                                <th>
                                    <h3>Order Amount</h3>
                                </th>
                                <th>
                                    <h3>Status</h3>
                                </th>
                                <th>
                                    <h3>Actions</h3>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($all_orders as $order) { ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($order['order_id']); ?></td>
                                    <td><?php echo htmlspecialchars($order['parent_name']); ?></td>
                                    <td><?php echo htmlspecialchars($order['order_datetime']); ?></td>
                                    <td>MYR <?php echo htmlspecialchars($order['order_price']); ?></td>
                                    <td><?php echo htmlspecialchars($order['payment_status']); ?></td>
                                    <td>
                                        <form action="" method="POST" style="display:inline;" onsubmit="return showRecoverConfirmDialog(event);">
                                            <input type="hidden" name="order_id" value="<?= htmlspecialchars($order['order_id']); ?>">
                                            <input type="hidden" name="action" value="recover_order">
                                            <button type="submit" class="recover-order-btn"><i class="bi bi-arrow-repeat"></i> Recover</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>
    <dialog id="detail-dialog">
        <!-- The dialog content remains the same as in the original order.php for viewing order details -->
    </dialog>
    <?php include  $_SERVER['DOCUMENT_ROOT'] . "/mips/components/confirm_dialog.php"; ?>
    <script src="/mips/javascript/admin.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.recover-order-btn').forEach(button => {
                button.addEventListener('click', function(event) {
                    event.preventDefault();
                    const form = event.target.closest('form');
                    const orderId = form.querySelector('input[name="order_id"]').value;

                    fetch('/mips/admin/ajax.php?action=recover_order', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/x-www-form-urlencoded'
                            },
                            body: new URLSearchParams({
                                order_id: orderId
                            })
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                alert('Order successfully recovered.');
                                location.reload();
                            } else {
                                alert('Failed to recover order: ' + data.error);
                            }
                        })
                        .catch(error => {
                            console.error('Error recovering order:', error);
                            alert('An error occurred while recovering the order.');
                        });
                });
            });
        });
    </script>
</body>

</html>