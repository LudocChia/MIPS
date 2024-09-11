<?php

$database_table = "Product";
$rows_per_page = 12;
include $_SERVER['DOCUMENT_ROOT'] . "/mips/php/admin.php";
include $_SERVER['DOCUMENT_ROOT'] . "/mips/php/activate_pagination.php";

function getSubcategories($pdo)
{
    $sql = "SELECT * FROM Product_Category WHERE parent_id IS NOT NULL AND status = 0";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$all_subcategories = getSubcategories($pdo);


function getAllProducts($pdo, $start, $rows_per_page)
{
    $sql = "SELECT p.product_id, p.product_name, p.product_description, p.product_price,
                   p.stock_quantity, p.color, p.gender, pi.image_url
            FROM Product p
            LEFT JOIN Product_Image pi ON p.product_id = pi.product_id AND pi.sort_order = 1
            WHERE p.status = 0
            GROUP BY p.product_id
            LIMIT :start, :rows_per_page";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':start', $start, PDO::PARAM_INT);
    $stmt->bindParam(':rows_per_page', $rows_per_page, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$all_products = getAllProducts($pdo, $start, $rows_per_page);

function getAllSizes($pdo)
{
    $sql = "SELECT * FROM Sizes WHERE status = 0";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$all_sizes = getAllSizes($pdo);

function handleFileUpload($files, $existingImagePaths = [])
{
    $uploadedImages = [];
    $allowedfileExtensions = ['jpg', 'jpeg', 'png'];

    foreach ($files['tmp_name'] as $index => $tmpName) {
        if ($files['error'][$index] === UPLOAD_ERR_NO_FILE) {
            continue;
        }

        $fileName = $files['name'][$index];
        $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        if (in_array($fileExtension, $allowedfileExtensions)) {
            $newFileName = uniqid() . '.' . $fileExtension;
            $dest_path = $_SERVER['DOCUMENT_ROOT'] . '/mips/uploads/product/' . $newFileName;

            if (move_uploaded_file($tmpName, $dest_path)) {
                if (isset($existingImagePaths[$index]) && file_exists($_SERVER['DOCUMENT_ROOT'] . '/mips/uploads/product/' . $existingImagePaths[$index])) {
                    unlink($_SERVER['DOCUMENT_ROOT'] . '/mips/uploads/product/' . $existingImagePaths[$index]);
                }
                $uploadedImages[] = $newFileName;
            } else {
                echo "<script>alert('There was an error moving the uploaded file: $fileName');</script>";
            }
        } else {
            echo "<script>alert('Upload failed. Allowed file types: " . implode(',', $allowedfileExtensions) . "');</script>";
        }
    }

    return $uploadedImages;
}

function generateProductId()
{
    return uniqid("PR");
}

function generateImageId()
{
    return uniqid("IMG");
}

function generateProductSizeId()
{
    return uniqid("PS");
}

if (isset($_POST['submit'])) {
    $pdo->beginTransaction();
    try {
        $productId = isset($_POST['product_id']) && !empty($_POST['product_id']) ? $_POST['product_id'] : generateProductId();
        $name = htmlspecialchars(trim($_POST['name']));
        $subcategoryId = htmlspecialchars(trim($_POST['subcategory']));
        $description = !empty($_POST['description']) ? htmlspecialchars(trim($_POST['description'])) : null;
        $price = htmlspecialchars(trim($_POST['price']));
        $stockQuantity = htmlspecialchars(trim($_POST['stock_quantity']));
        $color = !empty($_POST['color']) ? htmlspecialchars(trim($_POST['color'])) : null;
        $gender = htmlspecialchars(trim($_POST['gender']));

        if (isset($_POST['product_id']) && !empty($_POST['product_id'])) {
            $sql = "UPDATE Product SET product_name = :name, category_id = :subcategory, product_description = :description, product_price = :price, stock_quantity = :stock_quantity, color = :color, gender = :gender
                    WHERE product_id = :product_id";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':product_id', $productId);
            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':subcategory', $subcategoryId);
            $stmt->bindParam(':description', $description);
            $stmt->bindParam(':price', $price);
            $stmt->bindParam(':stock_quantity', $stockQuantity);
            $stmt->bindParam(':color', $color);
            $stmt->bindParam(':gender', $gender);
            $stmt->execute();

            if (!empty($_FILES['images']['name'][0])) {
                $uploadedImages = handleFileUpload($_FILES['images']);

                $stmtDeleteImages = $pdo->prepare("DELETE FROM Product_Image WHERE product_id = :product_id");
                $stmtDeleteImages->bindParam(':product_id', $productId);
                $stmtDeleteImages->execute();

                $sortOrder = 1;
                foreach ($uploadedImages as $image) {
                    $imageId = generateImageId();
                    $sqlImage = "INSERT INTO Product_Image (image_id, product_id, image_url, sort_order) 
                                 VALUES (:image_id, :product_id, :image_url, :sort_order)";
                    $stmtImage = $pdo->prepare($sqlImage);
                    $stmtImage->bindParam(':image_id', $imageId);
                    $stmtImage->bindParam(':product_id', $productId);
                    $stmtImage->bindParam(':image_url', $image);
                    $stmtImage->bindParam(':sort_order', $sortOrder);
                    $stmtImage->execute();
                    $sortOrder++;
                }
            }

            $stmtDeleteSizes = $pdo->prepare("DELETE FROM Product_Size WHERE product_id = :product_id");
            $stmtDeleteSizes->bindParam(':product_id', $productId);
            $stmtDeleteSizes->execute();

            if (!empty($_POST['sizes'])) {
                $stmtInsertSize = $pdo->prepare("INSERT INTO Product_Size (product_size_id, product_id, size_id) VALUES (:product_size_id, :product_id, :size_id)");
                foreach ($_POST['sizes'] as $sizeId) {
                    $productSizeId = generateProductSizeId();
                    $stmtInsertSize->bindParam(':product_size_id', $productSizeId);
                    $stmtInsertSize->bindParam(':product_id', $productId);
                    $stmtInsertSize->bindParam(':size_id', $sizeId);
                    $stmtInsertSize->execute();
                }
            }
        } else {
            $sql = "INSERT INTO Product (product_id, product_name, category_id, product_description, product_price, 
                                          stock_quantity, color, gender, admin_id) 
                    VALUES (:product_id, :name, :subcategory, :description, :price, :stock_quantity, :color, :gender, :admin_id)";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':product_id', $productId);
            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':subcategory', $subcategoryId);
            $stmt->bindParam(':description', $description);
            $stmt->bindParam(':price', $price);
            $stmt->bindParam(':stock_quantity', $stockQuantity);
            $stmt->bindParam(':color', $color);
            $stmt->bindParam(':gender', $gender);
            $stmt->bindParam(':admin_id', $_SESSION['admin_id']);
            $stmt->execute();

            if (!empty($_FILES['images']['name'][0])) {
                $uploadedImages = handleFileUpload($_FILES['images']);

                $sortOrder = 1;
                foreach ($uploadedImages as $image) {
                    $imageId = generateImageId();
                    $sqlImage = "INSERT INTO Product_Image (image_id, product_id, image_url, sort_order) 
                                 VALUES (:image_id, :product_id, :image_url, :sort_order)";
                    $stmtImage = $pdo->prepare($sqlImage);
                    $stmtImage->bindParam(':image_id', $imageId);
                    $stmtImage->bindParam(':product_id', $productId);
                    $stmtImage->bindParam(':image_url', $image);
                    $stmtImage->bindParam(':sort_order', $sortOrder);
                    $stmtImage->execute();
                    $sortOrder++;
                }
            }

            if (!empty($_POST['sizes'])) {
                $stmtInsertSize = $pdo->prepare("INSERT INTO Product_Size (product_size_id, product_id, size_id) VALUES (:product_size_id, :product_id, :size_id)");
                foreach ($_POST['sizes'] as $sizeId) {
                    $productSizeId = generateProductSizeId();
                    $stmtInsertSize->bindParam(':product_size_id', $productSizeId);
                    $stmtInsertSize->bindParam(':product_id', $productId);
                    $stmtInsertSize->bindParam(':size_id', $sizeId);
                    $stmtInsertSize->execute();
                }
            }
        }

        $pdo->commit();
        header('Location: /mips/admin/bookshop/product.php');
        exit();
    } catch (Exception $e) {
        $pdo->rollBack();
        echo "<script>alert('Database error: " . $e->getMessage() . "');</script>";
    }
}

$pageTitle = "Bookshop Products - MIPS";
include $_SERVER['DOCUMENT_ROOT'] . "/mips/components/admin_head.php";
?>

<body id="<?php echo $id ?>">
    <?php include $_SERVER['DOCUMENT_ROOT'] . "/mips/components/admin_header.php"; ?>
    <div class="container">
        <?php include $_SERVER['DOCUMENT_ROOT'] . "/mips/components/admin_sidebar.php"; ?>
        <main class="products">
            <div class="wrapper">
                <div class="title">
                    <div class="left">
                        <h1>Bookshop Products</h1>
                    </div>
                    <div class="right">
                        <button id="open-popup" class="btn btn-outline-primary"><i class="bi bi-plus-circle"></i>Add Bookshop Product</button>
                    </div>
                </div>
                <?php if (!empty($all_products)) : ?>
                    <div class="box-container">
                        <?php foreach ($all_products as $product) { ?>
                            <div class="box" data-product-id="<?= htmlspecialchars($product['product_id']); ?>">
                                <a href="/mips/admin/bookshop/item.php?pid=<?= htmlspecialchars($product['product_id']); ?>">
                                    <div class="image-container">
                                        <img src="/mips/uploads/product/<?php echo htmlspecialchars($product['image_url']); ?>" alt="Icon for <?php echo htmlspecialchars($product['product_name']); ?>">
                                    </div>
                                    <div class="info-container">
                                        <div class="name-field">
                                            <h4 style="font-weight: bold"><?php echo htmlspecialchars($product['product_name']); ?></h4>
                                        </div>
                                        <div class="price-size-container">
                                            <div class="price-field">
                                                MYR <?= number_format($product['product_price'], 2); ?>
                                            </div>
                                        </div>
                                        <p><?= htmlspecialchars($product['stock_quantity']); ?> pieces available</p>
                                        <p><?= htmlspecialchars($product['gender']); ?></p>
                                        <p><?= htmlspecialchars($product['color']); ?></p>
                                    </div>
                                </a>
                                <div class="actions">
                                    <form action="" method="POST" style="display:inline;" onsubmit="return showDeactivateConfirmDialog(event);">
                                        <input type="hidden" name="product_id" value="<?= htmlspecialchars($product['product_id']); ?>">
                                        <input type="hidden" name="action" value="deactivate_product">
                                        <button type="submit" class="delete-product-btn"><i class="bi bi-x-square"></i></button>
                                    </form>
                                    <button type="button" class="edit-product-btn" data-product-id="<?= htmlspecialchars($product['product_id']); ?>"><i class="bi bi-pencil-square"></i></button>
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                <?php else : ?>
                    <?php include $_SERVER['DOCUMENT_ROOT'] . "/mips/components/no_data_found.php"; ?>
                <?php endif; ?>
                <?php if (!empty($all_products)) : ?>
                    <?php include $_SERVER['DOCUMENT_ROOT'] . "/mips/components/pagination.php"; ?>
                <?php endif; ?>
            </div>
        </main>
    </div>
    <dialog id="add-edit-data">
        <div class="title">
            <div class="left">
                <h1>Add Bookshop Product</h1>
            </div>
            <div class="right">
                <button class="actions cancel"><i class="bi bi-x-circle"></i></button>
            </div>
        </div>
        <form method="post" enctype="multipart/form-data">
            <input type="hidden" name="product_id" value="">
            <div class="input-container">
                <h2>Product Name<sup>*</sup></h2>
                <div class="input-field">
                    <input type="text" name="name" value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>" required>
                </div>
                <p>Please enter the product name.</p>
            </div>
            <div class="input-container">
                <h2>Product Category<sup>*</sup></h2>
                <div class="select-field">
                    <select class="select-box" name="subcategory" id="subcategory" required>
                        <option value="">Select a category</option>
                        <?php foreach ($all_subcategories as $subcategory) { ?>
                            <option value="<?= $subcategory['category_id'] ?>"><?= $subcategory['category_name'] ?></option>
                        <?php } ?>
                    </select>
                    <div class="icon-container">
                        <i class="bi bi-caret-down-fill"></i>
                    </div>
                </div>
                <p>Please select a product category.</p>
            </div>
            <div class="input-container">
                <h2>Product Images<sup>*</sup></h2>
                <div class="input-field">
                    <input type="file" name="images[]" id="images" accept=".jpg, .jpeg, .png" multiple>
                </div>
                <p>Please upload images for the product.</p>
            </div>
            <div class="input-container">
                <h2>Product Description<sup>*</sup></h2>
                <div class="input-field">
                    <textarea name="description" id="description" cols="30" rows="10" required><?php echo isset($_POST['description']) ? htmlspecialchars($_POST['description']) : ''; ?></textarea>
                </div>
                <p>Please enter the product description.</p>
            </div>
            <div class="input-container">
                <h2>Product Price (RM)<sup>*</sup></h2>
                <div class="input-field">
                    <input type="number" step="0.01" name="price" value="<?php echo isset($_POST['price']) ? htmlspecialchars($_POST['price']) : ''; ?>" required>
                </div>
                <p>Please enter the product price.</p>
            </div>
            <div class="input-container">
                <h2>Product Sizes<sup>*</sup></h2>
                <div class="add-edit-table-container">
                    <table class="add-edit-table">
                        <thead>
                            <tr>
                                <th>Select</th>
                                <th>Size Name</th>
                                <th>Shoulder Width</th>
                                <th>Bust</th>
                                <th>Waist</th>
                                <th>Length</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($all_sizes as $size) { ?>
                                <tr>
                                    <td>
                                        <input type="checkbox" name="sizes[]" value="<?= htmlspecialchars($size['size_id']) ?>"
                                            <?php if (in_array($size['size_id'], $_POST['sizes'] ?? [])) echo 'checked'; ?>>
                                    </td>
                                    <td><?= htmlspecialchars($size['size_name']) ?></td>
                                    <td><?= htmlspecialchars($size['shoulder_width']) ?></td>
                                    <td><?= htmlspecialchars($size['bust']) ?></td>
                                    <td><?= htmlspecialchars($size['waist']) ?></td>
                                    <td><?= htmlspecialchars($size['length']) ?></td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
                <p>Please select one or more sizes for the product.</p>
            </div>

            <div class="input-container">
                <h2>Stock Quantity<sup>*</sup></h2>
                <div class="input-field">
                    <input type="number" name="stock_quantity" value="<?php echo isset($_POST['stock_quantity']) ? htmlspecialchars($_POST['stock_quantity']) : ''; ?>" required>
                </div>
                <p>Please enter the stock quantity available.</p>
            </div>
            <!-- <div class="input-container"> -->
            <!-- <h2>Color<sup>*</sup></h2> -->
            <div class="input-field">
                <input type="hidded" name="color" value="<?php echo isset($_POST['color']) ? htmlspecialchars($_POST['color']) : ''; ?>">
            </div>
            <!-- <p>Please enter the color of the product.</p> -->
            <!-- </div> -->
            <div class="input-container">
                <div class="select-field">
                    <select class="select-box" name="gender" id="gender" required>
                        <option value="">Select gender</option>
                        <option value="Boy" <?= isset($_POST['gender']) && $_POST['gender'] == 'Boy' ? 'selected' : '' ?>>Boy</option>
                        <option value="Girl" <?= isset($_POST['gender']) && $_POST['gender'] == 'Girl' ? 'selected' : '' ?>>Girl</option>
                        <option value="Unisex" <?= isset($_POST['gender']) && $_POST['gender'] == 'Unisex' ? 'selected' : '' ?>>Unisex</option>
                    </select>
                    <div class="icon-container">
                        <i class="bi bi-caret-down-fill"></i>
                    </div>
                </div>
                <p>Please select the gender for the product.</p>
            </div>
            <div class="controls">
                <button type="button" class="cancel">Cancel</button>
                <button type="reset" class="delete">Clear</button>
                <button type="submit" class="confirm" name="submit">Publish</button>
            </div>
        </form>
    </dialog>
    <?php include $_SERVER['DOCUMENT_ROOT'] . "/mips/components/confirm_dialog.php"; ?>
    <script src="/mips/javascript/common.js"></script>
    <script src="/mips/javascript/admin.js"></script>
    <script>
        document.querySelectorAll('.edit-product-btn').forEach(button => {
            button.addEventListener('click', function() {
                const productId = this.dataset.productId;
                fetch(`/mips/admin/ajax.php?action=get_product&product_id=${productId}`)
                    .then(response => response.json())
                    .then(product => {
                        if (product.error) {
                            alert(product.error);
                        } else {
                            document.querySelector('#add-edit-data [name="product_id"]').value = product.product_id;
                            document.querySelector('#add-edit-data [name="name"]').value = product.product_name;
                            document.querySelector('#add-edit-data [name="subcategory"]').value = product.category_id;
                            document.querySelector('#add-edit-data [name="description"]').value = product.product_description;
                            document.querySelector('#add-edit-data [name="price"]').value = product.product_price;
                            document.querySelector('#add-edit-data [name="stock_quantity"]').value = product.stock_quantity;
                            document.querySelector('#add-edit-data [name="color"]').value = product.color;
                            document.querySelector('#add-edit-data [name="gender"]').value = product.gender;

                            document.querySelectorAll('#add-edit-data input[type="checkbox"]').forEach(checkbox => {
                                checkbox.checked = false;
                            });
                            product.sizes.forEach(sizeId => {
                                const checkbox = document.querySelector(`#add-edit-data input[type="checkbox"][value="${sizeId}"]`);
                                if (checkbox) {
                                    checkbox.checked = true;
                                }
                            });

                            document.querySelector('#add-edit-data h1').textContent = "Edit Bookshop Product";
                            document.getElementById('add-edit-data').showModal();
                        }
                    })
                    .catch(error => {
                        console.error('Error fetching product data:', error);
                        alert('Failed to load product data.');
                    });
            });
        });

        document.querySelectorAll('#add-edit-data .cancel').forEach(button => {
            button.addEventListener('click', function() {
                document.querySelector('#add-edit-data h1').textContent = "Add Bookshop Product";
            });
        });
    </script>
</body>

</html>