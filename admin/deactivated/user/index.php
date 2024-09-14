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
                        <h1>Deactivated User Menu</h1>
                    </div>
                    <div class="right">
                        <a href="/mips/admin/deactivated/"><i class="bi bi-arrow-90deg-up"></i>Deactivated Menu</a>
                    </div>
                </div>
                <div class="box-container">
                    <div class="box">
                        <div class="image-container">
                            <a href="/mips/admin/deactivated/user/admin.php"><img src="/mips/images/default_folder.png" alt="Deactivated Admin"></a>
                        </div>
                        <div class="info-container">
                            <h3>Admin</h3>
                        </div>
                    </div>
                    <div class="box">
                        <div class="image-container">
                            <a href="/mips/admin/deactivated/user/parent.php"><img src="/mips/images/default_folder.png" alt="Deactivated Parent"></a>
                        </div>
                        <div class="info-container">
                            <h3>Parent</h3>
                        </div>
                    </div>
                    <div class="box">
                        <div class="image-container">
                            <a href="/mips/admin/deactivated/user/student.php"><img src="/mips/images/default_folder.png" alt="Deactivated Student"></a>
                        </div>
                        <div class="info-container">
                            <h3>Student</h3>
                        </div>
                    </div>
                </div>
            </div>
            <!-- </?php include $_SERVER['DOCUMENT_ROOT'] . "/mips/components/pagination.php"; ?> -->
        </main>
    </div>
    <?php include $_SERVER['DOCUMENT_ROOT'] . "/mips/components/confirm_dialog.php"; ?>
    <script src="/mips/javascript/common.js"></script>
    <script src="/mips/javascript/admin.js"></script>
</body>

</html>