<?php

session_start();

include 'components/db_connect.php';

// $parent_student_id = '';  // 初始化 parent_student_id

// if (isset($_SESSION['parent_student_id'])) {
//     $parent_student_id = $_SESSION['parent_student_id'];
// }

$product_id = isset($_GET['pid']) ? $_GET['pid'] : header('location: error.php');

$stmt = $pdo->prepare("
    SELECT p.product_id, p.product_name, p.product_description, p.product_price, p.stock_quantity, p.color, p.gender, 
           pc.category_name, pi.image_url
    FROM Product p
    LEFT JOIN Product_Category pc ON p.category_id = pc.category_id
    LEFT JOIN Product_Image pi ON p.product_id = pi.product_id AND pi.sort_order = 1
    WHERE p.product_id = ? AND p.is_deleted = 0
");
$stmt->execute([$product_id]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$product) {
    header('location: error.php');
}

$imagesStmt = $pdo->prepare("SELECT image_url FROM Product_Image WHERE product_id = ? ORDER BY sort_order");
$imagesStmt->execute([$product_id]);
$images = $imagesStmt->fetchAll(PDO::FETCH_ASSOC);

$stmt = $pdo->prepare("
    SELECT p.product_id, p.product_name, p.product_description, p.product_price, pi.image_url
    FROM Product p
    LEFT JOIN Product_Image pi ON p.product_id = pi.product_id AND pi.sort_order = 1
    WHERE p.category_id = ? AND p.product_id != ? AND p.is_deleted = 0
    LIMIT 5
");

$stockQuantity = $product['stock_quantity'] ?? 0;
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($product['product_name']); ?> | Mahans School</title>
    <link rel="icon" type="image/x-icon" href="./images/Mahans_internation_primary_school_logo.png">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
    <script src="https://code.jquery.com/jquery-3.7.1.js" integrity="sha256-eKhayi8LEQwp4NKxN+CfCh+3qOVUtJn3QNZ0TciWLP4=" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="./css/base.css">
    <link rel="stylesheet" href="./css/common.css">
    <link rel="stylesheet" href="./css/customer.css">
</head>

<body>
    <?php include 'components/customer_header.php'; ?>
    <div class="breadcrumbs">
        <ul>
            <li>
                <a href="home.php">Home</a>
            </li>
            <li>
                <span class="material-symbols-outlined">navigate_next</span>
            </li>
            <li>
                <a href="bookshop.php">Bookshop</a>
            </li>
            <li>
                <span class="material-symbols-outlined">navigate_next</span>
            </li>
            <li>
                <a href="#"><?php echo htmlspecialchars($product['product_name']); ?></a>
            </li>
        </ul>
    </div>
    <div class="product-detail">
        <div class="container">
            <div class="wrapper">
                <div class="title">
                    <div class="left">
                        <h1><?php echo htmlspecialchars($product['product_name']); ?></h1>
                        <p><?php echo htmlspecialchars($product['category_name']); ?></p>
                    </div>
                </div>
                <section class="product-details">
                    <section class="product-container">
                        <div class="picture-div">
                            <?php if (!empty($images)) : ?>
                                <img id="picture" alt="<?php echo htmlspecialchars($images[0]['image_url']); ?>" src="uploads/<?php echo htmlspecialchars($images[0]['image_url']); ?>">
                                <div class="thumbnails">
                                    <?php foreach ($images as $image) : ?>
                                        <img class="thumbnail" src="uploads/<?php echo htmlspecialchars($image['image_url']); ?>" style="width: 80px;">
                                    <?php endforeach; ?>
                                </div>
                            <?php else : ?>
                                <p>No images available.</p>
                            <?php endif; ?>
                        </div>
                        <div class="productInfo">
                            <h2>Product Description</h2>
                            <p><?php echo nl2br(htmlspecialchars($product['product_description'])); ?></p>
                            <h2>Price</h2>
                            <p> MYR <?php echo number_format($product['product_price'], 2); ?></p>
                            <h2>Quantity</h2>
                            <div class="product-actions">
                                <input type="number" id="qty" name="qty" min="1" max="<?php echo $stockQuantity; ?>" value="1">
                                <button type="button" class="add-to-cart-button" onclick="addToCart(<?php echo $product['product_id']; ?>)">Add to Cart</button>
                                <span><?php echo $stockQuantity; ?> pieces available</span>
                            </div>
                        </div>
                    </section>
                </section>
            </div>
        </div>
    </div>
    <?php include 'components/customer_footer.php'; ?>
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    <script src="javascript/index.js"></script>
    <script src="javascript/common.js"></script>
    <script src="javascript/product_page.js"></script>
    <script type="text/javascript">
        function addToCart(product_id) {
            var qty = document.getElementById('qty').value;
            $.ajax({
                url: 'item.php',
                type: 'POST',
                data: {
                    action: 'add_to_cart',
                    product_id: product_id,
                    qty: qty
                },
                success: function(response) {
                    var data = JSON.parse(response);
                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Added to Cart',
                            text: data.message,
                        });
                    }
                }
            });
        }
    </script>
</body>

</html>