<?php

session_start();
include $_SERVER['DOCUMENT_ROOT'] . "/mips/components/db_connect.php";
include $_SERVER['DOCUMENT_ROOT'] . "/mips/components/customer_login.php";
$currentPage = basename($_SERVER['PHP_SELF']);
$pageTitle = "My Account  - MIPS";
include $_SERVER['DOCUMENT_ROOT'] . "/mips/components/customer_head.php";

?>

<body>
    <?php include $_SERVER['DOCUMENT_ROOT'] . "/mips/components/customer_header.php"; ?>
    <div class="container aside-main">
        <?php include  $_SERVER['DOCUMENT_ROOT'] . "/mips/components/customer_sidebar.php"; ?>
        <main class="profile">
            <div class="wrapper">
                <div class="title">
                    <div class="left">
                        <h1>My Account</h1>
                    </div>
                </div>
                <div class="img">
                    <img src=<?php echo htmlspecialchars($_SESSION['user_image']); ?> alt="">
                </div>
                <div>
                    <p>Name: <?php echo htmlspecialchars($_SESSION['user_name']); ?></p>
                    <p>Email: <?php echo htmlspecialchars($_SESSION['user_email']); ?></p>
                    <p>Phone: <?php echo htmlspecialchars($_SESSION['user_phone']); ?></p>
                </div>
            </div>
        </main>

    </div>

    <?php include $_SERVER['DOCUMENT_ROOT'] . "/mips/components/customer_footer.php"; ?>
    <script src="/mips/javascript/common.js"></script>
    <script src="/mips/javascript/customer.js"></script>
</body>

</html>