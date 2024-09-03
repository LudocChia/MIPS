<?php

$database_table = "Product_Category";
$rows_per_page = 10;
include $_SERVER['DOCUMENT_ROOT'] . "/mips/php/admin.php";
include $_SERVER['DOCUMENT_ROOT'] . "/mips/php/deactivated_pagination.php";

function getDeactivatedCategories($pdo, $start, $rows_per_page)
{
    $sql = "SELECT category_id, category_name, category_icon 
            FROM Product_Category 
            WHERE status = 1 
            LIMIT :start, :rows_per_page";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':start', $start, PDO::PARAM_INT);
    $stmt->bindParam(':rows_per_page', $rows_per_page, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$deactivated_categories = getDeactivatedCategories($pdo, $start, $rows_per_page);

$pageTitle = "Bookshop Recycle Bin - MIPS";
include $_SERVER['DOCUMENT_ROOT'] . "/mips/components/admin_head.php"; ?>

<body id="<?php echo $id ?>">
    <?php include $_SERVER['DOCUMENT_ROOT'] . "/mips/components/admin_header.php"; ?>
    <div class="container">
        <?php include $_SERVER['DOCUMENT_ROOT'] . "/mips/components/admin_sidebar.php"; ?>
        <main class="category">
            <div class="wrapper">
                <div class="title">
                    <h1>Deactivated Bookshop Categories</h1>
                </div>
                <?php if (!empty($deactivated_categories)) : ?>
                    <div class="box-container">
                        <?php foreach ($deactivated_categories as $category): ?>
                            <div class="box" data-category-id="<?= htmlspecialchars($category['category_id']); ?>">
                                <div class="image-container">
                                    <img src="/mips/uploads/category/<?= htmlspecialchars($category['category_icon']); ?>" alt="<?= htmlspecialchars($category['category_name']); ?>">
                                </div>
                                <div class="actions">
                                    <form action="" method="POST" style="display:inline;" onsubmit="return showDeleteConfirmDialog(event);">
                                        <input type="hidden" name="category_id" value="<?= htmlspecialchars($category['category_id']); ?>">
                                        <input type="hidden" name="action" value="delete_product_category">
                                        <button type="submit" class="delete-category-btn"><i class="bi bi-x-square"></i></button>
                                    </form>
                                    <form action="" method="POST" style="display:inline;" onsubmit="return showRecoverConfirmDialog(event);">
                                        <input type="hidden" name="category_id" value="<?= htmlspecialchars($category['category_id']); ?>">
                                        <input type="hidden" name="action" value="recover_product_category">
                                        <button type="submit" class="recover-parent-btn"><i class="bi bi-arrow-clockwise"></i></button>
                                    </form>
                                </div>
                                <div class="info-container">
                                    <h4><?= htmlspecialchars($category['category_name']); ?></h4>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else : ?>
                    <?php include $_SERVER['DOCUMENT_ROOT'] . "/mips/components/no_data_found.php"; ?>
                <?php endif; ?>
                <?php if (!empty($deactivated_categories)) : ?>
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