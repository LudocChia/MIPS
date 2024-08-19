<?php

session_start();

include "../components/db_connect.php";

if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit();
}

$currentPage = basename($_SERVER['PHP_SELF']);

function getAllStudents($pdo)
{
    $sql = "
        SELECT s.*, c.class_name 
        FROM Student s
        LEFT JOIN Class c ON s.class_id = c.class_id
        WHERE s.is_deleted = 0
    ";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getAllClasses($pdo)
{
    $sql = "SELECT * FROM Class WHERE is_deleted = 0";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$all_students = getAllStudents($pdo);
$all_classes = getAllClasses($pdo);

function handleFileUpload($file, $studentId)
{
    $uploadDir = '../uploads/student/';

    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    if ($file['error'] === UPLOAD_ERR_NO_FILE) {
        return null;
    }

    $allowedfileExtensions = ['jpg', 'jpeg', 'png'];
    $fileName = $file['name'];
    $fileTmpPath = $file['tmp_name'];
    $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

    if (in_array($fileExtension, $allowedfileExtensions)) {
        $newFileName = $studentId . '_' . uniqid() . '.' . $fileExtension;
        $dest_path = $uploadDir . $newFileName;

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
    $classId = $_POST["class_id"];
    $existingStudentId = isset($_POST['existing_student_id']) ? $_POST['existing_student_id'] : null;

    try {
        $studentImage = handleFileUpload($_FILES['student_image'], $studentId);

        if ($existingStudentId) {
            $sql = "UPDATE Student SET student_id = :studentId, student_name = :name, class_id = :classId, student_image = :student_image WHERE student_id = :existing_student_id";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':existing_student_id', $existingStudentId);
        } else {
            // Add new student
            $sql = "INSERT INTO Student (student_id, student_name, class_id, student_image, is_deleted) VALUES (:studentId, :name, :classId, :student_image, 0)";
            $stmt = $pdo->prepare($sql);
        }

        $stmt->bindParam(':studentId', $studentId);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':classId', $classId);
        $stmt->bindParam(':student_image', $studentImage);
        $stmt->execute();

        header('Location: student.php');
        exit();
    } catch (PDOException $e) {
        echo "<script>alert('Database error: " . $e->getMessage() . "');</script>";
    }
}

// Handle deleting a student
if (isset($_POST['delete'])) {
    $studentId = $_POST['student_id'];

    try {
        $sql = "UPDATE Student SET is_deleted = 1 WHERE student_id = :student_id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':student_id', $studentId);
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
    <title>All Students - MIPS</title>
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
        <!-- END OF ASIDE -->
        <main class="category">
            <div class="wrapper">
                <div class="title">
                    <div class="left">
                        <h1>Mahans Students</h1>
                    </div>
                    <div class="right">
                        <button id="open-popup" class="btn btn-outline-primary"><i class="bi bi-person-fill-add"></i>Add New Student</button>
                    </div>
                </div>
                <div class="table-body">
                    <table>
                        <thead>
                            <tr>
                                <th>Student ID</th>
                                <th>Student Name</th>
                                <th>Class</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($all_students as $student) { ?>
                                <tr>
                                    <td><?= htmlspecialchars($student['student_id']) ?></td>
                                    <td><?= htmlspecialchars($student['student_name']) ?></td>
                                    <td><?= htmlspecialchars($student['class_name']) ?></td>
                                    <td>
                                        <button type="button" class="edit-student-btn" data-student-id="<?= htmlspecialchars($student['student_id']); ?>"><i class="bi bi-pencil-square"></i></button>
                                        <form action="" method="POST" style="display:inline;" onsubmit="return showDeactivateConfirmDialog(event);">
                                            <input type="hidden" name="student_id" value="<?= htmlspecialchars($student['student_id']); ?>">
                                            <input type="hidden" name="delete" value="true">
                                            <button type="submit" class="delete-student-btn"><i class="bi bi-x-square-fill"></i></button>
                                        </form>
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
        <h1>Add New Student</h1>
        <form action="" method="post" enctype="multipart/form-data">
            <input type="hidden" name="existing_student_id" value="">
            <div class="input-container">
                <h2>Student ID<sup>*</sup></h2>
                <input type="text" name="student_id" value="" required>
                <p>Please enter the student's ID.</p>
            </div>
            <div class="input-container">
                <h2>Student Name<sup>*</sup></h2>
                <input type="text" name="name" value="" required>
                <p>Please enter the student's full name.</p>
            </div>
            <div class="input-container">
                <h2>Class<sup>*</sup></h2>
                <select name="class_id" required>
                    <option value="">Select Class</option>
                    <?php foreach ($all_classes as $class) : ?>
                        <option value="<?= htmlspecialchars($class['class_id']) ?>"><?= htmlspecialchars($class['class_name']) ?></option>
                    <?php endforeach; ?>
                </select>
                <p>Please select the student's class.</p>
            </div>
            <div class="input-container">
                <h2>Student Image<sup>*</sup></h2>
                <input type="file" name="student_image" id="student_image" accept=".jpg, .jpeg, .png">
                <p>Please upload an image for the student.</p>
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
        document.querySelectorAll('.edit-student-btn').forEach(button => {
            button.addEventListener('click', function() {
                const studentId = this.dataset.studentId;
                fetch(`ajax.php?action=get_student&student_id=${studentId}`)
                    .then(response => response.json())
                    .then(student => {
                        if (student.error) {
                            alert(student.error);
                        } else {
                            document.querySelector('#add-edit-data [name="existing_student_id"]').value = student.student_id;
                            document.querySelector('#add-edit-data [name="student_id"]').value = student.student_id;
                            document.querySelector('#add-edit-data [name="name"]').value = student.student_name;
                            document.querySelector('#add-edit-data [name="class_id"]').value = student.class_id;
                            document.querySelector('#add-edit-data h1').textContent = "Edit Student";
                            document.getElementById('add-edit-data').showModal();
                        }
                    })
                    .catch(error => {
                        console.error('Error fetching student data:', error);
                        alert('Failed to load student data.');
                    });
            });
        });

        document.querySelector('.cancel').addEventListener('click', function() {
            document.getElementById('add-edit-data').close();
        });
    </script>
</body>

</html>