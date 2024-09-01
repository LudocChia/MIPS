<?php
session_start();
include $_SERVER['DOCUMENT_ROOT'] . "/mips/components/db_connect.php";


$parent_id = $_SESSION['user_id'];

function getOrders($pdo, $parent_id, $status = 'pending')
{
    $sql = "SELECT o.order_id, o.order_datetime, o.order_price, p.payment_status
            FROM Orders o
            JOIN Payment p ON o.order_id = p.order_id
            WHERE o.parent_student_id IN (
                SELECT parent_student_id
                FROM Parent_Student
                WHERE parent_id = ?
            ) AND o.is_deleted = 0";

    if ($status) {
        $sql .= " AND p.payment_status = ?";
    }

    $sql .= " ORDER BY o.order_datetime DESC";

    $stmt = $pdo->prepare($sql);

    if ($status) {
        $stmt->bindParam(1, $parent_id);
        $stmt->bindParam(2, $status);
    } else {
        $stmt->bindParam(1, $parent_id);
    }

    $stmt->execute();

    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $orders;
}

$orders = getOrders($pdo, $parent_id);



$pageTitle = "My Purchase - MIPS";
include $_SERVER['DOCUMENT_ROOT'] . "/mips/components/customer_head.php";
?>

<body>
    <?php include $_SERVER['DOCUMENT_ROOT'] . "/mips/components/customer_header.php"; ?>
    <div class="container">
        <?php include $_SERVER['DOCUMENT_ROOT'] . "/mips/components/customer_sidebar.php"; ?>
        <main class="profile">
            <div class="wrapper">
                <div class="title">
                    <div class="left">
                        <h1>My Purchase History</h1>
                    </div>
                </div>
                <div class="orders">
                    <?php if (!empty($orders)): ?>
                        <table>
                            <thead>
                                <tr>
                                    <th>Order ID</th>
                                    <th>Date</th>
                                    <th>Total Price</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($orders as $order): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($order['order_id']) ?></td>
                                        <td><?= date("d M Y H:i", strtotime($order['order_datetime'])) ?></td>
                                        <td>RM <?= number_format($order['order_price'], 2) ?></td>
                                        <td><?= htmlspecialchars($order['payment_status']) ?></td>
                                        <td><a href="/mips/order_details.php?order_id=<?= urlencode($order['order_id']) ?>">View Details</a></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <p>No orders found.</p>
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </div>

    <?php include $_SERVER['DOCUMENT_ROOT'] . "/mips/components/customer_footer.php"; ?>
    <script src="/mips/javascript/common.js"></script>
    <script src="/mips/javascript/customer.js"></script>
    <script src="/mips/javascript/common.js"></script>
    <script>
        function fetchOrders(status) {
            fetch('/mips/ajax.php?action=get_orders', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `parent_id=${encodeURIComponent(<?php echo $_SESSION['user_id']; ?>)}&status=${encodeURIComponent(status)}`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.error) {
                        console.error('Error:', data.error);
                    } else {
                        displayOrders(data);
                    }
                })
                .catch(error => console.error('Error fetching orders:', error));
        }

        function displayOrders(orders) {
            const ordersContainer = document.querySelector('.orders');
            let html = '<table><thead><tr><th>Order ID</th><th>Date</th><th>Total Price</th><th>Status</th><th>Action</th></tr></thead><tbody>';
            orders.forEach(order => {
                html += `<tr>
                    <td>${order.order_id}</td>
                    <td>${new Date(order.order_datetime).toLocaleDateString()}</td>
                    <td>RM ${parseFloat(order.order_price).toFixed(2)}</td>
                    <td>${order.payment_status}</td>
                    <td><a href="/mips/order_details.php?order_id=${encodeURIComponent(order.order_id)}">View Details</a></td>
                 </tr>`;
            });
            html += '</tbody></table>';
            ordersContainer.innerHTML = html;
        }

        document.addEventListener('DOMContentLoaded', function() {
            const statusButtons = document.querySelectorAll('.status-button');
            statusButtons.forEach(button => {
                button.addEventListener('click', function() {
                    fetchOrders(this.dataset.status);
                });
            });

            // Fetch initial orders
            fetchOrders('pending'); // Or any default status
        });
    </script>

</body>

</html>