<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mahans School Admin Dashboard</title>
    <link rel="icon" type="image/x-icon" href="../images/Mahans_internation_primary_school_logo.png">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
    <script src="https://code.jquery.com/jquery-3.7.1.js" integrity="sha256-eKhayi8LEQwp4NKxN+CfCh+3qOVUtJn3QNZ0TciWLP4=" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="../css/base.css">
    <link rel="stylesheet" href="../css/admin.css">
    <link rel="stylesheet" href="../css/index.css">
</head>

<body>
    <?php include "./topbar.php"; ?>
    <div class="container">
        <aside>
            <div class="top">
                <div class="logo">
                    <img src="../images/Mahans_IPS_logo.png" alt="Mahans Internation Primary School logo">
                    <h2>EGA<span class="danger">TOR</span></h2>
                </div>
                <div class="close" id="close-btn">
                    <i class="bi bi-x"></i>
                </div>
            </div>
            <div class="sidebar">
                <a href="#">
                    <i class="bi bi-grid"></i>
                    <h3>Dashboard</h3>
                </a>
                <a href="#">
                    <i class="bi bi-shop"></i>
                    <h3>Products</h3>
                </a>
                <a href="#">
                    <i class="bi bi-receipt"></i>
                    <h3>Orders</h3>
                </a>
                <a href="#">
                    <i class="bi bi-clipboard-data"></i>
                    <h3>Report</h3>
                </a>
                <a href="#">
                    <i class="bi bi-gear"></i>
                    <h3>Setting</h3>
                </a>
                <a href="#">
                    <i class="bi bi-megaphone"></i>
                    <h3>Annoument</h3>
                </a>
                <a href="#">
                    <i class="bi bi-box-arrow-right"></i>
                    <h3>Logout</h3>
                </a>
            </div>
        </aside>
    </div>
    <script src="../js/common.js"></script>
</body>

</html>