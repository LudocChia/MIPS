<?php
include "../components/db_connect.php";

$msg = [];
if (isset($_POST['submit'])) {
    $name = $_POST['name'];

    if (!empty($name)) {
        $sql = "INSERT INTO sizes (size_name) VALUES (:name)";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':name', $name);

        try {
            $stmt->execute();
            echo "<script>alert('Successfully Added');document.location.href ='category.php';</script>";
        } catch (PDOException $e) {
            echo "<script>alert('Database error: " . $e->getMessage() . "');</script>";
        }
    } else {
        echo "<script>alert('Please enter a product size name.');</script>";
    }
}
$sql = "SELECT * FROM Sizes";
$sizes = $pdo->query($sql);

?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bookshop Product Size - Mahans School</title>
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
                            <li><a href="category.php"><i class="bi bi-tag"></i>
                                    <h4>Main Category</h4>
                                </a>
                            </li>
                            <li><a href="category.php"><i class="bi bi-tag"></i>
                                    <h4>Subcategory</h4>
                                </a>
                            </li>
                            <li><a href="product_size.php" class="active"><span class="material-symbols-outlined">resize</span>
                                    <h4>Product Size</h4>
                                </a>
                            </li>
                            <li><a href="product.php"><i class="bi bi-box-seam"></i>
                                    <h4>All Product</h4>
                                </a>
                            </li>
                        </ul>
                    </li>
                </ul>
        </aside>
        <!-- END OF ASIDE -->
        <main>
            <div class="box-container">
                <div class="header">
                    <div class="left">
                        <h1>Bookshop Product Size</h1>
                    </div>
                    <div class="right">
                        <button id="open-popup"><i class="bi bi-plus-circle"></i>Add Product Size</button>
                    </div>
                </div>
                <div class="box">
                    <div class="image-container">
                        <a href="#"></a>
                    </div>
                </div>
            </div>
        </main>
    </div>
    <dialog id="add-product">
        <h2>Add Product Size</h2>
        <form action="" method="post">
            <div class="input-field">
                <h2>Product Size Name<sup>*</sup></h2>
                <input type="text" name="name" value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>">
                <p>Please enter the size name (e.g., XS, S, M, L, XL).</p>
            </div>
        </form>
        <div class="controls">
            <button onclick="showDialog('sub')" class="close-btn">Cancel</button>
            <button type="reset">Clear</button>
            <button type="submit" name="submit">Publish</button>
        </div>
    </dialog>
    <script src="../javascript/admin.js"></script>
</body>

</html>