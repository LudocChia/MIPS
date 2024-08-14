<?php

session_start();

include "./components/db_connect.php";

// Fetch products from the database
$sql = "SELECT p.product_id, p.product_name, p.product_description, p.product_price, p.stock_quantity, p.color, p.gender, 
               pi.image_url AS primary_image
        FROM Product p
        LEFT JOIN Product_Image pi ON p.product_id = pi.product_id AND pi.sort_order = 1
        WHERE p.is_deleted = 0";

$stmt = $pdo->prepare($sql);
$stmt->execute();
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bookshop - MIPS</title>
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
    <?php include "./components/customer_header.php"; ?>
    <?php include "./components/customer_login.php"; ?>
    <section class="bookshop">
        <div class="container">
            <div class="products">
                <div class="wrapper">
                    <div class="title">
                        <div class="left">
                            <h1>Mahans International Primary School Bookshop</h1>
                        </div>
                        <div class="right">
                            <p>Total <b id="count"><?= count($products) ?></b> products</p>
                        </div>
                    </div>
                    <div class="box-container">
                        <?php if (count($products) > 0) : ?>
                            <?php foreach ($products as $product) : ?>
                                <div class="box">
                                    <div class="image-container">
                                        <a href="item.php?pid=<?= htmlspecialchars($product['product_id']); ?>">
                                            <img src="<?= htmlspecialchars(!empty($product['primary_image']) ? "uploads/product/" . $product['primary_image'] : 'images/defaultproductimage.png'); ?>" alt="Product Image" class="primary-image">
                                        </a>
                                    </div>
                                    <div class="name"><?= htmlspecialchars($product['product_name']); ?></div>
                                    <div class="price-size-container">
                                        <div class="price">MYR <?= number_format($product['product_price'], 2); ?></div>
                                    </div>
                                    <div class="color-gender">
                                        <span class="color">Color: <?= htmlspecialchars($product['color']); ?></span>
                                        <span class="gender">Gender: <?= htmlspecialchars($product['gender']); ?></span>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else : ?>
                            <div class="empty">
                                <img src="images/empty_cart.png" alt="Empty Cart Image">
                                <h4>No Products Found</h4>
                                <p>Please check back later for new arrivals!</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <a href="#" class="back-to-top">
        <span class="material-symbols-outlined">arrow_upward</span>
    </a>
    <?php include './components/customer_footer.php'; ?>
    <script src="./js/common.js"></script>
    <script src="./javascript/customer.js"></script>
</body>

</html>