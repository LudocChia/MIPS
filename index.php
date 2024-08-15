<?php

session_start();

include "./components/db_connect.php";
include "./components/customer_login.php"

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mahans Internation Primary School</title>
    <link rel="icon" type="image/x-icon" href="./images/Mahans_internation_primary_school_logo.png">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
    <script src="https://code.jquery.com/jquery-3.7.1.js" integrity="sha256-eKhayi8LEQwp4NKxN+CfCh+3qOVUtJn3QNZ0TciWLP4=" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Abril+Fatface&family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="./css/base.css">
    <link rel="stylesheet" href="./css/common.css">
    <link rel="stylesheet" href="./css/customer.css">
</head>

<body>
    <?php include './components/customer_header.php'; ?>
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
    <?php include "./components/customer_footer.php"; ?>
    <script src="./javascript/home.js"></script>
    <script src="./javascript/customer.js"></script>
    <script src="../javascript/index.js"></script>
</body>

</html>