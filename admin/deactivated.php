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
                        <h1>Deactivated List</h1>
                    </div>
                </div>
                <div class="box-container">
                    <div class="box">
                        <div class="image-container">
                            <a href="/mips/admin/deactivated/announcement.php"><img src="/mips/images/default_folder.png" alt="Deactivated Announcement"></a>
                        </div>
                        <div class="info-container">
                            <h3>Announcement</h3>
                        </div>
                    </div>
                    <div class="box">
                        <div class="image-container">
                            <a href="/mips/admin/deactivated/order.php"><img src="/mips/images/default_folder.png" alt="Deactivated Order"></a>
                        </div>
                        <div class="info-container">
                            <h3>Order</h3>
                        </div>
                    </div>
                    <div class="box">
                        <div class="image-container">
                            <a href="/mips/admin/deactivated/user/"><img src="/mips/images/default_folder.png" alt="Deactivated User"></a>
                        </div>
                        <div class="info-container">
                            <h3>User</h3>
                        </div>
                    </div>
                    <div class="box">
                        <div class="image-container">
                            <a href="/mips/admin/deactivated/bookshop/"><img src="/mips/images/default_folder.png" alt="Deactivated User"></a>
                        </div>
                        <div class="info-container">
                            <h3>Bookshop</h3>
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