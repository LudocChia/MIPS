<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Mahans School</title>
    <link rel="icon" type="image/x-icon" href="../images/Mahans_internation_primary_school_logo.png">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
    <script src="https://code.jquery.com/jquery-3.7.1.js" integrity="sha256-eKhayi8LEQwp4NKxN+CfCh+3qOVUtJn3QNZ0TciWLP4=" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="../css/admin.css">
</head>

<body>
    <?php include "../components/admin_header.php"; ?>
    <!-- END OF NAVBAR -->
    <div class="content-container">
        <aside>
            <button id="close-btn">
                <i class="bi bi-x"></i>
            </button>
            <div class="sidebar">
                <ul>
                    <li>
                        <a href="index.php" class="active"><i class="bi bi-grid"></i>
                            <h4>Dashboard</h4>
                        </a>
                    </li>
                    <li>
                        <div class="icon-link">
                            <a href="#">
                                <i class="bi bi-shop-window"></i>
                                <h4>Bookshop</h4>
                                <i class="bi bi-chevron-down"></i>
                            </a>
                        </div>
                        <ul class="sub-menu">
                            <li><a href="mainCategory.php"><i class="bi bi-tag"></i>
                                    <h4>Main Category</h4>
                                </a>
                            </li>
                            <li><a href="subcategory.php"><i class="bi bi-tag"></i>
                                    <h4>Subcategory</h4>
                                </a>
                            </li>
                            <li><a href="product_size.php"><span class="material-symbols-outlined">resize</span>
                                    <h4>Product Size</h4>
                                </a>
                            </li>
                            <li><a href="product.php"><i class="bi bi-box-seam"></i>
                                    <h4>All Product</h4>
                                </a>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </aside>
        <!-- END OF ASIDE -->
        <main>
            <section class="middle">
                <div class="insights">
                    <div class="sales">
                        <i class="bi bi-file-bar-graph"></i>
                        <div class="middle">
                            <div class="left">
                                <h3>Total Sales</h3>
                                <h1>$25,024</h1>
                            </div>
                            <div class="progress">
                                <svg>
                                    <circle cx="38" cy="38" r="36"></circle>
                                </svg>
                                <div class="number">
                                    <p>81%</p>
                                </div>
                            </div>
                        </div>
                        <small class="text-muted">Last 24 Hours</small>
                    </div>
                    <!-- END OF SALES -->
                    <div class="sales">
                        <i class="bi bi-file-bar-graph"></i>
                        <div class="middle">
                            <div class="left">
                                <h3>Total Sales</h3>
                                <h1>$25,024</h1>
                            </div>
                            <div class="progress">
                                <svg>
                                    <circle cx="38" cy="38" r="36"></circle>
                                </svg>
                                <div class="number">
                                    <p>81%</p>
                                </div>
                            </div>
                        </div>
                        <small class="text-muted">Last 24 Hours</small>
                    </div>
                    <!-- END OF EXPENSE -->
                    <div class="sales">
                        <i class="bi bi-file-bar-graph"></i>
                        <div class="middle">
                            <div class="left">
                                <h3>Total Sales</h3>
                                <h1>$25,024</h1>
                            </div>
                            <div class="progress">
                                <svg>
                                    <circle cx="38" cy="38" r="36"></circle>
                                </svg>
                                <div class="number">
                                    <p>81%</p>
                                </div>
                            </div>
                        </div>
                        <small class="text-muted">Last 24 Hours</small>
                    </div>
                    <!-- END OF SALES -->
                </div>
                <!-- END OF INSIGHTS -->

                <div class="recent-orders">
                    <div class="box-container">
                        <h2>Recent Orders</h2>
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
            </section>
            <section class="right">
                <div class="recent-updates">
                    <div class="box-container">
                        <h2>Recent Updates</h2>
                        <div class="updates">

                            <div class="update">
                                <div class="profile-photo">
                                    <img src="../uploads/wangbingbing(2).jpg">
                                </div>
                                <div class="message">
                                    <p><b>Admin</b> received a new order</p>
                                    <small class="text-muted">2 minutes ago</small>
                                </div>
                            </div>
                            <div class="update">
                                <div class="profile-photo">
                                    <img src="../uploads/wangbingbing(3).jpg">
                                </div>
                                <div class="message">
                                    <p><b>Admin</b> received a new order</p>
                                    <small class="text-muted">2 minutes ago</small>
                                </div>
                            </div>
                            <div class="update">
                                <div class="profile-photo">
                                    <img src="../uploads/wangbingbing(4).png">
                                </div>
                                <div class="message">
                                    <p><b>Admin</b> received a new order</p>
                                    <small class="text-muted">2 minutes ago</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="sales-analytics">
                    <div class="box-container">
                        <h2>Sales Analytics</h2>
                        <div class="item online">
                            <div class="icon">
                                <i class="bi bi-cart"></i>
                            </div>
                            <div class="right">
                                <div class="info">
                                    <h3>Online Orders</h3>
                                    <small class="text-muted">Last 24 Hours</small>
                                </div>
                                <h5 class="success">+39%</h5>
                                <h3>3849</h3>
                            </div>
                        </div>
                        <div class="item offline">
                            <div class="icon">
                                <i class="bi bi-cart"></i>
                            </div>
                            <div class="right">
                                <div class="info">
                                    <h3>Offline Orders</h3>
                                    <small class="text-muted">Last 24 Hours</small>
                                </div>
                                <h5 class="danger">-17%</h5>
                                <h3>3849</h3>
                            </div>
                        </div>
                    </div>
                    <!-- <div class="item customers">
            <div class="icon">
                <i class="bi bi-cart"></i>
            </div>
            <div class="right">
                <div class="info">
                    <h3>New Customers</h3>
                    <small class="text-muted">Last 24 Hours</small>
                </div>
                <h5 class="success">+25%</h5>
                <h3>849</h3>
            </div>
        </div>
        <div class="item add-product">
            <div>
                <i class="bi bi-plus-circle"></i>
                <h3>Add Product</h3>
            </div>
        </div> -->
                </div>
            </section>
        </main>
        <!-- END OF MAIN -->
    </div>
    <script src="../javascript/admin.js"></script>
</body>

</html>