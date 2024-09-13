<?php
session_start();
include "../../components/db_connect.php";
if (!isset($_SESSION['user_id'])) {
    header('Location: /mips/admin/login.php');
    exit();
}

$stmt = $pdo->query("SELECT * FROM `event` where DATE(date) > DATE(now())");
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

try {
    function generateID()
    {
        return uniqid(); // Function to generate a unique ID
    }
    // Check if form data is submitted
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Get the form values
        $id = generateID();
        $name = $_POST['Name'];
        $place = $_POST['Place'];
        $time = $_POST['Time'];
        $date = $_POST['Date'];
        $desc = $_POST['Desc'];
        $pic = $_POST['Pic'];

        // Prepare SQL statement to insert data
        $sql = "INSERT INTO `event` (`event_id`, `name`, `time`, `date`, `place`, `description`, `picture`) 
                                VALUES (:id, :name, :time, :date, :place, :description, :picture)";

        // Prepare the statement
        $stmt = $pdo->prepare($sql);

        // Bind parameters
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':time', $time);
        $stmt->bindParam(':date', $date);
        $stmt->bindParam(':place', $place);
        $stmt->bindParam(':description', $desc);
        $stmt->bindParam(':picture', $pic);
        header("Location: /mips/admin/admin_meal/adminMain.php");

        // Execute the query
        if ($stmt->execute()) {
            echo "New event added successfully!";
        } else {
            echo "Error adding event.";
        }
    }
} catch (PDOException $e) {
    // Catch any errors and display an appropriate message
    echo "Connection failed: " . $e->getMessage();
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
    <div class="bigbox">
        <row id="row1">
            <a href="../index.php"><i class='bx bx-arrow-back'></i></a>
            <h1>Upcoming Event</h1>
        </row>
    </div>


    <div class="scroll-container">
        <?php if (!empty($rows)): ?>
            <?php foreach ($rows as $row): ?>
                <?php
                // Construct the URL for the next page with just the 'event_id' parameter
                $nextPageUrl = 'event.php?' . http_build_query([
                    'event_id' => $row['event_id']
                ]);

                // Debug: Output the URL for verification
                // Uncomment the line below to see the URLs being generated
                // echo '<p>Generated URL: ' . htmlspecialchars($nextPageUrl) . '</p>';
                ?>
                <!-- Wrap the box in an anchor tag -->
                <a href="<?= htmlspecialchars($nextPageUrl) ?>" style="text-decoration: none; color: inherit;">
                    <div class="box">
                        <!-- Essentials section -->
                        <div class="essentials">
                            <p></p>
                        </div>
                        <!-- Extra info section -->
                        <div class="extra-info">
                            <row id="row1">
                                <h3><?= htmlspecialchars($row['name']) ?></h3>
                            </row>
                            <row class="row">
                                <i class='bx bx-current-location'></i>
                                <p><?= htmlspecialchars($row['place']) ?></p>
                            </row>
                            <row class="row">
                                <i class='bx bx-time-five'></i>
                                <p><?= htmlspecialchars($row['time']) ?></p>
                            </row>
                            <row class="row">
                                <i class='bx bx-calendar'></i>
                                <p><?= htmlspecialchars($row['date']) ?></p>
                            </row>
                            <row class="row">
                                <p id="desc"><?= htmlspecialchars($row['description']) ?></p>
                            </row>
                        </div>
                    </div>
                </a>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No records found.</p>
        <?php endif; ?>
    </div>
    <a href="#">
        <div class="btnbox">
            <button class="slide-button">
                <i class='bx bx-add-to-queue'></i>
                <span>Add Event</span>
            </button>
        </div>
    </a>




    <dialog class="addEvent">
        <i class='bx bx-x' id="xbtn"></i>
        <form method="POST" action="">
            <div>
                <h1>Please fill in required credentials</h1>
            </div>
            <div>
                <label for="name">Name :</label>
                <input type="text" id="name" name="Name" required>
            </div>
            <div>
                <label for="time">Time :</label>
                <input type="text" id="time" name="Time" required>
            </div>
            <div>
                <label for="place">Place :</label>
                <input type="text" id="place" name="Place" required>
            </div>
            <div>
                <label for="date">Date :</label>
                <input type="date" id="date" name="Date" required>
            </div>
            <div>
                <label for="desc">Description :</label>
                <textarea id="desc" name="Desc" rows="4" cols="5" required></textarea>
            </div>
            <div>
                <label for="pic">Picture (Work in Progress) :</label>
                <input type="file" id="pic" name="Pic">
            </div>
            <div>
                <input type="submit" value="Add" id="btn1">
            </div>
        </form>
    </dialog>
    <script>
        const modal = document.querySelector('.addEvent');
        const openModal = document.querySelector('.btnbox');
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