<?php
session_start();
include "../../components/db_connect.php";

// Check if user is logged in as admin
if (!isset($_SESSION['admin_id'])) {
    header('Location: /mips/admin/login.php');
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
        $meal_id = generateID();
        $meal_name = $_POST['Name'];
        $person_per_set = $_POST['Ppl'];
        $sets = $_POST['Set'];

        // Generate event_meal_id only once
        $event_meal_id = generateID();

        try {
            // Start transaction
            $pdo->beginTransaction();

            // Insert into `event_meal` table
            $stmt = $pdo->prepare("INSERT INTO event_meal (event_meal_id, event_id, meal_type_id) VALUES (:event_meal_id, :event_id, :meal_type_id)");
            $stmt->execute([
                ':event_meal_id' => $event_meal_id,
                ':event_id' => $event_id,
                ':meal_type_id' => $meal_type_id
            ]);

            // Insert into `meals` table
            $stmt = $pdo->prepare("INSERT INTO meals (meal_id, meal_name, person_per_set, sets, event_meal_id) 
                                   VALUES (:meal_id, :meal_name, :person_per_set, :sets, :event_meal_id)");
            $stmt->execute([
                ':meal_id' => $meal_id,
                ':meal_name' => $meal_name,
                ':person_per_set' => $person_per_set,
                ':sets' => $sets,
                ':event_meal_id' => $event_meal_id
            ]);

            // Commit transaction
            $pdo->commit();

            // Redirect back to the same page with event_id and meal_type_id in the URL
            header("Location: /mips/admin/admin_meal/allMeal.php?event_id=$event_id&meal_type_id=$meal_type_id");
            exit(); // Prevent further execution
        } catch (Exception $e) {
            // Rollback transaction if something goes wrong
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
    <title>Meal Donation</title>
    <link rel="icon" type="image/x-icon" href="../../images/MIPS_icon.png">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
    <script src="https://code.jquery.com/jquery-3.7.1.js" integrity="sha256-eKhayi8LEQwp4NKxN+CfCh+3qOVUtJn3QNZ0TciWLP4=" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- Boxicons CSS -->
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="../admin_for_meal/base.css">
    <link rel="stylesheet" href="../admin_for_meal/common.css">
    <link rel="stylesheet" href="../admin_for_meal/admin.css">
    <link rel="stylesheet" href="../admin_meal/adminDonation.css">

</head>
<body>
    <?php include "../admin_for_meal/header.php";  ?>
    <script src="../admin_for_meal/admin.js"></script>
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
            <div ><img src="../admin_for_meal/pngwing.com.png" alt="Image 1"></div>
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
        <?php if (!empty($meals)): ?>
        <?php foreach ($meals as $meal): ?>
            <?php 
                // Construct the URL for the next page with 'event_id' and 'meal_type_id' parameters
                $nextPageUrl = 'mealDesc.php?' . http_build_query([
                    'meal_id' => $meal['meal_id'], 
                    'event_id' => $meal['event_id'],      // Add event_id
                    'meal_type_id' => $meal['meal_type_id']  // Add meal_type_id
                ]); 

                // Debug: Output the URL for verification
                // Uncomment the line below to see the URLs being generated
                // echo '<p>Generated URL: ' . htmlspecialchars($nextPageUrl) . '</p>';
            ?>
            <!-- Wrap the box in an anchor tag -->
            <a href="<?= htmlspecialchars($nextPageUrl) ?>" style="text-decoration: none; color: inherit;">
                <div class="row5">
                    <h3><?= htmlspecialchars($meal['meal_name']) ?></h3> 
                    <p><?= htmlspecialchars($meal['sets']) ?>  set needed </p>
                    <p><?= htmlspecialchars($meal['person_per_set']) ?>  person per set</p>
                </div>
            </a>
        <?php endforeach; ?>
    <?php else: ?>
        <p id="nRecord">No records found.</p>
    <?php endif; ?>


        <row id="row6">
            <button id="addbtn">Add New Food</button>
            <p></p>
        </row>
    </div>

    <dialog  class="addMeal"  >
        <i class='bx bx-x' id="xbtn"></i>
            <form method="POST" action="">
                <div>
                    <h1>Please fill in required credentials</h1>
                </div>
                <div>
                    <label for="name">Name :</label>
                    <input type="text" id="name" name="Name">
                </div>
                <div>
                    <label for="ppl">People per set (Portion):</label>
                    <input type="text" id="ppl" name="Ppl">
                </div>
                <div>
                    <label for="set">Set :</label>
                    <input type="text" id="set" name="Set">
                </div>
                <div>
                    <input type="submit" value="Add" id="btn1">
                </div>
            </form>
    </dialog>
    <script>
        const modal = document.querySelector('.addMeal');
        const openModal = document.querySelector('#addbtn');
        const closeModal = document.querySelector('#xbtn');

        openModal.addEventListener('click', () => {
            modal.showModal();
        })
        closeModal.addEventListener('click', () => {
            modal.close();
        })
    </script>

</body>
</html>