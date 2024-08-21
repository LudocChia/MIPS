<?php

session_start();

include $_SERVER['DOCUMENT_ROOT'] . "/mips/components/db_connect.php";
include $_SERVER['DOCUMENT_ROOT'] . "/mips/components/customer_login.php";
$pageTitle = "Home - MIPS";
include $_SERVER['DOCUMENT_ROOT'] . "/mips/components/customer_head.php";
?>

<body>
    <?php include $_SERVER['DOCUMENT_ROOT'] . "/mips/components/customer_header.php"; ?>
    <section class="banner">
        <div class="container">
            <div class="wrapper">
                <div class="slider">
                    <div class="list">
                        <div class="item">
                            <img src="images/mojave(1).png" alt="">
                        </div>
                        <div class="item">
                            <img src="images/mojave(2).png" alt="">
                        </div>
                        <div class="item">
                            <img src="images/mojave(3).png" alt="">
                        </div>
                        <div class="item">
                            <img src="images/mojave(4).png" alt="">
                        </div>
                        <div class="item">
                            <img src="images/mojave(5).png" alt="">
                        </div>
                    </div>
                    <div class="buttons">
                        <button id="prev"><span class="material-symbols-outlined">arrow_back_ios_new</span></button>
                        <button id="next"><span class="material-symbols-outlined">arrow_forward_ios</span></button>
                    </div>
                    <ul class="dots">
                        <li class="active"></li>
                        <li></li>
                        <li></li>
                        <li></li>
                        <li></li>
                    </ul>
                </div>
            </div>
        </div>
    </section>
    <?php include $_SERVER['DOCUMENT_ROOT'] . "/mips/components/customer_footer.php"; ?>
    <script src="/mips/javascript/home.js"></script>
    <script src="/mips/javascript/customer.js"></script>
</body>

</html>