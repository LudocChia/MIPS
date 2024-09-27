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
        <main class="gallery">
            <div class="wrapper">
                <div class="title">
                    <div class="left">
                        <h1>Deactivated Bookshop Item</h1>
                    </div>
                </div>
                <div class="box-container">
                    <div class="box">
                        <div class="image-container">
                            <a href="/mips/admin/deactivated/bookshop/category.php"><img src="/mips/images/default_folder.png" alt="Deactivated Category"></a>
                        </div>
                        <div class="info-container">
                            <h3>Bookshop Category</h3>
                        </div>
                    </div>
                    <div class="box">
                        <div class="image-container">
                            <a href="/mips/admin/deactivated/bookshop/product.php"><img src="/mips/images/default_folder.png" alt="Deactivated Product"></a>
                        </div>
                        <div class="info-container">
                            <h3>Bookshop Product</h3>
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