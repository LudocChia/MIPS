<?php

$database_table = "Sizes";
$rows_per_page = 12;
include $_SERVER['DOCUMENT_ROOT'] . "/mips/php/admin.php";
include $_SERVER['DOCUMENT_ROOT'] . "/mips/php/activate_pagination.php";

function getSizes($pdo, $start, $rows_per_page)
{
    $sql = "SELECT * FROM Sizes WHERE is_deleted = 0 ORDER BY size_name ASC
            LIMIT :start, :rows_per_page;";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':start', $start, PDO::PARAM_INT);
    $stmt->bindParam(':rows_per_page', $rows_per_page, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$all_product_sizes = getSizes($pdo, $start, $rows_per_page);

if (isset($_POST['submit'])) {
    $name = $_POST['name'];
    $shoulder_width = $_POST['shoulder_width'];
    $bust = $_POST['bust'];
    $waist = $_POST['waist'];
    $length = $_POST['length'];
    $sizeId = isset($_POST['product_size_id']) ? $_POST['product_size_id'] : null;

    if (!empty($name)) {
        if ($sizeId) {
            $sql = "UPDATE Sizes SET size_name = :name, shoulder_width = :shoulder_width, bust = :bust, waist = :waist, length = :length WHERE size_id = :size_id";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':size_id', $sizeId);
        } else {
            $sql = "INSERT INTO Sizes (size_name, shoulder_width, bust, waist, length) VALUES (:name, :shoulder_width, :bust, :waist, :length)";
            $stmt = $pdo->prepare($sql);
        }

        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':shoulder_width', $shoulder_width);
        $stmt->bindParam(':bust', $bust);
        $stmt->bindParam(':waist', $waist);
        $stmt->bindParam(':length', $length);

        try {
            $stmt->execute();
            header('Location: size.php');
            exit();
        } catch (PDOException $e) {
            echo "<script>alert('Database error: " . $e->getMessage() . "');</script>";
        }
    } else {
        echo "<script>alert('Please enter a product size name.');</script>";
    }
}

$pageTitle = "Apparel Size - MIPS";
include $_SERVER['DOCUMENT_ROOT'] . "/mips/components/admin_head.php"; ?>

<body id="<?php echo $id ?>">
    <?php include $_SERVER['DOCUMENT_ROOT'] . "/mips/components/admin_header.php"; ?>
    <div class="container">
        <?php include $_SERVER['DOCUMENT_ROOT'] . "/mips/components/admin_sidebar.php"; ?>
        <main class="product-size">
            <div class="wrapper">
                <div class="title">
                    <div class="left">
                        <h1>Apparel Size</h1>
                    </div>
                    <div class="right">
                        <button id="open-popup" class="btn btn-outline-primary"><i class="bi bi-plus-circle"></i>Add Apparel Size</button>
                    </div>
                </div>
                <div class="table-body">
                    <table>
                        <thead>
                            <tr>
                                <th>Apparel Size Name</th>
                                <th>Shoulder Width</th>
                                <th>Bust</th>
                                <th>Waist</th>
                                <th>Length</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($all_product_sizes as $size) { ?>
                                <tr>
                                    <td><?= htmlspecialchars($size['size_name']); ?></td>
                                    <td><?= htmlspecialchars($size['shoulder_width'] === null || $size['shoulder_width'] == 0 ? '-' : $size['shoulder_width']); ?></td>
                                    <td><?= htmlspecialchars(($size['bust'] === null || $size['bust'] == 0) ? '-' : $size['bust']); ?></td>
                                    <td><?= htmlspecialchars($size['waist'] === null || $size['waist'] == 0 ? '-' : $size['waist']); ?></td>
                                    <td><?= htmlspecialchars($size['length'] === null || $size['length'] == 0 ? '-' : $size['length']); ?></td>
                                    <td>
                                        <form action="" method="POST" style="display:inline;" onsubmit="return showDeactivateConfirmDialog(event);">
                                            <input type="hidden" name="product_size_id" value="<?= htmlspecialchars($size['size_id']); ?>">
                                            <input type="hidden" name="action" value="true">
                                            <button type="submit" class="delete-category-btn"><i class="bi bi-x-square"></i></button>
                                        </form>
                                        <button type="button" class="edit-size-btn" data-size-id="<?= htmlspecialchars($size['size_id']); ?>"><i class="bi bi-pencil-square"></i></button>
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
                <h1>Add Apparel Size</h1>
            </div>
            <div class="right">
                <button class="cancel"><i class="bi bi-x-circle"></i></button>
            </div>
        </div>
        <form action="" method="post" enctype="multipart/form-data">
            <input type="hidden" name="product_size_id" value="">
            <div class="input-container">
                <h2>Product Size Name<sup>*</sup></h2>
                <div class="input-field">
                    <input type="text" name="name" required>
                </div>
                <p>Please enter the size name (e.g., 100, 110, 120).</p>
            </div>
            <div class="input-container">
                <h2>Shoulder Width (cm)</h2>
                <div class="input-field">
                    <input type="number" step="0.01" name="shoulder_width">
                </div>
            </div>
            <div class="input-container">
                <h2>Bust (cm)</h2>
                <div class="input-field">
                    <input type="number" step="0.01" name="bust">
                </div>
            </div>
            <div class="input-container">
                <h2>Waist (cm)</h2>
                <div class="input-field">
                    <input type="number" step="0.01" name="waist">
                </div>
            </div>
            <div class="input-container">
                <h2>Length (cm)</h2>
                <div class="input-field">
                    <input type="number" step="0.01" name="length">
                </div>
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
        document.querySelectorAll('.edit-size-btn').forEach(button => {
            button.addEventListener('click', function() {
                const sizeId = this.dataset.sizeId;
                fetch(`/mips/admin/ajax.php?action=get_size&size_id=${sizeId}`)
                    .then(response => response.json())
                    .then(size => {
                        if (size.error) {
                            alert(size.error);
                        } else {
                            document.querySelector('#add-edit-data [name="product_size_id"]').value = size.size_id;
                            document.querySelector('#add-edit-data [name="name"]').value = size.size_name;
                            document.querySelector('#add-edit-data [name="shoulder_width"]').value = size.shoulder_width;
                            document.querySelector('#add-edit-data [name="bust"]').value = size.bust;
                            document.querySelector('#add-edit-data [name="waist"]').value = size.waist;
                            document.querySelector('#add-edit-data [name="length"]').value = size.length;
                            document.querySelector('#add-edit-data h1').textContent = "Edit Apparel Size";
                            document.getElementById('add-edit-data').showModal();
                        }
                    })
                    .catch(error => {
                        console.error('Error fetching size data:', error);
                        alert('Failed to load size data.');
                    });
            });
        });
    </script>
</body>

</html>