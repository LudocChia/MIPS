<?php

// $database_table = "admin";
// $rows_per_page = 12;
include $_SERVER['DOCUMENT_ROOT'] . "/mips/php/admin.php";

$pageTitle = "Admin Management - MIPS";
include $_SERVER['DOCUMENT_ROOT'] . "/mips/components/admin_head.php"; ?>

<body>
    <?php include $_SERVER['DOCUMENT_ROOT'] . "/mips/components/admin_header.php"; ?>
    <div class="container">
        <?php include $_SERVER['DOCUMENT_ROOT'] . "/mips/components/admin_sidebar.php"; ?>
        <main class="announcement">
            <div class="wrapper">
                <div class="title">
                    <div class="left">
                        <h1>Recycle Bin</h1>
                    </div>
                </div>
                <div class="box-container">
                    <div class="box">
                        <div class="image-container">
                            <a href="/mips/admin/recycleBin/announcement.php"><img src="images/" alt="Recycle Bin"></a>
                        </div>
                        <div class="txt">
                            <h3>Announcement</h3>
                        </div>
                    </div>
                    <div class="box">
                        <div class="image-container">
                            <a href="/mips/admin/recycleBin/order.php"><img src="images/" alt="Recycle Bin"></a>
                        </div>
                        <div class="txt">
                            <h3>Order</h3>
                        </div>
                    </div>
                    <div class="box">
                        <div class="image-container">
                            <a href="/mips/admin/recycleBin/parent.php"><img src="images/" alt="Recycle Bin"></a>
                        </div>
                        <div class="txt">
                            <h3>Parent</h3>
                        </div>
                    </div>
                </div>
                <!-- </?php include $_SERVER['DOCUMENT_ROOT'] . "/mips/components/pagination.php"; ?> -->
            </div>
        </main>
    </div>
    <?php include $_SERVER['DOCUMENT_ROOT'] . "/mips/components/confirm_dialog.php"; ?>
    <script src="/mips/javascript/common.js"></script>
    <script src="/mips/javascript/admin.js"></script>
</body>

</html>