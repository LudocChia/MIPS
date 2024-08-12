<?php

session_start();

include 'components/db_connect.php';

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
        WHERE p.product_id = ? AND p.is_deleted = 0
    ");
    $stmt->execute([$product_id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

$product = getProductDetail($pdo, $product_id);

if (!$product) {
    header('Location: error.php');
    exit();
}

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

$children = getParentChildren($pdo, $_SESSION['user_id'] ?? null);

if (empty($children)) {
    echo "<script>alert('No children found for the parent.');</script>";
} else {
    echo "<script>console.log('Children found: " . count($children) . "');</script>";
}

$stockQuantity = $product['stock_quantity'] ?? 0;

if (isset($_POST['submit'])) {
    $productId = $_POST['product_id'];
    $sizeId = $_POST['size_id'];
    $productPrice = $_POST['product_price'];
    $childId = $_POST['child'];
    $parentId = $_SESSION['user_id'];

    $targetDir = "uploads/receipts/";
    $fileName = basename($_FILES["payment_image"]["name"]);
    $targetFilePath = $targetDir . $fileName;
    $fileType = pathinfo($targetFilePath, PATHINFO_EXTENSION);

    if (move_uploaded_file($_FILES["payment_image"]["tmp_name"], $targetFilePath)) {
        try {
            $pdo->beginTransaction();

            $orderQuery = "INSERT INTO Orders (order_id, parent_student_id, order_price) 
                           VALUES (:order_id, (SELECT parent_student_id FROM Parent_Student WHERE parent_id = :parent_id AND student_id = :student_id), :order_price)";
            $orderStmt = $pdo->prepare($orderQuery);
            $orderId = uniqid('ORD');
            $orderStmt->bindParam(':order_id', $orderId);
            $orderStmt->bindParam(':parent_id', $parentId);
            $orderStmt->bindParam(':student_id', $childId);
            $orderStmt->bindParam(':order_price', $productPrice);
            $orderStmt->execute();

            $orderItemQuery = "INSERT INTO Order_Item (order_item_id, order_id, product_id, product_size_id, product_quantity, order_subtotal) 
                               VALUES (:order_item_id, :order_id, :product_id, :product_size_id, 1, :order_subtotal)";
            $orderItemStmt = $pdo->prepare($orderItemQuery);
            $orderItemId = uniqid('OI');
            $orderItemStmt->bindParam(':order_item_id', $orderItemId);
            $orderItemStmt->bindParam(':order_id', $orderId);
            $orderItemStmt->bindParam(':product_id', $productId);
            $orderItemStmt->bindParam(':product_size_id', $sizeId);
            $orderItemStmt->bindParam(':order_subtotal', $productPrice);
            $orderItemStmt->execute();

            // Insert into Payment table
            $paymentQuery = "INSERT INTO Payment (payment_id, parent_student_id, order_id, payment_amount, payment_status, payment_image) 
                             VALUES (:payment_id, (SELECT parent_student_id FROM Parent_Student WHERE parent_id = :parent_id AND student_id = :student_id), :order_id, :payment_amount, 'pending', :payment_image)";
            $paymentStmt = $pdo->prepare($paymentQuery);
            $paymentId = uniqid('PAY');
            $paymentStmt->bindParam(':payment_id', $paymentId);
            $paymentStmt->bindParam(':parent_id', $parentId);
            $paymentStmt->bindParam(':student_id', $childId);
            $paymentStmt->bindParam(':order_id', $orderId);
            $paymentStmt->bindParam(':payment_amount', $productPrice);
            $paymentStmt->bindParam(':payment_image', $targetFilePath);
            $paymentStmt->execute();

            $pdo->commit();

            echo "<script>alert('Purchase successful!'); window.location.href='order_history.php';</script>";
        } catch (PDOException $e) {
            $pdo->rollBack();
            echo "<script>alert('Purchase failed: " . $e->getMessage() . "');</script>";
        }
    } else {
        echo "<script>alert('Failed to upload receipt.');</script>";
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($product['product_name']); ?> - Mahans School</title>
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
    <?php include 'components/customer_login.php'; ?>
    <div class="breadcrumbs">
        <ul>
            <li>
                <a href="/mahans">
                    <h3>Home</h3>
                </a>
            </li>
            <li>
                <span class="material-symbols-outlined">navigate_next</span>
            </li>
            <li>
                <a href="bookshop.php">
                    <h3>Bookshop</h3>
                </a>
            </li>
            <li>
                <span class="material-symbols-outlined">navigate_next</span>
            </li>
            <li>
                <a href="#">
                    <h3><?php echo htmlspecialchars($product['product_name']); ?></h3>
                </a>
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
                    <div class="right">
                        <p><?php echo $stockQuantity; ?> pieces available</p>
                    </div>
                </div>
                <section class="product-details">
                    <section class="product-container">
                        <div class="picture-div">
                            <div class="product-image">
                                <?php if (!empty($images)) : ?>
                                    <img id="picture" alt="<?php echo htmlspecialchars($images[0]['image_url']); ?>" src="uploads/<?php echo htmlspecialchars($images[0]['image_url']); ?>">
                            </div>
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

                            <h2>Size</h2>
                            <div class="product-sizes">
                                <?php if (!empty($sizes)) : ?>
                                    <?php foreach ($sizes as $size) : ?>
                                        <button type="button" class="size-button" data-size-id="<?php echo htmlspecialchars($size['product_size_id']); ?>">
                                            <?php echo htmlspecialchars($size['size_name']); ?>
                                        </button>
                                    <?php endforeach; ?>
                                <?php else : ?>
                                    <p>No sizes available.</p>
                                <?php endif; ?>
                            </div>

                            <h2>Price</h2>
                            <p>MYR <?php echo number_format($product['product_price'], 2); ?></p>

                            <h2>Quantity</h2>
                            <div class="product-actions">
                                <input type="number" id="qty" name="qty" min="1" max="<?php echo $stockQuantity; ?>" value="1">
                                <button type="button" class="add-to-cart btn btn-outline" onclick="addToCart(<?php echo $product['product_id']; ?>)">Add to Cart</button>
                                <button type="button" class="buy-now btn btn-full">Buy Now</button>
                            </div>
                        </div>
                    </section>
                </section>
            </div>
        </div>
    </div>

    <dialog id="buy-now-dialog">
        <h2>Purchase Product</h2>
        <form action="" method="post" enctype="multipart/form-data">
            <input type="hidden" name="product_id" id="product-id" value="">
            <input type="hidden" name="size_id" id="size-id" value="">
            <input type="hidden" name="product_price" id="product-price" value="">

            <div class="input-field">
                <h2>Product Name</h2>
                <p id="product-name-display">Product Name Here</p>
            </div>
            <div class="input-field">
                <h2>Selected Size</h2>
                <p id="selected-size-display">Selected Size Here</p>
            </div>
            <div class="input-field">
                <h2>Price (RM)</h2>
                <p id="product-price-display">Product Price Here</p>
            </div>
            <div class="input-container">
                <h2>Select Child<sup>*</sup></h2>
                <select name="child" id="child" required>
                    <?php foreach ($children as $child) : ?>
                        <option value="<?= htmlspecialchars($child['student_id']) ?>"><?= htmlspecialchars($child['student_name']) ?></option>
                    <?php endforeach; ?>
                </select>
                <p>Please select which child you are buying for.</p>
            </div>
            <div class="input-container">
                <h2>Upload Transfer Receipt<sup>*</sup></h2>
                <input type="file" name="payment_image" accept=".jpg, .jpeg, .png" required>
                <p>Please upload the transfer receipt.</p>
            </div>
            <div class="controls">
                <button type="button" class="cancel" onclick="document.getElementById('buy-now-dialog').close()">Cancel</button>
                <button type="reset">Clear</button>
                <button type="submit" name="submit">Purchase</button>
            </div>
        </form>
    </dialog>


    <?php include 'components/customer_footer.php'; ?>
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    <script src="javascript/common.js"></script>
    <script type="text/javascript">
        document.querySelector('.buy-now').addEventListener('click', function() {
            const selectedSizeButton = document.querySelector('.size-button.selected');
            if (!selectedSizeButton) {
                alert('Please select a size.');
                return;
            }

            const sizeId = selectedSizeButton.getAttribute('data-size-id');
            const productName = '<?= htmlspecialchars($product['product_name']) ?>';
            const productPrice = '<?= number_format($product['product_price'], 2) ?>';

            document.getElementById('product-id').value = '<?= $product_id ?>';
            document.getElementById('size-id').value = sizeId;
            document.getElementById('product-price').value = productPrice;

            document.getElementById('product-name-display').textContent = productName;
            document.getElementById('selected-size-display').textContent = selectedSizeButton.textContent;
            document.getElementById('product-price-display').textContent = 'MYR ' + productPrice;

            const dialog = document.getElementById('buy-now-dialog');
            dialog.showModal();
        });

        document.querySelectorAll('.size-button').forEach(button => {
            button.addEventListener('click', function() {
                document.querySelectorAll('.size-button').forEach(btn => btn.classList.remove('selected'));
                this.classList.add('selected');
            });
        });

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