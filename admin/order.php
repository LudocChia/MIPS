<?php

session_start();
include "../components/db_connect.php";

if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit();
}

function getAllOrders($pdo)
{
    $sql = "SELECT o.order_id, o.order_datetime, o.order_price, p.parent_name
            FROM Orders o
            JOIN Parent_Student ps ON o.parent_student_id = ps.parent_student_id
            JOIN Parent p ON ps.parent_id = p.parent_id
            WHERE o.is_deleted = 0";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$all_orders = getAllOrders($pdo);

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

            $sqlOrder = "INSERT INTO Orders (order_id, parent_student_id, order_price, is_deleted) 
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
                $sqlOrderItem = "INSERT INTO Order_Item (order_item_id, order_id, product_id, product_quantity, order_subtotal, is_deleted) 
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


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bookshop Order - Mahans School</title>
    <link rel="icon" type="image/x-icon" href="../images/Mahans_internation_primary_school_logo.png">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
    <script src="https://code.jquery.com/jquery-3.7.1.js" integrity="sha256-eKhayi8LEQwp4NKxN+CfCh+3qOVUtJn3QNZ0TciWLP4=" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="../css/base.css">
    <link rel="stylesheet" href="../css/common.css">
    <link rel="stylesheet" href="../css/admin.css">
</head>

<body>
    <?php include "../components/admin_header.php"; ?>
    <div class="container">
        <aside>
            <button id="close-btn">
                <i class="bi bi-layout-sidebar-inset"></i>
            </button>
            <div class="sidebar">
                <ul>
                    <li>
                        <a href="index.php"><i class="bi bi-grid-1x2-fill"></i>
                            <h4>Dashboard</h4>
                        </a>
                    </li>
                    <li>
                        <a href="#" class="bookshop-btn">
                            <i class="bi bi-shop-window"></i>
                            <h4>Bookshop</h4>
                            <i class="bi bi-chevron-down first"></i>
                        </a>
                        <ul class="bookshop-show">
                            <li><a href="mainCategory.php"><i class="bi bi-tags-fill"></i>
                                    <h4>Main Category</h4>
                                </a>
                            </li>
                            <li><a href="subcategory.php"><i class="bi bi-tag-fill"></i>
                                    <h4>Subcategory</h4>
                                </a>
                            </li>
                            <li><a href="size.php"><i class="bi bi-aspect-ratio-fill"></i>
                                    <h4>Product Size</h4>
                                </a>
                            </li>
                            <li><a href="product.php"><i class="bi bi-box-seam-fill"></i>
                                    <h4>All Product</h4>
                                </a>
                            </li>
                        </ul>
                    </li>
                    <li>
                        <a href="order.php" class="active">
                            <i class="bi bi-receipt"></i>
                            <h4>Order</h4>
                            <span id="pending-order-count"></span>
                        </a>
                    </li>
                    <li>
                        <a href="announment.php">
                            <i class="bi bi-megaphone-fill"></i>
                            <h4>Announcement</h4>
                        </a>
                    </li>
                    <li>
                        <a href="deactivate.php">
                            <i class="bi bi-trash2-fill"></i>
                            <h4>Deactivate List</h4>
                        </a>
                    </li>
                    <li>
                        <a href="#" class="user-btn">
                            <i class="bi bi-person-fill"></i>
                            <h4>User Type</h4>
                            <i class="bi bi-chevron-down second"></i>
                        </a>
                        <ul class="user-show">
                            <li><a href="admin.php"><i class="bi bi-person-fill-gear"></i>
                                    <h4>All Admin</h4>
                                </a>
                            </li>
                            <li><a href="teacher.php"><i class="bi bi-mortarboard-fill"></i>
                                    <h4>All Teacher</h4>
                                </a>
                            </li>
                            <li>
                                <a href="parent.php"><i class="bi bi-people-fill"></i>
                                    <h4>All Parent</h4>
                                </a>
                            </li>
                        </ul>
                    </li>
                    <li><a href="#">
                            <i class="bi bi-file-text-fill"></i>
                            <h4>Report</h4>
                            <i class="bi bi-chevron-down first"></i>
                        </a>
                        <ul>
                            <li>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </aside>
        <!-- END OF ASIDE -->
        <main class="admin">
            <div class="wrapper">
                <div class="title">
                    <div class="left">
                        <h1>Bookshop Order</h1>
                    </div>
                    <div class="right">
                        <button class="btn btn-outline" id="open-popup"><i class="bi bi-plus-circle"></i></i>Add New Order</button>
                        <?php
                        try {
                            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                            $countQuery = "SELECT COUNT(*) FROM Payment WHERE payment_status = 0";
                            $stmt = $pdo->prepare($countQuery);
                            $stmt->execute();
                            $count = $stmt->fetchColumn();

                            echo "<p>Total $count Pending Order(s)</p>";
                        } catch (PDOException $e) {
                            echo "<script>alert('Database error: " . $e->getMessage() . "');</script>";
                        }
                        ?>
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
                                    <td>
                                        <form action="" method="POST" style="display:inline;">
                                            <input type="hidden" name="order_id" value="<?= htmlspecialchars($order['order_id']); ?>">
                                            <input type="hidden" name="delete" value="true">
                                            <button type="submit" class="delete-order-btn"><i class="bi bi-x-square-fill"></i></button>
                                        </form>
                                        <button type="button" class=""><i class="bi bi-info-circle-fill"></i></button>
                                        <button type="button" class="edit-order-btn" data-order-id="<?= htmlspecialchars($order['order_id']); ?>"><i class="bi bi-pencil-square"></i></button>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>
    <dialog id="add-edit-data">
        <h1>Add/Edit Order</h1>
        <form action="" method="post" enctype="multipart/form-data">
            <div class="input-container">
                <h2>Parent ID<sup>*</sup></h2>
                <select name="parent_student_id" required>
                    <!-- Populate with parent options -->
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
                    <option value="completed">Completed</option>
                    <option value="failed">Failed</option>
                </select>
                <p>Please select the order status.</p>
            </div>
            <div class="input-container">
                <h2>Order Item Details</h2>
                <div id="product-list">
                    <!-- Initial product input field -->
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
    <dialog id="delete-confirm-dialog">
        <form method="dialog">
            <h1>Order will be Deactivated!</h1>
            <label>Are you sure to proceed?</label>
            <div class="controls">
                <button value="cancel" class="cancel">Cancel</button>
                <button value="confirm" class="deactivate">Deactivate</button>
            </div>
        </form>
    </dialog>
    <script src="../javascript/admin.js"></script>
    <script>
        document.getElementById('add-product-btn').addEventListener('click', function() {
            // Clone the first product-info div
            const productInfo = document.querySelector('.product-info').cloneNode(true);

            // Clear the input fields in the cloned product-info
            const inputs = productInfo.querySelectorAll('input');
            inputs.forEach(input => input.value = '');

            // Append the cloned product-info to the product-list div
            document.getElementById('product-list').appendChild(productInfo);
        });

        $(document).ready(function() {
            $.ajax({
                url: 'ajax.php?action=get_pending_count',
                type: 'GET',
                success: function(response) {
                    if (parseInt(response) > 0) {
                        $('#pending-order-count').text(response);
                    } else {
                        $('#pending-order-count').hide();
                    }
                },
                error: function() {
                    $('#pending-order-count').hide();
                }
            });
        });

        document.querySelectorAll('.edit-order-btn').forEach(button => {
            button.addEventListener('click', function() {
                const orderId = this.dataset.orderId;

                fetch(`ajax.php?action=get_order&order_id=${orderId}`)
                    .then(response => response.json())
                    .then(orderDetails => {
                        if (orderDetails.error) {
                            alert(orderDetails.error);
                        } else {
                            const order = orderDetails[0];

                            document.querySelector('#add-edit-data [name="parent_student_id"]').value = order.parent_student_id;
                            document.querySelector('#add-edit-data [name="order_price"]').value = order.order_price;

                            document.querySelector('#add-edit-data [name="payment_status"]').value = order.payment_status;
                            document.querySelector('#add-edit-data [name="payment_image"]').src = order.payment_image;
                            document.querySelector('#add-edit-data [name="product_name"]').textContent = order.product_name;
                            document.querySelector('#add-edit-data [name="product_image"]').src = order.product_image;

                            document.querySelector('#add-edit-data h1').textContent = "Edit Order";
                            document.getElementById('add-edit-data').showModal();
                        }
                    })
                    .catch(error => {
                        console.error('Error fetching order data:', error);
                        alert('Failed to load order data.');
                    });
            });
        });
    </script>
</body>

</html>