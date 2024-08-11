<?php

session_start();

include "../components/db_connect.php";

function getAllStudents($pdo)
{
    $sql = "SELECT * FROM Student WHERE is_deleted = 0";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$all_students = getAllStudents($pdo);

function handleFileUpload($file, $studentId)
{
    if ($file['error'] === UPLOAD_ERR_NO_FILE) {
        return null;
    }

    $allowedfileExtensions = ['jpg', 'jpeg', 'png'];
    $fileName = $file['name'];
    $fileTmpPath = $file['tmp_name'];
    $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

    if (in_array($fileExtension, $allowedfileExtensions)) {
        $newFileName = $studentId . '_' . uniqid() . '.' . $fileExtension;
        $dest_path = '../uploads/' . $newFileName;

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
    $studentId = $_POST["student_id"];
    $name = $_POST["name"];
    $class = $_POST["class"];


    try {
        $studentImage = handleFileUpload($_FILES['student_image'], $studentId);

        $sql = "INSERT INTO Student (student_id, student_name, student_class, student_image, is_deleted) 
                        VALUES (:studentId, :name, :class, :student_image, 0)";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':studentId', $studentId);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':class', $class);
        $stmt->bindParam(':student_image', $studentImage);
        $stmt->execute();

        header('Location: student.php');
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
    <title>All Students - Mahans School</title>
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
                            <li><a href="size.php"><i class="bi bi-aspect-ratio-fill"></i>
                                    <h4>Product Size</h4>
                                </a>
                            </li>
                            <li><a href="product.php"><i class="bi bi-box-seam-fill"></i>
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
                                    <h4>All Teachers</h4>
                                </a>
                            </li>
                            <li>
                                <a href="parent.php"><i class="bi bi-people-fill"></i>
                                    <h4>All Parents</h4>
                                </a>
                            </li>
                            <li>
                                <a href="student.php" class="active"><i class="bi bi-people-fill"></i>
                                    <h4>All Students</h4>
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
        <main class="category">
            <div class="wrapper">
                <div class="title">
                    <div class="left">
                        <h1>Mahans Students</h1>
                    </div>
                    <div class="right">
                        <button id="open-popup"><i class="bi bi-person-fill-add"></i>Add New Student</button>
                        <?php
                        try {
                            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                            $countQuery = "SELECT COUNT(*) FROM Student WHERE is_deleted = 0";
                            $stmt = $pdo->prepare($countQuery);
                            $stmt->execute();
                            $count = $stmt->fetchColumn();

                            echo "<p>Total $count Student(s)</p>";
                        } catch (PDOException $e) {
                            echo "<script>alert('Database error: " . $e->getMessage() . "');</script>";
                        }
                        ?>
                    </div>
                </div>
                <div class="box-container">
                    <?php foreach ($all_students as $student) : ?>
                        <div class="box">
                            <h3><?php echo htmlspecialchars($student['student_name']); ?></h3>
                            <a href="#">
                                <div class="image-container">
                                    <img src="../uploads/<?php echo htmlspecialchars($student['student_image']); ?>" alt="Image for <?php echo htmlspecialchars($student['student_name']); ?>">
                                </div>
                            </a>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </main>
    </div>
    <dialog id="add-edit-data">
        <h1>Add New Student</h1>
        <form action="" method="post" enctype="multipart/form-data">
            <input type="hidden" name="student_id" value="">
            <div class="input-container">
                <h2>Student ID<sup>*</sup></h2>
                <input type="text" name="student_id" value="<?php echo isset($_POST['student_id']) ? htmlspecialchars($_POST['student_id']) : ''; ?>" required>
                <p>Please enter the student's ID.</p>
            </div>
            <div class="input-container">
                <h2>Student Name<sup>*</sup></h2>
                <input type="text" name="name" value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>" required>
                <p>Please enter the student's full name.</p>
            </div>
            <div class="input-container">
                <h2>Student Email<sup>*</sup></h2>
                <input type="email" name="email" value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" required>
                <p>Please enter the student's email address.</p>
            </div>
            <div class="input-container">
                <h2>Student Class</h2>
                <input type="text" name="class" value="<?php echo isset($_POST['class']) ? htmlspecialchars($_POST['class']) : ''; ?>" required>
            </div>
            <div class="input-container">
                <h2>Student Image<sup>*</sup></h2>
                <input type="file" name="student_image" id="student_image" accept=".jpg, .jpeg, .png" required>
                <p>Please upload an image for the student.</p>
            </div>
            <div class="input-container controls">
                <button type="button" class="cancel">Cancel</button>
                <button type="reset">Clear</button>
                <button type="submit" name="submit">Publish</button>
            </div>
        </form>
    </dialog>
    <script src="../javascript/admin.js"></script>
</body>

</html>