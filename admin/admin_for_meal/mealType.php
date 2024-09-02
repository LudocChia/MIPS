<?php
session_start();
include "../../components/db_connect.php";

if (isset($_GET['event_id'])) {
    $event_id = htmlspecialchars($_GET['event_id']);

    // Example:
    // $stmt = $pdo->prepare("SELECT * FROM events WHERE event_id = :event_id");
    // $stmt->bindParam(':event_id', $event_id);
    // $stmt->execute();
    // $event = $stmt->fetch();
    // echo '<p>Event_ID: ' . htmlspecialchars($event_id) . '</p>';
     // echo '<p>Name: ' . htmlspecialchars($event['name']) . '</p>';
} else {
    echo "No event selected.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meal Donation</title>
    <link rel="icon" type="image/x-icon" href="../../images/MIPS_icon.png">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
    <script src="https://code.jquery.com/jquery-3.7.1.js" integrity="sha256-eKhayi8LEQwp4NKxN+CfCh+3qOVUtJn3QNZ0TciWLP4=" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="../admin_for_meal/base.css">
    <link rel="stylesheet" href="../admin_for_meal/common.css">
    <link rel="stylesheet" href="../admin_for_meal/admin.css">
    <link rel="stylesheet" href="../admin_for_meal/donation.css">
</head>
<body>
    <?php include "../admin_for_meal/header.php";  ?>
    <script src="../admin_for_meal/admin.js"></script>

    <div class="topB">
        <a href="../admin_for_meal/event.php" id="back"><img src="back-button.png" alt="back-button" style="width: 80px;px;height:80px;"></a>
            <a href="#" id="add"><img src="pngwing.com(2).png" alt="back-button" style="width: 150px;height:150px;"></a>

    </div>

    <?php
    // Initialize $events as an empty array to avoid undefined variable error
    $events = [];

    try {
        // Example event_id for demonstration (you should replace this with actual logic to fetch $event_id)
        $event_id = $_GET['event_id'] ?? null; // Assuming you're getting the event_id from a GET request
        $meal_type_id = $_GET['meal_type_id'] ?? null;

        // Ensure event_id is not null or empty to avoid SQL errors
        if (!empty($event_id)) {
            // Using a prepared statement to prevent SQL injection
            $stmt = $pdo->prepare("SELECT * FROM `event_meal` 
                                INNER JOIN `meal_type` ON event_meal.meal_type_id = meal_type.meal_type_id 
                                WHERE event_meal.event_id = :event_id");

            // Bind the event_id parameter to the placeholder
            $stmt->bindParam(':event_id', $event_id);

            // Execute the prepared statement
            $stmt->execute();

            // Fetch all events
            $events = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Check if any rows were returned
            if (empty($events)) {
                // No results found, display the picture
                echo '<div class="center-container">
                        <img src="../admin_for_meal/pngwing(1).com.png" alt="No results found" style="width: 520px;height:520px;">
                        <p class="center-text">-No event for now-</p>
                    </div>';
            }
        } else {
            // Handle the case where event_id is not provided
            echo '<div class="center-container">
                    <img src="../admin_for_meal/pngwing(1).com.png" alt="No results found" style="width: 520px;height:520px;">
                    <p class="center-text">-No meal type for now-</p>
                </div>';
        }
    } catch (PDOException $e) {
        // Handle any errors
        echo "Error: " . $e->getMessage();
    }
    ?>


<?php if (!empty($events)): ?>
    <?php foreach ($events as $event): ?>
        <?php 
        // Construct the URL with the event_id parameter
        $nextPageUrl = 'menuOption.php?event_id=' . urlencode($event['event_id']) . '&meal_type_id=' . urlencode($event['meal_type_id']);
        ?>
        <!-- Wrap the event box in an anchor tag -->
        <a href="<?= htmlspecialchars($nextPageUrl) ?>" style="text-decoration: none; color: inherit;">
            <div class="mealBox">
                <row id="row">
                    <p id="meal"><?= htmlspecialchars($event['name']) ?></p>
                </row>
            </div>
        </a>
    <?php endforeach; ?>
<?php endif; ?>
</body>
</html>