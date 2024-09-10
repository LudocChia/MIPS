<?php

session_start();

include $_SERVER['DOCUMENT_ROOT'] . "/mips/components/db_connect.php";
include $_SERVER['DOCUMENT_ROOT'] . "/mips/components/customer_login.php";

function getProducts($pdo)
{
    $sql = "SELECT p.product_id, p.product_name, p.product_description, p.product_price, p.stock_quantity, p.color, p.gender, 
                   pi.image_url AS primary_image
            FROM Product p
            LEFT JOIN Product_Image pi ON p.product_id = pi.product_id AND pi.sort_order = 1
            WHERE p.status = 0";

    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$all_products = getProducts($pdo);

function getProductsQuantity($pdo)
{
    $sql = "SELECT p.product_id, p.product_name, p.product_description, p.product_price, p.stock_quantity, p.color, p.gender, 
                   pi.image_url AS primary_image
            FROM Product p
            LEFT JOIN Product_Image pi ON p.product_id = pi.product_id AND pi.sort_order = 1
            WHERE p.status = 0";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    return $stmt->rowCount();
}

$count = getProductsQuantity($pdo);

$pageTitle = "Bookshop - MIPS";
include $_SERVER['DOCUMENT_ROOT'] . "/mips/components/customer_head.php";
?>

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
                            <p>Total <b id="count"><?= $count ?></b> products</p>
                        </div>
                    </div>
                    <?php if (!empty($all_products)) : ?>
                        <div class="box-container">
                            <?php foreach ($all_products as $product) : ?>
                                <div class="box">
                                    <a href="/mips/item.php?pid=<?= htmlspecialchars($product['product_id']); ?>">
                                        <div class="image-container">
                                            <img src="<?= htmlspecialchars(!empty($product['primary_image']) ? "uploads/product/" . $product['primary_image'] : 'images/defaultproductimage.png'); ?>" alt="Product Image" class="primary-image">
                                        </div>
                                        <div class="info-container">
                                            <div class="name-field">
                                                <h3 style="font-weight: bold"><?= htmlspecialchars($product['product_name']); ?></h3>
                                            </div>
                                            <div class="price-size-container">
                                                <div class="price-field">
                                                    MYR <?= number_format($product['product_price'], 2); ?>
                                                </div>
                                            </div>
                                            <div class="color-gender-container">
                                                <span class="gender">Gender: <?= htmlspecialchars($product['gender']); ?></span>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else : ?>
                        <div class="empty">
                            <img src="/mips/images/no_data_found.png" alt="No Data Found Image">
                            <h3>No Products Found</h3>
                            <p>Please check back later for new arrivals!</p>
                        </div>
                    <?php endif; ?>
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