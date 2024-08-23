<?php

$database_table = "Student";
$rows_per_page = 12;
include $_SERVER['DOCUMENT_ROOT'] . "/mips/php/admin.php";


function getAllStudents($pdo, $start, $rows_per_page)
{
    $sql = "SELECT s.*, c.class_name 
            FROM Student s
            LEFT JOIN Class c ON s.class_id = c.class_id
            WHERE s.is_deleted = 0
            LIMIT :start, :rows_per_page";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':start', $start, PDO::PARAM_INT);
    $stmt->bindParam(':rows_per_page', $rows_per_page, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


$all_students = getAllStudents($pdo, $start, $rows_per_page);

function getAllClasses($pdo)
{
    $sql = "SELECT * FROM Class WHERE is_deleted = 0";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$all_classes = getAllClasses($pdo);

function handleFileUpload($file, $studentId)
{
    $uploadDir = '/mips/uploads/student/';

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

$pageTitle = "Student Management - MIPS";
include $_SERVER['DOCUMENT_ROOT'] . "/mips/components/admin_head.php"; ?>

<body>
    <?php include $_SERVER['DOCUMENT_ROOT'] . "/mips/components/admin_header.php"; ?>
    <div class="container">
        <?php include $_SERVER['DOCUMENT_ROOT'] . "/mips/components/admin_sidebar.php"; ?>
        <main class="category">
            <div class="wrapper">
                <div class="title">
                    <div class="left">
                        <h1>Student Management</h1>
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
                                        <form action="" method="POST" style="display:inline;" onsubmit="return showDeactivateConfirmDialog(event);">
                                            <input type="hidden" name="student_id" value="<?= htmlspecialchars($student['student_id']); ?>">
                                            <input type="hidden" name="action" value="deactivate_student">
                                            <button type="submit" class="delete-student-btn"><i class="bi bi-x-square"></i></button>
                                        </form>
                                        <button type="button" class="edit-student-btn" data-student-id="<?= htmlspecialchars($student['student_id']); ?>"><i class="bi bi-pencil-square"></i></button>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
                <?php include $_SERVER['DOCUMENT_ROOT'] . "/mips/components/pagination.php"; ?>
            </div>
        </main>
    </div>
    <dialog id="add-edit-data">
        <div class="title">
            <div class="left">
                <h1>Add New Student</h1>
            </div>
            <div class="right">
                <button id="cancel"><i class="bi bi-x-square"></i></button>
            </div>
        </div>
        <form action="" method="post" enctype="multipart/form-data">
            <input type="hidden" name="student_id" value="">
            <div class="input-container">
                <h2>Student ID<sup>*</sup></h2>
                <div class="input-field">
                    <input type="text" name="student_id" value="" required>
                </div>
                <p>Please enter the student's ID.</p>
            </div>
            <div class="input-container">
                <h2>Student Name<sup>*</sup></h2>
                <div class="input-field">
                    <input type="text" name="name" value="" required>
                </div>
                <p>Please enter the student's full name.</p>
            </div>
            <div class="input-container">
                <h2>Class<sup>*</sup></h2>
                <div class="input-field">
                    <select name="class_id" required>
                        <option value="">Select Class</option>
                        <?php foreach ($all_classes as $class) : ?>
                            <option value="<?= htmlspecialchars($class['class_id']) ?>"><?= htmlspecialchars($class['class_name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <p>Please select the student's class.</p>
            </div>
            <div class="input-container">
                <h2>Student Image<sup>*</sup></h2>
                <div class="input-field">
                    <input type="file" name="student_image" id="student_image" accept=".jpg, .jpeg, .png">
                </div>
                <p>Please upload an image for the student.</p>
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
        document.querySelectorAll('.edit-student-btn').forEach(button => {
            button.addEventListener('click', function() {
                const studentId = this.dataset.studentId;
                fetch(`/mips/admin/ajax.php?action=get_student`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded'
                        },
                        body: `student_id=${encodeURIComponent(studentId)}`
                    })
                    .then(response => response.json())
                    .then(student => {
                        if (student.error) {
                            alert(student.error);
                        } else {
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
    </script>
</body>

</html>