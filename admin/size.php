<?php

session_start();

include "../components/db_connect.php";

if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit();
}

$currentPage = basename($_SERVER['PHP_SELF']);

if (isset($_POST['submit'])) {
    $name = $_POST['name'];
    $shoulder_width = $_POST['shoulder_width'];
    $bust = $_POST['bust'];
    $waist = $_POST['waist'];
    $length = $_POST['length'];

    if (!empty($name)) {
        $sql = "INSERT INTO sizes (size_name, shoulder_width, bust, waist, length) VALUES (:name, :shoulder_width, :bust, :waist, :length)";
        $stmt = $pdo->prepare($sql);

        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':shoulder_width', $shoulder_width);
        $stmt->bindParam(':bust', $bust);
        $stmt->bindParam(':waist', $waist);
        $stmt->bindParam(':length', $length);

        try {
            $stmt->execute();
            header('Refresh:0');
            exit();
        } catch (PDOException $e) {
            echo "<sricpt>alert('Database error: );</sricpt>";
        }
    } else {
        echo "<sricpt>alert('Please enter a product size name.');</sricpt>";
    }
}

function getSizes($pdo)
{
    $sql = "SELECT * FROM Sizes";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$all_product_sizes = getSizes($pdo);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bookshop Apparel Size - MIPS</title>
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
        <?php include "../components/admin_sidebar.php"; ?>
        <!-- END OF ASIDE -->
        <main class="product-size">
            <div class="wrapper">
                <div class="title">
                    <div class="left">
                        <h1>Apparel Size</h1>
                    </div>
                    <div class="right">
                        <button id="open-popup" class="btn btn-outline"><i class="bi bi-plus-circle"></i>Add Apparel Size</button>
                    </div>
                </div>
                <div class="table-body">
                    <table>
                        <thead>
                            <tr>
                                <th>
                                    <h3>Apparel Size Name</h3>
                                </th>
                                <th>
                                    <h3>Shoulder Width</h3>
                                </th>
                                <th>
                                    <h3>Bust</h3>
                                </th>
                                <th>
                                    <h3>Waist</h3>
                                </th>
                                <th>
                                    <h3>Hip</h3>
                                </th>
                                <th>
                                    <h3>Actions</h3>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($all_product_sizes as $size) { ?>
                                <tr>
                                    <td><?= $size['size_name'] ?></td>
                                    <td><?= $size['shoulder_width'] ?></td>
                                    <td><?= $size['bust'] ?></td>
                                    <td><?= $size['waist'] ?></td>
                                    <td><?= $size['length'] ?></td>
                                    <td>
                                        <button id="edit"><i class="bi bi-pencil-square"></i></button>
                                        <button id="delete"><i class="bi bi-trash-fill"></i></button>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>
    <dialog id="add-edit-data">
        <h2>Add Apparel Size</h2>
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
                <button type="button" class="cancel">Cancel</button>
                <button type="reset">Clear</button>
                <button type="submit" name="submit">Publish</button>
            </div>
        </form>
    </dialog>
    <script src="../javascript/admin.js"></script>
</body>

</html>