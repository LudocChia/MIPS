<?php
session_start();
include "../../components/db_connect.php";
// include $_SERVER['DOCUMENT_ROOT'] . "/mips/components/db_connect.php";

if (!isset($_SESSION['admin_id'])) {
    header('Location: /mips/admin/login.php');
    exit();
}
    // session_start();
    include "../admin_for_meal/header.php"; 

    function getAll($pdo){
        // Query to fetch all users
        $stmt = $pdo->query("SELECT * FROM `event`");
        $visitors = $stmt->fetchAll();
    }
    function getByDate($pdo){
        $stmt = $pdo->query("SELECT * FROM `event` where DATE(date) > DATE(now())");
        $visitors = $stmt->fetchAll();
    }
    function generateID(){
        uniqid();
    }
    $all = getByDate($pdo);
    $sql = "SELECT * FROM `event` where DATE(date) > DATE(now())";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meal Donation</title>
    <link rel="icon" type="image/x-icon" href="../images/MIPS_icon.png">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
    <script src="https://code.jquery.com/jquery-3.7.1.js" integrity="sha256-eKhayi8LEQwp4NKxN+CfCh+3qOVUtJn3QNZ0TciWLP4=" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="../admin_for_meal/base.css">
    <link rel="stylesheet" href="../admin_for_meal/common.css">
    <link rel="stylesheet" href="../admin_for_meal/admin.css">
    <link rel="stylesheet" href="../admin_for_meal/donation.css">
    <style>
        h1{
            margin-top: 50px;
            margin-left: 350px;
        }
        .eventBox{
            padding: 0px;
        }
        .eventBox p{
            margin: 0px;
        }
    </style>
</head>
<body>
    <script src="../admin_for_meal/admin.js"></script>
    <div class="top-bar">
        <a href="../admin_for_meal/donationMain.php" ><img src="back-button.png" alt="back-button" style="width: 80px;px;height:80px;"></a>
        <h1>Upcoming Event</h1>
        <button id="add">Add</button>
    </div>

    <?php
    // Initialize $events as an empty array to avoid undefined variable error
    $events = [];

    try {
        // Your initial query (modify this to your actual conditions)
        $stmt = $pdo->query("SELECT * FROM `event` WHERE event_id = 1");

        // Check if any rows were returned
        if ($stmt->rowCount() > 0) {
            // Execute a new query to fetch events where date is in the future
            $stmt = $pdo->query("SELECT * FROM `event` WHERE DATE(date) > DATE(now())");
            // Fetch all events
            $events = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } else {
            // No results found, display the picture
            echo '<div class="center-container">
            <img src="../admin_for_meal/pngwing(1).com.png" alt="No results found" style="width: 520px;px;height:520px;">
            <p class="center-text">-No event for now-</p>
            </div>';
        }
    } catch (PDOException $e) {
        // Handle any errors
        echo "Error: " . $e->getMessage();
    }
    ?>

    <?php if (!empty($events)): ?>
        <?php foreach ($events as $event): ?>
            <div class="eventBox">
                <div class="row" id="row">
                    <p id="date">Date: <?= htmlspecialchars($event['date']) ?></p>
                </div>
                <div class="row">
                    <p id="name"><?= htmlspecialchars($event['name']) ?></p>
                </div>
                <div class="row" id="row1">
                    <p id="time">Time: <?= htmlspecialchars($event['time']) ?></p>
                    <p id="place">Place: <?= htmlspecialchars($event['place']) ?></p>
                </div>
                <div class="row">
                    <p id="desc">Description: <?= htmlspecialchars($event['description']) ?></p>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>


</body>
</html>