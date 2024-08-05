<?php
include "../components/db_connect.php";

// Function to display alerts
function displayAlert($message)
{
    echo "<script>alert('$message');</script>";
}

if (isset($_POST['submit'])) {
    // Collect form data
    $name = $_POST['name'];
    $shoulder_width = $_POST['shoulder_width'];
    $bust = $_POST['bust'];
    $waist = $_POST['waist'];
    $length = $_POST['length'];

    // Validate size name
    if (!empty($name)) {
        // Prepare the SQL statement
        $sql = "INSERT INTO sizes (size_name, shoulder_width, bust, waist, length) VALUES (:name, :shoulder_width, :bust, :waist, :length)";
        $stmt = $pdo->prepare($sql);

        // Bind parameters
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':shoulder_width', $shoulder_width);
        $stmt->bindParam(':bust', $bust);
        $stmt->bindParam(':waist', $waist);
        $stmt->bindParam(':length', $length);

        try {
            // Execute the statement
            $stmt->execute();
            displayAlert('Successfully Added');
            echo "<script>document.location.href ='productSize.php';</script>";
        } catch (PDOException $e) {
            // Handle SQL execution error
            displayAlert('Database error: ' . $e->getMessage());
        }
    } else {
        displayAlert('Please enter a product size name.');
    }
}

// Retrieve existing sizes
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
                            <li><a href="mainCategory.php"><i class="bi bi-tag"></i>
                                    <h4>Main Category</h4>
                                </a>
                            </li>
                            <li><a href="subcategory.php"><i class="bi bi-tag"></i>
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
            </div>
        </aside>
        <!-- END OF ASIDE -->
        <main class="product-size">
            <div class="wrapper">
                <div class="title">
                    <div class="left">
                        <h1>Bookshop Product Size</h1>
                    </div>
                    <div class="right">
                        <button id="open-popup"><i class="bi bi-plus-circle"></i>Add Product Size</button>
                    </div>
                </div>
                <div class="box-container">
                    <div class="box">
                        <div class="image-container">
                            <a href="#"></a>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
    <dialog id="add-product">
        <h2>Add Product Size</h2>
        <form action="" method="post" enctype="multipart/form-data">
            <div class="input-field">
                <h2>Product Size Name<sup>*</sup></h2>
                <input type="text" name="name" value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>">
                <p>Please enter the size name (e.g., 100, 110, 120).</p>
            </div>
            <div class="input-field">
                <h2>Shoulder Width (cm)</h2>
                <input type="number" step="0.01" name="shoulder_width" value="<?php echo isset($_POST['shoulder_width']) ? htmlspecialchars($_POST['shoulder_width']) : ''; ?>">
            </div>
            <div class="input-field">
                <h2>Bust (cm)</h2>
                <input type="number" step="0.01" name="bust" value="<?php echo isset($_POST['bust']) ? htmlspecialchars($_POST['bust']) : ''; ?>">
            </div>
            <div class="input-field">
                <h2>Waist (cm)</h2>
                <input type="number" step="0.01" name="waist" value="<?php echo isset($_POST['waist']) ? htmlspecialchars($_POST['waist']) : ''; ?>">
            </div>
            <div class="input-field">
                <h2>Length (cm)</h2>
                <input type="number" step="0.01" name="length" value="<?php echo isset($_POST['length']) ? htmlspecialchars($_POST['length']) : ''; ?>">
            </div>
            <div class="controls">
                <button type="button" onclick="document.getElementById('add-product').close();" class="close-btn">Cancel</button>
                <button type="reset">Clear</button>
                <button type="submit" name="submit">Publish</button>
            </div>
        </form>
    </dialog>
    <script src="../javascript/admin.js"></script>
    <script>
        // Open dialog for adding a size
        document.getElementById('open-popup').addEventListener('click', function() {
            document.getElementById('add-product').showModal();
        });
    </script>
</body>

</html>