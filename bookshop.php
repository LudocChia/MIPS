<?php

session_start();

include $_SERVER['DOCUMENT_ROOT'] . "/mips/components/db_connect.php";
include $_SERVER['DOCUMENT_ROOT'] . "/mips/components/customer_login.php";

$sql = "SELECT p.product_id, p.product_name, p.product_description, p.product_price, p.stock_quantity, p.color, p.gender, 
               pi.image_url AS primary_image
        FROM Product p
        LEFT JOIN Product_Image pi ON p.product_id = pi.product_id AND pi.sort_order = 1
        WHERE p.is_deleted = 0";

$stmt = $pdo->prepare($sql);
$stmt->execute();
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<?php include $_SERVER['DOCUMENT_ROOT'] . "/mips/components/customer_head.php"; ?>

<body>
    <?php include $_SERVER['DOCUMENT_ROOT'] . "/mips/components/customer_header.php"; ?>
    <section class="bookshop">
        <div class="container">
            <div class="products">
                <div class="wrapper">
                    <div class="title">
                        <div class="left">
                            <h1>MIPS Bookshop</h1>
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
    <?php include $_SERVER['DOCUMENT_ROOT'] . '/mips/components/customer_footer.php'; ?>
    <script src="/mips/javascript/common.js"></script>
    <script src="/mips/javascript/customer.js"></script>
</body>

</html>