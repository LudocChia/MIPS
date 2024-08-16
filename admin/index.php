<?php

session_start();

include "../components/db_connect.php";

if (!isset($_SESSION['admin_id'])) {
    header('Location: ./login.php');
    exit();
}

$currentPage = basename($_SERVER['PHP_SELF']);

function getTotalParentsAndStudents($pdo)
{
    $sqlParents = "SELECT COUNT(*) as total_parents FROM Parent WHERE is_deleted = 0";
    $stmtParents = $pdo->query($sqlParents);
    $totalParents = $stmtParents->fetch(PDO::FETCH_ASSOC)['total_parents'];

    $sqlStudents = "SELECT COUNT(*) as total_students FROM Student WHERE is_deleted = 0";
    $stmtStudents = $pdo->query($sqlStudents);
    $totalStudents = $stmtStudents->fetch(PDO::FETCH_ASSOC)['total_students'];

    return $totalParents + $totalStudents;
}

$totalParentsAndStudents = getTotalParentsAndStudents($pdo);

function getTotalAdmins($pdo)
{
    $sqlAdmins = "SELECT COUNT(*) as total_admins FROM Admin WHERE is_deleted = 0";
    $stmtAdmins = $pdo->query($sqlAdmins);
    return $stmtAdmins->fetch(PDO::FETCH_ASSOC)['total_admins'];
}

$totalAdmins = getTotalAdmins($pdo);

function getTotalCashInAmount($pdo)
{
    $sqlCashIn = "SELECT SUM(payment_amount) as total_cash_in FROM Payment WHERE payment_status = 'completed'";
    $stmtCashIn = $pdo->query($sqlCashIn);
    return $stmtCashIn->fetch(PDO::FETCH_ASSOC)['total_cash_in'];
}

$totalCashIn = getTotalCashInAmount($pdo);

function getRecentOrders($pdo, $limit = 5)
{
    $sqlRecentOrders = "
        SELECT 
            o.order_id, 
            p.product_name, 
            ps.student_id, 
            s.student_name, 
            oi.order_subtotal, 
            py.payment_status
        FROM 
            Orders o
        JOIN 
            Order_Item oi ON o.order_id = oi.order_id
        JOIN 
            Product p ON oi.product_id = p.product_id
        JOIN 
            Parent_Student ps ON o.parent_student_id = ps.parent_student_id
        JOIN 
            Student s ON ps.student_id = s.student_id
        JOIN 
            Payment py ON o.order_id = py.order_id
        ORDER BY 
            o.order_datetime DESC
        LIMIT :limit
    ";
    $stmtRecentOrders = $pdo->prepare($sqlRecentOrders);
    $stmtRecentOrders->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
    $stmtRecentOrders->execute();
    return $stmtRecentOrders->fetchAll(PDO::FETCH_ASSOC);
}

$recentOrders = getRecentOrders($pdo, 5);

?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - MIPS</title>
    <link rel="icon" type="image/x-icon" href="../images/Mahans_IPS_icon.png">
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
        <?php include "../components/admin_sidebar.php"; ?>
        <main>
            <section class="middle">
                <div class="insights">
                    <div class="wrapper">
                        <div class="sales">
                            <i class="bi bi-person-plus"></i>
                            <div class="middle">
                                <div class="left">
                                    <h3>Total Registered Parents and Students</h3>
                                    <h1><?php echo $totalParentsAndStudents; ?></h1>
                                </div>
                                <!-- <div class="progress">
                                <svg>
                                    <circle cx="38" cy="38" r="36"></circle>
                                </svg>
                                <div class="number">
                                    <p>81%</p>
                                </div>
                            </div> -->
                            </div>
                            <small class="text-muted">Last 12 Months</small>
                        </div>
                    </div>
                    <!-- END OF SALES -->
                    <div class="wrapper">
                        <div class="sales">
                            <i class="bi bi-person-gear"></i>
                            <div class="middle">
                                <div class="left">
                                    <h3>Total Registered Admin and Staff</h3>
                                    <h1><?php echo $totalAdmins; ?></h1>
                                </div>
                                <!-- <div class="progress">
                                <svg>
                                    <circle cx="38" cy="38" r="36"></circle>
                                </svg>
                                <div class="number">
                                    <p>81%</p>
                                </div>
                            </div> -->
                            </div>
                            <small class="text-muted">Last 12 Months</small>
                        </div>
                    </div>
                    <!-- END OF EXPENSE -->
                    <div class="wrapper">
                        <div class="sales">
                            <i class="bi bi-credit-card"></i>
                            <div class="middle">
                                <div class="left">
                                    <h3>Total Cash in Amount</h3>
                                    <h1>MYR <?php echo number_format($totalCashIn, 2); ?></h1>
                                </div>
                                <!-- <div class="progress">
                                <svg>
                                    <circle cx="38" cy="38" r="36"></circle>
                                </svg>
                                <div class="number">
                                    <p>81%</p>
                                </div>
                            </div> -->
                            </div>
                            <small class="text-muted">Last 12 Months</small>
                        </div>
                    </div>
                    <!-- END OF SALES -->
                </div>
                <!-- END OF INSIGHTS -->
                <div class="recent-orders">
                    <div class="wrapper">
                        <!-- <div class="recent-orders"> -->
                        <div class="title">
                            <div class="left">
                                <h2>Recent Orders</h2>
                            </div>
                            <div class="right">
                                <a href="./order.php" class="more">View All<i class="bi bi-chevron-right"></i></a>
                            </div>
                        </div>
                        <div class="table-body">
                            <table>
                                <thead>
                                    <tr>
                                        <th>Product</th>
                                        <th>Customer</th>
                                        <th>Price</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($recentOrders as $order) { ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($order['product_name']); ?></td>
                                            <td><?php echo htmlspecialchars($order['student_name']); ?></td>
                                            <td>MYR <?php echo number_format($order['order_subtotal'], 2); ?></td>
                                            <td><span class="<?php echo $order['payment_status'] == 'completed' ? 'success' : 'pending'; ?>">
                                                    <?php echo ucfirst($order['payment_status']); ?></span></td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </section>
            <section class="right">
                <div class="recent-updates">
                    <div class="wrapper">
                        <div class="title">
                            <div class="left">
                                <h2>Recent Updates</h2>
                            </div>
                            <div class="right">
                                <a href="#" class="more">View All<i class="bi bi-chevron-right"></i></a>
                            </div>
                        </div>
                        </title>
                        <div class="updates">
                            <div class="update">
                                <div class="profile-photo">
                                    <!-- <img src="../uploads/wangbingbing(2).jpg"> -->
                                </div>
                                <div class="message">
                                    <p><b>Admin</b> received a new order</p>
                                    <small class="text-muted">2 minutes ago</small>
                                </div>
                            </div>
                            <div class="update">
                                <div class="profile-photo">
                                    <!-- <img src="../uploads/wangbingbing(3).jpg"> -->
                                </div>
                                <div class="message">
                                    <p><b>Admin</b> received a new order</p>
                                    <small class="text-muted">2 minutes ago</small>
                                </div>
                            </div>
                            <div class="update">
                                <div class="profile-photo">
                                    <!-- <img src="../uploads/wangbingbing(4).png"> -->
                                </div>
                                <div class="message">
                                    <p><b>Admin</b> received a new order</p>
                                    <small class="text-muted">2 minutes ago</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="web-analytics">
                    <div class="wrapper">
                        <div class="title">
                            <div class="left">
                                <h2>Website Analytics</h2>
                            </div>
                        </div>
                        <div class="item">
                            <div class="icon">
                                <span class="material-symbols-outlined">travel_explore</span>
                            </div>
                            <div class="right">
                                <div class="info">
                                    <h3>Page Views</h3>
                                    <small class="text-muted">Last 24 Hours</small>
                                </div>
                                <h5 class="success">+1%</h5>
                                <h3>1</h3>
                            </div>
                        </div>
                        <div class="item">
                            <div class="icon">
                                <span class="material-symbols-outlined">co_present</span>
                            </div>
                            <div class="right">
                                <div class="info">
                                    <h3>Unique Visitors</h3>
                                    <small class="text-muted">Last 24 Hours</small>
                                </div>
                                <h5 class="danger">0%</h5>
                                <h3>1</h3>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </main>
    </div>
    <script src="../javascript/admin.js"></script>
    <script>
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
    </script>
</body>

</html>