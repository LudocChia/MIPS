<?php

$database_table = "Parent";
$rows_per_page = 12;
include $_SERVER['DOCUMENT_ROOT'] . "/mips/php/admin.php";
include $_SERVER['DOCUMENT_ROOT'] . "/mips/php/activate_pagination.php";

function getAllParents($pdo, $start, $rows_per_page)
{
    $sql = "SELECT * FROM Parent WHERE status IN (-1, 0) ORDER BY created_at DESC LIMIT :start, :rows_per_page";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':start', $start, PDO::PARAM_INT);
    $stmt->bindParam(':rows_per_page', $rows_per_page, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$all_parents = getAllParents($pdo, $start, $rows_per_page);


function generateParentId()
{
    return uniqid("PR");
}

$pageTitle = "Parent Management - MIPS";
include $_SERVER['DOCUMENT_ROOT'] . "/mips/components/admin_head.php";
?>

<body id="<?php echo $id ?>">
    <?php include $_SERVER['DOCUMENT_ROOT'] . "/mips/components/admin_header.php"; ?>
    <div class="container">
        <?php include $_SERVER['DOCUMENT_ROOT'] . "/mips/components/admin_sidebar.php"; ?>
        <main class="parent">
            <div class="wrapper">
                <div class="title">
                    <div class="left">
                        <h1>Parent Management</h1>
                    </div>
                    <div class="right">
                        <button class="btn btn-outline-primary" id="open-popup"><i class="bi bi-person-fill-add"></i>Add New Parent</button>
                    </div>
                </div>
                <div class="table-body">
                    <?php if (!empty($all_parents)) : ?>
                        <table>
                            <thead>
                                <tr>
                                    <th>Parent ID</th>
                                    <th>Parent Name</th>
                                    <th>Parent Email</th>
                                    <th>Parent Phone</th>
                                    <th>Register Date</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($all_parents as $parent) { ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($parent['parent_id']); ?></td>
                                        <td><?php echo htmlspecialchars($parent['parent_name']); ?></td>
                                        <td><?php echo htmlspecialchars($parent['parent_email']); ?></td>
                                        <td style="text-align: center;"><?php echo htmlspecialchars($parent['parent_phone']) ? htmlspecialchars($parent['parent_phone']) : '-'; ?></td>
                                        <td><?php echo htmlspecialchars($parent['created_at']); ?></td>
                                        <td style="text-align: center;"><?php echo getStatusLabel($parent['status']); ?></td>
                                        <td>
                                            <div class="actions">
                                                <form action="" method="POST" style="display:inline;" onsubmit="return showDeactivateConfirmDialog(event);">
                                                    <input type="hidden" name="parent_id" value="<?= htmlspecialchars($parent['parent_id']); ?>">
                                                    <input type="hidden" name="action" value="deactivate_parent">
                                                    <button type="submit" class="delete-parent-btn"><i class="bi bi-x-square"></i></button>
                                                </form>
                                                <button type="button" class="edit-parent-btn" data-parent-id="<?= htmlspecialchars($parent['parent_id']); ?>"><i class="bi bi-pencil-square"></i></button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    <?php else : ?>
                        <?php include $_SERVER['DOCUMENT_ROOT'] . "/mips/components/no_data_found.php"; ?>
                    <?php endif; ?>
                </div>
                <?php if (!empty($all_parents)) : ?>
                    <?php include $_SERVER['DOCUMENT_ROOT'] . "/mips/components/pagination.php"; ?>
                <?php endif; ?>
            </div>
        </main>
    </div>
    <dialog id="add-edit-data">
        <form id="parent-form-ajax" method="post">
            <div class="title">
                <div class="left">
                    <h1>Add New Parent</h1>
                </div>
                <div class="right">
                    <button class="actions cancel"><i class="bi bi-x-circle"></i></button>
                </div>
            </div>
            <div id="alert-container"></div>
            <input type="hidden" name="parent_id" value="">
            <div class="input-container">
                <h2>Parent Name<sup>*</sup></h2>
                <div class="input-field">
                    <input type="text" name="name" required>
                </div>
                <p>Please enter the parent's full name.</p>
            </div>
            <div class="input-container">
                <h2>Parent Email<sup>*</sup></h2>
                <div class="input-field">
                    <input type="email" name="email" required>
                </div>
                <p>Please enter the parent's email address.</p>
            </div>
            <div class="input-container">
                <h2>Parent Phone</h2>
                <div class="input-field">
                    <input type="text" name="phone">
                </div>
                <p>Please enter the parent's phone number.</p>
            </div>
            <div class="input-container">
                <h2>Password<sup>*</sup></h2>
                <div class="input-field">
                    <input type="password" name="password" required>
                </div>
                <p>Please enter a secure password.</p>
            </div>
            <div class="input-container">
                <h2>Confirm Password<sup>*</sup></h2>
                <div class="input-field">
                    <input type="password" name="confirm_password" required>
                </div>
                <p>Please confirm the password.</p>
            </div>
            <div class="controls">
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
        document.querySelectorAll('.edit-parent-btn').forEach(button => {
            button.addEventListener('click', function() {
                const parentId = this.dataset.parentId;
                fetch(`/mips/admin/ajax.php?action=get_parent`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded'
                        },
                        body: new URLSearchParams({
                            parent_id: parentId
                        })
                    })
                    .then(response => response.json())
                    .then(parent => {
                        if (parent.error) {
                            alert(parent.error);
                        } else {
                            document.querySelector('#add-edit-data [name="name"]').value = parent.parent_name;
                            document.querySelector('#add-edit-data [name="email"]').value = parent.parent_email;
                            document.querySelector('#add-edit-data [name="phone"]').value = parent.parent_phone;
                            document.querySelector('#add-edit-data [name="parent_id"]').value = parent.parent_id;

                            document.querySelector('#add-edit-data [name="password"]').removeAttribute('required');
                            document.querySelector('#add-edit-data [name="confirm_password"]').removeAttribute('required');

                            document.querySelector('#add-edit-data h1').textContent = "Edit Parent";
                            document.getElementById('add-edit-data').showModal();
                        }
                    })
                    .catch(error => {
                        console.error('Error fetching parent data:', error);
                        alert('Failed to load parent data.');
                    });
            });
        });

        const parentForm = document.getElementById('parent-form-ajax');

        parentForm.addEventListener('submit', function(e) {
            e.preventDefault();

            const name = document.querySelector('#add-edit-data [name="name"]').value;
            const email = document.querySelector('#add-edit-data [name="email"]').value;
            const phone = document.querySelector('#add-edit-data [name="phone"]').value;
            const password = document.querySelector('#add-edit-data [name="password"]').value;
            const confirmPassword = document.querySelector('#add-edit-data [name="confirm_password"]').value;
            const adminId = '<?php echo $_SESSION['admin_id']; ?>';

            if (password !== confirmPassword) {
                showAlert('Passwords do not match!');
                return;
            }

            fetch('/mips/admin/ajax.php?action=save_parent', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: new URLSearchParams({
                        name: name,
                        email: email,
                        phone: phone,
                        password: password,
                        confirm_password: confirmPassword,
                        parent_id: document.querySelector('#add-edit-data [name="parent_id"]').value,
                        admin_id: adminId
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        window.location.reload();
                    } else {
                        showAlert(data.error || 'An error occurred while saving the parent.');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showAlert('An unexpected error occurred.');
                });
        });

        function showAlert(message) {
            const alertHtml = `<div class="mini-alert">${message}</div>`;
            const alertContainer = document.getElementById('alert-container');
            alertContainer.innerHTML = alertHtml;

            setTimeout(function() {
                const alertElement = document.querySelector('.mini-alert');
                if (alertElement) {
                    alertElement.style.opacity = '0';
                    setTimeout(() => alertElement.remove(), 600);
                }
            }, 3000);
        }

        document.querySelectorAll('#add-edit-data .cancel').forEach(button => {
            button.addEventListener('click', function() {
                document.querySelector('#add-edit-data h1').textContent = "Add New Parent";
            });
        });
    </script>
</body>

</html>