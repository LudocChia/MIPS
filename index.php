<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mahans School</title>
    <link rel="icon" type="image/x-icon" href="./images/Mahans_internation_primary_school_logo.png">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
    <script src="https://code.jquery.com/jquery-3.7.1.js" integrity="sha256-eKhayi8LEQwp4NKxN+CfCh+3qOVUtJn3QNZ0TciWLP4=" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="./css/base.css">
    <link rel="stylesheet" href="./css/customer.css">
    <link rel="stylesheet" href="./css/index.css">
</head>

<body>
    <?php include './components/customer_header.php'; ?>
    <section class="banner">
        <div class="wrapper">
            <div class="slider">
                <div class="list">
                    <div class="item">
                        <img src="./images/start_your_online_grocery_shop.jpeg" alt="">
                    </div>
                    <div class="item">
                        <img src="./images/stay_home_we_delivery.jpg" alt="">
                    </div>
                    <div class="item">
                        <img src="./images/organic-food.png" alt="">
                    </div>
                    <div class="item">
                        <img src="./images/1717732903539.jpg" alt="">
                    </div>
                    <div class="item">
                        <img src="./images/a7056c19-6780-452a-9ae6-46c6ec849163_s2tntc9_2k.jpeg" alt="">
                    </div>
                </div>
                <ul class="dots">
                    <li class="active"></li>
                    <li></li>
                    <li></li>
                    <li></li>
                    <li></li>
                </ul>
            </div>
            <div class="buttons">
                <button id="prev"><i class="bi bi-chevron-left"></i></button>
                <button id="next"><i class="bi bi-chevron-right"></i></button>
            </div>
        </div>
    </section>
    <?php include "./components/customer_footer.php"; ?>
    <script src=""></script>
</body>

</html>