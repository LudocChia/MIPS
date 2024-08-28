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
</head>
<body>
    <script src="../admin_for_meal/admin.js"></script>
    <div class="bigflex">
        <a href="../admin_for_meal/event.php" ><img src="back-button.png" alt="back-button" style="width: 80px;px;height:80px;"></a>
        <box1>
            <h1>Please fill in required credentials</h1>
            <row>
                <column>
                    <label for="name">Name :</label>
                    <input type="text" id="name" name="Name">
                </column>
                <column>
                    <label for="place">Place :</label>
                    <input type="text" id="place" name="Place">
                </column>
            </row>
            <row>
                <column>
                    <label for="time">Time :</label>
                    <input type="text" id="time" name="Time">
                </column>
                <column>
                    <label for="date">Date :</label>
                    <input type="date" id="date" name="Date">
                </column>
            </row>
            <row>
                <column>
                    <label for="meals">Meals type:</label>
                    <row>
                        <row1>
                            <input type="checkbox" id="morningTea" name="meals" value="morningTea">
                            <label for="morningTea">Morning tea</label>
                        </row1>
                        <row1>
                            <input type="checkbox" id="lunch" name="meals" value="lunch">
                            <label for="lunch">Lunch</label>
                        </row1>
                        <row1>
                            <input type="checkbox" id="eveningTea" name="meals" value="eveningTea">
                            <label for="eveningTea">Evening tea</label>
                        </row1>
                    </row>
                </column>
            </row>
            <row>
                <column>
                    <label for="desc">Description :</label>
                    <textarea id="desc" name="desc" rows="5" cols="75" required></textarea>
                </column>
            </row>
        </box1>
    </div>
</body>
</html>