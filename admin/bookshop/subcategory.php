<?php

session_start();

include $_SERVER['DOCUMENT_ROOT'] . "/mips/components/db_connect.php";

if (!isset($_SESSION['admin_id'])) {
    header('Location: /mips/admin/login.php');
    exit();
}

$currentPage = basename($_SERVER['PHP_SELF']);

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

if (isset($_POST["submit"])) {
    $name = $_POST["name"];
    $parentId = !empty($_POST["parent_category"]) ? $_POST["parent_category"] : null;
    $subcategoryId = isset($_POST['subcategory_id']) ? $_POST['subcategory_id'] : null;

    if ($_FILES["image"]["error"] === 4 && !$subcategoryId) {
        echo "<script>alert('Image Does Not Exist');</script>";
    } else {
        if ($subcategoryId) {
            $sql = "SELECT category_icon FROM Product_Category WHERE category_id = :subcategory_id";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':subcategory_id', $subcategoryId);
            $stmt->execute();
            $existingSubcategory = $stmt->fetch(PDO::FETCH_ASSOC);
            $existingImagePath = $existingSubcategory['category_icon'] ?? null;
        }

        $newImageName = handleFileUpload($_FILES['image'], $existingImagePath);

        if ($subcategoryId) {
            $sql = "UPDATE Product_Category SET category_name = :name, parent_id = :parentId";

            if ($newImageName) {
                $sql .= ", category_icon = :icon";
            }

            $sql .= " WHERE category_id = :subcategory_id";

            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':parentId', $parentId);
            $stmt->bindParam(':subcategory_id', $subcategoryId);

            if ($newImageName) {
                $stmt->bindParam(':icon', $newImageName);
            }

            try {
                $stmt->execute();
                header('Location: ' . $_SERVER['PHP_SELF']);
                exit();
            } catch (PDOException $e) {
                echo "<script>alert('Database error: " . $e->getMessage() . "');</script>";
            }
        } else {
            if ($newImageName) {
                $sql = "INSERT INTO Product_Category (category_name, category_icon, parent_id, is_deleted) VALUES (:name, :icon, :parentId, 0)";
                $stmt = $pdo->prepare($sql);
                $stmt->bindParam(':name', $name);
                $stmt->bindParam(':icon', $newImageName);
                $stmt->bindParam(':parentId', $parentId);

                try {
                    $stmt->execute();
                    header('Location: ' . $_SERVER['PHP_SELF']);
                    exit();
                } catch (PDOException $e) {
                    echo "<script>alert('Database error: " . $e->getMessage() . "');</script>";
                }
            }
        }
    }
}

function getSubcategories($pdo)
{
    $sql = "SELECT * FROM Product_Category WHERE parent_id IS NOT NULL AND is_deleted = 0";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getMainCategories($pdo)
{
    $sql = "SELECT * FROM Product_Category WHERE parent_id IS NULL AND is_deleted = 0";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$all_subcategories = getSubcategories($pdo);
$all_main_categories = getMainCategories($pdo);

$pageTitle = "Bookshop Subcategory - MIPS";
include $_SERVER['DOCUMENT_ROOT'] . "/mips/components/admin_head.php"; ?>

<body>
    <?php include $_SERVER['DOCUMENT_ROOT'] . "/mips/components/admin_header.php"; ?>
    <div class="container">
        <?php include $_SERVER['DOCUMENT_ROOT'] . "/mips/components/admin_sidebar.php"; ?>
        <main class="category">
            <div class="wrapper">
                <div class="title">
                    <div class="left">
                        <h1>Bookshop Subcategory</h1>
                    </div>
                    <div class="right">
                        <button id="open-popup" class="btn btn-outline-primary"><i class="bi bi-plus-circle"></i>Add New Subcategory</button>
                    </div>
                </div>
                <div class="box-container">
                    <?php foreach ($all_subcategories as $subcategory) : ?>
                        <div class="box" data-subcategory-id="<?= htmlspecialchars($subcategory['category_id']); ?>">
                            <div class="image-container">
                                <img src="/mips/uploads/category/<?php echo htmlspecialchars($subcategory['category_icon']); ?>" alt="Icon for <?php echo htmlspecialchars($subcategory['category_name']); ?>">
                            </div>
                            <div class="actions">
                                <form action="" method="POST" style="display:inline;" onsubmit="return showDeactivateConfirmDialog(event);">
                                    <input type="hidden" name="subcategory_id" value="<?= htmlspecialchars($subcategory['category_id']); ?>">
                                    <input type="hidden" name="action" value="deactivate_product_category">
                                    <button type="submit" class="delete-subcategory-btn"><i class="bi bi-x-square"></i></button>
                                </form>
                                <button type="button" class="edit-subcategory-btn" data-subcategory-id="<?= htmlspecialchars($subcategory['category_id']); ?>"><i class="bi bi-pencil-square"></i></button>
                            </div>
                            <div class="txt">
                                <h3><?php echo htmlspecialchars($subcategory['category_name']); ?></h3>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </main>
    </div>
    <dialog id="add-edit-data">
        <div class="title">
            <div class="left">
                <h1>Add Subcategory</h1>
            </div>
            <div class="right">
                <button class="cancel"><i class="bi bi-x-circle"></i></button>
            </div>
        </div>
        <form action="" method="post" enctype="multipart/form-data">
            <input type="hidden" name="subcategory_id" value="">
            <div class="input-container">
                <h2>Subcategory Name<sup>*</sup></h2>
                <div class="input-field">
                    <input type="text" name="name" value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>">
                </div>
                <p>Please enter the subcategory name.</p>
            </div>
            <div class="input-container">
                <h2>Subcategory Icon<sup>*</sup></h2>
                <div class="input-field">
                    <input type="file" name="image" id="image" accept=".jpg, .jpeg, .png">
                </div>
                <p>Please upload an image for the subcategory.</p>
            </div>
            <div class="input-container">
                <h2>Parent Category<sup>*</sup></h2>
                <div class="input-field">
                    <select name="parent_category" id="parent_category" required>
                        <option value="">Select a main category</option>
                        <?php foreach ($all_main_categories as $mainCategory) : ?>
                            <option value="<?php echo $mainCategory['category_id']; ?>">
                                <?php echo htmlspecialchars($mainCategory['category_name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <p>Select the main category for this subcategory.</p>
                </div>
            </div>
            <div class="input-container controls">
                <button type="button" class="cancel">Cancel</button>
                <button type="reset">Clear</button>
                <button type="submit" name="submit">Publish</button>
            </div>
        </form>
    </dialog>
    <?php include $_SERVER['DOCUMENT_ROOT'] . "/mips/components/confirm_dialog.php"; ?>

    <script src="/mips/javascript/admin.js"></script>
    <script>
        document.querySelectorAll('.edit-subcategory-btn').forEach(button => {
            button.addEventListener('click', function() {
                const subcategoryId = this.dataset.subcategoryId;
                fetch(`/mips/admin/ajax.php?action=get_subcategory&subcategory_id=${subcategoryId}`)
                    .then(response => response.json())
                    .then(subcategory => {
                        if (subcategory.error) {
                            alert(subcategory.error);
                        } else {
                            document.querySelector('#add-edit-data [name="subcategory_id"]').value = subcategory.category_id;
                            document.querySelector('#add-edit-data [name="name"]').value = subcategory.category_name;
                            document.querySelector('#add-edit-data [name="parent_category"]').value = subcategory.parent_id;
                            document.querySelector('#add-edit-data h1').textContent = "Edit Subcategory";
                            document.getElementById('add-edit-data').showModal();
                        }
                    })
                    .catch(error => {
                        console.error('Error fetching subcategory data:', error);
                        alert('Failed to load subcategory data.');
                    });
            });
        });
    </script>
</body>

</html>