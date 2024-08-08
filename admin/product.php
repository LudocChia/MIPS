<?php
session_start();

include "../components/db_connect.php";

if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit();
}

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
    $productId = isset($_POST['product_id']) ? $_POST['product_id'] : null;

    if (!empty($name) && !empty($subcategoryId) && !empty($price) && !empty($stockQuantity) && !empty($gender)) {
        if ($productId) {
            // Update existing product
            $sql = "UPDATE Product SET product_name = :name, category_id = :subcategory, product_description = :description, 
                    product_price = :price, product_unit_price = :unit_price, stock_quantity = :stock_quantity, 
                    color = :color, gender = :gender WHERE product_id = :product_id";

            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':subcategory', $subcategoryId);
            $stmt->bindParam(':description', $description);
            $stmt->bindParam(':price', $price);
            $stmt->bindParam(':unit_price', $unitPrice);
            $stmt->bindParam(':stock_quantity', $stockQuantity);
            $stmt->bindParam(':color', $color);
            $stmt->bindParam(':gender', $gender);
            $stmt->bindParam(':product_id', $productId);
            $stmt->execute();

            echo "<script>alert('Product updated successfully!');</script>";
        } else {
            // Add new product
            if (isset($_FILES['images'])) {
                $imageCount = count($_FILES['images']['name']);
                $uploadedImages = [];
                $allowedfileExtensions = ['jpg', 'jpeg', 'png'];

                for ($i = 0; $i < $imageCount; $i++) {
                    $fileTmpPath = $_FILES['images']['tmp_name'][$i];
                    $fileName = $_FILES['images']['name'][$i];
                    $fileSize = $_FILES['images']['size'][$i];
                    $fileType = $_FILES['images']['type'][$i];
                    $fileNameCmps = explode(".", $fileName);
                    $fileExtension = strtolower(end($fileNameCmps));

                    if (in_array($fileExtension, $allowedfileExtensions)) {
                        $newFileName = uniqid() . '.' . $fileExtension;
                        $dest_path = '../uploads/' . $newFileName;

                        if (move_uploaded_file($fileTmpPath, $dest_path)) {
                            $uploadedImages[] = $newFileName;
                        } else {
                            echo "<script>alert('There was an error moving the uploaded file: $fileName');</script>";
                        }
                    } else {
                        echo "<script>alert('Upload failed. Allowed file types: " . implode(',', $allowedfileExtensions) . "');</script>";
                    }
                }

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

                $productId = $pdo->lastInsertId();

                $sortOrder = 1;
                foreach ($uploadedImages as $image) {
                    $sqlImage = "INSERT INTO Product_Image (product_id, image_url, sort_order) VALUES (:product_id, :image_url, :sort_order)";
                    $stmtImage = $pdo->prepare($sqlImage);
                    $stmtImage->bindParam(':product_id', $productId);
                    $stmtImage->bindParam(':image_url', $image);
                    $stmtImage->bindParam(':sort_order', $sortOrder);
                    $stmtImage->execute();
                    $sortOrder++;
                }

                header('Loction: product.php');
                exit();
            } else {
                echo "<script>alert('No files uploaded.');</script>";
            }
        }
    } else {
        echo "<script>alert('Please fill in all required fields.');</script>";
    }
}

if (isset($_POST['delete'])) {
    $productId = $_POST['product_id'];

    $sql = "UPDATE Product SET is_deleted = 1 WHERE product_id = :product_id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':product_id', $productId);

    try {
        $stmt->execute();
        header('Location: product.php');
        exit();
    } catch (PDOException $e) {
        echo "<script>alert('Database error: " . $e->getMessage() . "');</script>";
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
    <link rel="stylesheet" href="../css/base.css">
    <link rel="stylesheet" href="../css/common.css">
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
                    <li>
                        <a href="announment.php">
                            <i></i>
                            <h4>Announment</h4>
                        </a>
                    </li>
                    <li>
                        <a href="deactivate.php">
                            <i></i>
                            <h4>Deactivate List</h4>
                        </a>
                    </li>
                    <li>
                        <i></i>
                        <h4>Report</h4>
                        <ul>
                            <li>

                            </li>
                        </ul>

                    </li>
                </ul>
            </div>
        </aside>
        <!-- END OF ASIDE -->
        <main class="products">
            <div class="wrapper">
                <div class="title">
                    <div class="left">
                        <h1>Bookshop Products</h1>
                    </div>
                    <div class="right">
                        <button id="open-popup"><i class="bi bi-plus-circle"></i>Add Bookshop Product</button>
                    </div>
                </div>
                <div class="box-container">
                    <?php foreach ($all_products as $product) { ?>
                        <div class="box" data-product-id="<?= htmlspecialchars($product['product_id']); ?>">
                            <div class="image-container">
                                <a href="item.php?pid=<?= htmlspecialchars($product['product_id']); ?>">
                                    <img src="<?= htmlspecialchars("../uploads/" . $product['image_url']) ?>" alt="Product Image">
                                </a>
                                <form action="" method="POST" style="display:inline;" onsubmit="return showDeleteConfirmDialog(event);">
                                    <input type="hidden" name="product_id" value="<?= htmlspecialchars($product['product_id']); ?>">
                                    <input type="hidden" name="delete" value="true">
                                    <button type="submit" class="delete-product-btn"><i class="bi bi-x-square"></i></button>
                                </form>

                                <!-- <div class="deactivate-product"> -->
                                <button type="button" class="edit-product-btn" data-product-id="<?= htmlspecialchars($product['product_id']); ?>"><i class="bi bi-pencil-square"></i></button>
                                <!-- </div> -->
                            </div>
                            <div class="name"><?= htmlspecialchars($product['product_name']); ?></div>
                            <div class="price">
                                MYR <?= number_format($product['product_price'], 2); ?>
                            </div>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </main>
    </div>
    <dialog id="add-data">
        <h2>Add Bookshop Product</h2>
        <form action="" method="post" enctype="multipart/form-data">
            <input type="hidden" name="product_id" value="">
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
                <h2>Product Images<sup>*</sup></h2>
                <input type="file" name="images[]" id="images" accept=".jpg, .jpeg, .png" multiple required>
                <p>Please upload images for the product.</p>
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
                <button type="button" class="close-btn">Cancel</button>
                <button type="reset">Clear</button>
                <button type="submit" name="submit">Publish</button>
            </div>
        </form>
    </dialog>
    <dialog id="delete-confirm-dialog">
        <form method="dialog">
            <h1>Your Product will be Deactivated!</h1>
            <label>Are you sure to proceed?</label>
            <div class="btns">
                <button value="cancel" class="btn1">Cancel Process</button>
                <button value="confirm" class="btn2">Deactivate Product</button>
            </div>
        </form>
    </dialog>

    <script src="../javascript/admin.js"></script>
    <script>
        // Handle edit product button click
        document.querySelectorAll('.edit-product-btn').forEach(button => {
            button.addEventListener('click', function() {
                const productId = this.dataset.productId;

                fetch(`ajax.php?product_id=${productId}`)
                    .then(response => response.json())
                    .then(product => {
                        document.querySelector('#add-product [name="name"]').value = product.product_name;
                        document.querySelector('#add-product [name="subcategory"]').value = product.category_id;
                        document.querySelector('#add-product [name="description"]').value = product.product_description;
                        document.querySelector('#add-product [name="price"]').value = product.product_price;
                        document.querySelector('#add-product [name="unit_price"]').value = product.product_unit_price;
                        document.querySelector('#add-product [name="stock_quantity"]').value = product.stock_quantity;
                        document.querySelector('#add-product [name="color"]').value = product.color;
                        document.querySelector('#add-product [name="gender"]').value = product.gender;

                        document.querySelector('#add-product').dataset.productId = productId;

                        document.getElementById('add-product').showModal();
                    })
                    .catch(error => {
                        console.error('Error fetching product data:', error);
                        alert('Failed to load product data.');
                    });
            });
        });
        document.addEventListener('DOMContentLoaded', function() {
            const deleteConfirmDialog = document.getElementById('delete-confirm-dialog');
            let deleteForm = null;

            window.showDeleteConfirmDialog = function(event) {
                event.preventDefault();
                deleteForm = event.target;
                deleteConfirmDialog.showModal();
            }

            deleteConfirmDialog.addEventListener('close', function() {
                if (deleteConfirmDialog.returnValue === 'confirm') {
                    deleteForm.submit();
                }
            });
        });
    </script>
</body>

</html>