<?php
session_start();
include "../../components/db_connect.php"; // Your database connection

// Fetch the event_id (example event_id given)
$event_id = 'KJ3298392';

try {
    // Check if meal_type_id is set and retrieve it
    if (isset($_GET['meal_type_id'])) {
        $meal_type_id = $_GET['meal_type_id'];

        // Prepare and execute the query to search for meals based on meal_type_id
        $stmt = $pdo->prepare("SELECT * FROM `event_meal` 
                                INNER JOIN `meal_type` ON event_meal.meal_type_id = meal_type.meal_type_id
                                INNER JOIN `meals` ON event_meal.event_meal_id = meals.event_meal_id
                                WHERE event_meal.event_id = :event_id AND event_meal.meal_type_id = :meal_type_id");

        // Bind the parameters
        $stmt->bindParam(':event_id', $event_id);
        $stmt->bindParam(':meal_type_id', $meal_type_id);
        $stmt->execute();

        // Fetch the results
        $meals = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Display the meals if available
        if ($meals) {
            echo '<h2>Meal Information</h2>';
            foreach ($meals as $meal) {
                echo '<p><strong>Meal Name:</strong> ' . htmlspecialchars($meal['meal_name']) . '</p>';
                // Uncomment below if you want to show descriptions too
                // echo '<p><strong>Description:</strong> ' . htmlspecialchars($meal['description']) . '</p>';
                echo '<hr>';
            }
        } else {
            echo '<p>No meals found for this meal type.</p>';
        }
    } else {
        echo '<p>Error: No meal type selected.</p>';
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
    exit();
}

// Debugging output (only if $meals is set)
if (!empty($meals)) {
    // echo '<pre>';
    // var_dump($meals);
    // echo '</pre>';
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Event Slider</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <!-- form.php -->
    <form method="GET" action="">
        <button type="submit" name="meal_type_id" value="1">View Breakfast</button>
        <button type="submit" name="meal_type_id" value="2">View Lunch</button>
    </form>

</body>
</html>
