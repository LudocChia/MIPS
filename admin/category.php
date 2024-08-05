<?php
include "../components/db_connect.php";

$msg = [];
if (isset($_POST["submit"])) {
    $name = $_POST["name"];
    $parentId = !empty($_POST["parent_id"]) ? $_POST["parent_id"] : null;


    if ($_FILES["image"]["error"] === 4) {
        echo "<script> alert('Image Does Not Exist'); </script>";
    } else {
        $fileName = $_FILES["image"]["name"];
        $fileSize = $_FILES["image"]["size"];
        $tmpName = $_FILES["image"]["tmp_name"];

        $validImageExtension = ['jpg', 'jpeg', 'png'];
        $imageExtension = explode('.', $fileName);
        $imageExtension = strtolower(end($imageExtension));
        if (!in_array($imageExtension, $validImageExtension)) {
            echo "<script> alert('Invalid Image Extension'); </script>";
        } else if ($fileSize > 1000000) {
            echo "<script> alert('Image Size Is Too Large'); </script>";
        } else {
            $newImageName = uniqid() . '.' . $imageExtension;
            if (move_uploaded_file($tmpName, '../uploads/' . $newImageName)) {
                $sql = "INSERT INTO Product_Category (category_name, category_icon, parent_id, is_deleted) VALUES (:name, :icon, :parentId, 0)";
                $stmt = $pdo->prepare($sql);
                $stmt->bindParam(':name', $name);
                $stmt->bindParam(':icon', $newImageName);
                $stmt->bindParam(':parentId', $parentId);
                try {
                    $stmt->execute();
                    echo "<script>alert('Successfully Added');document.location.href ='category.php';</script>";
                } catch (PDOException $e) {
                    echo "<script>alert('Database error: " . $e->getMessage() . "');</script>";
                }
            } else {
                echo "<script>alert('Failed to move uploaded file.');</script>";
            }
        }
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
    <title>Bookshop Category - Mahans School</title>
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
                        <a href="index.php"><i class="bi bi-grid"></i>
                            <h4>Dashboard</h4>
                        </a>
                    </li>
                    <li>
                        <div class="icon-link">
                            <a href="#">
                                <i class="bi bi-shop-window"></i>
                                <h4>Bookshop</h4>
                                <i class="bi bi-chevron-down"></i>
                            </a>

                        </div>
                        <ul class="sub-menu">
                            <li><a href="category.php" class="active"><i class="bi bi-tag"></i>
                                    <h4>Category</h4>
                                </a>
                            </li>
                            <li><a href="product.php"><i class="bi bi-box-seam"></i>
                                    <h4>Product</h4>
                                </a>
                            </li>
                        </ul>
                    </li>
                </ul>
        </aside>
        <!-- END OF ASIDE -->
        <main class="category">
            <div class="box-container">
                <div class="header">
                    <div class="left">
                        <h1>Bookshop Main Category</h1>
                    </div>
                    <div class="right">
                        <button id="open-popup"><i class="bi bi-plus-circle"></i>Add Main Category</button>
                    </div>
                </div>
                <?php foreach ($all_main_categories as $category) : ?>
                    <div class="box">
                        <h3><?php echo htmlspecialchars($category['category_name']); ?></h3>
                        <a href="#">
                            <div class="image-container">
                                <img src="../uploads/<?php echo htmlspecialchars($category['category_icon']); ?>" alt="Icon for <?php echo htmlspecialchars($category['category_name']); ?>">
                            </div>
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
        </main>
    </div>
    <dialog>
        <h1>Add Category</h1>
        <form class="" action="" method="post" enctype="multipart/form-data">
            <div>
                <h2>Category Name<sup>*</sup></h2>
                <input type="text" name="name" value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>">
                <p>Please enter full name as per IC or Passport.</p>
            </div>
            <div>
                <h2>Category Icon<sup>*</sup></h2>
                <input type="file" name="image" id="image" accept=".jpg, .jpeg, .png" value="">
                <p>Please enter full name as per IC or Passport.</p>
            </div>
            <div>
                <h2>Parent Category</h2>
                <select name="parent_category" id="parent_category">
                    <option value="">None</option>
                    <?php foreach ($all_main_categories as $category) {
                        if ($category['parent_id'] === NULL) {
                            echo '<option value="' . $category['category_id'] . '">' . $category['category_name'] . '</option>';
                        }
                    }
                    ?>
                </select>
                <p>Please enter full name as per IC or Passport.</p>
            </div>
            <div class="controls">
                <button onclick="showDialog('main')">Cancel</button>
                <button type="reset">Clear</button>
                <button type="submit" name="submit">Publish</button>
            </div>
        </form>
    </dialog>
    <script src="../javascript/admin.js"></script>
</body>

</html>