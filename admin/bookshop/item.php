<?php

include $_SERVER['DOCUMENT_ROOT'] . "/mips/php/admin.php";

$product_id = $_GET['pid'] ?? null;
if (!$product_id) {
    header('Location: 404.html');
    exit();
}

function getProductDetail($pdo, $product_id)
{
    $stmt = $pdo->prepare("
        SELECT p.product_id, p.product_name, p.product_description, p.product_price, p.stock_quantity, p.color, p.gender, 
               pc.category_name, pi.image_url
        FROM Product p
        LEFT JOIN Product_Category pc ON p.category_id = pc.category_id
        LEFT JOIN Product_Image pi ON p.product_id = pi.product_id AND pi.sort_order = 1
        WHERE p.product_id = ? AND p.status = 0
    ");
    $stmt->execute([$product_id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

$product = getProductDetail($pdo, $product_id);

function getApparelSizes($pdo, $product_id)
{
    $stmt = $pdo->prepare("
        SELECT s.size_name, s.shoulder_width, s.bust, s.waist, s.length, ps.size_id
        FROM Sizes s
        JOIN Product_Size ps ON s.size_id = ps.size_id
        JOIN Product p ON ps.product_id = p.product_id
        WHERE p.product_id = :product_id
        AND p.status = 0
        ORDER BY s.size_name ASC
    ");
    $stmt->bindParam(':product_id', $product_id, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$get_apparel_sizes = getApparelSizes($pdo, $product_id);

function getProductSizes($pdo, $product_id)
{
    $stmt = $pdo->prepare("
        SELECT s.size_name, ps.product_size_id
        FROM Product_Size ps
        JOIN Sizes s ON ps.size_id = s.size_id
        WHERE ps.product_id = ?
    ");
    $stmt->execute([$product_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$sizes = getProductSizes($pdo, $product_id);

function getProductImages($pdo, $product_id)
{
    $stmt = $pdo->prepare("SELECT image_url FROM Product_Image WHERE product_id = ? ORDER BY sort_order");
    $stmt->execute([$product_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$images = getProductImages($pdo, $product_id);

function getParentChildren($pdo, $user_id)
{
    $stmt = $pdo->prepare("
        SELECT s.student_id, s.student_name
        FROM Parent_Student ps
        JOIN Student s ON ps.student_id = s.student_id
        WHERE ps.parent_id = ?
    ");
    $stmt->execute([$user_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$children = getParentChildren($pdo, $_SESSION['admin_id'] ?? null);

$stockQuantity = $product['stock_quantity'] ?? 0;


$pageTitle = $product['product_name'] . " - MIPS";
include $_SERVER['DOCUMENT_ROOT'] . "/mips/components/admin_head.php"; ?>

<body>
    <?php include $_SERVER['DOCUMENT_ROOT'] . "/mips/components/admin_header.php"; ?>
    <div class="container">
        <?php include $_SERVER['DOCUMENT_ROOT'] . "/mips/components/admin_sidebar.php"; ?>
        <main class="product-detail">
            <div class="wrapper">
                <div class="title">
                    <div class="left">
                        <h1><?php echo htmlspecialchars($product['product_name']); ?></h1>
                        <p><?php echo htmlspecialchars($product['category_name']); ?></p>
                    </div>
                    <div class="right">
                        <a href="/mips/admin/bookshop/"><i class="bi bi-arrow-90deg-up"></i>Bookshop Product Menu</a>
                    </div>
                </div>
                <div class="product-details">
                    <div class="product-container">
                        <div class="picture-div">
                            <div class="product-image">
                                <?php if (!empty($images)) : ?>
                                    <img id="picture" alt="<?php echo htmlspecialchars($images[0]['image_url']); ?>" src="/mips/uploads/product/<?php echo htmlspecialchars($images[0]['image_url']); ?>">
                            </div>
                            <div class="popup-image">
                                <span class="close">&times;</span>
                                <img id="popup-image" src="/mips/uploads/product/<?php echo htmlspecialchars($images[0]['image_url']); ?>">
                            </div>
                            <div class="thumbnails">
                                <?php foreach ($images as $image) : ?>
                                    <img class="thumbnail" src="/mips/uploads/product/<?php echo htmlspecialchars($image['image_url']); ?>" data-src="/mips/uploads/product/<?php echo htmlspecialchars($image['image_url']); ?>">
                                <?php endforeach; ?>
                            </div>
                        <?php else : ?>
                            <p>No images available.</p>
                        <?php endif; ?>
                        </div>
                        <div class="product-info">
                            <h2>Product Description</h2>
                            <p><?php echo nl2br(htmlspecialchars($product['product_description'])); ?></p>
                            <div class="product-details-container">
                                <?php if (!empty($sizes)) : ?>
                                    <h2>Size</h2>
                                    <?php foreach ($sizes as $size) : ?>
                                        <button type="button" class="size-button" data-size-id="<?php echo htmlspecialchars($size['product_size_id']); ?>">
                                            <?php echo htmlspecialchars($size['size_name']); ?>
                                        </button>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                            <div class="product-details-container">
                                <h2>Price</h2>
                                <p>MYR <?php echo number_format($product['product_price'], 2); ?></p>
                            </div>
                            <div class="product-details-container">
                                <h2>Stock</h2>
                                <p><?php echo $stockQuantity; ?> pieces available</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
        <!-- </div>
            </section>
            <section class="size-chart">
                <div class="container">
                    <div class="wrapper">
                        <div class="title">
                            <div class="left">
                                <h1>Apparel Size</h1>
                            </div>
                            <div class="right">
                            </div>
                        </div>
                        <div class="table-body">
                            <table>
                                <thead>
                                    <tr>
                                        <th>Apparel Size Name</th>
                                        <th>Shoulder Width</th>
                                        <th>Bust</th>
                                        <th>Waist</th>
                                        <th>Length</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    </?php if (!empty($get_apparel_sizes)) : ?>
                                        </?php foreach ($get_apparel_sizes as $size) : ?>
                                            <tr>
                                                <td></?= htmlspecialchars($size['size_name']); ?></td>
                                                <td></?= htmlspecialchars($size['shoulder_width'] === null || $size['shoulder_width'] == 0 ? '-' : $size['shoulder_width']); ?></td>
                                                <td></?= htmlspecialchars(($size['bust'] === null || $size['bust'] == 0) ? '-' : $size['bust']); ?></td>
                                                <td></?= htmlspecialchars($size['waist'] === null || $size['waist'] == 0 ? '-' : $size['waist']); ?></td>
                                                <td></?= htmlspecialchars($size['length'] === null || $size['length'] == 0 ? '-' : $size['length']); ?></td>
                                            </tr>
                                        </?php endforeach; ?>
                                    </?php else : ?>
                                        <tr>
                                            <td colspan="6">No apparel sizes available.</td>
                                        </tr>
                                    </?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </section> -->
        <script src="/mips/javascript/common.js"></script>
        <script src="/mips/javascript/admin.js"></script>
</body>

</html>