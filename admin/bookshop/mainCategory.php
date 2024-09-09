<?php

$database_table = "Product Category";
$rows_per_page = 10;
include $_SERVER['DOCUMENT_ROOT'] . "/mips/php/admin.php";
include $_SERVER['DOCUMENT_ROOT'] . "/mips/php/activate_pagination.php";

function getMainCategories($pdo, $start, $rows_per_page)
{
    $sql = "SELECT * FROM Product_Category WHERE parent_id IS NULL AND status = 0 LIMIT :start, :rows_per_page;";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':start', $start, PDO::PARAM_INT);
    $stmt->bindParam(':rows_per_page', $rows_per_page, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$all_main_categories = getMainCategories($pdo, $start, $rows_per_page);

function handleFileUpload($file, $existingImagePath = null)
{
    if ($file['error'] === UPLOAD_ERR_NO_FILE) {
        return $existingImagePath;
    }

    $allowedfileExtensions = ['jpg', 'jpeg', 'png'];
    $fileName = $file['name'];
    $fileTmpPath = $file['tmp_name'];
    $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

    if (in_array($fileExtension, $allowedfileExtensions)) {
        $newFileName = uniqid() . '.' . $fileExtension;
        $dest_path = $_SERVER['DOCUMENT_ROOT'] . '/mips/uploads/category/' . $newFileName;

        if (move_uploaded_file($fileTmpPath, $dest_path)) {
            if ($existingImagePath && file_exists($_SERVER['DOCUMENT_ROOT'] . '/mips/uploads/category/' . $existingImagePath)) {
                unlink($_SERVER['DOCUMENT_ROOT'] . '/mips/uploads/category/' . $existingImagePath);
            }
            return $newFileName;
        } else {
            echo "<script>alert('There was an error moving the uploaded file: $fileName');</script>";
            return $existingImagePath;
        }
    } else {
        echo "<script>alert('Upload failed. Allowed file types: " . implode(',', $allowedfileExtensions) . "');</script>";
        return $existingImagePath;
    }
}

function generateCategoryId()
{
    return uniqid("BC");
}

if (isset($_POST["submit"])) {
    $categoryId = isset($_POST['category_id']) && !empty($_POST['category_id']) ? $_POST['category_id'] : generateCategoryId();
    $name = $_POST["name"];

    if (!$categoryId && $_FILES["image"]["error"] === 4) {
        echo "<script>alert('Image Does Not Exist');</script>";
    } else {

        $oldImagePath = null;
        if (isset($_POST['announcement_id']) && !empty($_POST['announcement_id'])) {
            $stmt = $pdo->prepare("SELECT category_icon FROM Product_Category WHERE category_id = :category_id");
            $stmt->bindParam(':category_id', $categoryId);
            $stmt->execute();
            $oldImage = $stmt->fetch(PDO::FETCH_ASSOC);
        }

        $newImageName = handleFileUpload($_FILES['image'], $oldImagePath);

        if (isset($_POST['category_id']) && !empty($_POST['category_id'])) {

            $sql = "UPDATE Product_Category SET category_name = :name, admin_id = :admin_id";

            if ($newImageName) {
                $sql .= ", category_icon = :icon";
            }

            $sql .= " WHERE category_id = :category_id";

            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':category_id', $categoryId);
            $stmt->bindParam(':admin_id', $_SESSION['admin_id']);

            if ($newImageName) {
                $stmt->bindParam(':icon', $newImageName);
            }
        } else {
            $sql = "INSERT INTO Product_Category (category_id, category_name, category_icon, admin_id) VALUES (:category_id, :name, :icon, :admin_id)";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':category_id', $categoryId);
            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':icon', $newImageName);
            $stmt->bindParam(':admin_id', $_SESSION['admin_id']);
        }

        include $_SERVER['DOCUMENT_ROOT'] . "/mips/php/refresh_page.php";
    }
}

$pageTitle = "Bookshop Main Category - MIPS";
include $_SERVER['DOCUMENT_ROOT'] . "/mips/components/admin_head.php"; ?>

<body id="<?php echo $id ?>">
    <?php include $_SERVER['DOCUMENT_ROOT'] . "/mips/components/admin_header.php"; ?>
    <div class="container">
        <?php include $_SERVER['DOCUMENT_ROOT'] . "/mips/components/admin_sidebar.php"; ?>
        <main class="category">
            <div class="wrapper">
                <div class="title">
                    <div class="left">
                        <h1>Bookshop Main Category</h1>
                    </div>
                    <div class="right">
                        <button id="open-popup" class="btn btn-outline-primary"><i class="bi bi-plus-circle"></i>Add Main Category</button>
                    </div>
                </div>
                <?php if (!empty($all_main_categories)) : ?>
                    <div class="box-container">
                        <?php foreach ($all_main_categories as $category) : ?>
                            <div class="box" data-category-id="<?= htmlspecialchars($category['category_id']); ?>">
                                <div class="image-container">
                                    <img src="/mips/uploads/category/<?php echo htmlspecialchars($category['category_icon']); ?>" alt="Icon for <?php echo htmlspecialchars($category['category_name']); ?>">
                                </div>
                                <div class="actions">
                                    <form action="" method="POST" style="display:inline;" onsubmit="return showDeactivateConfirmDialog(event);">
                                        <input type="hidden" name="category_id" value="<?= htmlspecialchars($category['category_id']); ?>">
                                        <input type="hidden" name="action" value="deactivate_product_category">
                                        <button type="submit" class="delete-category-btn"><i class="bi bi-x-square"></i></button>
                                    </form>
                                    <button type="button" class="edit-category-btn" data-category-id="<?= htmlspecialchars($category['category_id']); ?>"><i class="bi bi-pencil-square"></i></button>
                                </div>
                                <div class="info-container">
                                    <h3><?php echo htmlspecialchars($category['category_name']); ?></h3>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else : ?>
                    <?php include $_SERVER['DOCUMENT_ROOT'] . "/mips/components/no_data_found.php"; ?>
                <?php endif; ?>
            </div>
            <?php if (!empty($all_classes)) : ?>
                <?php include $_SERVER['DOCUMENT_ROOT'] . "/mips/components/pagination.php"; ?>
            <?php endif; ?>
        </main>
    </div>
    <dialog id="add-edit-data">
        <div class="title">
            <div class="left">
                <h1>Add Main Category</h1>
            </div>
            <div class="right">
                <button class="actions cancel"><i class="bi bi-x-circle"></i></button>
            </div>
        </div>
        <form method="post" enctype="multipart/form-data">
            <input type="hidden" name="category_id" value="">
            <div class="input-container">
                <h2>Category Name<sup>*</sup></h2>
                <div class="input-field">
                    <input type="text" name="name" value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>">
                </div>
                <p>Please enter the category name.</p>
            </div>
            <div class="input-container">
                <h2>Category Icon<sup>*</sup></h2>
                <div class="input-field">
                    <input type="file" name="image" id="image" accept=".jpg, .jpeg, .png">
                </div>
                <p>Please upload an image for the category.</p>
            </div>
            <div class="controls">
                <button type="button" class="cancel">Cancel</button>
                <button type="reset">Clear</button>
                <button type="submit" name="submit">Publish</button>
            </div>
        </form>
    </dialog>
    <?php include $_SERVER['DOCUMENT_ROOT'] . "/mips/components/confirm_dialog.php"; ?>
    <script src="/mips/javascript/common.js"></script>
    <script src="/mips/javascript/admin.js"></script>
    <script>
        document.querySelectorAll('.edit-category-btn').forEach(button => {
            button.addEventListener('click', function() {
                const categoryId = this.dataset.categoryId;
                fetch(`/mips/admin/ajax.php?action=get_category&category_id=${categoryId}`)
                    .then(response => response.json())
                    .then(category => {
                        if (category.error) {
                            alert(category.error);
                        } else {
                            document.querySelector('#add-edit-data [name="category_id"]').value = category.category_id;
                            document.querySelector('#add-edit-data [name="name"]').value = category.category_name;
                            document.querySelector('#add-edit-data h1').textContent = "Edit Main Category";
                            document.getElementById('add-edit-data').showModal();
                        }
                    })
                    .catch(error => {
                        console.error('Error fetching category data:', error);
                        alert('Failed to load category data.');
                    });
            });
        });

        document.querySelectorAll('#add-edit-data .cancel').forEach(button => {
            button.addEventListener('click', function() {
                document.querySelector('#add-edit-data h1').textContent = "Add Main Category";
            });
        });
    </script>
</body>

</html>