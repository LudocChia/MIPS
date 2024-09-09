<?php
// Database connection
session_start();
include "../components/db_connect.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Handle form submission
    $donator_id = $_POST['donator_id'];
    $parent_id = $_POST['parent_id'];
    $meal_id = $_POST['meal_id'];
    $p_set = $_POST['P_set'];
    $current_date = date('Y-m-d H:i:s'); // Current date and time
    $event_meal_id = 1; // Example static value, change as needed

    // Insert the data into the Donation table
    $sql = "INSERT INTO Donation (donator_id, parent_id, meal_id, P_set, current_date, event_meal_id) 
            VALUES (?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssss", $donator_id, $parent_id, $meal_id, $p_set, $current_date, $event_meal_id);

    if ($stmt->execute()) {
        $message = "Donation successfully recorded!";
    } else {
        $message = "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}

// Fetch meals (example code; modify as needed)
$meals = [
    ['meal_id' => 1, 'meal_name' => 'Meal A', 'sets' => 3, 'person_per_set' => 5],
    ['meal_id' => 2, 'meal_name' => 'Meal B', 'sets' => 2, 'person_per_set' => 4],
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meal Donation</title>
    <style>
        /* Simple modal styling */
        #credentialDialog {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            border: 1px solid #ccc;
            padding: 20px;
            background-color: white;
            z-index: 1000;
        }
        .row5 {
            cursor: pointer;
            margin: 10px;
            padding: 10px;
            border: 1px solid #ccc;
        }
    </style>
</head>
<body>

<?php if (!empty($meals)): ?>
    <?php foreach ($meals as $meal): ?>
        <div class="row5" onclick="openDialog('<?= $meal['meal_id'] ?>')">
            <h3><?= htmlspecialchars($meal['meal_name']) ?></h3>
            <p><?= htmlspecialchars($meal['sets']) ?> set needed</p>
            <p><?= htmlspecialchars($meal['person_per_set']) ?> person per set</p>
        </div>
    <?php endforeach; ?>
<?php else: ?>
    <p id="nRecord">No meals for now.</p>
<?php endif; ?>

<!-- Dialog Box -->
<div id="credentialDialog">
    <h3>Enter your credentials</h3>
    <form method="post">
        <input type="hidden" name="meal_id" id="meal_id">
        <label for="donator_id">Donator ID:</label>
        <input type="text" id="donator_id" name="donator_id" required>
        <label for="parent_id">Parent ID:</label>
        <input type="text" id="parent_id" name="parent_id" required>
        <label for="P_set">P Set:</label>
        <input type="number" id="P_set" name="P_set" required>
        <button type="submit">Submit</button>
    </form>
</div>

<?php if (isset($message)): ?>
    <script>
        alert("<?= $message ?>");
    </script>
<?php endif; ?>

<script>
    // Open the dialog
    function openDialog(meal_id) {
        document.getElementById('meal_id').value = meal_id; // Pass meal_id to the form
        document.getElementById('credentialDialog').style.display = 'block';
    }

    // Close the dialog when clicking outside (optional)
    window.onclick = function(event) {
        var dialog = document.getElementById('credentialDialog');
        if (event.target === dialog) {
            dialog.style.display = 'none';
        }
    }
</script>

</body>
</html>
