<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bookshop - Mahans School</title>
    <link rel="icon" type="image/x-icon" href="../images/Mahans_internation_primary_school_logo.png">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
    <script src="https://code.jquery.com/jquery-3.7.1.js" integrity="sha256-eKhayi8LEQwp4NKxN+CfCh+3qOVUtJn3QNZ0TciWLP4=" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="../css/admin.css">
</head>

<body>
    <?php include "../components/admin_header.php"; ?>
    <div class="container">
        <aside>
            <button id="close-btn">
                <i class="bi bi-x"></i>
            </button>

            <div class="sidebar">
                <a href="index.php"><i class="bi bi-grid"></i>
                    <h4>Dashboard</h4>
                </a>
                <a href="bookshop.php" class="active"><i class="bi bi-receipt"></i>
                    <h4>Bookshop</h4>
                </a>
                <a href="#"><i class="bi bi-clipboard-data"></i>
                    <h4>Orders</h4>
                </a>
                <a href="#"><i class="bi bi-megaphone"></i>
                    <h4>Announcement</h4>
                </a>
                <a href="#"><i class="bi bi-box-arrow-right"></i>
                    <h4>Logout</h4>
                </a>
            </div>
        </aside>
        <!-- END OF ASIDE -->
        <main>
            <div class="box-container">
                <div class="header">
                    <h1>Bookshop</h1>
                </div>
            </div>
        </main>
    </div>
    </div>
    <script src="../javascript/admin.js"></script>
</body>

</html>