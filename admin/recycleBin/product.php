<?php

$database_table = "Product";
$rows_per_page = 10;
include $_SERVER['DOCUMENT_ROOT'] . "/mips/php/admin.php";
// include $_SERVER['DOCUMENT_ROOT'] . "/mips/php/deactivate_pagination.php";

function getDeletedProducts($pdo, $start, $rows_per_page)
{
    $sql = "SELECT p.product_id, p.product_name, p.product_price, pi.image_url
            FROM Product p
            JOIN Product_Image pi ON p.product_id = pi.product_id AND pi.sort_order = 1
            WHERE p.is_deleted = 1
            GROUP BY p.product_id
            LIMIT :start, :rows_per_page";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':start', $start, PDO::PARAM_INT);
    $stmt->bindParam(':rows_per_page', $rows_per_page, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$deleted_products = getDeletedProducts($pdo, $start, $rows_per_page);

// Assume functions to recover and delete products are defined elsewhere
if (isset($_POST['recover'])) {
    // Call function to recover product
}

if (isset($_POST['delete'])) {
    // Call function to permanently delete product
}

$pageTitle = "Bookshop Recycle Bin - MIPS";
include $_SERVER['DOCUMENT_ROOT'] . "/mips/components/admin_head.php";

$pageTitle = "Bookshop Products - MIPS";
include $_SERVER['DOCUMENT_ROOT'] . "/mips/components/admin_head.php"; ?>

<body id="<?php echo $id ?>">
    <?php include $_SERVER['DOCUMENT_ROOT'] . "/mips/components/admin_header.php"; ?>
    <div class="container">
        <?php include $_SERVER['DOCUMENT_ROOT'] . "/mips/components/admin_sidebar.php"; ?>
        <main class="products">
            <div class="wrapper">
                <div class="title">
                    <h1>Bookshop Recycle Bin</h1>
                </div>
                <div class="box-container">
                    <?php foreach ($deleted_products as $product): ?>
                        <div class="box" data-product-id="<?= htmlspecialchars($product['product_id']); ?>">
                            <div class="image-container">
                                <img src="/mips/uploads/product/<?= htmlspecialchars($product['image_url']); ?>" alt="<?= htmlspecialchars($product['product_name']); ?>">
                            </div>
                            <div class="details">
                                <h4><?= htmlspecialchars($product['product_name']); ?></h4>
                                <p>Price: RM <?= number_format($product['product_price'], 2); ?></p>
                                <form method="post">
                                    <input type="hidden" name="product_id" value="<?= htmlspecialchars($product['product_id']); ?>">
                                    <button type="submit" name="recover">Recover</button>
                                    <button type="submit" name="delete">Delete Permanently</button>
                                </form>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <?php include $_SERVER['DOCUMENT_ROOT'] . "/mips/components/pagination.php"; ?>
            </div>
        </main>
    </div>
    <script src="/mips/javascript/admin.js"></script>
</body>

</html>