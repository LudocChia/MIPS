<?php

$database_table = "Student";
$rows_per_page = 12;
include $_SERVER['DOCUMENT_ROOT'] . "/mips/php/admin.php";
include $_SERVER['DOCUMENT_ROOT'] . "/mips/php/activate_pagination.php";

function getAllStudents($pdo, $start, $rows_per_page)
{
    $sql = "SELECT s.*, c.class_name, p.parent_name
            FROM Student s
            LEFT JOIN Class c ON s.class_id = c.class_id
            LEFT JOIN Parent_Student ps ON s.student_id = ps.student_id
            LEFT JOIN Parent p ON ps.parent_id = p.parent_id
            WHERE s.status = 0
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
    $sql = "SELECT * FROM Class WHERE status = 0";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$all_classes = getAllClasses($pdo);

function handleFileUpload($file, $studentId)
{
    $uploadDir = $_SERVER['DOCUMENT_ROOT'] . '/mips/uploads/student/';

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
            echo "<script>alert('Error moving uploaded file: $fileName');</script>";
            return null;
        }
    } else {
        echo "<script>alert('Upload failed. Allowed file types: " . implode(',', $allowedfileExtensions) . "');</script>";
        return null;
    }
}
if (isset($_POST["submit"])) {
    $studentId = $_POST["student_id"];
    $classId = $_POST["class_id"];
    $name = $_POST["name"];
    $parentId = $_POST['selected_parent_id'] ?? null;
    $relationship = $_POST['relationship'] ?? 'father';
    $existingStudentId = isset($_POST['existing_student_id']) ? $_POST['existing_student_id'] : null;

    try {
        $pdo->beginTransaction();

        $studentImage = handleFileUpload($_FILES['student_image'], $studentId);

        if ($existingStudentId) {
            $sql = "UPDATE Student 
                    SET student_id = :studentId, student_name = :name, class_id = :classId, student_image = :student_image 
                    WHERE student_id = :existing_student_id";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':existing_student_id', $existingStudentId);
        } else {
            $sql = "INSERT INTO Student (student_id, student_name, class_id, student_image, status) 
                    VALUES (:studentId, :name, :classId, :student_image, 0)";
            $stmt = $pdo->prepare($sql);
        }

        $stmt->bindParam(':studentId', $studentId);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':classId', $classId);
        $stmt->bindParam(':student_image', $studentImage);
        $stmt->execute();

        if ($parentId) {
            $sqlCheck = "SELECT parent_student_id FROM Parent_Student WHERE student_id = :student_id";
            $stmtCheck = $pdo->prepare($sqlCheck);
            $stmtCheck->bindParam(':student_id', $studentId);
            $stmtCheck->execute();
            $oldParentStudent = $stmtCheck->fetch(PDO::FETCH_ASSOC);

            $newParentStudentId = uniqid('PS');

            $sqlInsert = "INSERT INTO Parent_Student (parent_student_id, parent_id, student_id, relationship) 
                          VALUES (:parent_student_id, :parent_id, :student_id, :relationship)";
            $stmtInsert = $pdo->prepare($sqlInsert);
            $stmtInsert->bindParam(':parent_student_id', $newParentStudentId);
            $stmtInsert->bindParam(':parent_id', $parentId);
            $stmtInsert->bindParam(':student_id', $studentId);
            $stmtInsert->bindParam(':relationship', $relationship);
            $stmtInsert->execute();

            if ($oldParentStudent) {
                $oldParentStudentId = $oldParentStudent['parent_student_id'];

                $sqlUpdateOrders = "UPDATE Orders SET parent_student_id = :new_parent_student_id WHERE parent_student_id = :old_parent_student_id";
                $stmtUpdateOrders = $pdo->prepare($sqlUpdateOrders);
                $stmtUpdateOrders->bindParam(':new_parent_student_id', $newParentStudentId);
                $stmtUpdateOrders->bindParam(':old_parent_student_id', $oldParentStudentId);
                $stmtUpdateOrders->execute();

                $sqlUpdatePayment = "UPDATE Payment SET parent_student_id = :new_parent_student_id WHERE parent_student_id = :old_parent_student_id";
                $stmtUpdatePayment = $pdo->prepare($sqlUpdatePayment);
                $stmtUpdatePayment->bindParam(':new_parent_student_id', $newParentStudentId);
                $stmtUpdatePayment->bindParam(':old_parent_student_id', $oldParentStudentId);
                $stmtUpdatePayment->execute();

                $sqlDeleteOldParent = "DELETE FROM Parent_Student WHERE parent_student_id = :old_parent_student_id";
                $stmtDelete = $pdo->prepare($sqlDeleteOldParent);
                $stmtDelete->bindParam(':old_parent_student_id', $oldParentStudentId);
                $stmtDelete->execute();
            }
        }

        $pdo->commit();

        header('Location: /mips/admin/user/student.php');
        exit();
    } catch (PDOException $e) {
        $pdo->rollBack();
        echo "<script>alert('Database error: " . $e->getMessage() . "');</script>";
    }
}


$pageTitle = "Student Management - MIPS";
include $_SERVER['DOCUMENT_ROOT'] . "/mips/components/admin_head.php";
?>

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
                <?php if (!empty($all_students)) : ?>
                    <div class="table-container">
                        <table>
                            <thead>
                                <tr>
                                    <th>Student ID</th>
                                    <th>Student Name</th>
                                    <th>Student Parent Name</th>
                                    <th>Class</th>
                                    <th>Register Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($all_students as $student) { ?>
                                    <tr>
                                        <td><?= htmlspecialchars($student['student_id']) ?></td>
                                        <td><?= htmlspecialchars($student['student_name']) ?></td>
                                        <td><?= htmlspecialchars($student['parent_name'] ?? '-') ?></td>
                                        <td style="text-align: center;"><?= htmlspecialchars($student['class_name']) ?></td>
                                        <td><?= htmlspecialchars($student['created_at']) ?></td>
                                        <td>
                                            <div class="actions">
                                                <form action="" method="POST" style="display:inline;" onsubmit="return showDeactivateConfirmDialog(event);">
                                                    <input type="hidden" name="student_id" value="<?= htmlspecialchars($student['student_id']); ?>">
                                                    <input type="hidden" name="action" value="deactivate_student">
                                                    <button type="submit" class="delete-student-btn"><i class="bi bi-x-square"></i></button>
                                                </form>
                                                <button type="button" class="edit-student-btn" data-student-id="<?= htmlspecialchars($student['student_id']); ?>"><i class="bi bi-pencil-square"></i></button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                <?php else : ?>
                    <?php include $_SERVER['DOCUMENT_ROOT'] . "/mips/components/no_data_found.php"; ?>
                <?php endif; ?>
                <?php if (!empty($all_students)) : ?>
                    <?php include $_SERVER['DOCUMENT_ROOT'] . "/mips/components/pagination.php"; ?>
                <?php endif; ?>
            </div>
        </main>
    </div>

    <dialog id="add-edit-data">
        <form method="post" enctype="multipart/form-data">
            <div class="title">
                <div class="left">
                    <h1>Add New Student</h1>
                </div>
                <div class="right">
                    <div class="actions"><button class="cancel"><i class="bi bi-x-circle"></i></button></div>
                </div>
            </div>
            <input type="hidden" name="existing_student_id" id="existing_student_id" value="">
            <div class="input-container">
                <h2>Class<sup>*</sup></h2>
                <div class="select-field">
                    <select class="select-box" name="class_id" id="class_id" required>
                        <option value="">Select Class</option>
                        <?php foreach ($all_classes as $class) : ?>
                            <option value="<?= htmlspecialchars($class['class_id']) ?>"><?= htmlspecialchars($class['class_name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <div class="icon-container">
                        <i class="bi bi-caret-down-fill"></i>
                    </div>
                </div>
                <p>Please select the student's class.</p>
            </div>

            <div class="input-container">
                <h2>Student ID<sup>*</sup></h2>
                <div class="input-field">
                    <input type="text" name="student_id" id="student_id" value="" required>
                </div>
                <p>Please enter or modify the student's ID.</p>
            </div>

            <div class="input-container">
                <h2>Student Name<sup>*</sup></h2>
                <div class="input-field">
                    <input type="text" name="name" value="" required>
                </div>
                <p>Please enter the student's full name.</p>
            </div>

            <div class="input-container">
                <h2>Search Parent</h2>
                <div class="input-field">
                    <input type="text" id="search-parent" placeholder="Search by parent name or ID" value="<?= isset($parentId) ? htmlspecialchars($selected_parent_name) : '' ?>">
                </div>
                <div id="add-edit-search-results">
                    <!-- Search results will be appended here -->
                </div>
            </div>
            <input type="hidden" name="selected_parent_id" id="selected_parent_id" value="<?= isset($parentId) ? htmlspecialchars($parentId) : '' ?>">

            <div class="input-container">
                <h2>Relationship<sup>*</sup></h2>
                <div class="select-field">
                    <select class="select-box" name="relationship" id="relationship" required>
                        <option value="father">Father</option>
                        <option value="mother">Mother</option>
                        <option value="guardian">Guardian</option>
                    </select>
                    <div class="icon-container">
                        <i class="bi bi-caret-down-fill"></i>
                    </div>
                </div>
                <p>Please select the relationship between the student and the parent.</p>
            </div>
            <input type="hidden" name="selected_parent_id" id="selected_parent_id">
            <div class="input-container">
                <h2>Student Image<sup>*</sup></h2>
                <div class="input-field">
                    <input type="file" name="student_image" id="student_image" accept=".jpg, .jpeg, .png">
                </div>
                <p>Please upload an image for the student.</p>
            </div>
            <div class="input-container controls">
                <button type="button" class="cancel">Cancel</button>
                <button type="reset" class="delete">Clear</button>
                <button type="submit" class="confirm" name="submit">Publish</button>
            </div>
        </form>
    </dialog>


    <?php include $_SERVER['DOCUMENT_ROOT'] . "/mips/components/confirm_dialog.php"; ?>
    <script src="/mips/javascript/common.js"></script>
    <script src="/mips/javascript/admin.js"></script>
    <script>
        document.getElementById('class_id').addEventListener('change', function() {
            const classId = this.value;

            if (classId) {
                fetch(`/mips/admin/ajax.php?action=get_student_prefix`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded'
                        },
                        body: `class_id=${encodeURIComponent(classId)}`
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.prefix) {
                            document.getElementById('student_id').value = data.prefix;
                        } else {
                            alert('Failed to get prefix for selected class.');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Failed to retrieve student prefix.');
                    });
            }
        });
        document.querySelectorAll('.edit-student-btn').forEach(button => {
            button.addEventListener('click', function() {
                document.querySelector('.confirm').textContent = "Publish";
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
                            document.querySelector('#existing_student_id').value = student.student_id;
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

        function loadDefaultParents() {
            fetch(`/mips/admin/ajax.php?action=get_default_parents`)
                .then(response => response.json())
                .then(parents => {
                    let resultsContainer = document.getElementById('add-edit-search-results');
                    resultsContainer.innerHTML = '';

                    parents.forEach(parent => {
                        let parentElement = document.createElement('div');
                        parentElement.classList.add('search-result');
                        parentElement.innerHTML = `<span>${parent.parent_id} - ${parent.parent_name}</span>`;
                        parentElement.addEventListener('click', function() {
                            document.getElementById('selected_parent_id').value = parent.parent_id;
                            document.getElementById('search-parent').value = `${parent.parent_id} - ${parent.parent_name}`;
                            resultsContainer.innerHTML = '';
                        });
                        resultsContainer.appendChild(parentElement);
                    });
                })
                .catch(error => {
                    console.error('Error fetching parent data:', error);
                });
        }

        window.addEventListener('DOMContentLoaded', loadDefaultParents);

        document.getElementById('search-parent').addEventListener('input', function() {
            const query = this.value;

            if (query.length > 2) {
                fetch(`/mips/admin/ajax.php?action=search_parent`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded'
                        },
                        body: `query=${encodeURIComponent(query)}`
                    })
                    .then(response => response.json())
                    .then(parents => {
                        let resultsContainer = document.getElementById('add-edit-search-results');
                        resultsContainer.innerHTML = '';

                        parents.forEach(parent => {
                            let parentElement = document.createElement('div');
                            parentElement.classList.add('search-result');
                            parentElement.innerHTML = `<span>${parent.parent_id} - ${parent.parent_name}</span>`;
                            parentElement.addEventListener('click', function() {
                                document.getElementById('selected_parent_id').value = parent.parent_id;
                                document.getElementById('search-parent').value = `${parent.parent_id} - ${parent.parent_name}`;
                                resultsContainer.innerHTML = '';
                            });
                            resultsContainer.appendChild(parentElement);
                        });
                    })
                    .catch(error => {
                        console.error('Error fetching parent data:', error);
                    });
            }
        });


        document.querySelectorAll('#add-edit-data .cancel').forEach(button => {
            button.addEventListener('click', function() {
                document.querySelector('#add-edit-data h1').textContent = "Add New Student";
            });
        });
    </script>
</body>

</html>