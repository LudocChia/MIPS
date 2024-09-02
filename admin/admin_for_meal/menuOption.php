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
if (isset($_GET['meal_type_id'])) {
    $meal_type_id = htmlspecialchars($_GET['meal_type_id']);

    // Example:
    // $stmt = $pdo->prepare("SELECT * FROM events WHERE event_id = :event_id");
    // $stmt->bindParam(':event_id', $event_id);
    // $stmt->execute();
    // $event = $stmt->fetch();
    // echo '<p>Meal_type_ID: ' . htmlspecialchars($meal_type_id) . '</p>';
     // echo '<p>Name: ' . htmlspecialchars($event['name']) . '</p>';
} else {
    echo "No meal type selected.";
}

function generateID(){
    uniqid();
}
$sql = "SELECT * FROM `event` where DATE(date) = 1";
$stmt = $pdo->prepare($sql);
$stmt->execute();

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
    <div class="top-bar">
        <a href="../admin_for_meal/event.php" ><img src="back-button.png" alt="back-button" style="width: 80px;px;height:80px;"></a>
        <h1>Menu Option</h1>
        <button id="add">Add</button>
        
    </div>

    <?php
    // Initialize $events as an empty array to avoid undefined variable error
    $events = [];

    try {
        // Your initial query (modify this to your actual conditions)
        $stmt = $pdo->query("SELECT * FROM `event` WHERE event_id = null");

        // Check if any rows were returned
        if ($stmt->rowCount() > 0) {
            // Execute a new query to fetch events where date is in the future
            $stmt = $pdo->query("SELECT * FROM `event` WHERE DATE(date) = 1");
            // Fetch all events
            $events = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } else {
            // No results found, display the picture
            echo '<div class="center-container">
            <img src="../admin_for_meal/pngwing.com.png" alt="No results found" style="width: 520px;px;height:520px;"><br><br><br>
            <p class="center-text">-No meals for now-</p>
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
        $nextPageUrl = 'mealType.php?event_id=' . urlencode($event['event_id']); 
        ?>
        <!-- Wrap the event box in an anchor tag -->
        <a href="<?= htmlspecialchars($nextPageUrl) ?>" style="text-decoration: none; color: inherit;">
            <div class="eventBox">
                <row id="row">
                    <p id="date">Date: <?= htmlspecialchars($event['date']) ?></p>
                </row>
                <row>
                    <p id="name"><?= htmlspecialchars($event['name']) ?></p>
                </row>
                <row id="row1">
                    <p id="time">Time: <?= htmlspecialchars($event['time']) ?></p>
                    <p id="place">Place: <?= htmlspecialchars($event['place']) ?></p>
                </row>
                <row>
                    <p id="desc">Description: <?= htmlspecialchars($event['description']) ?></p>
                </row>
            </div>
        </a>
    <?php endforeach; ?>
<?php endif; ?>

<dialog id="formDialog">
    <form method="dialog"> <!-- Using method="dialog" for better form handling within dialogs -->
        <div id="x">
            <i class="bi bi-x-circle" id="xbtn"></i>
        </div>
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
    const modal = document.querySelector('#formDialog');
    const openModal = document.querySelector('#add');
    const closeModal = document.querySelector('#btn1');
    const closeModal1 = document.querySelector('#xbtn');

    openModal.addEventListener('click', () => {
        modal.showModal();
    });

    closeModal.addEventListener('click', () => {
        modal.close();
    });

    closeModal1.addEventListener('click', () => {
        modal.close();
    });
</script>

</body>
</html>