<?php

$database_table = "Product";
$rows_per_page = 10;
include $_SERVER['DOCUMENT_ROOT'] . "/mips/php/admin.php";
include $_SERVER['DOCUMENT_ROOT'] . "/mips/php/deactivated_pagination.php";

function getDeactivatedProducts($pdo, $start, $rows_per_page)
{
    $sql = "SELECT p.product_id, p.product_name, p.product_price, pi.image_url
            FROM Product p
            JOIN Product_Image pi ON p.product_id = pi.product_id AND pi.sort_order = 1
            WHERE p.status = 1
            GROUP BY p.product_id
            LIMIT :start, :rows_per_page";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':start', $start, PDO::PARAM_INT);
    $stmt->bindParam(':rows_per_page', $rows_per_page, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$deactivated_products = getDeactivatedProducts($pdo, $start, $rows_per_page);

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
                    <h1>Deactivated Bookshop Items</h1>
                </div>
                <?php if (!empty($deactivated_products)): ?>
                    <div class="box-container">
                        <?php foreach ($deactivated_products as $product): ?>
                            <div class="box" data-product-id="<?= htmlspecialchars($product['product_id']); ?>">
                                <div class="image-container">
                                    <img src="/mips/uploads/product/<?= htmlspecialchars($product['image_url']); ?>" alt="<?= htmlspecialchars($product['product_name']); ?>">
                                </div>
                                <div class="info-container">
                                    <h4><?= htmlspecialchars($product['product_name']); ?></h4>
                                    <p><?= number_format($product['product_price'], 2); ?></p>
                                    <div class="actions">
                                        <form method="POST" style="display:inline;" onsubmit="return showDeleteConfirmDialog(event);">
                                            <input type="hidden" name="product_id" value="<?= htmlspecialchars($product['product_id']); ?>">
                                            <input type="hidden" name="action" value="delete_product">
                                            <button type="submit" class="delete-product-btn"><i class="bi bi-x-square"></i></button>
                                        </form>
                                        <form method="POST" style="display:inline;" onsubmit="return showRecoverConfirmDialog(event);">
                                            <input type="hidden" name="product_id" value="<?= htmlspecialchars($product['product_id']); ?>">
                                            <input type="hidden" name="action" value="recover_product">
                                            <button type="submit" class="recover-product-btn"><i class="bi bi-arrow-clockwise"></i></button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else : ?>
                    <?php include $_SERVER['DOCUMENT_ROOT'] . "/mips/components/no_data_found.php"; ?>
                <?php endif; ?>
                <?php if (!empty($deactivated_products)) : ?>
                    <?php include $_SERVER['DOCUMENT_ROOT'] . "/mips/components/pagination.php"; ?>
                <?php endif; ?>
            </div>
        </main>
    </div>
    <?php include $_SERVER['DOCUMENT_ROOT'] . "/mips/components/confirm_dialog.php"; ?>
    <script src="/mips/javascript/common.js"></script>
    <script src="/mips/javascript/admin.js"></script>
</body>

</html>