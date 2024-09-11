<?php

$database_table = "Orders";
$rows_per_page = 12;
include $_SERVER['DOCUMENT_ROOT'] . "/mips/php/admin.php";
include $_SERVER['DOCUMENT_ROOT'] . "/mips/php/activate_pagination.php";

function getAllOrders($pdo, $start, $rows_per_page)
{
    $sql = "SELECT o.order_id, o.order_datetime, o.order_price, p.parent_name, pm.payment_status
            FROM Orders o
            JOIN Parent_Student ps ON o.parent_student_id = ps.parent_student_id
            JOIN Parent p ON ps.parent_id = p.parent_id
            JOIN Payment pm ON o.order_id = pm.order_id
            WHERE o.status = 0
            ORDER BY o.order_datetime DESC
            LIMIT :start, :rows_per_page";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':start', $start, PDO::PARAM_INT);
    $stmt->bindParam(':rows_per_page', $rows_per_page, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$all_orders = getAllOrders($pdo, $start, $rows_per_page);

function generateOrderId()
{
    return uniqid('ORD');
}

if (isset($_POST["submit"])) {
    $parent_student_id = $_POST["parent_student_id"];
    $order_price = $_POST["order_price"];
    $order_status = $_POST["order_status"];
    $order_id = generateOrderId();
    $product_ids = $_POST['product_id'];
    $quantities = $_POST['product_quantity'];

    if (empty($parent_student_id) || empty($order_price) || empty($order_status) || empty($product_ids) || empty($quantities)) {
        echo "<script>alert('Please fill in all required fields.');</script>";
    } else {
        try {
            $pdo->beginTransaction();

            $sqlOrder = "INSERT INTO Orders (order_id, parent_student_id, order_price, status) 
                         VALUES (:orderId, :parent_student_id, :order_price, 0)";
            $stmtOrder = $pdo->prepare($sqlOrder);
            $stmtOrder->bindParam(':orderId', $order_id);
            $stmtOrder->bindParam(':parent_student_id', $parent_student_id);
            $stmtOrder->bindParam(':order_price', $order_price);
            $stmtOrder->execute();

            $sqlPayment = "INSERT INTO Payment (payment_id, parent_student_id, order_id, payment_amount, payment_status, payment_image) 
                           VALUES (:paymentId, :parent_student_id, :order_id, :payment_amount, :payment_status, '')";
            $payment_id = uniqid('PAY');
            $stmtPayment = $pdo->prepare($sqlPayment);
            $stmtPayment->bindParam(':paymentId', $payment_id);
            $stmtPayment->bindParam(':parent_student_id', $parent_student_id);
            $stmtPayment->bindParam(':order_id', $order_id);
            $stmtPayment->bindParam(':payment_amount', $order_price);
            $stmtPayment->bindParam(':payment_status', $order_status);
            $stmtPayment->execute();

            foreach ($product_ids as $index => $product_id) {
                $quantity = $quantities[$index];
                $sqlOrderItem = "INSERT INTO Order_Item (order_item_id, order_id, product_id, product_quantity, order_subtotal, status) 
                                 VALUES (:order_item_id, :order_id, :product_id, :product_quantity, :order_subtotal, 0)";
                $stmtOrderItem = $pdo->prepare($sqlOrderItem);
                $order_item_id = uniqid('OI');
                $stmtOrderItem->bindParam(':order_item_id', $order_item_id);
                $stmtOrderItem->bindParam(':order_id', $order_id);
                $stmtOrderItem->bindParam(':product_id', $product_id);
                $stmtOrderItem->bindParam(':product_quantity', $quantity);
                $stmtOrderItem->bindParam(':order_subtotal', $order_price);
                $stmtOrderItem->execute();
            }

            $pdo->commit();

            echo "<script>alert('Order Successfully Added');document.location.href ='order.php';</script>";
        } catch (PDOException $e) {
            $pdo->rollBack();
            echo "<script>alert('Database error: " . $e->getMessage() . "');</script>";
        }
    }
}

$pageTitle = "Bookshop Order - MIPS";
include $_SERVER['DOCUMENT_ROOT'] . "/mips/components/admin_head.php"; ?>

<body id="<?php echo $id ?>">
    <?php include  $_SERVER['DOCUMENT_ROOT'] . "/mips/components/admin_header.php"; ?>
    <div class="container">
        <?php include  $_SERVER['DOCUMENT_ROOT'] . "/mips/components/admin_sidebar.php"; ?>
        <main class="admin">
            <div class="wrapper">
                <div class="title">
                    <div class="left">
                        <h1>Bookshop Order</h1>
                    </div>
                    <div class="right">
                        <!-- <button class="btn btn-outline-primary" id="open-popup"><i class="bi bi-plus-circle"></i>Add New Order</button> -->
                    </div>
                </div>
                <div class="table-body">
                    <?php if (!empty($all_orders)) : ?>
                        <table>
                            <thead>
                                <tr>
                                    <th>Order ID</th>
                                    <th>Parent Name</th>
                                    <th>Order Date</th>
                                    <th>Order Amount</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($all_orders as $order) : ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($order['order_id']); ?></td>
                                        <td><?php echo htmlspecialchars($order['parent_name']); ?></td>
                                        <td><?php echo htmlspecialchars($order['order_datetime']); ?></td>
                                        <td>MYR <?php echo htmlspecialchars($order['order_price']); ?></td>
                                        <td>
                                            <form action="" method="POST" style="display:inline;">
                                                <input type="hidden" name="order_id" value="<?= htmlspecialchars($order['order_id']); ?>">
                                                <select name="order_status" class="status-select" data-order-id="<?= htmlspecialchars($order['order_id']); ?>">
                                                    <option value="pending" <?= $order['payment_status'] == 'pending' ? 'selected' : ''; ?>>Pending</option>
                                                    <option value="received" <?= $order['payment_status'] == 'received' ? 'selected' : ''; ?>>Received</option>
                                                    <option value="completed" <?= $order['payment_status'] == 'completed' ? 'selected' : ''; ?>>Completed</option>
                                                    <option value="underpaid" <?= $order['payment_status'] == 'underpaid' ? 'selected' : ''; ?>>Underpaid</option>
                                                    <option value="cancelled" <?= $order['payment_status'] == 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                                                </select>
                                            </form>
                                        </td>
                                        <td>
                                            <div class="actions">
                                                <form action="" method="POST" style="display:inline;" onsubmit="return showDeactivateConfirmDialog(event);">
                                                    <input type="hidden" name="order_id" value="<?= htmlspecialchars($order['order_id']); ?>">
                                                    <input type="hidden" name="action" value="deactivate_order">
                                                    <button type="submit" class="delete-order-btn"><i class="bi bi-x-square"></i></button>
                                                </form>
                                                <button type="button" class="view-order-detail-btn" data-order-id="<?= htmlspecialchars($order['order_id']); ?>"><i class="bi bi-info-circle-fill"></i></button>
                                                <!-- <button type="button" class="edit-order-btn" data-order-id="<?= htmlspecialchars($order['order_id']); ?>"><i class="bi bi-pencil-square"></i></button> -->
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
                <?php if (!empty($all_orders)) : ?>
                    <?php include $_SERVER['DOCUMENT_ROOT'] . "/mips/components/pagination.php"; ?>
                <?php endif; ?>
            </div>
        </main>
    </div>
    <dialog id="add-edit-data">
        <h1>Add Order</h1>
        <form action="" method="post" enctype="multipart/form-data">
            <div class="input-container">
                <h2>Parent ID<sup>*</sup></h2>
                <select name="parent_student_id" required>
                </select>
                <p>Please select the parent.</p>
            </div>
            <div class="input-container">
                <h2>Order Amount (RM)<sup>*</sup></h2>
                <input type="number" step="0.01" name="order_price" required>
                <p>Please enter the order amount.</p>
            </div>
            <div class="input-container">
                <h2>Order Status<sup>*</sup></h2>
                <select name="order_status" required>
                    <option value="pending">Pending</option>
                    <option value="received">Received</option>
                    <option value="completed">Completed</option>
                    <option value="cancelled">Cancelled</option>
                </select>
                <p>Please select the order status.</p>
            </div>
            <div class="input-container">
                <h2>Order Item Details</h2>
                <div id="product-list">
                    <div class="product-info">
                        <div class="input-container">
                            <div class="input-field">
                                <h2>Product ID<sup>*</sup></h2>
                                <input type="text" name="product_id[]" required>
                            </div>
                            <div class="input-field">
                                <h2>Quantity<sup>*</sup></h2>
                                <input type="number" name="product_quantity[]" min="1" required>
                            </div>
                        </div>
                        <div class="input-container">
                            <h2>Product Image</h2>
                            <img name="product_image" src="" alt="Product Image" style="width: 100px; height: 100px;">
                        </div>
                    </div>
                </div>
                <button type="button" id="add-product-btn">Add Another Product</button>
            </div>
            <div class="input-container">
                <h2>Payment Receipt</h2>
                <img name="payment_image" src="" alt="Payment Image" style="width: 100px; height: 100px;">
            </div>
            <div class="input-container controls">
                <button type="button" class="cancel">Cancel</button>
                <button type="reset">Clear</button>
                <button type="submit" name="submit">Publish</button>
            </div>
        </form>
    </dialog>
    <dialog id="detail-dialog">
        <form>
            <div class="title">
                <div class="right">
                    <h1>Order Details</h1>
                </div>
                <div class="left">
                    <button class="actions cancel"><i class="bi bi-x-circle"></i></button>
                </div>
            </div>
            <div class="order-details-content">
                <table class="two-column-table">
                    <tr>
                        <td style="width: 30%">
                            <h4>Order ID :<h4>
                        </td>
                        <td style="width: 70%;">
                            <h4 id="order-id"></h4>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <h4>Parent Name :<h4>
                        </td>
                        <td>
                            <h4 id="parent-name"></h4>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <h4>Order Date :<h4>
                        </td>
                        <td>
                            <h4 id="order-date"></h4>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <h4>Order Amount :<h4>
                        </td>
                        <td>
                            <h4 id="order-amount"></h4>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <h4>Order Status :<h4>
                        </td>
                        <td>
                            <h4 id="order-status"></h4>
                        </td>
                    </tr>
                </table>
                <h2>Payment Receipt</h2>
                <img id="payment-image" src="" alt="Payment Image">
                <div class="order-items">
                    <h2>Order Items</h2>
                    <table>
                        <thead>
                            <tr>
                                <th>Product ID</th>
                                <th>Product Name</th>
                                <th>Quantity</th>
                                <th>Subtotal</th>
                            </tr>
                        </thead>
                        <tbody id="order-items-list">
                        </tbody>
                    </table>
                </div>
                <div class="controls">
                    <button type="button" class="cancel">Close</button>
                </div>
            </div>
        </form>
    </dialog>
    <?php include  $_SERVER['DOCUMENT_ROOT'] . "/mips/components/confirm_dialog.php"; ?>
    <script src="/mips/javascript/common.js"></script>
    <script src="/mips/javascript/admin.js"></script>
    <script>
        document.getElementById('add-product-btn').addEventListener('click', function() {
            const productInfo = document.querySelector('.product-info').cloneNode(true);
            const inputs = productInfo.querySelectorAll('input');
            inputs.forEach(input => input.value = '');
            document.getElementById('product-list').appendChild(productInfo);
        });

        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.status-select').forEach(select => {
                select.addEventListener('change', function() {
                    const orderId = this.closest('form').querySelector('input[name="order_id"]').value;
                    const orderStatus = this.value;

                    fetch('/mips/admin/ajax.php?action=update_order_status', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/x-www-form-urlencoded'
                            },
                            body: new URLSearchParams({
                                order_id: orderId,
                                order_status: orderStatus
                            })
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                fetch('ajax.php?action=get_pending_count')
                                    .then(response => response.text())
                                    .then(count => {
                                        const pendingOrderCountElement = document.getElementById('pending-order-count');
                                        if (parseInt(count) > 0) {
                                            pendingOrderCountElement.textContent = `${count}`;
                                            pendingOrderCountElement.style.display = 'inline';
                                        } else {
                                            pendingOrderCountElement.style.display = 'none';
                                        }
                                    })
                                    .catch(error => {
                                        console.error('Error fetching pending order count:', error);
                                        alert('Failed to update pending order count.');
                                    });
                            } else {
                                alert('Failed to update order status: ' + data.error);
                            }
                        })
                        .catch(error => {
                            console.error('Error updating order status:', error);
                            alert('Failed to update order status.');
                        });
                });
            });

            document.querySelectorAll('.view-order-detail-btn').forEach(button => {
                button.addEventListener('click', function() {
                    const orderId = this.dataset.orderId;

                    fetch('/mips/admin/ajax.php?action=get_order', {
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
                            if (data && data.order_id) {
                                document.getElementById('order-id').textContent = data.order_id;
                                document.getElementById('parent-name').textContent = data.parent_name;
                                document.getElementById('order-date').textContent = data.order_datetime;
                                document.getElementById('order-amount').textContent = `MYR ${data.order_price}`;
                                document.getElementById('order-status').textContent = data.payment_status;
                                document.getElementById('payment-image').src = '/mips/uploads/receipts/' + data.payment_image || '/mips/images/default_image_path.png';

                                const itemsList = document.getElementById('order-items-list');
                                itemsList.innerHTML = '';

                                data.items.forEach(item => {
                                    const row = document.createElement('tr');
                                    row.innerHTML = `
                                        <td>${item.product_id}</td>
                                        <td>${item.product_name}</td>
                                        <td>${item.product_quantity}</td>
                                        <td>MYR ${item.order_subtotal}</td>
                                        `;
                                    itemsList.appendChild(row);
                                });

                                document.getElementById('detail-dialog').showModal();
                            } else {
                                alert('Failed to fetch order details: No data found.');
                            }
                        })
                        .catch(error => {
                            console.error('Error fetching order details:', error);
                            alert('Failed to fetch order details.');
                        });
                });
            });
        });
    </script>
</body>

</html>