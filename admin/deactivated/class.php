<?php

$database_table = "Class";
$rows_per_page = 5;
include $_SERVER['DOCUMENT_ROOT'] . "/mips/php/admin.php";
include $_SERVER['DOCUMENT_ROOT'] . "/mips/php/activated_pagination.php";

function getDeactivatedClasses($pdo, $start, $rows_per_page)
{
    $sql = "SELECT c.*, g.grade_name, a.admin_name AS teacher_name 
            FROM Class c
            LEFT JOIN Grade g ON c.grade_id = g.grade_id
            LEFT JOIN Admin a ON c.class_teacher_id = a.admin_id
            WHERE c.status = 1 LIMIT :start, :rows_per_page";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':start', $start, PDO::PARAM_INT);
    $stmt->bindParam(':rows_per_page', $rows_per_page, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$all_classes = getDeactivatedClasses($pdo, $start, $rows_per_page);

$pageTitle = "Class Management - MIPS";
include $_SERVER['DOCUMENT_ROOT'] . "/mips/components/admin_head.php"; ?>

<body id="<?php echo $id ?>">
    <?php include $_SERVER['DOCUMENT_ROOT'] . "/mips/components/admin_header.php"; ?>
    <div class="container">
        <?php include $_SERVER['DOCUMENT_ROOT'] . "/mips/components/admin_sidebar.php"; ?>
        <main class="class">
            <div class="wrapper">
                <div class="title">
                    <div class="left">
                        <h1>Deactivated Class</h1>
                    </div>
                    <div class="right">
                        <a href="/mips/admin/deactivated/"><i class="bi bi-arrow-90deg-up"></i>Deactivated Menu</a>
                    </div>
                </div>
                <?php if (!empty($all_classes)) : ?>
                    <div class="box-container">
                        <?php foreach ($all_classes as $class) : ?>
                            <div class="box" data-class-id="<?= htmlspecialchars($class['class_id']); ?>">
                                <div class="info-container">
                                    <h3><?php echo htmlspecialchars($class['class_name']); ?> (<?php echo htmlspecialchars($class['grade_name']); ?>)</h3>
                                    <p>Class Teacher: <?php echo htmlspecialchars($class['teacher_name'] ?? 'N/A'); ?></p>
                                </div>
                                <div class="actions">
                                    <form action="" method="POST" style="display:inline;" onsubmit="return showDeleteConfirmDialog(event);">
                                        <input type="hidden" name="class_id" value="<?= htmlspecialchars($class['class_id']); ?>">
                                        <input type="hidden" name="action" value="delete_class">
                                        <button type="submit" class="delete-class-btn"><i class="bi bi-x-square"></i></button>
                                    </form>
                                    <form action="" method="POST" style="display:inline;" onsubmit="return showRecoverConfirmDialog(event);">
                                        <input type="hidden" name="class_id" value="<?= htmlspecialchars($class['class_id']); ?>">
                                        <input type="hidden" name="action" value="recover_class">
                                        <button type="submit" class="recover-class-btn"><i class="bi bi-arrow-clockwise"></i></button>
                                    </form>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else : ?>
                    <?php include $_SERVER['DOCUMENT_ROOT'] . "/mips/components/no_data_found.php"; ?>
                <?php endif; ?>
                <?php if (!empty($all_classes)) : ?>
                    <?php include $_SERVER['DOCUMENT_ROOT'] . "/mips/components/pagination.php"; ?>
                <?php endif; ?>
            </div>
        </main>
    </div>

    <dialog id="add-edit-data">
        <div class="title">
            <div class="left">
                <h1>Add New Class</h1>
            </div>
            <div class="right">
                <button class="cancel"><i class="bi bi-x-circle"></i></button>
            </div>
        </div>
        <form action="" method="post" enctype="multipart/form-data">
            <input type="hidden" name="class_id" value="">
            <div class="input-container">
                <h2>Class Name<sup>*</sup></h2>
                <div class="input-field">
                    <input type="text" name="class_name" value="<?php echo isset($_POST['class_name']) ? htmlspecialchars($_POST['class_name']) : ''; ?>">
                </div>
                <p>Please enter the class name.</p>
            </div>
            <div class="input-container">
                <h2>Grade<sup>*</sup></h2>
                <div class="select-field">
                    <select class="select-box" name="grade_id" required>
                        <!-- You will need to dynamically load the grades here -->
                        <option value="">Select Grade</option>
                        <!-- Add grade options dynamically from database -->
                    </select>
                    <div class="icon-container">
                        <i class="bi bi-caret-down-fill"></i>
                    </div>
                </div>
                <p>Please select the grade for the class.</p>
            </div>
            <div class="input-container controls">
                <button type="button" class="cancel">Cancel</button>
                <button type="reset" class="delete">Clear</button>
                <button type="submit" class="confirm" name="submit">Publish</button>
            </div>
        </form>
    </dialog>

    <?php include $_SERVER['DOCUMENT_ROOT'] . "/mips/components/confirm_dialog.php"; ?>
    <script src="/mips/javascript/admin.js"></script>
    <script>
        document.querySelectorAll('.edit-class-btn').forEach(button => {
            button.addEventListener('click', function() {
                const classId = this.dataset.classId;
                fetch(`/mips/admin/ajax.php?action=get_class&class_id=${classId}`)
                    .then(response => response.json())
                    .then(classData => {
                        if (classData.error) {
                            alert(classData.error);
                        } else {
                            document.querySelector('#add-edit-data [name="class_id"]').value = classData.class_id;
                            document.querySelector('#add-edit-data [name="class_name"]').value = classData.class_name;
                            document.querySelector('#add-edit-data [name="grade_id"]').value = classData.grade_id;
                            document.querySelector('#add-edit-data h1').textContent = "Edit Class";
                            document.getElementById('add-edit-data').showModal();
                        }
                    })
                    .catch(error => {
                        console.error('Error fetching class data:', error);
                        alert('Failed to load class data.');
                    });
            });
        });
    </script>
</body>

</html>