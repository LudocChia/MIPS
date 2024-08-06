<?php
include "../components/db_connect.php";

// Function to fetch all subcategories
function getSubcategories($pdo)
{
    $sql = "SELECT * FROM Product_Category WHERE parent_id IS NOT NULL AND is_deleted = 0";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$all_subcategories = getSubcategories($pdo);

function getAllProducts($pdo)
{
    $sql = "
        SELECT p.product_id, p.product_name, p.product_description, p.product_price, p.product_unit_price, 
               p.stock_quantity, p.color, p.gender, pi.image_url
        FROM Product p
        LEFT JOIN Product_Image pi ON p.product_id = pi.product_id
        WHERE p.is_deleted = 0 AND pi.sort_order = 1
        GROUP BY p.product_id
    ";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$all_products = getAllProducts($pdo);

if (isset($_POST['submit'])) {
    $name = $_POST['name'];
    $subcategoryId = $_POST['subcategory'];
    $description = !empty($_POST['description']) ? $_POST['description'] : null;
    $price = $_POST['price'];
    $unitPrice = !empty($_POST['unit_price']) ? $_POST['unit_price'] : null;
    $stockQuantity = $_POST['stock_quantity'];
    $color = !empty($_POST['color']) ? $_POST['color'] : null;
    $gender = $_POST['gender'];

    // Validate required inputs
    if (!empty($name) && !empty($subcategoryId) && !empty($price) && !empty($stockQuantity) && !empty($gender)) {
        // Handle file upload
        if ($_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $fileTmpPath = $_FILES['image']['tmp_name'];
            $fileName = $_FILES['image']['name'];
            $fileSize = $_FILES['image']['size'];
            $fileType = $_FILES['image']['type'];
            $fileNameCmps = explode(".", $fileName);
            $fileExtension = strtolower(end($fileNameCmps));

            $allowedfileExtensions = ['jpg', 'jpeg', 'png'];
            if (in_array($fileExtension, $allowedfileExtensions)) {
                // Directory where the uploaded file will be moved
                $uploadFileDir = '../uploads/';
                $newFileName = uniqid() . '.' . $fileExtension; // Generate unique filename
                $dest_path = $uploadFileDir . $newFileName;

                if (move_uploaded_file($fileTmpPath, $dest_path)) {
                    // Insert product into the Product table
                    $sql = "INSERT INTO Product (product_name, category_id, product_description, product_price, product_unit_price, stock_quantity, color, gender) 
                            VALUES (:name, :subcategory, :description, :price, :unit_price, :stock_quantity, :color, :gender)";
                    $stmt = $pdo->prepare($sql);
                    $stmt->bindParam(':name', $name);
                    $stmt->bindParam(':subcategory', $subcategoryId);
                    $stmt->bindParam(':description', $description);
                    $stmt->bindParam(':price', $price);
                    $stmt->bindParam(':unit_price', $unitPrice);
                    $stmt->bindParam(':stock_quantity', $stockQuantity);
                    $stmt->bindParam(':color', $color);
                    $stmt->bindParam(':gender', $gender);
                    $stmt->execute();

                    // Get the last inserted product ID
                    $productId = $pdo->lastInsertId();

                    // Insert the image into the Product_Image table
                    $sqlImage = "INSERT INTO Product_Image (product_id, image_url, sort_order) VALUES (:product_id, :image_url, :sort_order)";
                    $stmtImage = $pdo->prepare($sqlImage);
                    $sortOrder = 1; // Default sort order for now
                    $stmtImage->bindParam(':product_id', $productId);
                    $stmtImage->bindParam(':image_url', $newFileName);
                    $stmtImage->bindParam(':sort_order', $sortOrder);
                    $stmtImage->execute();

                    echo "<script>alert('Product added successfully!');</script>";
                } else {
                    echo "<script>alert('There was an error moving the uploaded file.');</script>";
                }
            } else {
                echo "<script>alert('Upload failed. Allowed file types: " . implode(',', $allowedfileExtensions) . "');</script>";
            }
        } else {
            echo "<script>alert('There was an error uploading the file.');</script>";
        }
    } else {
        echo "<script>alert('Please fill in all required fields.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bookshop - Mahans School</title>
    <link rel="icon" type="image/x-icon" href="../images/Mahans_internation_primary_school_logo.png">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
    <script src="https://code.jquery.com/jquery-3.7.1.js" integrity="sha256-eKhayi8LEQwp4NKxN+CfCh+3qOVUtJn3QNZ0TciWLP4=" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="../css/admin.css">
</head>

<body>
    <?php include "../components/admin_header.php"; ?>
    <div class="container">
        <aside>
            <button id="close-btn">
                <i class="bi bi-x"></i>
            </button>
            <div class="sidebar">
                <ul>
                    <li>
                        <a href="index.php"><i class="bi bi-grid-1x2-fill"></i>
                            <h4>Dashboard</h4>
                        </a>
                    </li>
                    <li>
                        <a href="#" class="bookshop-btn">
                            <i class="bi bi-shop-window"></i>
                            <h4>Bookshop</h4>
                            <i class="bi bi-chevron-down first"></i>
                        </a>
                        <ul class="bookshop-show">
                            <li><a href="mainCategory.php"><i class="bi bi-tags-fill"></i>
                                    <h4>Main Category</h4>
                                </a>
                            </li>
                            <li><a href="subcategory.php"><i class="bi bi-tag-fill"></i>
                                    <h4>Subcategory</h4>
                                </a>
                            </li>
                            <li><a href="productSize.php"><i class="bi bi-aspect-ratio-fill"></i>
                                    <h4>Product Size</h4>
                                </a>
                            </li>
                            <li><a href="product.php" class="active"><i class="bi bi-box-seam-fill"></i>
                                    <h4>All Product</h4>
                                </a>
                            </li>
                        </ul>
                    </li>
                    <li>
                        <a href="#" class="user-btn">
                            <i class="bi bi-person-fill"></i>
                            <h4>User Type</h4>
                            <i class="bi bi-chevron-down second"></i>
                        </a>
                        <ul class="user-show">
                            <li><a href="admin.php"><i class="bi bi-person-fill-gear"></i>
                                    <h4>All Admin</h4>
                                </a>
                            </li>
                            <li><a href="teacher.php"><i class="bi bi-mortarboard-fill"></i>
                                    <h4>All Teacher</h4>
                                </a>
                            </li>
                            <li>
                                <a href="parent.php"><i class="bi bi-people-fill"></i>
                                    <h4>All Parent</h4>
                                </a>
                            </li>
                        </ul>
                    </li>
                    <li>
                        <a href="order.php">
                            <i class="bi bi-receipt"></i>
                            <h4>Order</h4>
                        </a>
                    </li>
                </ul>
            </div>
        </aside>
        <!-- END OF ASIDE -->
        <main class="product">
            <div class="wrapper">
                <div class="title">
                    <div class="left">
                        <h1>Bookshop Product</h1>
                    </div>
                    <div class="right">
                        <button id="open-popup"><i class="bi bi-plus-circle"></i>Add Bookshop Product</button>
                    </div>
                </div>
                <div class="box-container">
                    <?php foreach ($all_products as $product) { ?>
                        <div class="box">
                            <div class="image-container">
                                <a href="productPage.php?pid=<?= htmlspecialchars($product['product_id']); ?>">
                                    <img src="<?= htmlspecialchars("../uploads/" . $product['image_url'] . "") ?>" alt="Product Image">
                                    <div class="badge">
                                        <div class="sales-badge"><?= !empty($product['monthly_sales']) ? htmlspecialchars($product['monthly_sales']) : 0 ?> sold this month</div>
                                    </div>
                                </a>
                            </div>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </main>
    </div>
    <dialog id="add-product">
        <h2>Add Bookshop Product</h2>
        <form action="" method="post" enctype="multipart/form-data">
            <div class="input-field">
                <h2>Product Name<sup>*</sup></h2>
                <input type="text" name="name" value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>" required>
                <p>Please enter the product name.</p>
            </div>
            <div class="input-field">
                <h2>Product Category</h2>
                <select name="subcategory" id="subcategory" required>
                    <option value="">Select a category</option>
                    <?php foreach ($all_subcategories as $subcategory) { ?>
                        <option value="<?= $subcategory['category_id'] ?>"><?= $subcategory['category_name'] ?></option>
                    <?php } ?>
                </select>
                <p>Please select a product category.</p>
            </div>
            <div class="input-field">
                <h2>Product Image<sup>*</sup></h2>
                <input type="file" name="image" id="image" accept=".jpg, .jpeg, .png" required>
                <p>Please upload an image for the product.</p>
            </div>
            <div class="input-field">
                <h2>Product Description<sup>*</sup></h2>
                <textarea name="description" id="description" cols="30" rows="10" required><?php echo isset($_POST['description']) ? htmlspecialchars($_POST['description']) : ''; ?></textarea>
                <p>Please enter the product description.</p>
            </div>
            <div class="input-field">
                <h2>Product Price (RM)<sup>*</sup></h2>
                <input type="number" step="0.01" name="price" value="<?php echo isset($_POST['price']) ? htmlspecialchars($_POST['price']) : ''; ?>" required>
                <p>Please enter the product price.</p>
            </div>
            <div class="input-field">
                <h2>Product Unit Price<sup>*</sup></h2>
                <input type="text" name="unit_price" value="<?php echo isset($_POST['unit_price']) ? htmlspecialchars($_POST['unit_price']) : ''; ?>">
                <p>Please enter the product unit price (e.g., per piece, per set).</p>
            </div>
            <div class="input-field">
                <h2>Stock Quantity<sup>*</sup></h2>
                <input type="number" name="stock_quantity" value="<?php echo isset($_POST['stock_quantity']) ? htmlspecialchars($_POST['stock_quantity']) : ''; ?>" required>
                <p>Please enter the stock quantity available.</p>
            </div>
            <div class="input-field">
                <h2>Color<sup>*</sup></h2>
                <input type="text" name="color" value="<?php echo isset($_POST['color']) ? htmlspecialchars($_POST['color']) : ''; ?>" required>
                <p>Please enter the color of the product.</p>
            </div>
            <div class="input-field">
                <h2>Gender<sup>*</sup></h2>
                <select name="gender" id="gender" required>
                    <option value="">Select gender</option>
                    <option value="Boy" <?= isset($_POST['gender']) && $_POST['gender'] == 'Boy' ? 'selected' : '' ?>>Boy</option>
                    <option value="Girl" <?= isset($_POST['gender']) && $_POST['gender'] == 'Girl' ? 'selected' : '' ?>>Girl</option>
                    <option value="Unisex" <?= isset($_POST['gender']) && $_POST['gender'] == 'Unisex' ? 'selected' : '' ?>>Unisex</option>
                </select>
                <p>Please select the gender for the product.</p>
            </div>
            <div class="controls">
                <button type="button" onclick="document.getElementById('add-product').close();">Cancel</button>
                <button type="reset">Clear</button>
                <button type="submit" name="submit">Publish</button>
            </div>
        </form>
    </dialog>
    <script src="../javascript/admin.js"></script>
    <script>
        // Open dialog for adding a product
        document.getElementById('open-popup').addEventListener('click', function() {
            document.getElementById('add-product').showModal();
        });
    </script>
</body>

</html>