<?php
session_start();
include "../components/db_connect.php";

// Check if user is logged in as parent
if (!isset($_SESSION['user_id'])) {
    header('Location: /mips/index.php');
    exit();
}



// Check if the event_id and meal_type_id parameters are set in the URL
if (isset($_GET['event_id']) && isset($_GET['meal_type_id'])) {
    // Retrieve the event_id and meal_type_id from the GET parameters
    $event_id = $_GET['event_id'];
    $meal_type_id = $_GET['meal_type_id'];

    // Prepare and execute the query to search for meals based on event_id and meal_type_id
    $stmt = $pdo->prepare("SELECT * FROM `event_meal` 
                            INNER JOIN `meal_type` ON event_meal.meal_type_id = meal_type.meal_type_id
                            INNER JOIN `meals` ON event_meal.event_meal_id = meals.event_meal_id
                            INNER JOIN `event` ON event_meal.event_id = event.event_id
                            WHERE event_meal.event_id = :event_id AND event_meal.meal_type_id = :meal_type_id");

    // Bind the parameters
    $stmt->bindParam(':event_id', $event_id);
    $stmt->bindParam(':meal_type_id', $meal_type_id);
    $stmt->execute();

    // Fetch the results
    $meals = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $stmt2 = $pdo->prepare("SELECT * FROM `event` WHERE event_id = :event_id");
    $stmt2->bindParam(':event_id', $event_id);
    $stmt2->execute();

    // Fetch the results of the second query
    $datas = $stmt2->fetchAll(PDO::FETCH_ASSOC);

    // Check if the result set is empty
    if (empty($meals)) {
        // If the result is empty, run another SQL command
        $stmt2 = $pdo->prepare("SELECT * FROM `event` WHERE event_id = :event_id");
        $stmt2->bindParam(':event_id', $event_id);
        $stmt2->execute();

        // Fetch the results of the second query
        $datas = $stmt2->fetchAll(PDO::FETCH_ASSOC);

        if (!empty($datas)) {
            // Process and display results from the fallback query
            foreach ($datas as $data) {
                // echo "Name of event: " . htmlspecialchars($data['name']);
            }
        } else {
            echo "No results found in the fallback query.";
        }
    } else {
        // Process and display results from the primary query
        foreach ($datas as $data) {
            // echo "Meal Name: " . htmlspecialchars($data['meal_name']);
            // Add additional processing if needed
        }
    }



    // Display the meals if available
    if ($meals) {
        // echo '<h2>Meal Information</h2>';
        foreach ($meals as $meal) {
            // Uncomment these lines to display meal details
            // echo '<p>Event_ID: ' . htmlspecialchars($meal['event_id']) . '</p>';
            // echo '<p>Name: ' . htmlspecialchars($meal['name']) . '</p>';
            // echo '<p><strong>Meal Name:</strong> ' . htmlspecialchars($meal['meal_name']) . '</p>';
            // echo '<hr>';
        }
    } else {
        // echo '<p>No meals found for this meal type.</p>';
    }

    // Function to generate a unique ID
    function generateID() {
        return uniqid(); 
    }

    // Check if form is submitted
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $donator_id = generateID();
        $parent_id = $_SESSION['user_id'];
        $meal_id = $meal['meal_id'];
        $p_set = $_POST['Sets'];
        $current_date = date('Y-m-d H:i:s'); // Current date and time

        // Generate event_meal_id only once
        $event_meal_id = $meal['event_meal_id'];


    
        try {
            // Start transaction
            $pdo->beginTransaction();

            // Insert into `donator` table
            $stmt = $pdo->prepare("INSERT INTO `donator`(`donator_id`, `parent_id`, `meal_id`, `p_set`, `date`, `event_meal_id`) 
                                    VALUES (:donator_id, :parent_id, :meal_id, :p_set, :current_date, :event_meal_id)");
            $stmt->execute([
                ':donator_id' => $donator_id,
                ':parent_id' => $parent_id,
                ':meal_id' => $meal_id,
                ':p_set' => $p_set,
                ':current_date' => $current_date,
                ':event_meal_id' => $event_meal_id
            ]);

            // Update `meals` table, deduct sets
            $stmt = $pdo->prepare("UPDATE `meals` SET `sets` = `sets` - :p_set WHERE `meal_id` = :meal_id");
            $stmt->execute([
                ':p_set' => $p_set,
                ':meal_id' => $meal_id
            ]);

            // Commit transaction
            $pdo->commit();

            // Redirect after successful transaction
            header("Location: /mips/parent/allMeal.php?event_id=$event_id&meal_type_id=$meal_type_id");
            exit();
        } catch (Exception $e) {
            $pdo->rollBack();
            echo "Failed to insert data: " . $e->getMessage();
        }
        
    }
} else {
    // Handle the case where the parameters are not set
    echo '<p>Error: Missing event_id or meal_type_id.</p>';
}



// echo '<pre>';
// var_dump($meals);
// echo '</pre>';



?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meal Donation</title>
    <link rel="icon" type="image/x-icon" href="../images/MIPS_icon.png">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
    <script src="https://code.jquery.com/jquery-3.7.1.js" integrity="sha256-eKhayi8LEQwp4NKxN+CfCh+3qOVUtJn3QNZ0TciWLP4=" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- Boxicons CSS -->
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="../admin/admin_for_meal/base.css">
    <link rel="stylesheet" href="../admin/admin_for_meal/common.css">
    <link rel="stylesheet" href="../admin/admin_for_meal/admin.css">
    <link rel="stylesheet" href="../admin/admin_meal/adminDonation.css">
</head>
<body>
    <?php include "customer_header.php";  ?>
    <script src="../admin/admin_for_meal/admin.js"></script>
    <div class="bigbox1">
        <?php 
            // Construct the URL for the next page with just the 'event_id' parameter
            $backPageUrl = 'event.php?' . http_build_query([
                'event_id' => $data['event_id']
            ]); 

            // Debug: Output the URL for verification
            // Uncomment the line below to see the URLs being generated
            // echo '<p>Generated URL: ' . htmlspecialchars($nextPageUrl) . '</p>';
        ?>
        <row id="row1">
            <a href="<?= htmlspecialchars($backPageUrl) ?>" style="text-decoration: none; color: inherit;">
                <i class='bx bx-arrow-back' ></i>
            </a>
        </row>
        <row id="row2">
            <div ><img src="../admin/admin_for_meal/pngwing.com.png" alt="Image 1"></div>
            <column id="column1">
                <h1><?= htmlspecialchars($data['name']) ?></h1>
                <row id="rowF)">
                    <i class='bx bx-time-five'></i>
                    <p><?= htmlspecialchars($data['time']) ?></p>
                </row>
                <row>
                    <i class='bx bx-calendar'></i>
                    <p><?= htmlspecialchars($data['date']) ?></p>
                </row>
            </column>
        </row>
        <row id="row3">
            <h2>Food and Beverage</h2>
        </row>
        <form method="GET" action="allMeal.php">
            <row id="row4">
                <button type="submit" name="meal_type_id" value="1" id="btn1">Breakfast</button>
                <button type="submit" name="meal_type_id" value="2" id="btn2">Lunch</button>
                <input type="hidden" name="event_id" value="<?= htmlspecialchars($event_id); ?>">
            </row>
        </form>
<div class="test">
    <?php if (!empty($meals)): ?>
        <?php foreach ($meals as $meal): ?>
            <?php if ($meal['sets'] > 0): // Only display meals with sets greater than 0 ?>
                <div class="row5" data-meal-id="<?= htmlspecialchars($meal['meal_id']) ?>" data-max-sets="<?= htmlspecialchars($meal['sets']) ?>">
                    <h3><?= htmlspecialchars($meal['meal_name']) ?></h3> 
                    <p><?= htmlspecialchars($meal['sets']) ?> set needed</p>
                    <p><?= htmlspecialchars($meal['person_per_set']) ?> person per set</p>
                </div>
            <?php endif; ?>
        <?php endforeach; ?>
    <?php else: ?>
        <p id="nRecord">No meals for now.</p>
    <?php endif; ?>
</div>


<!-- Modal for adding meals -->
<dialog class="addMeal">
    <i class='bx bx-x' id="xbtn"></i>
    <form method="POST" action="">
        <div>
            <h1>How many sets will you donate?</h1>
        </div>
        <div>
            <label for="sets">Sets:</label>
            <input type="number" id="setsInput" name="Sets" value="1" min="1">
            <p id="maxSetDisplay"></p>
            <input type="hidden" id="mealIdInput" name="meal_id">
        </div>
        <div>
            <input type="submit" value="Add" id="btn1">
        </div>
    </form>
</dialog>

<script>
    const modal = document.querySelector('.addMeal');
    const openModalBtns = document.querySelectorAll('.row5');
    const closeModal = document.querySelector('#xbtn');
    const setsInput = document.querySelector('#setsInput');
    const maxSetDisplay = document.querySelector('#maxSetDisplay');
    const mealIdInput = document.querySelector('#mealIdInput');

    // Add event listener to each "Donate for this meal" button
    openModalBtns.forEach(button => {
        button.addEventListener('click', () => {
            const maxSets = button.getAttribute('data-max-sets');
            const mealId = button.getAttribute('data-meal-id');

            // Set max value for the sets input
            setsInput.setAttribute('max', maxSets);
            setsInput.value = 1; // Reset the input to 1 on opening modal
            maxSetDisplay.textContent = `Maximum sets: ${maxSets}`;

            // Set meal ID in the hidden input field
            mealIdInput.value = mealId;

            // Show the modal
            modal.showModal();
        });
    });

    // Close modal when the close button is clicked
    closeModal.addEventListener('click', () => {
        modal.close();
    });
</script>


</body>
</html>