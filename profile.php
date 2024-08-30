<?php

include $_SERVER['DOCUMENT_ROOT'] . "/mips/components/db_connect.php";
include $_SERVER['DOCUMENT_ROOT'] . "/mips/components/customer_login.php";

$pageTitle = "My Account  - MIPS";
include $_SERVER['DOCUMENT_ROOT'] . "/mips/components/customer_head.php";

?>

<body>
    <?php include $_SERVER['DOCUMENT_ROOT'] . "/mips/components/customer_header.php"; ?>
    <div class="container">
        <?php include  $_SERVER['DOCUMENT_ROOT'] . "/mips/components/customer_sidebar.php"; ?>
        <main class="profile">
            <div class="wrapper">
                <div class="title">
                    <div class="left">
                        <h1>My Account</h1>
                    </div>
                </div>
            </div>
        </main>

    </div>

    <?php include $_SERVER['DOCUMENT_ROOT'] . "/mips/components/customer_footer.php"; ?>
    <script src="/mips/javascript/common.js"></script>
    <script src="/mips/javascript/customer.js"></script>
</body>

</html>