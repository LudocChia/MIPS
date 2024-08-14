<?php

session_start();

include "../components/db_connect.php";

if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit();
}

$currentPage = basename($_SERVER['PHP_SELF']);

?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - MIPS</title>
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
        <?php include "../components/admin_sidebar.php"; ?>
        <!-- END OF ASIDE -->
        <main>
            <section class="middle">
                <div class="insights">
                    <div class="sales">
                        <i class="bi bi-person-plus"></i>
                        <div class="middle">
                            <div class="left">
                                <h3>Total Registered Parents and Students</h3>
                                <h1>100+</h1>
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
                    <!-- END OF SALES -->
                    <div class="sales">
                        <i class="bi bi-person-gear"></i>
                        <div class="middle">
                            <div class="left">
                                <h3>Total Registered Admin and Staff</h3>
                                <h1>10000+</h1>
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
                    <!-- END OF EXPENSE -->
                    <div class="sales">
                        <i class="bi bi-credit-card"></i>
                        <div class="middle">
                            <div class="left">
                                <h3>Total Cash in Amount</h3>
                                <h1>MYR 100</h1>
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
                                    <tr>
                                        <td>iPhone 14</td>
                                        <td>John Doe</td>
                                        <td>$1200</td>
                                        <td><span class="success">Paid</span></td>
                                    </tr>
                                    <tr>
                                        <td>iPhone 14</td>
                                        <td>John Doe</td>
                                        <td>$1200</td>
                                        <td><span class="success">Paid</span></td>
                                    </tr>
                                    <tr>
                                        <td>iPhone 14</td>
                                        <td>John Doe</td>
                                        <td>$1200</td>
                                        <td><span class="success">Paid</span></td>
                                    </tr>
                                    <tr>
                                        <td>iPhone 14</td>
                                        <td>John Doe</td>
                                        <td>$1200</td>
                                        <td><span class="success">Paid</span></td>
                                    </tr>
                                    <tr>
                                        <td>iPhone 14</td>
                                        <td>John Doe</td>
                                        <td>$1200</td>
                                        <td><span class="success">Paid</span></td>
                                    </tr>
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
                                <h5 class="success">+39%</h5>
                                <h3>3849</h3>
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
                                <h5 class="danger">-17%</h5>
                                <h3>3849</h3>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </main>
    </div>
    <script src="../javascript/admin.js"></script>
</body>

</html>