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
        $stmt = $pdo->query("SELECT * FROM visitor");
        $visitors = $stmt->fetchAll();
    }
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
            margin-top: -50px;
            margin-left: 350px;
        }
    </style>
</head>
<body>
    <script src="../admin_for_meal/admin.js"></script>
    <a href="../admin_for_meal/donationMain.php" class="back"><img src="back-button.png" alt="back-button" style="width: 80px;px;height:80px;"></a>
    <button id="add">Add</button>
    <h1>Upcoming Event</h1>
    <div>

    </div>

</body>
</html>