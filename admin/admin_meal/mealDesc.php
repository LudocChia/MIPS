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
    // Retrieve the event_id and meal_type_id values
    $meal_id = $_GET['meal_id'];
    $event_id = $_GET['event_id'];
    $meal_type_id = $_GET['meal_type_id'];
        // Prepare and execute the query to search for meals based on meal_type_id
        $stmt = $pdo->prepare("SELECT * FROM `event_meal` 
                                INNER JOIN `meal_type` ON event_meal.meal_type_id = meal_type.meal_type_id
                                INNER JOIN `meals` ON event_meal.event_meal_id = meals.event_meal_id
                                INNER JOIN `event` ON event_meal.event_id = event.event_id
                                WHERE event_meal.event_id = :event_id AND event_meal.meal_type_id = :meal_type_id AND meals.meal_id = :meal_id");

        // Bind the parameters
        $stmt->bindParam(':meal_id', $meal_id);
        $stmt->bindParam(':event_id', $event_id);
        $stmt->bindParam(':meal_type_id', $meal_type_id);
        $stmt->execute();

        // Fetch the results
        $meals = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Display the meals if available
        if ($meals) {
            echo '<h2>Meal Information</h2>';
            foreach ($meals as $meal) {
                echo '<p>Event_ID: ' . htmlspecialchars($meal['event_id']) . '</p>';
                echo '<p>Name: ' . htmlspecialchars($meal['name']) . '</p>';
                echo '<p><strong>Description:</strong> ' . htmlspecialchars($meal['description']) . '</p>';
                echo '<p><strong>Meal Name:</strong> ' . htmlspecialchars($meal['meal_name']) . '</p>';
                echo '<p>Meal_ID: ' . htmlspecialchars($meal['meal_id']) . '</p>';
                // Uncomment below if you want to show descriptions too
                // echo '<hr>';
            }
        } else {
            echo '<p>No meals found for this meal type.</p>';
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
    <div class="bigbox2">
        <?php 
            // Construct the URL for the next page with both 'event_id' and 'meal_type_id' parameters
            $backPageUrl = 'allMeal.php?' . http_build_query([
                'event_id' => $meal['event_id'],     // Add comma here
                'meal_type_id' => $meal['meal_type_id'] // Ensure comma separates array elements
            ]); 

            // Debug: Output the URL for verification
            // Uncomment the line below to see the URLs being generated
            // echo '<p>Generated URL: ' . htmlspecialchars($backPageUrl) . '</p>';
        ?>
        <row id="row1">
            <a href="<?= htmlspecialchars($backPageUrl) ?>" style="text-decoration: none; color: inherit;">
                <i class='bx bx-arrow-back' ></i>
            </a>
        </row>
    </div>

</body>
</html>