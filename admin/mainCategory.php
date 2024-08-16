<?php

session_start();

include "../components/db_connect.php";

if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit();
}

$currentPage = basename($_SERVER['PHP_SELF']);

if (isset($_POST['delete'])) {
    $category_id = $_POST['category_id'];

    $sql = "UPDATE Product_Category SET is_deleted = 1 WHERE category_id = :category_id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['category_id' => $category_id]);
    header('Location: ' . $currentPage);
    exit();
}

function handleFileUpload($file)
{
    if ($file['error'] === UPLOAD_ERR_NO_FILE) {
        return null;
    }

    $allowedfileExtensions = ['jpg', 'jpeg', 'png'];
    $fileName = $file['name'];
    $fileTmpPath = $file['tmp_name'];
    $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

    if (in_array($fileExtension, $allowedfileExtensions)) {
        $newFileName = uniqid() . '.' . $fileExtension;
        $dest_path = '../uploads/category/' . $newFileName;

        if (move_uploaded_file($fileTmpPath, $dest_path)) {
            return $newFileName;
        } else {
            echo "<script>alert('There was an error moving the uploaded file: $fileName');</script>";
            return null;
        }
    } else {
        echo "<script>alert('Upload failed. Allowed file types: " . implode(',', $allowedfileExtensions) . "');</script>";
        return null;
    }
}

if (isset($_POST["submit"])) {
    $name = $_POST["name"];
    $categoryId = isset($_POST['category_id']) ? $_POST['category_id'] : null;

    if ($_FILES["image"]["error"] === 4 && !$categoryId) {
        echo "<script> alert('Image Does Not Exist'); </script>";
    } else {
        $newImageName = handleFileUpload($_FILES['image']);

        if ($categoryId) {
            $sql = "UPDATE Product_Category SET category_name = :name";

            if ($newImageName) {
                $sql .= ", category_icon = :icon";
            }

            $sql .= " WHERE category_id = :category_id";

            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':category_id', $categoryId);

            if ($newImageName) {
                $stmt->bindParam(':icon', $newImageName);
            }

            try {
                $stmt->execute();
                echo "<script>alert('Successfully Updated');document.location.href ='mainCategory.php';</script>";
            } catch (PDOException $e) {
                echo "<script>alert('Database error: " . $e->getMessage() . "');</script>";
            }
        } else {
            if ($newImageName) {
                $sql = "INSERT INTO Product_Category (category_name, category_icon, parent_id, is_deleted) VALUES (:name, :icon, NULL, 0)";
                $stmt = $pdo->prepare($sql);
                $stmt->bindParam(':name', $name);
                $stmt->bindParam(':icon', $newImageName);
                try {
                    $stmt->execute();
                    echo "<script>alert('Successfully Added');document.location.href ='mainCategory.php';</script>";
                } catch (PDOException $e) {
                    echo "<script>alert('Database error: " . $e->getMessage() . "');</script>";
                }
            }
        }
    }
}

if (isset($_POST['delete'])) {
    $categoryId = $_POST['category_id'];

    $sql = "UPDATE Product_Category SET is_deleted = 1 WHERE category_id = :category_id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':category_id', $categoryId);

    try {
        $stmt->execute();
        echo "<script>alert('Category successfully deleted');document.location.href ='mainCategory.php';</script>";
    } catch (PDOException $e) {
        echo "<script>alert('Database error: " . $e->getMessage() . "');</script>";
    }
}

function getMainCategories($pdo)
{
    $sql = "SELECT * FROM Product_Category WHERE parent_id IS NULL AND is_deleted = 0";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$all_main_categories = getMainCategories($pdo);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bookshop Main Category - MIPS</title>
    <link rel="icon" type="image/x-icon" href="../images/Mahans_IPS_icon.png">
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
        <?php include "../components/admin_sidebar.php"; ?>
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
                <div class="box-container">
                    <?php foreach ($all_main_categories as $category) : ?>
                        <div class="box" data-category-id="<?= htmlspecialchars($category['category_id']); ?>">
                            <div class="image-container">
                                <img src="../uploads/category/<?php echo htmlspecialchars($category['category_icon']); ?>" alt="Icon for <?php echo htmlspecialchars($category['category_name']); ?>">
                            </div>
                            <div class="actions">
                                <form action="" method="POST" style="display:inline;" onsubmit="return showDeactivateConfirmDialog(event);">
                                    <input type="hidden" name="category_id" value="<?= htmlspecialchars($category['category_id']); ?>">
                                    <input type="hidden" name="delete" value="true">
                                    <button type="submit" class="delete-category-btn"><i class="bi bi-x-square-fill"></i></button>
                                </form>
                                <button type="button" class="edit-category-btn" data-category-id="<?= htmlspecialchars($category['category_id']); ?>"><i class="bi bi-pencil-square"></i></button>
                            </div>
                            <div class="txt">
                                <h3><?php echo htmlspecialchars($category['category_name']); ?></h3>
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
                <h1>Add Main Category</h1>
            </div>
            <div class="right">
                <button class="cancel"><i class="bi bi-x-circle"></i></button>
            </div>
        </div>
        <form action="" method="post" enctype="multipart/form-data">
            <input type="hidden" name="category_id" value="">
            <div class="input-container">
                <div class="input-field">
                    <h2>Category Name<sup>*</sup></h2>
                    <input type="text" name="name" value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>" required>
                </div>
                <p>Please enter the category name.</p>
            </div>
            <div class="input-container">
                <div class="input-field">
                    <h2>Category Icon<sup>*</sup></h2>
                    <input type="file" name="image" id="image" accept=".jpg, .jpeg, .png" required>
                </div>
                <p>Please upload an image for the category.</p>
            </div>
            <div class="input-container controls">
                <button type="button" class="cancel">Cancel</button>
                <button type="reset">Clear</button>
                <button type="submit" name="submit">Publish</button>
            </div>
        </form>
    </dialog>
    <?php include "../components/deactivate_confirm_dialog.php"; ?>
    <script src="../javascript/admin.js"></script>
    <script>
        document.querySelectorAll('.edit-category-btn').forEach(button => {
            button.addEventListener('click', function() {
                const categoryId = this.dataset.categoryId;
                fetch(`ajax.php?action=get_category&category_id=${categoryId}`)
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

        document.querySelector('.cancel').addEventListener('click', function() {
            document.getElementById('add-edit-data').close();
        });
    </script>
</body>

</html>